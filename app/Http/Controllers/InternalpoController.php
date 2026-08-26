<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use App\Models\SsoTicket;
use App\Models\UserAuth;
use Carbon\Carbon;
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

    // สถานะที่แสดง/กรองในหน้า dashboard เอาไว้ 3 กลุ่มเท่านั้น
    // "finish" เป็นกลุ่มรวม: สถานะอื่นๆ ที่ไม่ใช่ pending/cancel ทั้งหมดจะถูกนับ+แสดงเป็น "จัดเสร็จแล้ว"
    const STATUS_ALL_KEY = '__all__';
    const STATUS_FINISH_KEY = '__finish__';

    const VISIBLE_STATUSES = [
        self::STATUS_ALL_KEY      => 'ทั้งหมด',
        internal_po::ST_PENDING   => 'รอดำเนินการ',
        self::STATUS_FINISH_KEY   => 'จัดเสร็จแล้ว',
        internal_po::ST_CANCEL    => 'ยกเลิก',
    ];

    const PER_PAGE = 100;

    // ค่า config ของ hikaripower API (เว็บภายใน ฝังตรงนี้เลย ไม่ผ่าน config/.env)
    const HIKARI_API_URL = 'https://api.hikaripower.com';
    const HIKARI_API_KEY = 'hikari20259f3c6e1b0f2d9c9c0e5e0b4d8b4e6e9c0c6c2f3e7b8a9f1d2e3c4b5a6f7d8e9';

    // ประเภท transaction ที่ยิงไป hikaripower ตอนจัดเสร็จ (ตัดสต็อกออกจากการขาย)
    const HIKARI_TX_TYPE_STOCKOUT = 'ขายสินค้าออก';

    /**
     * ตรวจ ticket SSO (client_key '3e') แล้ว login ให้อัตโนมัติถ้ายังไม่มี session
     * ไม่มี session เลย -> abort 403 (แบบเดียวกับ MobilePoappController::index)
     */
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
    private function baseQuery(Request $request, bool $withStatusFilter = true)
    {
        $q = internal_po::with('lines');

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
            // ไม่ได้ส่ง status มาเลย -> default ให้เห็นเฉพาะ "รอดำเนินการ"
            $status = $request->filled('status') ? $request->input('status') : internal_po::ST_PENDING;

            if ($status === self::STATUS_ALL_KEY) {
                // เลือก "ทั้งหมด" เอง -> ไม่ใส่เงื่อนไข status
            } elseif ($status === self::STATUS_FINISH_KEY) {
                $q->whereNotIn('status', [internal_po::ST_PENDING, internal_po::ST_CANCEL]);
            } elseif (array_key_exists($status, self::VISIBLE_STATUSES)) {
                $q->where('status', $status);
            } else {
                // ค่าที่ไม่รู้จัก -> fallback เป็น pending กันพัง
                $q->where('status', internal_po::ST_PENDING);
            }
        }

        return $q;
    }

    private function loadHeads(Request $request, string $todoStatus)
    {
        return $this->baseQuery($request)
            ->orderByRaw('FIELD(status, ?) DESC', [$todoStatus])
            ->orderBy('internal_id')
            ->paginate(self::PER_PAGE)
            ->appends($request->except('page'));
    }

    /**
     * นับจำนวนต่อสถานะ (ยึดตามฟิลเตอร์คำค้นหาปัจจุบัน แต่ไม่ยึดตามฟิลเตอร์สถานะ)
     * ใช้แสดง badge/caption เหนือตาราง
     */
    private function statusCounts(Request $request): array
    {
        $rows = $this->baseQuery($request, false)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $out = [
            internal_po::ST_PENDING => (int) ($rows[internal_po::ST_PENDING] ?? 0),
            internal_po::ST_CANCEL  => (int) ($rows[internal_po::ST_CANCEL] ?? 0),
        ];

        // รวมทุกสถานะที่ไม่ใช่ pending/cancel เข้ากลุ่ม "จัดเสร็จแล้ว"
        $finishTotal = 0;
        foreach ($rows as $statusKey => $count) {
            if ($statusKey !== internal_po::ST_PENDING && $statusKey !== internal_po::ST_CANCEL) {
                $finishTotal += (int) $count;
            }
        }
        $out[self::STATUS_FINISH_KEY] = $finishTotal;

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

        $heads          = $this->loadHeads($request, internal_po::ST_PENDING);
        $locations      = $this->recentLocations();
        $printers       = self::PRINTERS;
        $statuses       = self::VISIBLE_STATUSES;
        $statusCounts   = $this->statusCounts($request);
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

        // โหลดหัว PO ที่ยัง pending พร้อมรายการสินค้า เพื่อคำนวณยอดที่ต้องตัดสต็อกล่วงหน้า
        $candidateHeads = internal_po::whereIn('internal_id', $ids)
            ->where('status', internal_po::ST_PENDING)
            ->with('lines')
            ->get();

        if ($candidateHeads->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        // รวมจำนวนที่ต้องตัดสต็อกต่อ item_id (กันกรณี item เดียวกันซ้ำหลายบรรทัด/หลายใบในการจัดครั้งเดียว)
        $neededByItem = [];
        foreach ($candidateHeads as $h) {
            foreach ($h->lines as $it) {
                if (!$it->item_id) continue;
                $neededByItem[$it->item_id] = ($neededByItem[$it->item_id] ?? 0) + (float) $it->item_quantity;
            }
        }

        // ตรวจสอบยอดคงเหลือใน hikaripower ก่อนตัดสต็อกจริง
        $itemSnapshots = [];
        $shortages     = [];
        foreach ($neededByItem as $itemId => $needQty) {
            $item = $this->hikariGetItem($itemId);
            if (!$item) {
                $shortages[] = "{$itemId} (ไม่พบสินค้าใน inventory)";
                continue;
            }
            $itemSnapshots[$itemId] = $item;
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

        // ตัดสต็อก + เขียน transaction ที่ hikaripower ตามรายการที่ "จัดเสร็จจริง" (post-lock)
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
     * ยิง insert เข้า printwarehouse (mysql_3e) — รูปแบบเดียวกับ app mobile
     * แต่ละใบใน $heads พิมพ์ $printQty แผ่น (แยกต่อใบ ไม่รวมทั้งหมด)
     */
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

    /**
     * ตัดสต็อก (update item_quantity) + เขียน transaction ขายสินค้าออกที่ hikaripower
     * ต่อ 1 บรรทัดสินค้า = 1 transaction record ตาม detail ในหน้าเว็บ
     *
     * @return bool true ถ้ามีบาง item sync ไม่สำเร็จ (caller เอาไว้แจ้งเตือนแบบ non-blocking)
     */
    private function syncHikariStockout($heads, array $itemSnapshots, string $operatorName): bool
    {
        $hadError = false;

        // 1) รวมยอดตัดสต็อกจริงจาก $heads ที่จัดเสร็จสำเร็จ (หลัง lock) แล้วสั่ง update item_quantity ทีละ item
        $actualNeededByItem = [];
        foreach ($heads as $h) {
            foreach ($h->lines as $it) {
                if (!$it->item_id) continue;
                $actualNeededByItem[$it->item_id] = ($actualNeededByItem[$it->item_id] ?? 0) + (float) $it->item_quantity;
            }
        }

        foreach ($actualNeededByItem as $itemId => $needQty) {
            $snapshot = $itemSnapshots[$itemId] ?? $this->hikariGetItem($itemId);
            if (!$snapshot) {
                Log::error("syncHikariStockout: ไม่พบ snapshot ของ item_id={$itemId} ข้ามการตัดสต็อก");
                $hadError = true;
                continue;
            }

            $newQty = (float) $snapshot['quantity'] - $needQty;
            $ok = $this->hikariUpdateItemQuantity($itemId, $snapshot, $newQty);
            if (!$ok) $hadError = true;
        }

        // 2) เขียน transaction แยกทีละบรรทัดสินค้าของแต่ละใบ (ตาม detail ที่แสดงในหน้าเว็บ)
        foreach ($heads as $h) {
            foreach ($h->lines as $it) {
                if (!$it->item_id) continue;
                $ok = $this->hikariInsertStockoutTransaction(
                    $it->item_id,
                    (float) $it->item_quantity,
                    $h->SO_id,
                    $operatorName
                );
                if (!$ok) $hadError = true;
            }
        }
        Cache::forget('all_items_list');
        Cache::forget('all_transactions');

        return $hadError;
    }

    /**
     * GET รายละเอียดสินค้าปัจจุบันจาก hikaripower (เอาไว้เช็คยอดคงเหลือ + เอา field เดิมไปใช้ตอน update)
     * ⚠️ สมมติ endpoint เป็น GET /items/{id} — โปรดตรวจสอบกับ ItemsController จริง
     */
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

    /**
     * PUT อัปเดตยอดคงเหลือของ item (ตัดสต็อก) — ใช้ field เดิมจาก snapshot แค่เปลี่ยน quantity
     * ⚠️ สมมติ endpoint เป็น PUT /items/{id} รับ payload เต็ม (name, quantity, typeitem, location, brand, privilege)
     */
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

    /**
     * POST insert transaction ขายสินค้าออกที่ hikaripower
     * ⚠️ endpoint ตรงกับ TransactionController: POST /transaction/stockout
     */
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
}