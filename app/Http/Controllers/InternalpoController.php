<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use App\Models\internal_poline;
use App\Models\SsoTicket;
use App\Models\UserAuth;
use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class InternalPoController extends Controller
{
    const PRINTERS = [
        'TSC TTP-247 internal' => 'ภายใน',
        'TSC TTP-247 store'    => 'สโตร์',
        '\\\\ว้าล\\TSC TTP-247' => 'ภายนอก',
    ];

    const LEGACY_CONNECTION = 'mysql_3e';

    const STATUS_ALL_KEY = '__all__';
    const STATUS_FINISH_KEY = '__finish__';

    const VISIBLE_STATUSES = [
        self::STATUS_ALL_KEY      => 'ทั้งหมด',
        internal_po::ST_PENDING   => 'รอดำเนินการ',
        self::STATUS_FINISH_KEY   => 'จัดเสร็จแล้ว',
        internal_po::ST_CANCEL    => 'ยกเลิก',
    ];

    const PER_PAGE = 100;

    const HIKARI_API_URL = 'https://api.hikaripower.com';
    const HIKARI_API_KEY = 'hikari20259f3c6e1b0f2d9c9c0e5e0b4d8b4e6e9c0c6c2f3e7b8a9f1d2e3c4b5a6f7d8e9';
    const HIKARI_TX_TYPE_STOCKOUT = 'ขายสินค้าออก';

    private ?int $lastLegacyPendingTotal = null;

    private function resolveSsoUser(Request $request, string $logTag): UserAuth
    {
        $ticket = $request->input('ticket');

        if ($ticket && !Auth::guard('web')->check()) {
            $ticketRecord = SsoTicket::where('ticket', $ticket)
                ->where('client_key', '3e')
                ->first();

            if ($ticketRecord && $ticketRecord->markAsUsed()) {
                $user = UserAuth::find($ticketRecord->id_emp);
                if ($user && $user->is_active) {
                    Auth::guard('web')->login($user);
                    Log::info("{$logTag}: SSO login success user={$user->id_emp}");
                } else {
                    Log::warning("{$logTag}: ticket valid but user not found/inactive id_emp={$ticketRecord->id_emp}");
                }
            } else {
                Log::warning("{$logTag}: invalid or expired ticket={$ticket}");
            }
        }

        if (!Auth::guard('web')->check()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->guest(route('login'))
            );
        }

        return Auth::guard('web')->user();
    }

    private function ensureLegacyInternalPoMigrated(array $ids): void
    {
        $existing   = internal_po::whereIn('internal_id', $ids)->pluck('internal_id')->flip();
        $missingIds = collect($ids)->reject(fn ($id) => $existing->has($id))->values();
        if ($missingIds->isEmpty()) return;

        $rows = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->select('ID', 'PO', 'SO')
            ->whereIn('PO', $missingIds->all())
            ->where(function ($q) {
                $q->whereNull('boxS')->orWhere('boxS', '');
            })
            ->get();

        if ($rows->isEmpty()) return;

        $soIdsAll   = $rows->pluck('SO')->unique()->values()->all();
        $custBySoId = DB::connection(self::LEGACY_CONNECTION)->table('so')
            ->whereIn('SONum', $soIdsAll)
            ->get(['SONum', 'CustName'])
            ->keyBy('SONum');

        $poNumsAll = $rows->pluck('PO')->unique()->values()->all();
        $linesByPo = DB::connection(self::LEGACY_CONNECTION)->table('internal_poline')
            ->whereIn('PONum', $poNumsAll)
            ->orderBy('POLineSeq')
            ->get()
            ->groupBy('PONum');

        DB::transaction(function () use ($rows, $custBySoId, $linesByPo) {
            foreach ($rows as $row) {
                if (internal_po::where('internal_id', $row->PO)->exists()) continue;

                internal_po::create([
                    'internal_id'   => $row->PO,
                    'SO_id'         => $row->SO,
                    'customer_name' => optional($custBySoId->get($row->SO))->CustName,
                    'status'        => internal_po::ST_PENDING,
                ]);

                foreach ($linesByPo->get($row->PO, collect()) as $line) {
                    internal_poline::create([
                        'internal_id'   => $row->PO,
                        'SO_id'         => $row->SO,
                        'item_id'       => null,
                        'item_name'     => $line->Description ?: '—',
                        'item_quantity' => (float) $line->Quantity,
                    ]);
                }
            }
        });
    }

    private function legacyPoQuery(Request $request, \Closure $boxSFilter)
    {
        $query = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->select('ID', 'PO', 'SO')
            ->where('PO', 'LIKE', '____-A____')
            ->where($boxSFilter)
            ->whereNotNull('PO')->where('PO', '<>', '')
            ->whereNotNull('SO')->where('SO', '<>', '')
            ->when($request->filled('SONum'), fn ($q) => $q->where('SO', 'LIKE', '%' . $request->input('SONum') . '%'))
            ->when($request->filled('internal_id'), fn ($q) => $q->where('PO', 'LIKE', '%' . $request->input('internal_id') . '%'))
            ->orderByDesc('PO');

        if ($request->filled('customer_name')) {
            $matchedSoIds = DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->where('CustName', 'LIKE', '%' . $request->input('customer_name') . '%')
                ->pluck('SONum');
            $query->whereIn('SO', $matchedSoIds->all());
        }

        return $query;
    }

    private function fetchLegacyRows(Request $request, \Closure $boxSFilter, string $status, int $need): \Illuminate\Support\Collection
    {
        $result = collect();
        $offset = 0;
        $batch  = max($need * 2, 200);

        for ($i = 0; $i < 8; $i++) {
            $chunk = (clone $this->legacyPoQuery($request, $boxSFilter))
                ->offset($offset)
                ->limit($batch)
                ->get();

            if ($chunk->isEmpty()) break;

            $migrated = internal_po::whereIn('internal_id', $chunk->pluck('PO'))->pluck('internal_id')->flip();
            $result = $result->concat($chunk->reject(fn ($r) => $migrated->has($r->PO)));

            $offset += $chunk->count();

            if ($result->count() >= $need) break;
            if ($chunk->count() < $batch) break;
        }

        return $result->unique('PO')->take($need)->values()->map(fn ($r) => (object) [
            'internal_id' => $r->PO,
            'SO_id'       => $r->SO,
            'status'      => $status,
        ]);
    }

    private function countLegacyRows(Request $request, \Closure $boxSFilter): int
    {
        $ids = (clone $this->legacyPoQuery($request, $boxSFilter))->pluck('PO');
        if ($ids->isEmpty()) return 0;

        $migratedCount = 0;
        foreach ($ids->chunk(1000) as $chunk) {
            $migratedCount += internal_po::whereIn('internal_id', $chunk->all())->count();
        }

        return $ids->count() - $migratedCount;
    }

    private function hydrateLegacyPageItems(\Illuminate\Support\Collection $lightItems): \Illuminate\Support\Collection
    {
        if ($lightItems->isEmpty()) return $lightItems;

        $poNums = $lightItems->pluck('internal_id')->unique()->values()->all();
        $soIds  = $lightItems->pluck('SO_id')->unique()->values()->all();

        $custBySoId = DB::connection(self::LEGACY_CONNECTION)->table('so')
            ->whereIn('SONum', $soIds)
            ->get(['SONum', 'CustName'])
            ->keyBy('SONum');

        $linesByPo = DB::connection(self::LEGACY_CONNECTION)->table('internal_poline')
            ->whereIn('PONum', $poNums)
            ->orderBy('POLineSeq')
            ->get()
            ->groupBy('PONum');

        return $lightItems->map(function ($r) use ($custBySoId, $linesByPo) {
            $r->customer_name = optional($custBySoId->get($r->SO_id))->CustName;
            $r->lines = $linesByPo->get($r->internal_id, collect())->map(fn ($l) => (object) [
                'item_id'       => null,
                'item_name'     => $l->Description ?: '—',
                'item_quantity' => (float) $l->Quantity,
            ]);
            return $r;
        });
    }

    private function baseQuery(Request $request, bool $withStatusFilter = true, bool $withLines = true)
    {
        $q = $withLines ? internal_po::with('lines') : internal_po::query();

        if ($request->filled('SONum')) {
            $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
        }
        if ($request->filled('internal_id')) {
            $q->where('internal_id', 'LIKE', '%' . $request->input('internal_id') . '%');
        }
        if ($request->filled('customer_name')) {
            $q->where('customer_name', 'LIKE', '%' . $request->input('customer_name') . '%');
        }

        if ($withStatusFilter) {
            $status = $request->filled('status') ? $request->input('status') : internal_po::ST_PENDING;

            if ($status === self::STATUS_ALL_KEY) {
                //
            } elseif ($status === self::STATUS_FINISH_KEY) {
                $q->whereNotIn('status', [internal_po::ST_PENDING, internal_po::ST_CANCEL]);
            } elseif (array_key_exists($status, self::VISIBLE_STATUSES)) {
                $q->where('status', $status);
            } else {
                $q->where('status', internal_po::ST_PENDING);
            }
        }

        return $q;
    }

    private function loadHeads(Request $request, string $todoStatus)
    {
        $effectiveStatus = $request->filled('status') ? $request->input('status') : internal_po::ST_PENDING;
        $perPage = self::PER_PAGE;
        $page    = max(1, (int) $request->input('page', 1));
        $need    = $page * $perPage;

        $pendingBoxS  = function ($q) { $q->whereNull('boxS')->orWhere('boxS', ''); };
        $finishedBoxS = function ($q) { $q->whereNotNull('boxS')->where('boxS', '<>', ''); };

        $internalTotal = $this->baseQuery($request, true, false)->count();

        $internalRows = $this->baseQuery($request, true, false)
            ->orderByRaw('FIELD(status, ?) DESC', [$todoStatus])
            ->orderByDesc('internal_id')
            ->limit($need)
            ->get();

        $allHeads = $internalRows;
        $legacyPendingTotal  = 0;
        $legacyFinishedTotal = 0;

        if (in_array($effectiveStatus, [internal_po::ST_PENDING, self::STATUS_ALL_KEY], true)) {
            $allHeads = $allHeads->concat($this->fetchLegacyRows($request, $pendingBoxS, internal_po::ST_PENDING, $need));
            $legacyPendingTotal = $this->countLegacyRows($request, $pendingBoxS);
        }

        if (in_array($effectiveStatus, [self::STATUS_FINISH_KEY, self::STATUS_ALL_KEY], true)) {
            $allHeads = $allHeads->concat($this->fetchLegacyRows($request, $finishedBoxS, internal_po::ST_FINISH, $need));
            $legacyFinishedTotal = $this->countLegacyRows($request, $finishedBoxS);
        }

        $allHeads = $allHeads->sort(function ($a, $b) use ($todoStatus) {
            $aPending = $a->status === $todoStatus;
            $bPending = $b->status === $todoStatus;
            if ($aPending !== $bPending) return $aPending ? -1 : 1;
            return strcmp((string) $b->internal_id, (string) $a->internal_id);
        })->values();

        $pageItems = $allHeads->slice(($page - 1) * $perPage, $perPage)->values();

        $newIds = $pageItems->filter(fn ($r) => $r instanceof internal_po)->pluck('internal_id')->values();
        if ($newIds->isNotEmpty()) {
            $linesByInternalId = internal_po::with('lines')
                ->whereIn('internal_id', $newIds)
                ->get()
                ->keyBy('internal_id');

            $pageItems = $pageItems->map(function ($r) use ($linesByInternalId) {
                if ($r instanceof internal_po) {
                    $fresh = $linesByInternalId->get($r->internal_id);
                    if ($fresh) $r->setRelation('lines', $fresh->lines);
                }
                return $r;
            });
        }

        $legacyItemsOnPage = $pageItems->reject(fn ($r) => $r instanceof internal_po)->values();
        if ($legacyItemsOnPage->isNotEmpty()) {
            $hydrated = $this->hydrateLegacyPageItems($legacyItemsOnPage)->keyBy('internal_id');
            $pageItems = $pageItems->map(function ($r) use ($hydrated) {
                if (!($r instanceof internal_po) && $hydrated->has($r->internal_id)) {
                    return $hydrated->get($r->internal_id);
                }
                return $r;
            });
        }

        $this->lastLegacyPendingTotal = $legacyPendingTotal;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $pageItems,
            $internalTotal + $legacyPendingTotal + $legacyFinishedTotal,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );
    }

    private function statusCounts(Request $request): array
    {
        $rows = $this->baseQuery($request, false, false)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $out = [
            internal_po::ST_PENDING => (int) ($rows[internal_po::ST_PENDING] ?? 0),
            internal_po::ST_CANCEL  => (int) ($rows[internal_po::ST_CANCEL] ?? 0),
        ];

        $finishTotal = 0;
        foreach ($rows as $statusKey => $count) {
            if ($statusKey !== internal_po::ST_PENDING && $statusKey !== internal_po::ST_CANCEL) {
                $finishTotal += (int) $count;
            }
        }
        $out[self::STATUS_FINISH_KEY] = $finishTotal;

        $out[internal_po::ST_PENDING] += $this->lastLegacyPendingTotal
            ?? $this->countLegacyRows($request, function ($q) { $q->whereNull('boxS')->orWhere('boxS', ''); });

        return $out;
    }

    private function recentLocations()
    {
        return internal_po::whereNotNull('location_at')
            ->where('location_at', '<>', '')
            ->orderBy('internal_id', 'desc')
            ->limit(200)
            ->pluck('location_at')
            ->unique()->take(50)->values();
    }

    public function pickDashboard(Request $request)
    {
        $authUser     = $this->resolveSsoUser($request, 'internal_po.pick');
        $operatorName = $authUser->name;

        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        $heads        = $this->loadHeads($request, internal_po::ST_PENDING);
        $statusCounts = $this->statusCounts($request);
        $locations    = $this->recentLocations();

        $printers       = self::PRINTERS;
        $statuses       = self::VISIBLE_STATUSES;
        $selectedStatus = $request->filled('status') ? $request->input('status') : internal_po::ST_PENDING;

        return view('internal_po.dashboard', compact(
            'heads', 'locations', 'operatorName', 'printers', 'statuses', 'statusCounts', 'selectedStatus'
        ));
    }

    public function pickSubmit(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        $request->validate([
            'ids'          => 'required|array|min:1',
            'ids.*'        => 'string',
            'printer'      => 'required|string|in:' . implode(',', array_keys(self::PRINTERS)),
            'print_sheets' => 'nullable|integer|min:1|max:20',
        ]);

        $ids          = $request->input('ids');
        $printer      = $request->input('printer');
        $printSheets  = (int) $request->input('print_sheets', 1);
        $operatorName = $authUser->name;

        $this->ensureLegacyInternalPoMigrated($ids);

        $candidateHeads = internal_po::whereIn('internal_id', $ids)
            ->where('status', internal_po::ST_PENDING)
            ->with('lines')
            ->get();

        if ($candidateHeads->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        $neededByItem = [];
        foreach ($candidateHeads as $h) {
            foreach ($h->lines as $it) {
                if (!$it->item_id) continue;
                $neededByItem[$it->item_id] = ($neededByItem[$it->item_id] ?? 0) + (float) $it->item_quantity;
            }
        }

        $itemSnapshots = $this->hikariGetItemsBulk(array_keys($neededByItem));

        $shortages = [];
        foreach ($neededByItem as $itemId => $needQty) {
            $item = $itemSnapshots[$itemId] ?? null;
            if (!$item) {
                $shortages[] = "{$itemId} (ไม่พบสินค้าใน inventory)";
                continue;
            }
            if ((float) $item['quantity'] < $needQty) {
                $shortages[] = "{$itemId} (คงเหลือ {$item['quantity']}, ต้องการ {$needQty})";
            }
        }

        if (!empty($shortages)) {
            return response()->json([
                'ok'      => false,
                'message' => 'ไม่สามารถจัดได้เนื่องจากของใน inventory ไม่เพียงพอ: ' . implode(', ', $shortages),
            ], 422);
        }

        try {
            [$updated, $heads] = DB::transaction(function () use ($ids, $authUser) {
                $heads = internal_po::whereIn('internal_id', $ids)
                    ->where('status', internal_po::ST_PENDING)
                    ->with('lines')
                    ->lockForUpdate()
                    ->get();

                if ($heads->isEmpty()) {
                    return [0, $heads];
                }

                $n = internal_po::whereIn('internal_id', $heads->pluck('internal_id'))
                    ->update([
                        'status'  => internal_po::ST_FINISH,
                        'pick_by' => $authUser->name,
                        'pick_at' => Carbon::now()->toDateTimeString(),
                    ]);

                return [$n, $heads];
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'จัดเสร็จไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        $hikariHadError = $this->syncHikariStockout($heads, $itemSnapshots, $operatorName);

        $this->insertPrintWarehouse($heads, $printer, $printSheets);

        $message = 'จัดเสร็จ ' . $updated . ' ใบ (สั่งพิมพ์ ' . $printSheets . ' แผ่น/ใบ ที่ ' . $printer . ')';
        if ($hikariHadError) {
            $message .= ' (คำเตือน: ซิงก์ inventory บาง item ไม่สำเร็จ กรุณาตรวจสอบ log)';
        }

        return response()->json(['ok' => true, 'message' => $message]);
    }

    public function markCancel(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'string',
        ]);

        $this->ensureLegacyInternalPoMigrated($request->input('ids'));

        try {
            $updated = DB::transaction(function () use ($request, $authUser) {
                return internal_po::whereIn('internal_id', $request->input('ids'))
                    ->where('status', internal_po::ST_PENDING)
                    ->update([
                        'status'  => internal_po::ST_CANCEL,
                        'pick_by' => $authUser->name,
                        'pick_at' => Carbon::now()->toDateTimeString(),
                    ]);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ยกเลิกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ยกเลิก ' . $updated . ' ใบ']);
    }

    private function insertPrintWarehouse($heads, string $printerName, int $printQty): void
    {
        $rows = [];
        foreach ($heads as $h) {
            for ($i = 0; $i < $printQty; $i++) {
                $rows[] = [
                    'SONum'        => $h->SO_id,
                    'PORef'        => $h->POref,
                    'CustName'     => $h->customer_name,
                    'Print_Qty'    => 1,
                    'Printed_Flag' => 'N',
                    'printerName'  => $printerName,
                ];
            }
        }

        if (!$rows) return;

        try {
            DB::connection('mysql_3e')->table('printwarehouse')->insert($rows);
        } catch (\Exception $e) {
            Log::error('insertPrintWarehouse failed: ' . $e->getMessage());
        }
    }

    private function syncHikariStockout($heads, array $itemSnapshots, string $operatorName): bool
    {
        $hadError = false;

        $actualNeededByItem = [];
        foreach ($heads as $h) {
            foreach ($h->lines as $it) {
                if (!$it->item_id) continue;
                $actualNeededByItem[$it->item_id] = ($actualNeededByItem[$it->item_id] ?? 0) + (float) $it->item_quantity;
            }
        }

        $missingSnapshotIds = array_values(array_diff(array_keys($actualNeededByItem), array_keys($itemSnapshots)));
        if (!empty($missingSnapshotIds)) {
            $itemSnapshots = $itemSnapshots + $this->hikariGetItemsBulk($missingSnapshotIds);
        }

        $updates = [];
        foreach ($actualNeededByItem as $itemId => $needQty) {
            $snapshot = $itemSnapshots[$itemId] ?? null;
            if (!$snapshot) {
                Log::error("syncHikariStockout: ไม่พบ snapshot ของ item_id={$itemId} ข้ามการตัดสต็อก");
                $hadError = true;
                continue;
            }
            $updates[$itemId] = [
                'snapshot'    => $snapshot,
                'newQuantity' => (float) $snapshot['quantity'] - $needQty,
            ];
        }

        $failedUpdateIds = $this->hikariUpdateItemQuantitiesBulk($updates);
        if (!empty($failedUpdateIds)) {
            $hadError = true;
        }

        $transactions = [];
        foreach ($heads as $h) {
            foreach ($h->lines as $idx => $it) {
                if (!$it->item_id) continue;
                $key = $h->internal_id . '#' . $idx;
                $transactions[$key] = [
                    'transaction_id'   => (string) Str::uuid(),
                    'addby'            => $operatorName,
                    'transaction_type' => self::HIKARI_TX_TYPE_STOCKOUT,
                    'document_id'      => $h->SO_id,
                    'item_id'          => $it->item_id,
                    'item_quantity'    => (float) $it->item_quantity,
                ];
            }
        }

        $failedTxCount = $this->hikariInsertStockoutTransactionsBulk($transactions);
        if ($failedTxCount > 0) {
            $hadError = true;
        }

        Cache::forget('all_items_list');
        Cache::forget('all_transactions');

        return $hadError;
    }

    private function hikariGetItem(string $itemId): ?array
    {
        try {
            $res = Http::withHeaders(['x-api-key' => self::HIKARI_API_KEY])
                ->baseUrl(self::HIKARI_API_URL)
                ->get('/items/' . urlencode($itemId));

            if ($res->successful()) {
                return $res->json();
            }
            Log::warning("hikariGetItem: failed itemId={$itemId} status=" . $res->status() . ' body=' . $res->body());
        } catch (\Exception $e) {
            Log::error("hikariGetItem: exception itemId={$itemId} " . $e->getMessage());
        }
        return null;
    }

    private function hikariGetItemsBulk(array $itemIds): array
    {
        $itemIds = array_values(array_unique(array_filter($itemIds)));
        if (empty($itemIds)) return [];

        $responses = Http::pool(fn (Pool $pool) => collect($itemIds)->map(
            fn ($id) => $pool->as($id)
                ->withHeaders(['x-api-key' => self::HIKARI_API_KEY])
                ->baseUrl(self::HIKARI_API_URL)
                ->get('/items/' . urlencode($id))
        )->all());

        $out = [];
        foreach ($itemIds as $id) {
            $res = $responses[$id] ?? null;
            if ($res instanceof \Throwable) {
                Log::error("hikariGetItemsBulk: exception itemId={$id} " . $res->getMessage());
                continue;
            }
            if ($res && $res->successful()) {
                $out[$id] = $res->json();
            } else {
                $status = $res ? $res->status() : 'no-response';
                Log::warning("hikariGetItemsBulk: failed itemId={$id} status={$status}");
            }
        }
        return $out;
    }

    private function hikariUpdateItemQuantity(string $itemId, array $snapshot, float $newQuantity): bool
    {
        try {
            $res = Http::withHeaders(['x-api-key' => self::HIKARI_API_KEY])
                ->baseUrl(self::HIKARI_API_URL)
                ->put('/items/' . urlencode($itemId), [
                    'name'      => $snapshot['name']      ?? '',
                    'quantity'  => $newQuantity,
                    'typeitem'  => $snapshot['typeitem']  ?? '',
                    'location'  => $snapshot['location']  ?? '',
                    'brand'     => $snapshot['brand']     ?? '',
                    'privilege' => $snapshot['privilege'] ?? '',
                ]);

            if ($res->successful()) return true;
            Log::error("hikariUpdateItemQuantity: failed itemId={$itemId} status=" . $res->status() . ' body=' . $res->body());
        } catch (\Exception $e) {
            Log::error("hikariUpdateItemQuantity: exception itemId={$itemId} " . $e->getMessage());
        }
        return false;
    }

    private function hikariUpdateItemQuantitiesBulk(array $updates): array
    {
        if (empty($updates)) return [];

        $responses = Http::pool(fn (Pool $pool) => collect($updates)->map(
            fn ($u, $id) => $pool->as($id)
                ->withHeaders(['x-api-key' => self::HIKARI_API_KEY])
                ->baseUrl(self::HIKARI_API_URL)
                ->put('/items/' . urlencode($id), [
                    'name'      => $u['snapshot']['name']      ?? '',
                    'quantity'  => $u['newQuantity'],
                    'typeitem'  => $u['snapshot']['typeitem']  ?? '',
                    'location'  => $u['snapshot']['location']  ?? '',
                    'brand'     => $u['snapshot']['brand']     ?? '',
                    'privilege' => $u['snapshot']['privilege'] ?? '',
                ])
        )->all());

        $failed = [];
        foreach ($updates as $id => $u) {
            $res = $responses[$id] ?? null;
            $ok  = $res && !($res instanceof \Throwable) && $res->successful();
            if (!$ok) {
                $failed[] = $id;
                $detail = $res instanceof \Throwable
                    ? $res->getMessage()
                    : ('status=' . ($res ? $res->status() : 'no-response'));
                Log::error("hikariUpdateItemQuantitiesBulk: failed itemId={$id} {$detail}");
            }
        }
        return $failed;
    }

    private function hikariInsertStockoutTransaction(string $itemId, float $qty, ?string $soId, string $operatorName): bool
    {
        try {
            $res = Http::withHeaders(['x-api-key' => self::HIKARI_API_KEY])
                ->baseUrl(self::HIKARI_API_URL)
                ->post('/transaction/stockout', [
                    'transaction_id'   => (string) Str::uuid(),
                    'addby'            => $operatorName,
                    'transaction_type' => self::HIKARI_TX_TYPE_STOCKOUT,
                    'document_id'      => $soId,
                    'item_id'          => $itemId,
                    'item_quantity'    => $qty,
                ]);

            if ($res->successful()) return true;
            Log::error("hikariInsertStockoutTransaction: failed itemId={$itemId} status=" . $res->status() . ' body=' . $res->body());
        } catch (\Exception $e) {
            Log::error("hikariInsertStockoutTransaction: exception itemId={$itemId} " . $e->getMessage());
        }
        return false;
    }

    private function hikariInsertStockoutTransactionsBulk(array $transactions): int
    {
        if (empty($transactions)) return 0;

        $responses = Http::pool(fn (Pool $pool) => collect($transactions)->map(
            fn ($tx, $key) => $pool->as($key)
                ->withHeaders(['x-api-key' => self::HIKARI_API_KEY])
                ->baseUrl(self::HIKARI_API_URL)
                ->post('/transaction/stockout', $tx)
        )->all());

        $failedCount = 0;
        foreach ($transactions as $key => $tx) {
            $res = $responses[$key] ?? null;
            $ok  = $res && !($res instanceof \Throwable) && $res->successful();
            if (!$ok) {
                $failedCount++;
                $detail = $res instanceof \Throwable
                    ? $res->getMessage()
                    : ('status=' . ($res ? $res->status() : 'no-response'));
                Log::error("hikariInsertStockoutTransactionsBulk: failed key={$key} item={$tx['item_id']} {$detail}");
            }
        }
        return $failedCount;
    }
}