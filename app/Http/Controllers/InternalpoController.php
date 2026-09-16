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
        return $this->requireLogin($request, $logTag);
    }

private function ensureLegacyInternalPoMigrated(array $ids): void
{
    $existing   = internal_po::whereIn('internal_id', $ids)->pluck('internal_id')->flip();
    $missingIds = collect($ids)->reject(fn ($id) => $existing->has($id))->values();
    if ($missingIds->isEmpty()) return;

    $rows = DB::connection(self::LEGACY_CONNECTION)->table('internal_po')
        ->select('PONum as PO', 'SONum as SO')
        ->whereIn('PONum', $missingIds->all())
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
        ->where('POLineStatus', '1')
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
private function legacyPoQuery(Request $request, string $statusFilter)
{
    // statusFilter: 'pending' = ต้องมีไส้ในสถานะ '1' เหลืออยู่ / 'finished' = มีไส้ในแต่ไม่มี '1' เหลือแล้ว
    $query = DB::connection(self::LEGACY_CONNECTION)->table('internal_po as ip')
        ->select('ip.PONum as PO', 'ip.SONum as SO')
        ->where('ip.PONum', 'LIKE', '____-A____')
        ->whereNotNull('ip.SONum')->where('ip.SONum', '<>', '')
        ->when($statusFilter === 'pending', function ($q) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('internal_poline as ipl')
                    ->whereColumn('ipl.PONum', 'ip.PONum')
                    ->where('ipl.POLineStatus', '1');
            });
        })
        ->when($statusFilter === 'finished', function ($q) {
            $q->whereExists(function ($sub) {
                    // ต้องมีไส้ในอย่างน้อย 1 บรรทัด (ไม่ใช่ PO ที่ไม่มีไส้ในเลยตั้งแต่แรก)
                    $sub->select(DB::raw(1))
                        ->from('internal_poline as ipl')
                        ->whereColumn('ipl.PONum', 'ip.PONum');
                })
                ->whereNotExists(function ($sub) {
                    // และไม่มีบรรทัดไหนเหลือสถานะ '1' (รอจัด) แล้ว
                    $sub->select(DB::raw(1))
                        ->from('internal_poline as ipl')
                        ->whereColumn('ipl.PONum', 'ip.PONum')
                        ->where('ipl.POLineStatus', '1');
                });
        })
        ->when($request->filled('SONum'), fn ($q) => $q->where('ip.SONum', 'LIKE', '%' . $request->input('SONum') . '%'))
        ->when($request->filled('internal_id'), fn ($q) => $q->where('ip.PONum', 'LIKE', '%' . $request->input('internal_id') . '%'))
        ->orderByDesc('ip.PONum');

    if ($request->filled('customer_name')) {
        $matchedSoIds = DB::connection(self::LEGACY_CONNECTION)->table('so')
            ->where('CustName', 'LIKE', '%' . $request->input('customer_name') . '%')
            ->pluck('SONum');
        $query->whereIn('ip.SONum', $matchedSoIds->all());
    }

    return $query;
}

   private function fetchLegacyRows(Request $request, string $statusFilter, string $status, int $need): \Illuminate\Support\Collection
{
    $result = collect();
    $offset = 0;
    $batch  = max($need * 2, 200);

    for ($i = 0; $i < 8; $i++) {
        $chunk = (clone $this->legacyPoQuery($request, $statusFilter))
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

private function countLegacyRows(Request $request, string $statusFilter): int
{
    $ids = (clone $this->legacyPoQuery($request, $statusFilter))->pluck('PO');
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
        ->where('POLineStatus', '1')   // '1' = รอจัด เท่านั้น (2=จัดแล้ว, 3=ยกเลิก ไม่เอา)
        ->orderBy('POLineSeq')
        ->get()
        ->groupBy('PONum');

    return $lightItems->map(function ($r) use ($custBySoId, $linesByPo) {
        $r->customer_name = optional($custBySoId->get($r->SO_id))->CustName;
        $r->lines = $linesByPo->get($r->internal_id, collect())->map(fn ($l) => (object) [
            'id'            => null,   // legacy ยังไม่ migrate → ไม่มี id ราย line (จัดทั้ง PO)
            'item_id'       => null,   // ระบบเก่าไม่มี item_id
            'item_name'     => $l->Description ?: '—',
            'item_quantity' => (float) $l->Quantity,
            'picked_at'     => null,
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
        $allHeads = $allHeads->concat($this->fetchLegacyRows($request, 'pending', internal_po::ST_PENDING, $need));
        $legacyPendingTotal = $this->countLegacyRows($request, 'pending');
    }

    if (in_array($effectiveStatus, [self::STATUS_FINISH_KEY, self::STATUS_ALL_KEY], true)) {
        $allHeads = $allHeads->concat($this->fetchLegacyRows($request, 'finished', internal_po::ST_FINISH, $need));
        $legacyFinishedTotal = $this->countLegacyRows($request, 'finished');
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
            ?? $this->countLegacyRows($request, 'pending');
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

        $rawIds       = $request->input('ids');
        $printer      = $request->input('printer');
        $printSheets  = (int) $request->input('print_sheets', 1);
        $operatorName = $authUser->name;

        // แยกประเภท id: line:<lineId> = จัดไส้ในทีละรายการ (ระบบใหม่) / po:<internalId> = จัดทั้ง PO (ของเก่า ยังไม่ migrate)
        $lineIds = [];
        $poIds   = [];
        foreach ($rawIds as $raw) {
            if (strpos($raw, 'line:') === 0)   $lineIds[] = substr($raw, 5);
            elseif (strpos($raw, 'po:') === 0) $poIds[]   = substr($raw, 3);
            else                               $poIds[]   = $raw; // เผื่อรูปแบบเดิม (internal_id ตรง ๆ)
        }

        // ของเก่า → migrate เข้าระบบใหม่ก่อน แล้วรวม line id ของทุก PO นั้นเข้ามาด้วย
        if (!empty($poIds)) {
            $this->ensureLegacyInternalPoMigrated($poIds);
            $lineIds = array_merge(
                $lineIds,
                internal_poline::whereIn('internal_id', $poIds)->pluck('id')->map(fn ($v) => (string) $v)->all()
            );
        }

        $lineIds = array_values(array_unique(array_filter($lineIds)));
        if (empty($lineIds)) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        try {
            $finishedPoIds = DB::transaction(function () use ($lineIds, $operatorName) {
                // มาร์คไส้ในที่เลือกว่า "จัดแล้ว"
                internal_poline::whereIn('id', $lineIds)
                    ->whereNull('picked_at')
                    ->update([
                        'picked_at' => Carbon::now()->toDateTimeString(),
                        'picked_by' => $operatorName,
                    ]);

                // PO ที่ไส้ในถูกจัดครบทุกอันแล้ว → ปิดงาน (FINISH)
                $affectedPoIds = internal_poline::whereIn('id', $lineIds)
                    ->pluck('internal_id')->unique()->values()->all();

                $finished = [];
                foreach ($affectedPoIds as $poId) {
                    $total   = internal_poline::where('internal_id', $poId)->count();
                    $pickedN = internal_poline::where('internal_id', $poId)->whereNotNull('picked_at')->count();
                    if ($total > 0 && $pickedN === $total) {
                        $head = internal_po::where('internal_id', $poId)
                            ->where('status', internal_po::ST_PENDING)
                            ->lockForUpdate()->first();
                        if ($head) {
                            $head->update([
                                'status'  => internal_po::ST_FINISH,
                                'pick_by' => $operatorName,
                                'pick_at' => Carbon::now()->toDateTimeString(),
                            ]);
                            $finished[] = $poId;
                        }
                    }
                }
                return $finished;
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'จัดเสร็จไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        // พิมพ์สติ๊กเกอร์ + ซิงก์ inventory เฉพาะ PO ที่จัดครบแล้วในรอบนี้
        $printOk = true;
        $hikariHadError = false;
        if (!empty($finishedPoIds)) {
            $finishedHeads = internal_po::whereIn('internal_id', $finishedPoIds)->with('lines')->get();

            $needItemIds = $finishedHeads->flatMap(fn ($h) => $h->lines->pluck('item_id'))->filter()->unique()->values()->all();
            $itemSnapshots = !empty($needItemIds) ? $this->hikariGetItemsBulk($needItemIds) : [];
            $hikariHadError = $this->syncHikariStockout($finishedHeads, $itemSnapshots, $operatorName);

            $printOk = $this->insertPrintWarehouse($finishedHeads, $printer, $printSheets);
        }

        $message = 'จัดไส้ในแล้ว ' . count($lineIds) . ' รายการ';
        if (!empty($finishedPoIds)) {
            $message .= ' · ปิดงานครบ ' . count($finishedPoIds) . ' PO (พิมพ์สติ๊กเกอร์ ' . $printSheets . ' แผ่น/ใบ ที่ ' . $printer . ')';
        }
        if (!$printOk) {
            $message .= ' ⚠️ แต่สั่งพิมพ์สติ๊กเกอร์ไม่สำเร็จ (เขียน printwarehouse ไม่ได้ — ตรวจสิทธิ์ DB 3e)';
        }
        if ($hikariHadError) {
            $message .= ' (คำเตือน: ซิงก์ inventory บาง item ไม่สำเร็จ)';
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

    /**
     * คิวพิมพ์สติ๊กเกอร์ลง printwarehouse (3e)
     * คืน true = สั่งพิมพ์สำเร็จ, false = ล้มเหลว (เช่น user ไม่มีสิทธิ์ INSERT บน 3e) เพื่อให้แจ้งเตือนผู้ใช้ได้
     */
    private function insertPrintWarehouse($heads, string $printerName, int $printQty): bool
    {
        $rows = [];
        foreach ($heads as $h) {
            for ($i = 0; $i < $printQty; $i++) {
                $rows[] = [
                    'SONum'        => $h->SO_id,
                    'PORef'        => $h->POref ?? null,
                    'CustName'     => $h->customer_name,
                    'Print_Qty'    => 1,
                    'Printed_Flag' => 'N',
                    'printerName'  => $printerName,
                ];
            }
        }

        if (!$rows) return true;

        try {
            DB::connection('mysql_3e')->table('printwarehouse')->insert($rows);
            return true;
        } catch (\Exception $e) {
            Log::error('insertPrintWarehouse failed: ' . $e->getMessage());
            return false;
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