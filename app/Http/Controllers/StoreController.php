<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use App\Models\internal_poline;
use App\Models\PoReceive;
use App\Models\PoReceiveLine;
use App\Models\SsoTicket;
use App\Models\UserAuth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    const LEGACY_CONNECTION = 'mysql_3e';
    const LEGACY_PO_DETAIL_URL = 'http://server_update:8000/api/getPODetail';

    /** จำนวนใบต่อหน้าของหน้าของออก (ด่าน 3) */
    const CHECKOUT_PER_PAGE = 100;
    const LEGACY_STORE_MIN_DATE = '2021-01-01';
    /** จำนวน "การ์ด SO" ต่อหน้าของหน้าของออกแบบใหม่ (ด่าน 3) — การ์ด 1 ใบ = 1 SO อาจมีหลายบิลขนส่งข้างใน */
    const CHECKOUT_BILLS_PER_PAGE = 100;

    /** จำนวนใบต่อหน้าของหน้าระบุตำแหน่ง (ด่าน 2) */
    const LOCATION_PER_PAGE = 100;

    /**
     * จำนวน PONum สูงสุดที่รวมส่งไปในคำขอเดียว (แบบ array) ก่อนจะตัดเป็นคำขอถัดไป
     * ใช้ตอนยิง getPODetail แบบ "รวมหลาย PO ต่อคำขอ" แทนการยิงทีละ PO ต่อคำขอ
     */
    const LEGACY_PO_ARRAY_BATCH_SIZE = 50;

    /**
     * ชื่อคอลัมน์เก็บ "ชื่อลูกค้า" ในตาราง tblbill (database logistic)
     * ใช้สำหรับดึงชื่อลูกค้าของรายการ ภายนอก/ระบบเก่า ที่ตัวตารางเองไม่ได้เก็บชื่อลูกค้าไว้
     * (รายการ "ภายใน" ดึงชื่อลูกค้าจาก internal_po.customer_name ตรงๆ อยู่แล้ว ไม่เกี่ยวกับตรงนี้)
     */
    const TBLBILL_CUSTOMER_COLUMN = 'customer_name';
    const TBLBILL_CUSTOMER_ID_COLUMN = 'customer_id';
    const TBLBILL_POREF_COLUMN = 'ponum';
    const TBLBILL_OPENED_BY_COLUMN = 'emp_name';

    /**
     * ชื่อคอลัมน์ "เลขที่บิลส่งของ (DN)" ในตาราง tblbill — ยืนยันจากข้อมูลจริงแล้วว่าใช้ 'billid'
     * (รูปแบบข้อมูลจริงเช่น "46907-01626")
     * หมายเหตุ: 'bill_issue_no' เป็นคนละเลขกัน (รูปแบบ "BI6907-00666") ไม่ใช่เลขบิลส่งของ จึงไม่ใช้ตรงนี้
     */
    const TBLBILL_DN_COLUMN = 'billid';

    /**
     * ค่า tblbill.status ที่แปลว่าบิลขนส่งใบนั้น "ถูกยกเลิก" — ยังต้องแสดงในรายการ (ให้เห็นว่ามีบิลนี้อยู่)
     * แต่ห้ามเลือกรายการไปกับบิลนี้ และไม่นับรวมในสถิติ "บิลขนส่งทั้งหมด/บิลยังค้างอยู่"
     */
    const TBLBILL_STATUS_CANCELLED = 6;
    const TBLBILL_PICKER_COLUMN      = 'emp_picker';
    const TBLBILL_PICKER_TIME_COLUMN = 'picker_time';

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
            // ★ เดิม: abort(403, 'กรุณาเข้าใช้งานผ่านเมนูหลัก');
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->guest(route('login'))
            );
        }

        return Auth::guard('web')->user();
    }
    public function itemsDetailBatch(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1|max:300',
            'items.*.type'   => 'required|string|in:external,legacy',
            'items.*.id'     => 'required|string',
            'items.*.so_id'  => 'nullable|string',
        ]);

        $rows = collect($request->input('items'));
        $result = [];

        $externalRows = $rows->where('type', 'external')->values();
        $legacyRows   = $rows->where('type', 'legacy')->values();

        // ── รวบ PO number "ดิบ" ของทั้ง external + legacy เป็น set เดียว ยิง pool ทีเดียว ──
        $externalPoNumOf = fn ($poId) => preg_replace('/^PO/', '', $poId); // map: po_id (external) -> เลขดิบ

        $allPoNums = collect()
            ->merge($externalRows->pluck('id')->unique()->map($externalPoNumOf))
            ->merge($legacyRows->pluck('id')->unique())
            ->filter()
            ->unique()
            ->values()
            ->all();

        $legacyDetail = $this->fetchLegacyPoItemsBatch($allPoNums); // [poNum => collect(items)] เดียวใช้ร่วมกันทั้งสองส่วน

        // ── external: เทียบ "สั่งจริง" (getPODetail) กับ "รับจริง" (PoReceiveLine) ทีละสินค้า ──
        if ($externalRows->isNotEmpty()) {
            $poIds = $externalRows->pluck('id')->unique()->values()->all();

            $receives = PoReceive::with('lines')
                ->whereIn('po_id', $poIds)
                ->get()
                ->keyBy(fn ($r) => $r->po_id . '|' . $r->so_id);

            $orderedByPo = collect($poIds)->mapWithKeys(function ($poId) use ($legacyDetail, $externalPoNumOf) {
                $lines = $legacyDetail->get($externalPoNumOf($poId), collect());
                return [$poId => $lines->pluck('item_quantity', 'item_name')];
            });

            foreach ($externalRows as $row) {
                $key        = 'external:' . $row['id'] . ':' . ($row['so_id'] ?? '');
                $receive    = $receives->get($row['id'] . '|' . ($row['so_id'] ?? ''));
                $orderedMap = $orderedByPo->get($row['id'], collect());

                $receivedByName = $receive
                    ? $receive->lines->groupBy('good_name')->map(fn ($g) => (float) $g->sum('recv_qty'))
                    : collect();

                $items    = [];
                $anyShort = false;

                if ($receive) {
                    foreach ($receive->lines->unique('good_name') as $l) {
                        $ordered = $orderedMap->has($l->good_name) ? (float) $orderedMap->get($l->good_name) : null;
                        $recv    = $receivedByName->get($l->good_name, 0);
                        $short   = $ordered !== null && $recv < $ordered;
                        if ($short) $anyShort = true;

                        $items[] = [
                            'item_name'    => $l->good_name,
                            'received_qty' => $recv,
                            'ordered_qty'  => $ordered,
                            'short'        => $short,
                            'shelf'        => $l->shelf,
                            'photo_url'    => $l->photo_path ? $l->photoUrl() : null,
                        ];
                    }
                }

                foreach ($orderedMap as $name => $ordered) {
                    if ($receivedByName->has($name)) continue;
                    $anyShort = true;
                    $items[] = [
                        'item_name'    => $name,
                        'received_qty' => 0,
                        'ordered_qty'  => (float) $ordered,
                        'short'        => true,
                        'shelf'        => null,
                        'photo_url'    => null,
                    ];
                }

                $status = $receive->status ?? null;
                if ($status === null && $orderedMap->isNotEmpty()) {
                    $status = $anyShort ? 'บางส่วน' : 'ครบ';
                }

                $result[$key] = ['status' => $status, 'items' => $items];
            }
        }

        // ── legacy: ตาราง store ไม่มีจำนวน "รับจริง" ต่อสินค้า มีแค่ตำแหน่งจัดเก็บ → โชว์ได้แค่จำนวนสั่ง ──
        if ($legacyRows->isNotEmpty()) {
            foreach ($legacyRows as $row) {
                $key   = 'legacy:' . $row['id'] . ':' . ($row['so_id'] ?? '');
                $lines = $legacyDetail->get($row['id'], collect());

                $items = $lines->isEmpty()
                    ? [['item_name' => '—', 'received_qty' => 1, 'ordered_qty' => null, 'short' => false, 'shelf' => null, 'photo_url' => null]]
                    : $lines->map(fn ($it) => [
                        'item_name'    => $it->item_name,
                        'received_qty' => $it->item_quantity,
                        'ordered_qty'  => $it->item_quantity,
                        'short'        => false,
                        'shelf'        => null,
                        'photo_url'    => null,
                    ])->values()->all();

                $result[$key] = ['status' => 'ครบ', 'items' => $items];
            }
        }

        return response()->json(['ok' => true, 'items' => $result]);
    }

    /**
     * ดึงรายละเอียด PO จากระบบเก่า (getPODetail) ให้ครบทุกเลขที่ขอมา
     *
     * ⚡ กลยุทธ์ 2 ชั้น เพื่อลดจำนวน round-trip ไปเซิร์ฟเวอร์เก่าให้เหลือน้อยที่สุด:
     *   1) "แบบ array" (ใหม่): รวมหลาย PONum ส่งไปในคำขอเดียว (สูงสุด LEGACY_PO_ARRAY_BATCH_SIZE ต่อคำขอ)
     *      แทนที่จะยิงทีละ PO ต่อคำขอ — ลดจำนวนคำขอลงหลายสิบเท่า ถ้าเซิร์ฟเวอร์ปลายทางรองรับ
     *   2) fallback "แบบ pool" (ของเดิม): ยิงทีละ PO แบบ concurrent จำกัด 30 พร้อมกัน + retry
     *      ใช้เฉพาะตอนที่วิธีที่ 1 ใช้ไม่ได้ (เซิร์ฟเวอร์ไม่รองรับ array หรือตอบกลับไม่มีทางแยกว่า
     *      แต่ละบรรทัดเป็นของ PO ไหน) — กันไม่ให้ข้อมูลสินค้าสลับ PO กันโดยไม่รู้ตัว
     */
    /**
     * ปุ่ม "กำลังจัดการ" — ล็อค PO ภายนอกใบนี้ไม่ให้ถูกเลือกระบุตำแหน่งโดยคนอื่น
     * อัปเดตทุกบรรทัดของ po_id ที่ shelf ยังว่าง (ขอบเขตเดียวกับตอนระบุตำแหน่งจริง)
     *
     * ★ เพิ่ม: ถ้า PO นี้เคยถูกกด "จัดการเสร็จสิ้น" ไปแล้ว (sus_time มีค่า) ห้ามกดจัดการซ้ำอีก
     */
    public function locationClaim(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            return response()->json(['ok' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ'], 403);
        }

        $request->validate(['po_id' => 'required|string']);
        $poId = $request->input('po_id');

        try {
            $result = DB::transaction(function () use ($poId, $authUser) {
                $lines = PoReceiveLine::where('po_id', $poId)
                    ->whereNull('shelf')
                    ->lockForUpdate()
                    ->get();

                if ($lines->isEmpty()) {
                    return ['ok' => false, 'message' => 'ไม่พบรายการที่ต้องจัดการ'];
                }

                // ★ เคยกด "จัดการเสร็จสิ้น" ไปแล้ว (sus_time มีค่า) → ห้ามกลับมากดจัดการซ้ำ
                $finishedLine = $lines->first(fn ($l) => $l->sus_time);
                if ($finishedLine) {
                    return ['ok' => false, 'message' => 'PO นี้จัดการเสร็จสิ้นไปแล้ว ไม่สามารถกดจัดการซ้ำได้'];
                }

                $claimedByLine = $lines->first(fn ($l) => $l->do_it_time);
                if ($claimedByLine) {
                    return ['ok' => false, 'message' => 'มีคนกำลังจัดการ PO นี้อยู่ (' . $claimedByLine->do_it . ')'];
                }

                PoReceiveLine::where('po_id', $poId)
                    ->whereNull('shelf')
                    ->update(['do_it' => $authUser->name, 'do_it_time' => Carbon::now()]);

                return ['ok' => true, 'message' => 'เริ่มจัดการงานแล้ว'];
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }

        return response()->json($result, $result['ok'] ? 200 : 409);
    }
    /**
     * ปุ่ม "จัดการเสร็จสิ้น" — ปลดล็อค PO ภายนอกใบนี้ ให้กลับมาเลือกระบุตำแหน่งได้ตามปกติ
     *
     * ★ แก้ไข: ไม่ล้าง do_it / do_it_time อีกต่อไป — เก็บไว้เป็นประวัติว่าใครเป็นคน "กำลังจัดการ"
     *   ก่อนหน้านี้ (เดิมโค้ดเซ็ตเป็น null ทำให้ประวัติหาย และทำให้สถานะ "claimed" ที่ดูจาก
     *   do_it_time กลับไปว่างเปล่า ปุ่ม "กำลังจัดการ" เลยโผล่กลับมาให้กดซ้ำได้ทั้งที่เสร็จแล้ว)
     *   ใช้ sus_time เป็นตัวบอกสถานะ "เสร็จสิ้นแบบถาวร" แทน และกันไม่ให้กดเสร็จสิ้นซ้ำ
     */
    public function locationFinish(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            return response()->json(['ok' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ'], 403);
        }

        $request->validate(['po_id' => 'required|string']);
        $poId = $request->input('po_id');

        try {
            $result = DB::transaction(function () use ($poId, $authUser) {
                $lines = PoReceiveLine::where('po_id', $poId)
                    ->whereNull('shelf')
                    ->lockForUpdate()
                    ->get();

                if ($lines->isEmpty()) {
                    return ['ok' => false, 'message' => 'ไม่พบรายการที่ต้องจัดการ'];
                }

                // ★ กันกด "จัดการเสร็จสิ้น" ซ้ำ ถ้าเคยกดไปแล้ว
                $alreadyFinished = $lines->contains(fn ($l) => $l->sus_time);
                if ($alreadyFinished) {
                    return ['ok' => false, 'message' => 'PO นี้จัดการเสร็จสิ้นไปแล้ว'];
                }

                $isClaimed = $lines->contains(fn ($l) => $l->do_it_time);
                if (!$isClaimed) {
                    return ['ok' => false, 'message' => 'PO นี้ยังไม่ได้อยู่ระหว่างจัดการ'];
                }

                PoReceiveLine::where('po_id', $poId)
                    ->whereNull('shelf')
                    ->update([
                        'sus'        => $authUser->name,
                        'sus_time'   => Carbon::now(),
                        // ★ ไม่ล้าง do_it / do_it_time อีกต่อไป — เก็บไว้เป็นประวัติผู้จัดการ
                    ]);

                return ['ok' => true, 'message' => 'จัดการเสร็จสิ้นแล้ว'];
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }

        return response()->json($result, $result['ok'] ? 200 : 409);
    }
    private function buildExternalPendingLocationRows(Request $request): \Illuminate\Support\Collection
    {
        if ($request->filled('location')) {
            // แถวกลุ่มนี้ shelf เป็น null เสมอ จึงไม่มีทางตรงกับคำค้น "ที่เก็บ"
            return collect();
        }

        $headers = PoReceive::with(['lines' => fn ($q) => $q->whereNull('shelf')])
            ->whereNull('checkout_by')
            ->whereHas('lines', fn ($q) => $q->whereNull('shelf'))
            ->when($request->filled('PONum'), fn ($q) => $q->where('po_id', 'LIKE', '%' . $request->input('PONum') . '%'))
            ->when($request->filled('SONum'), fn ($q) => $q->where('so_id', 'LIKE', '%' . $request->input('SONum') . '%'))
            ->when($request->filled('customer'), fn ($q) => $q->where('cust_name', 'LIKE', '%' . $request->input('customer') . '%'))
            ->when($request->filled('item'), fn ($q) => $q->whereHas('lines', fn ($q2) =>
                $q2->whereNull('shelf')->where('good_name', 'LIKE', '%' . $request->input('item') . '%')
            ))
            ->get();

        return $headers->map(function ($h) {
            $lines = $h->lines;
            $claim = $this->externalClaimStateFromLines($lines);

            return (object) [
                'type'          => 'external',
                'id'            => $h->po_id,
                'po_display'    => $h->po_id,
                'so_id'         => $h->so_id,
                'customer_name' => $h->cust_name,
                'items'         => $lines->map(fn ($l) => (object) [
                    'item_name'     => $l->good_name,
                    'item_quantity' => $l->recv_qty,
                ]),
                'total_qty'   => $lines->sum('recv_qty'),
                'location'    => null,
                'packed_by'   => optional($lines->first())->received_by,
                'packed_at'   => $lines->max('received_at'),
                'todo'        => true,
                'claimed'     => $claim['claimed'],
                'claimed_by'  => $claim['by'],
                'claimed_at'  => $claim['at'],
                // ★ สถานะ "จัดการเสร็จสิ้นแล้ว" (ถาวร) แยกจาก claimed
                'finished'    => $claim['finished'],
                'finished_by' => $claim['finished_by'],
                'finished_at' => $claim['finished_at'],
            ];
        })->values();
    }

    /**
     * ตรวจสถานะ "กำลังจัดการ" / "จัดการเสร็จสิ้นแล้ว" ของ PO ภายนอกใบหนึ่ง จากบรรทัดสินค้า
     * (PoReceiveLine, shelf ยังว่าง) ที่ preload มาแล้ว
     *
     * ★ แก้ไข: เดิมดูแค่ do_it_time อย่างเดียว (claimed / not claimed) — ตอนนี้ do_it_time จะไม่ถูกล้าง
     *   อีกต่อไปหลังกด "จัดการเสร็จสิ้น" (ดู locationFinish) จึงต้องเช็ค sus_time ก่อนเป็นอันดับแรก
     *   เพื่อแยกสถานะ "เสร็จสิ้นแบบถาวร" ออกจาก "กำลังจัดการอยู่ตอนนี้" ให้ถูกต้อง
     *
     * ลำดับการตัดสิน:
     *   1) มี sus_time (บรรทัดล่าสุด) → ถือว่า "เสร็จสิ้นแล้ว" (finished) ไม่ใช่ claimed อีกต่อไป
     *   2) ไม่มี sus_time แต่มี do_it_time → ถือว่า "กำลังจัดการอยู่" (claimed)
     *   3) ไม่มีทั้งคู่ → ยังไม่มีใครแตะ
     */
    private function externalClaimStateFromLines(\Illuminate\Support\Collection $lines): array
    {
        $finishedLine = $lines->filter(fn ($l) => $l->sus_time)->sortByDesc('sus_time')->first();

        if ($finishedLine) {
            return [
                'claimed'     => false,
                'by'          => $finishedLine->do_it,
                'at'          => $finishedLine->do_it_time,
                'finished'    => true,
                'finished_by' => $finishedLine->sus,
                'finished_at' => $finishedLine->sus_time,
            ];
        }

        $latest = $lines->filter(fn ($l) => $l->do_it_time)->sortByDesc('do_it_time')->first();

        if (!$latest) {
            return ['claimed' => false, 'by' => null, 'at' => null, 'finished' => false, 'finished_by' => null, 'finished_at' => null];
        }

        return ['claimed' => true, 'by' => $latest->do_it, 'at' => $latest->do_it_time, 'finished' => false, 'finished_by' => null, 'finished_at' => null];
    }
    private function fetchLegacyPoItemsBatch(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = collect($poNums)->filter()->unique()->values();
        if ($poNums->isEmpty()) return collect();

        // ⚡ perf: cache ผลต่อ PO สั้นๆ — ลดปัญหา "บันทึกข้อมูล" ช้า เพราะเดิมยิง HTTP ไปเซิร์ฟเวอร์เก่า
        // ซ้ำสองรอบสำหรับ PO ชุดเดียวกัน: รอบแรกตอนโหลดหน้าของออก (โชว์รายการสินค้า), รอบสองตอนกด
        // "บันทึกข้อมูล" (checkoutLegacyAndMigrate) รายการสินค้าใน PO ที่ปิดแล้วแทบไม่เปลี่ยน จึง cache ได้
        $cacheKey = fn ($num) => 'legacy_po_items:' . $num;

        $data = collect();
        $poNums->each(function ($num) use (&$data, $cacheKey) {
            $hit = Cache::get($cacheKey($num));
            if ($hit !== null) $data->put($num, collect($hit));
        });

        $remaining = $poNums->diff($data->keys())->values();

        if ($remaining->isNotEmpty()) {
            // ═══ รอบที่ 1: ยิงรวมเป็นชุด (array) ต่อคำขอ ═══
            $needFallback = collect();
            foreach ($remaining->chunk(self::LEGACY_PO_ARRAY_BATCH_SIZE) as $chunk) {
                $chunkNums   = $chunk->values()->all();
                $batchResult = $this->fetchLegacyPoItemsArrayRequest($chunkNums);

                if ($batchResult === null) {
                    $needFallback = $needFallback->merge($chunkNums);
                } else {
                    $data = $data->merge($batchResult);
                }
            }

            // ═══ รอบที่ 2 (fallback): เฉพาะ PO ที่ยิงแบบ array ไม่สำเร็จ ═══
            if ($needFallback->isNotEmpty()) {
                $data = $data->merge($this->fetchLegacyPoItemsPooled($needFallback->values()->all()));
            }

            // เก็บเฉพาะที่ยิงสำเร็จจริง (ไม่ cache รายการว่าง กัน PO ที่ข้อมูลยังไม่มาไม่ถูกปิดกั้นในรอบถัดไป)
            $remaining->each(function ($num) use ($data, $cacheKey) {
                $items = $data->get($num);
                if ($items && $items->isNotEmpty()) {
                    Cache::put($cacheKey($num), $items->all(), now()->addMinutes(10));
                }
            });
        }

        $poNums->each(function ($num) use ($data) {
            if (!$data->has($num)) $data->put($num, collect());
        });

        // ── fallback: เลข PO ที่ getPODetail ไม่มีข้อมูล (เช่น PO ภายใน รูปแบบ 6907-A0104)
        //     ลองหาใน internal_poline (DB 3e) โดยตรงแทน ── (โค้ดเดิม ไม่เปลี่ยน)
        $emptyNums = $poNums->filter(fn ($num) => $data->get($num)->isEmpty())->values();

        if ($emptyNums->isNotEmpty()) {
            $rows = DB::connection(self::LEGACY_CONNECTION)
                ->table('internal_poline')
                ->whereIn('PONum', $emptyNums->all())
                ->orderBy('POLineSeq')
                ->get()
                ->groupBy('PONum');

            $rows->each(function ($lines, $num) use ($data) {
                $data->put($num, $lines->map(fn ($l) => (object) [
                    'item_name'     => $l->Description ?: '—',
                    'item_quantity' => (float) $l->Quantity,
                ]));
            });
        }

        return $data;
    }
    /**
     * ยิงคำขอ "เดียว" ไปยัง getPODetail โดยส่ง PONum เป็น array (หลายเลข PO ในคำขอเดียว)
     * เพื่อลดจำนวน round-trip แทนการยิงทีละ PO
     *
     * คืนค่า null เมื่อไม่สามารถเชื่อถือผลลัพธ์ได้ (เซิร์ฟเวอร์ error / ไม่มีข้อมูล / ตอบกลับมาแบบ
     * แยกไม่ออกว่าแต่ละบรรทัดเป็นของ PO ไหน) — กรณีนี้ผู้เรียกจะ fallback ไปยิงทีละใบแทนโดยอัตโนมัติ
     * เพื่อไม่ให้สินค้าสลับ PO กัน ถ้าเจอ warning นี้บ่อยๆ ใน log แปลว่าเซิร์ฟเวอร์เก่าไม่รองรับคำขอแบบ
     * array จริงๆ (หรือใช้ชื่อคอลัมน์ระบุ PO ต่อบรรทัดไม่ตรงกับที่ลองเดาไว้ด้านล่าง) ให้เช็ค field จริง
     * จากฝั่งเซิร์ฟเวอร์เก่าแล้วเพิ่มชื่อ key เข้าไปใน $ponumKeys
     */
    private function fetchLegacyPoItemsArrayRequest(array $poNums): ?\Illuminate\Support\Collection
    {
        try {
            // Http client จะ serialize ค่า array เป็น PONum[]=A&PONum[]=B&... ให้อัตโนมัติ
            $resp = Http::timeout(15)->get(self::LEGACY_PO_DETAIL_URL, ['PONum' => $poNums]);
        } catch (\Exception $e) {
            Log::warning('fetchLegacyPoItemsArrayRequest: request failed: ' . $e->getMessage());
            return null;
        }

        if (!$resp->ok()) {
            Log::warning('fetchLegacyPoItemsArrayRequest: HTTP ' . $resp->status());
            return null;
        }

        $lines = $resp->json('ms_podt') ?? [];
        if (empty($lines)) {
            // ไม่มีรายการเลย (อาจแปลว่าเซิร์ฟเวอร์ไม่รองรับ array แล้วคืนว่างมา) → fallback ให้ชัวร์
            return null;
        }

        // หา field ที่ระบุว่าบรรทัดนี้เป็นของ PONum ไหน (เดาชื่อคอลัมน์เท่าที่เป็นไปได้ เผื่อเซิร์ฟเวอร์
        // เก่าตั้งชื่อไม่ตรงกับที่คาด — ถ้าไม่ตรงสักชื่อเลย จะ fallback ไปยิงทีละใบแทนโดยอัตโนมัติ)
        $ponumKeys = ['PONum', 'PoNum', 'ponum', 'PO_NUM', 'PONUM'];
        $sample    = $lines[0];
        $key       = collect($ponumKeys)->first(fn ($k) => is_array($sample) && array_key_exists($k, $sample));

        if (!$key) {
            Log::warning('fetchLegacyPoItemsArrayRequest: ตอบกลับไม่มีคอลัมน์ระบุ PONum ต่อบรรทัด แยกคืนตาม PO ไม่ได้ ใช้วิธีเดิมแทน (ดูหมายเหตุใน docblock)');
            return null;
        }

        $grouped = collect($lines)->groupBy($key);

        return collect($poNums)->mapWithKeys(fn ($num) => [
            $num => $grouped->get($num, collect())->map(fn ($l) => (object) [
                'item_name'     => $l['GoodName'] ?? '—',
                'item_quantity' => (float) ($l['GoodQty2'] ?? $l['AppvQty2'] ?? 0),
            ]),
        ]);
    }

    /**
     * วิธีเดิม: ยิงทีละ PO แบบ concurrent (Http::pool) จำกัด 30 พร้อมกัน + retry รอบเดียว
     * (เซิร์ฟเวอร์ปลายทางรับ connection พร้อมกันเยอะๆ ไม่ไหว จะ reset connection ถ้ายิงทีเดียว 100+ ตัว)
     * ใช้เป็น fallback เฉพาะตอนที่คำขอแบบ array (fetchLegacyPoItemsArrayRequest) ใช้ไม่ได้เท่านั้น
     */
    private function fetchLegacyPoItemsPooled(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = collect($poNums)->filter()->unique()->values();
        if ($poNums->isEmpty()) return collect();

        $concurrency = 30;

        $data   = collect();
        $failed = collect();

        foreach ($poNums->chunk($concurrency) as $chunk) {
            [$chunkData, $chunkFailed] = $this->fetchLegacyPoItemsChunk($chunk->values()->all());
            $data   = $data->merge($chunkData);
            $failed = $failed->merge($chunkFailed);
        }

        if ($failed->isNotEmpty()) {
            usleep(300000);
            foreach ($failed->chunk(10) as $chunk) {
                [$chunkData, $stillFailed] = $this->fetchLegacyPoItemsChunk($chunk->values()->all());
                $data = $data->merge($chunkData);
                if ($stillFailed->isNotEmpty()) {
                    Log::warning('fetchLegacyPoItemsPooled: ล้มเหลวแม้ retry แล้ว: ' . $stillFailed->implode(', '));
                }
            }
        }

        return $data;
    }

    /**
     * ยิง Http::pool หนึ่ง batch แล้วคืนค่า [data, failed]
     * data   = collect [ poNum => collect(items) ] เฉพาะที่สำเร็จ
     * failed = collect ของ poNum ที่ยิงไม่สำเร็จ
     */
    private function fetchLegacyPoItemsChunk(array $poNums): array
    {
        $data   = collect();
        $failed = collect();

        try {
            $responses = Http::pool(fn ($pool) => collect($poNums)->map(
                fn ($num) => $pool->as($num)->timeout(8)->get(self::LEGACY_PO_DETAIL_URL, ['PONum' => $num])
            )->all());
        } catch (\Exception $e) {
            Log::warning('fetchLegacyPoItemsChunk pool failed: ' . $e->getMessage());
            return [collect(), collect($poNums)];
        }

        foreach ($poNums as $num) {
            $resp = $responses[$num] ?? null;

            if (!$resp || $resp instanceof \Throwable || !method_exists($resp, 'ok') || !$resp->ok()) {
                if ($resp instanceof \Throwable) {
                    Log::warning("fetchLegacyPoItemsBatch({$num}) failed: " . $resp->getMessage());
                }
                $failed->push($num);
                continue;
            }

            $lines = $resp->json('ms_podt') ?? [];
            $data->put($num, collect($lines)->map(fn ($l) => (object) [
                'item_name'     => $l['GoodName'] ?? '—',
                'item_quantity' => (float) ($l['GoodQty2'] ?? $l['AppvQty2'] ?? 0),
            ]));
        }

        return [$data, $failed];
    }

    /**
     * สร้าง query builder สำหรับหน้าระบุตำแหน่ง (ด่าน 2) พร้อมเงื่อนไข filter ทั้งหมด
     * แยกออกมาจาก ->get()/->paginate() เพื่อให้สามารถ clone ไปนับจำนวนแยกได้ (เช่น totalTodo)
     */
    private function buildLocationQuery(Request $request, ?array $statuses)
    {
        $q = internal_po::with('lines');

        if ($statuses !== null) {
            $q->whereIn('status', $statuses);
        }
        if ($request->filled('SONum')) {
            $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
        }
        if ($request->filled('PONum')) {
            $q->where('internal_id', 'LIKE', '%' . $request->input('PONum') . '%');
        }
        if ($request->filled('customer')) {
            $q->where('customer_name', 'LIKE', '%' . $request->input('customer') . '%');
        }
        if ($request->filled('location')) {
            $q->where('location', 'LIKE', '%' . $request->input('location') . '%');
        }
        if ($request->filled('item')) {
            // PO หนึ่งใบมีได้หลายสินค้า (internal_poline) — ค้นหาแบบ whereHas เพื่อจับได้ทุกบรรทัดสินค้าในใบเดียวกัน
            $item = $request->input('item');
            $q->whereHas('lines', function ($q2) use ($item) {
                $q2->where('item_name', 'LIKE', '%' . $item . '%');
            });
        }

        return $q;
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

    /**
     * หา so_id ทั้งหมดที่ "เปิด" ในวันที่ระบุ จากตาราง tblbill (database logistic)
     * ใช้คอลัมน์ time (DATETIME) เป็นตัวแทนวันที่เปิด SO — ยืนยันแล้วว่า time คือเวลาที่ถูกสร้างบิลจริง
     * (เคยสงสัยว่าอาจต้องใช้ date_of_dali แทน แต่เช็คแล้วว่าเป็นคนละคอลัมน์ ไม่เกี่ยวข้องกับวันที่เปิดบิล
     * จึงยืนยันใช้ time ตามเดิม — ไม่ต้องเปลี่ยน)
     *
     * คืนค่า null = ไม่ได้กรองวันที่ (ไม่ได้ส่งพารามิเตอร์มา)
     * คืนค่า array (อาจว่างเปล่า) = กรองแล้ว เอาไปทำ whereIn ต่อ
     */
    private function soIdsByBillDate(?string $date): ?array
    {
        if (!$date) return null;

        return DB::table('tblbill')
            ->whereDate('time', $date)   // <-- เปลี่ยนคอลัมน์ตรงนี้ถ้าจำเป็น
            ->pluck('so_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * ดึงบิลขนส่งทั้งหมด (ทุกแถวใน tblbill) ของแต่ละ SO — 1 SO เปิดบิลขนส่งได้หลายครั้ง
     * (ของอาจไม่ได้ส่งครบในเที่ยวเดียว) ใช้แสดงเป็น "หัวข้อย่อย" ใต้การ์ด SO ในหน้าของออก
     *
     * ⚠️ ข้อมูลรับเข้า (internal_po / po_receives / store) ไม่มีคอลัมน์เชื่อมกับ billid โดยตรง
     * จึงไม่สามารถระบุล่วงหน้าได้ว่า PO กลุ่มไหน "ไปกับ" บิลขนส่งใบไหน — รายการ PO ที่ยังไม่จัดออก
     * (todo) จะแสดงซ้ำใต้ทุกบิลขนส่งของ SO นั้น ให้ผู้จัดของเลือกเองว่ารายการไหนไปกับบิลไหนตอนกด
     * "ของออก" — เมื่อกดสำเร็จแล้วรายการนั้นจะครบและหายไปจากทุกบิลย่อยที่เหลือทันทีหลัง reload
     *
     * $billDate: ถ้าระบุ (ไม่ใช่ null) จะกรองให้เหลือเฉพาะบิลที่เปิดในวันนั้นวันเดียว — ป้องกันไม่ให้บิลขนส่ง
     * เก่าของ SO เดียวกันจากวันอื่นๆ โผล่มาปนตอนดูงานของวันที่เลือกอยู่ (เช่น หน้า default ที่ดูงานวันนี้
     * หรือตอนค้นด้วย bill_date) ถ้าไม่ระบุ (null, เช่นตอนค้นด้วย SONum/PONum แบบไม่ระบุวันที่) จะคืนบิลทุกวัน
     *
     * คืนค่า [so_id => collect({so_id, dn_no, time, customer_name})] เรียงตามเวลาเปิดบิล (เก่า -> ใหม่)
     */
    private function billRowsBySo(array $soIds, ?string $billDate = null): \Illuminate\Support\Collection
    {
        if (!$soIds) return collect();

        $dnColumn = self::TBLBILL_DN_COLUMN;
        $columns  = array_values(array_filter([
            'so_id', 'time', self::TBLBILL_CUSTOMER_COLUMN, self::TBLBILL_CUSTOMER_ID_COLUMN,
            self::TBLBILL_OPENED_BY_COLUMN, $dnColumn, 'status',
            self::TBLBILL_PICKER_COLUMN, self::TBLBILL_PICKER_TIME_COLUMN,
        ]));

        $rows = DB::table('tblbill')
            ->whereIn('so_id', $soIds)
            ->when($billDate, fn ($q) => $q->whereDate('time', $billDate))
            ->orderBy('time')
            ->get($columns);

        // ── fallback ระบบเก่า: บิลไหนที่ tblbill.emp_picker ยังไม่มีข้อมูล ให้ลองหาใน bills (DB เก่า,
        //    connection mysql_3e) คีย์ด้วยเลขบิล (billNo = tblbill.billid) — วิธีเดียวกับที่
        //    SOPOController::showDetail() ใช้อยู่แล้ว: recNameBill/dateRecBill คือชื่อ+เวลาผู้จัดบิลจากระบบเก่า ──
        $needFallback = $rows
            ->filter(fn ($r) => empty($r->{self::TBLBILL_PICKER_COLUMN}) && !empty($r->{$dnColumn}))
            ->pluck($dnColumn)
            ->unique()
            ->values()
            ->all();

        $legacyPickers = $needFallback
            ? DB::connection(self::LEGACY_CONNECTION)
                ->table('bills')
                ->whereIn('billNo', $needFallback)
                ->get(['billNo', 'recNameBill', 'dateRecBill'])
                ->keyBy('billNo')
            : collect();

        return $rows
            ->map(function ($row) use ($dnColumn, $legacyPickers) {
                $dnNo = ($dnColumn && !empty($row->{$dnColumn})) ? $row->{$dnColumn} : null;

                $pickedBy = $row->{self::TBLBILL_PICKER_COLUMN} ?? null;
                $pickedAt = $row->{self::TBLBILL_PICKER_TIME_COLUMN} ?? null;

                if (empty($pickedBy) && $dnNo && $legacyPickers->has($dnNo)) {
                    $legacy   = $legacyPickers->get($dnNo);
                    $pickedBy = $legacy->recNameBill ?: null;
                    $pickedAt = $legacy->dateRecBill ?: null;
                }
            return (object) [
                'so_id'         => $row->so_id,
                'dn_no'         => $dnNo,
                'time'          => $row->time,
                'customer_name' => $row->{self::TBLBILL_CUSTOMER_COLUMN} ?? null,
                'customer_id'   => $row->{self::TBLBILL_CUSTOMER_ID_COLUMN} ?? null,
                'opened_by'     => $row->{self::TBLBILL_OPENED_BY_COLUMN} ?? null,
                'cancelled'     => (int) ($row->status ?? 0) === self::TBLBILL_STATUS_CANCELLED,
                'picked'        => !empty($pickedBy),
                'picked_by'     => $pickedBy,
                'picked_at'     => $pickedAt,
            ];
            })
            ->groupBy('so_id');
    }
    /* ==================== ด่าน 2: ระบุตำแหน่ง (เฉพาะภายใน — ภายนอกระบุชั้นวางตอนรับเข้าแล้ว) ==================== */
    public function locationDashboard(Request $request)
    {
    $authUser = $this->resolveSsoUser($request, 'store.location');

    if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
        abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
    }
    $creator    = $authUser->name;
    $todoStatus = internal_po::ST_FINISH;
    $statuses   = [internal_po::ST_FINISH, internal_po::ST_STORED, internal_po::ST_CHECKOUT];

    $query = $this->buildLocationQuery($request, $statuses);
    $totalTodoInternal = (clone $query)->where('status', $todoStatus)->count();

    $internalHeads = $query
        ->orderByRaw('FIELD(status, ?) DESC', [$todoStatus])
        ->orderBy('internal_id')
        ->get()
        ->map(fn ($h) => (object) [
            'type'          => 'internal',
            'id'            => $h->internal_id,
            'po_display'    => $h->internal_id,
            'so_id'         => $h->SO_id,
            'customer_name' => $h->customer_name,
            'items'         => $h->lines->map(fn ($it) => (object) [
                'item_name'     => $it->item_name,
                'item_quantity' => $it->item_quantity,
            ]),
            'total_qty' => $h->lines->sum('item_quantity'),
            'location'  => $h->location,
            'packed_by' => $h->pick_by,
            'packed_at' => $h->pick_at,
            'todo'      => $h->status === $todoStatus,
        ]);

    $externalHeads = $this->buildExternalPendingLocationRows($request);
    $totalTodo     = $totalTodoInternal + $externalHeads->count();

    $allHeads = $internalHeads->concat($externalHeads)->sort(function ($a, $b) {
        if ($a->todo !== $b->todo) return $a->todo ? -1 : 1;
        return strcmp((string) $a->po_display, (string) $b->po_display);
    })->values();

    $perPage = self::LOCATION_PER_PAGE;
    $page    = max(1, (int) $request->input('page', 1));
    $heads   = new LengthAwarePaginator(
        $allHeads->forPage($page, $perPage)->values(),
        $allHeads->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $locations = $this->recentLocations();

    return view('store.store_location', compact('heads', 'locations', 'creator', 'totalTodo'));
}
    public function locationSubmit(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        $request->validate([
            'ids'      => 'required|array|min:1',
            'ids.*'    => 'string',
            'location' => 'required|string|max:100',
        ]);

        $ids      = $request->input('ids');
        $location = $request->input('location');

        $internalIds   = [];
        $externalPoIds = [];
        foreach ($ids as $raw) {
            [$type, $id] = array_pad(explode(':', $raw, 2), 2, null);
            if ($id === null) { $internalIds[] = $raw; continue; } // backward-compat ค่าเดิมไม่มี prefix
            if ($type === 'internal') $internalIds[] = $id;
            if ($type === 'external') $externalPoIds[] = $id;
        }

        try {
            $updated = DB::transaction(function () use ($internalIds, $externalPoIds, $authUser, $location) {
                $count = 0;

                if ($internalIds) {
                    $count += internal_po::whereIn('internal_id', $internalIds)
                        ->where('status', internal_po::ST_FINISH)
                        ->update([
                            'status'      => internal_po::ST_STORED,
                            'location_by' => $authUser->name,
                            'location'    => $location,
                            'location_at' => Carbon::now()->toDateTimeString(),
                        ]);
                }
                if ($externalPoIds) {
                    // ⚠️ กันรายการที่ "กำลังจัดการอยู่จริง" (do_it_time มีค่า แต่ยังไม่กดเสร็จสิ้น)
                    //    ไม่ให้ถูกระบุตำแหน่งทับ แม้ UI จะซ่อน checkbox ไว้แล้ว แต่กันไว้อีกชั้นฝั่ง backend
                    //    เผื่อยิง request ตรงๆ
                    //
                    //    ★ แก้ไข: เดิมใช้ whereNull('do_it_time') อย่างเดียว — แต่ตอนนี้ do_it_time จะไม่ถูก
                    //    ล้างอีกต่อไปหลังกด "จัดการเสร็จสิ้น" (เก็บไว้เป็นประวัติ) ถ้ายังใช้เงื่อนไขเดิมจะ
                    //    บล็อกรายการที่จัดการเสร็จแล้วไปด้วย จึงต้องอนุญาตกรณี sus_time มีค่า (เสร็จสิ้นแล้ว)
                    //    ควบคู่ไปกับกรณี do_it_time ยังไม่มีค่า (ไม่เคยถูกจัดการเลย)
                    $externalUpdatedLines = PoReceiveLine::whereIn('po_id', $externalPoIds)
                        ->whereNull('shelf')
                        ->where(function ($q) {
                            $q->whereNull('do_it_time')->orWhereNotNull('sus_time');
                        })
                        ->update(['shelf' => $location]);

                    if ($externalUpdatedLines > 0) {
                        $count += count($externalPoIds);
                    }
                }
                return $count;
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ระบุตำแหน่งไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ระบุตำแหน่ง ' . $updated . ' รายการ']);
    }
    /* ==================== ระบบเก่า: ตาราง store (database "3e") ==================== */
    /**
     * ดึงรายการที่ "มีตำแหน่งแล้ว แต่ยังไม่ได้ checkout"
     * เงื่อนไข: statusArea = '1' (ยังอยู่ในสต็อก) และ Area ถูกระบุแล้ว (ไม่ว่าง)
     *
     * $soNum          : คำค้น SO (LIKE) จากช่องค้นหา — ส่งมาตรงๆ ไม่ใช้ Request อีกต่อไป
     *                    เพื่อให้เรียกจาก Phase B (buildBillCards) ได้โดยไม่ต้องพก Request object
     * $soIds          : null = ไม่กรองตามวันที่เปิดบิล, array = จำกัดเฉพาะ SO ที่มีบิลเปิดในวันที่เลือก
     * $poNum          : คำค้น PO (LIKE)
     * $restrictSoIds  : ใช้ตอนเรียกจาก Phase B (buildBillCards) เพื่อจำกัดเฉพาะ SO ของ "หน้าปัจจุบัน" เท่านั้น
     *                    (ตัดหน้าไปแล้วจาก buildSoSummaries) ป้องกันไม่ให้ query ทั้งวันซ้ำทุกครั้งที่เปลี่ยนหน้า
     */
    private function loadLegacyStoreHeads(?string $soNum, ?array $soIds, ?string $poNum, ?array $restrictSoIds = null)
    {
        $q = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->leftJoin('area', 'area.ID', '=', 'store.Area')
            ->leftJoin('box',  'box.ID',  '=', 'store.BOX')
            ->select('store.*', 'area.areaName', 'box.boxName')
            ->whereIn('store.statusArea', ['0', '1'])
            ->whereNotNull('store.Area')
            ->where('store.Area', '<>', '')
            ->where('store.DATEAREA', '>=', self::LEGACY_STORE_MIN_DATE);   // ← เพิ่มบรรทัดนี้

        if ($soNum) {
            $q->where('store.SO', 'LIKE', '%' . $soNum . '%');
        }
        if ($poNum) {
            $q->where('store.PO', 'LIKE', '%' . $poNum . '%');
        }
        if ($soIds !== null) {
            $q->whereIn('store.SO', $soIds);
        }
        if ($restrictSoIds !== null) {
            // จำกัดเฉพาะ SO ของหน้าปัจจุบัน (เรียกจาก buildBillCards / Phase B)
            $q->whereIn('store.SO', $restrictSoIds);
        }

        return $q->orderBy('store.DATEAREA', 'asc')->get();
    }

    /* ==================== ด่าน 3: ของออก (รวม internal_po + PoReceive + store (ระบบเก่า)) ====================
     * แสดงผลเป็น "การ์ดต่อ SO" แต่ละการ์ดย่อยแยกแสดงเป็น "บิลขนส่ง" (จาก tblbill) ที่เปิดไว้ — 1 SO
     * อาจมีบิลขนส่งได้หลายใบ (ของไม่ได้ส่งครบในเที่ยวเดียวก็ได้) ในแต่ละบิลย่อยจะมี checklist ของ PO
     * ที่ยังไม่จัดออกให้เลือกกด "ของออก" — เนื่องจากข้อมูลรับเข้าไม่มีคอลัมน์เชื่อมกับ billid โดยตรง
     * รายการ PO เดียวกันจะแสดงซ้ำใต้ทุกบิลขนส่งของ SO นั้น ให้ผู้จัดของเลือกเองว่ารายการไหนไปกับบิลไหน
     *
     * ค่าเริ่มต้น: ถ้าไม่ได้ระบุเงื่อนไขค้นหาใดๆ เลย จะโชว์ "งานของวันนี้" ทันที (กรองผ่าน tblbill.time)
     * เพราะ tblbill จะแสดงข้อมูลแยกตามวันที่เปิดบิลอยู่แล้ว — ไม่ต้องกดค้นหาก่อน
     * ผู้ใช้ยังสามารถพิมพ์เลข SO / เลข PO / เปลี่ยนวันที่ เพื่อค้นย้อนหลังได้ตามปกติ
     *
     * ⚡ perf: แยกเป็น Phase A (เบา) + Phase B (หนัก) เพื่อไม่ให้ทุกครั้งที่เปลี่ยนหน้า (page=2, page=3, ...)
     * ต้องแบกงาน join รายการสินค้า + ยิง HTTP ไปเซิร์ฟเวอร์เก่า (fetchLegacyPoItemsBatch) ของ "ทั้งวัน" ซ้ำ
     * ทุกครั้ง — เดิมโค้ด paginate หลังโหลด/ประมวลผลทุกอย่างเสร็จแล้ว (->forPage() ในหน่วยความจำ) ทำให้ทุกหน้า
     * ช้าเท่ากันหมด ตอนนี้ตัดหน้าตั้งแต่ Phase A (เบา ไม่ join, ไม่ยิง HTTP) แล้วค่อยไปทำงานหนักเฉพาะ SO ที่
     * อยู่ในหน้าปัจจุบันเท่านั้นใน Phase B
     */
    public function checkoutDashboard(Request $request)
    {
        $authUser = $this->resolveSsoUser($request, 'store.checkout');

        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        $creator  = $authUser->name;

        $hasSoOrPoSearch = $request->filled('SONum') || $request->filled('PONum');
        $isDefaultView   = !$hasSoOrPoSearch && !$request->filled('bill_date');

        if ($hasSoOrPoSearch) {
            // ค้นด้วยเลข SO/PO แล้ว ไม่จำกัดด้วยวันที่เปิดบิลเลย แม้ช่อง bill_date จะมีค่าค้างอยู่ก็ตาม
            // (ผู้ใช้ตั้งใจค้นย้อนหลังทั้งหมด ไม่ใช่แค่วันที่ที่ดันโชว์อยู่ในช่องวันที่)
            $billDate = null;
        } elseif ($isDefaultView) {
            $billDate = Carbon::today()->toDateString(); // ← โหมดค่าเริ่มต้น: งานวันนี้
        } else {
            $billDate = $request->input('bill_date');
        }

        $soNum = $request->input('SONum');
        $poNum = $request->input('PONum');

        $soIds = $this->soIdsByBillDate($billDate); // null = ไม่กรอง, array = กรองตาม tblbill

        $soSummaries = $this->buildSoSummaries($soIds, $soNum, $poNum, $billDate);
        $daySummary  = $this->buildDaySummary($soSummaries);

        $perPage = self::CHECKOUT_BILLS_PER_PAGE;
        $page    = max(1, (int) $request->input('page', 1));
        $pagedSummaries = $soSummaries->forPage($page, $perPage)->values();

        // ═══ Phase B (หนัก): โหลดรายละเอียดสินค้า + ยิง legacy HTTP เฉพาะ SO ในหน้านี้เท่านั้น ═══
        $billsForPage = $this->buildBillCards($pagedSummaries, $soNum, $poNum);

        $bills = new LengthAwarePaginator(
            $billsForPage,
            $soSummaries->count(),
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('store.store_checkout', [
            'bills'         => $bills,
            'creator'       => $creator,
            'billDate'      => $billDate,
            'isDefaultView' => $isDefaultView,
            'daySummary'    => $daySummary,
        ]);
    }

    /**
     * Phase A (เบา): สร้างรายชื่อ "การ์ด SO" พร้อมสถานะ todo/done + เวลาบิลล่าสุด
     * โดยไม่ join lines/items และไม่ยิง legacy HTTP — ใช้เป็นฐานสำหรับตัดหน้า (pagination)
     * ก่อนไปทำงานหนักเฉพาะ SO ที่ต้องแสดงจริงในหน้านั้นๆ (ดู buildBillCards)
     *
     * คืนค่า Collection ที่เรียงแล้ว (ค้างก่อน, เวลาล่าสุดก่อน) พร้อมตัดหน้าได้ทันที
     * แต่ละ item: { so_id, all_done, latest_time, customer_name, not_received, bills }
     * โดย 'bills' คือแถว tblbill ของ SO นั้น (เก็บไว้เผื่อ Phase B เรียกใช้ต่อ ไม่ต้อง query ซ้ำ)
     */
private function buildSoSummaries(?array $soIds, ?string $soNum, ?string $poNum, ?string $billDate): \Illuminate\Support\Collection
{
    $internalLight = internal_po::query()
        ->whereIn('status', [internal_po::ST_STORED, internal_po::ST_CHECKOUT])
        ->when($soNum, fn ($q) => $q->where('SO_id', 'LIKE', '%' . $soNum . '%'))
        ->when($poNum, fn ($q) => $q->where('internal_id', 'LIKE', '%' . $poNum . '%'))
        ->when($soIds !== null, fn ($q) => $q->whereIn('SO_id', $soIds))
        ->get(['SO_id', 'status'])
        ->map(fn ($h) => (object) ['so_id' => $h->SO_id, 'todo' => $h->status === internal_po::ST_STORED]);

    $externalLight = PoReceive::query()
        ->when($soNum, fn ($q) => $q->where('so_id', 'LIKE', '%' . $soNum . '%'))
        ->when($poNum, fn ($q) => $q->where('po_id', 'LIKE', '%' . $poNum . '%'))
        ->when($soIds !== null, fn ($q) => $q->whereIn('so_id', $soIds))
        ->get(['so_id', 'checkout_by'])
        ->map(fn ($h) => (object) ['so_id' => $h->so_id, 'todo' => is_null($h->checkout_by)]);

    $legacyLight = DB::connection(self::LEGACY_CONNECTION)->table('store')
        ->whereIn('statusArea', ['0', '1'])
        ->whereNotNull('Area')->where('Area', '<>', '')
        ->where('DATEAREA', '>=', self::LEGACY_STORE_MIN_DATE)  
        ->when($soNum, fn ($q) => $q->where('SO', 'LIKE', '%' . $soNum . '%'))
        ->when($poNum, fn ($q) => $q->where('PO', 'LIKE', '%' . $poNum . '%'))
        ->when($soIds !== null, fn ($q) => $q->whereIn('SO', $soIds))
        ->get(['SO', 'statusArea'])
        ->map(fn ($h) => (object) ['so_id' => $h->SO, 'todo' => $h->statusArea === '1']);

    $grouped = $internalLight->concat($externalLight)->concat($legacyLight)
        ->filter(fn ($h) => !empty($h->so_id))
        ->groupBy('so_id');

    $soIdsFromHeads = $grouped->keys()->values()->all();
    $soIdsForBills  = $soIds !== null
        ? array_values(array_unique(array_merge($soIds, $soIdsFromHeads)))
        : $soIdsFromHeads;
    $billRowsBySo = $this->billRowsBySo($soIdsForBills, $billDate);

    // "จัดของแล้ว" ของ SO นี้ ยึดตามบิลขนส่ง (tblbill) เป็นหลัก: ถ้า SO มีบิลขนส่งอยู่ (ไม่นับใบยกเลิก)
    // ต้อง "จัดครบทุกใบ" (มีผู้จัด/pick แล้วทุกบิล) ถึงจะถือว่าเสร็จ — ไม่ใช่ดูจาก PO ครบ/ไม่ครบอีกต่อไป
    // ถ้า SO ยังไม่มีบิลขนส่งเลย fallback ไปดูสถานะ PO เดิมไปพลางก่อน
    $calcAllDone = function ($billRows, $rows = null) {
        $activeBills = $billRows->where('cancelled', false);
        if ($activeBills->isNotEmpty()) {
            return $activeBills->every(fn ($b) => $b->picked);
        }
        return $rows ? $rows->where('todo', true)->isEmpty() : false;
    };

    $summaries = $grouped->map(function ($rows, $soId) use ($billRowsBySo, $calcAllDone) {
        $billRows  = $billRowsBySo->get($soId, collect());
        $firstBill = $billRows->first();
        return (object) [
            'so_id'         => $soId,
            'all_done'      => $calcAllDone($billRows, $rows),
            'latest_time'   => $billRows->max('time'),
            'customer_name' => optional($firstBill)->customer_name,
            'customer_id'   => optional($firstBill)->customer_id,
            'not_received'  => false,
            'bills'         => $billRows,
        ];
    })->values();

    if ($soIds !== null) {
        $presentSoIds = $summaries->pluck('so_id')->all();
        $missingSoIds = array_values(array_diff($soIds, $presentSoIds));

        if ($missingSoIds) {
            // SO กลุ่มนี้มักไม่มี PO เชื่อมเลย — ยังบันทึก "ผู้จัดบิล" เองได้ผ่านช่องติ๊กเฉพาะในหน้าเว็บ
            $phantoms = collect($missingSoIds)->map(function ($soId) use ($billRowsBySo, $calcAllDone) {
                $billRows  = $billRowsBySo->get($soId, collect());
                $firstBill = $billRows->first();
                return (object) [
                    'so_id'         => $soId,
                    'all_done'      => $calcAllDone($billRows),
                    'latest_time'   => $billRows->max('time'),
                    'customer_name' => optional($firstBill)->customer_name,
                    'customer_id'   => optional($firstBill)->customer_id,
                    'not_received'  => true,
                    'bills'         => $billRows,
                ];
            });
            $summaries = $summaries->concat($phantoms)->values();
        }
    }
        return $summaries->sort(function ($a, $b) {
            if ($a->all_done !== $b->all_done) {
                return $a->all_done ? 1 : -1;
            }
            return strcmp((string) $a->latest_time, (string) $b->latest_time);
        })->values();
}
private function buildDaySummary(\Illuminate\Support\Collection $soSummaries): array
{
    $activeBills = $soSummaries->flatMap(fn ($s) => $s->bills)->where('cancelled', false);

    $doneBillsCount    = $activeBills->where('picked', true)->count();
    $pendingBillsCount = $activeBills->where('picked', false)->count();

    return [
        'total'         => $soSummaries->count(),
        'done'          => $soSummaries->where('all_done', true)->count(),
        'pending'       => $soSummaries->where('all_done', false)->count(),
        'total_bills'   => $doneBillsCount + $pendingBillsCount,
        'done_bills'    => $doneBillsCount,
        'pending_bills' => $pendingBillsCount,
    ];
}

    /**
     * Phase B (หนัก): โหลดรายละเอียดสินค้า (join lines) + ยิง legacy HTTP (fetchLegacyPoItemsBatch)
     * เฉพาะ SO ที่อยู่ใน $pagedSummaries (คือ SO ของหน้าปัจจุบันเท่านั้น) ทำให้เปลี่ยนหน้าแล้วไม่ต้อง
     * แบกงานของ SO ทั้งวันซ้ำอีกต่อไป — จุดนี้คืองานหนักเดิมทั้งหมดที่ย้ายมาจาก checkoutDashboard() เดิม
     * เพียงแต่ query ทุกตัวถูกจำกัดขอบเขตด้วย whereIn(SO_id, $pageSoIds) ก่อนแล้ว
     *
     * นอกจากนี้ยังทำ fallback "เช็คของออกโดย/เมื่อ" — ถ้าระบบใหม่ (internal_po.checkout_at /
     * PoReceive.checkout_time) ไม่มีข้อมูล (เช่น รายการนั้นถูกเช็คของออกผ่านระบบเก่าก่อนย้ายระบบ)
     * จะ fallback ไปดึง DATECHECKOUT จากตาราง store (3e) โดย match ด้วยเลข PO — ระบบเก่าไม่มีชื่อผู้ใช้จริง
     * เก็บไว้ (boxS คือชื่อกล่อง ไม่ใช่ชื่อคน) จึง fallback ได้แค่ "เวลา" ไม่ใช่ "ชื่อคน"
     */
    private function buildBillCards(\Illuminate\Support\Collection $pagedSummaries, ?string $soNum, ?string $poNum): \Illuminate\Support\Collection
    {
        $pageSoIds = $pagedSummaries->pluck('so_id')->values()->all();
        if (!$pageSoIds) return collect();

        $billsBySo = $pagedSummaries->keyBy('so_id')->map->bills; // ใช้ bills ที่ Phase A ดึงมาแล้ว ไม่ query ซ้ำ

        // ── ภายใน: internal_po ที่ระบุตำแหน่งแล้ว (ST_STORED) หรือเอาของออกแล้ว (ST_CHECKOUT) — เฉพาะ SO ในหน้านี้ ──
        $internalHeadsRaw = internal_po::with('lines')
            ->whereIn('status', [internal_po::ST_STORED, internal_po::ST_CHECKOUT])
            ->whereIn('SO_id', $pageSoIds)
            ->when($soNum, fn ($q) => $q->where('SO_id', 'LIKE', '%' . $soNum . '%'))
            ->when($poNum, fn ($q) => $q->where('internal_id', 'LIKE', '%' . $poNum . '%'))
            ->get();

        // ⚡ perf: เดิมโค้ดยิง query ไปที่ 3e.internal_poline "ทีละใบ" ข้างใน ->map() ด้านล่าง (N+1) — รวบ
        // internal_id ของใบที่ยังไม่มี lines ทั้งหมดไว้ก่อน แล้วยิง query เดียว (whereIn) ดึงทีเดียว
        $emptyLineIds = $internalHeadsRaw->filter(fn ($h) => $h->lines->isEmpty())->pluck('internal_id')->values()->all();

        $fallbackLinesByPoNum = $emptyLineIds
            ? DB::connection(self::LEGACY_CONNECTION)
                ->table('internal_poline')
                ->whereIn('PONum', $emptyLineIds)
                ->orderBy('POLineSeq')
                ->get()
                ->groupBy('PONum')
            : collect();

        $internalHeads = $internalHeadsRaw
            ->map(function ($h) use ($fallbackLinesByPoNum) {
                $items = $h->lines->map(fn ($it) => (object) [
                    'item_name'     => $it->item_name,
                    'item_quantity' => $it->item_quantity,
                ]);

                // ไม่เจอบรรทัดสินค้าใน DB logistic (เช่นใบเก่าที่ยังไม่ migrate) → ใช้ข้อมูลที่ batch มาแล้วด้านบน
                if ($items->isEmpty()) {
                    $items = $fallbackLinesByPoNum->get($h->internal_id, collect())
                        ->map(fn ($it) => (object) [
                            'item_name'     => $it->Description ?: '—',
                            'item_quantity' => (float) $it->Quantity,
                        ]);
                }

                return (object) [
                    'type'          => 'internal',
                    'id'            => $h->internal_id,
                    'po_display'    => $h->internal_id,
                    'so_id'         => $h->SO_id,
                    'customer_name' => null,
                    'items'         => $items,
                    'location'      => $h->location,
                    'done_by'       => $h->location_by,
                    'done_at'       => $h->location_at,
                    'checkout_by'   => $h->checkout_by, // คนเช็คของออกจริง (ระบบใหม่)
                    'checkout_at'   => $h->checkout_at,
                    'status'        => $h->status,
                    'status_color'  => $h->status_color,
                    'todo'          => $h->status === internal_po::ST_STORED,
                ];
            });

        // ── ภายนอก: PoReceive (รับเข้าจาก mobile) — แต่ละสินค้ามีที่เก็บ/ผู้รับ/เวลาของตัวเอง — เฉพาะ SO ในหน้านี้ ──
        $externalHeads = PoReceive::with('lines')
            ->whereIn('so_id', $pageSoIds)
            ->when($soNum, fn ($q) => $q->where('so_id', 'LIKE', '%' . $soNum . '%'))
            ->when($poNum, fn ($q) => $q->where('po_id', 'LIKE', '%' . $poNum . '%'))
            ->get()
            ->map(function ($h) {
                $items = $h->lines;
                $todo  = is_null($h->checkout_by);
                $first = $items->first();
                return (object) [
                    'type'          => 'external',
                    'id'            => $h->po_id,            // ใช้เป็น key ส่งกลับตอนกด "ของออก"
                    'po_display'    => $h->po_id,             // เลข PO ที่แสดงในคอลัมน์ "PO ภายใน"
                    'so_id'         => $h->so_id,
                    'customer_name' => null, // po_receives ไม่มีคอลัมน์ชื่อลูกค้า ดึงจาก tblbill ผ่าน so_id แทน
                    'items'         => $items->map(fn ($it) => (object) [
                        'item_name'     => $it->good_name,
                        'item_quantity' => $it->recv_qty,
                        // ⚠️ per-item: สมมติว่า PoReceiveLine มีคอลัมน์เหล่านี้ (ใช้อยู่แล้วที่อื่นในไฟล์นี้)
                        //    ถ้าชื่อคอลัมน์จริงต่างจากนี้ แก้ตรงนี้ที่เดียว
                        'shelf'         => $it->shelf,
                        'done_by'       => $it->received_by,
                        'done_at'       => $it->received_at,
                    ]),
                    'location'      => optional($first)->shelf,
                    'done_by'       => optional($first)->received_by,
                    'done_at'       => optional($first)->received_at,
                    'checkout_by'   => $h->checkout_by,
                    'checkout_at'   => $h->checkout_time,
                    'status'        => $todo ? 'รับเข้าแล้ว (รอของออก)' : 'เอาของออกแล้ว',
                    'status_color'  => $todo ? 'orange' : 'green',
                    'todo'          => $todo,
                ];
            });

        // ── ระบบเก่า: store (database "3e") — มีตำแหน่งแล้ว ยังไม่ checkout — เฉพาะ SO ในหน้านี้ ──
        $legacyHeadsRaw = $this->loadLegacyStoreHeads($soNum, null, $poNum, $pageSoIds);

        // ดึงรายการสินค้าของทุกใบ "ระบบเก่า" แบบพร้อมกันทีเดียว — ยิง HTTP เฉพาะ PO ของหน้านี้เท่านั้น
        $legacyPoNums     = $legacyHeadsRaw->pluck('PO')->filter()->unique()->values()->all();
        $legacyItemsByPo  = $this->fetchLegacyPoItemsBatch($legacyPoNums);

        $legacyHeads = $legacyHeadsRaw->map(function ($h) use ($legacyItemsByPo) {
            $todo  = $h->statusArea === '1';
            $lines = $legacyItemsByPo->get($h->PO, collect());

            return (object) [
                'type'          => 'legacy',
                'id'            => $h->ID,
                'po_display'    => $h->PO ?: '—',
                'so_id'         => $h->SO,
                'customer_name' => null,
                'items'         => $lines->isEmpty()
                    ? collect([(object) ['item_name' => '—', 'item_quantity' => 1]])
                    : $lines->map(fn ($it) => (object) [
                        'item_name'     => $it->item_name,
                        'item_quantity' => $it->item_quantity,
                    ]),
                'location'      => $todo ? $h->areaName : ($h->areaS ?: $h->areaName),
                'done_by'       => $todo ? ($h->boxName ?: '—') : ($h->boxS ?: '—'),
                'done_at'       => $h->DATEAREA,              
                'checkout_by'   => null,
                'checkout_at'   => $todo ? null : $h->DATECHECKOUT,
                'status'        => $todo ? 'รอเอาออก (ระบบเก่า)' : 'เอาของออกแล้ว (ระบบเก่า)',
                'status_color'  => $todo ? 'orange' : 'green',
                'todo'          => $todo,
            ];
        });
        // ── เติม "เช็คของออกเมื่อไหร่" ให้รายการภายใน/ภายนอกที่ done แล้ว แต่ระบบใหม่ไม่มีข้อมูล checkout
        //    (เช่น ถูกเช็คของออกผ่านระบบเก่าก่อนย้ายระบบ) — fallback ไปดึง DATECHECKOUT จากตาราง store (3e)
        //    โดย match ด้วยเลข PO ตรงๆ ระบบเก่าไม่มีชื่อผู้ใช้จริงเก็บไว้ (boxS คือชื่อกล่อง ไม่ใช่ชื่อคน)
        //    จึง fallback ได้แค่ "เวลา" ไม่ใช่ "ชื่อคน" ──
        $needCheckoutFallback = $internalHeads->concat($externalHeads)
            ->filter(fn ($h) => !$h->todo && empty($h->checkout_at))
            ->values();

        if ($needCheckoutFallback->isNotEmpty()) {
            $poNumsForFallback = $needCheckoutFallback->pluck('po_display')->filter()->unique()->values()->all();

            $legacyCheckoutAtByPo = DB::connection(self::LEGACY_CONNECTION)
                ->table('store')
                ->whereIn('PO', $poNumsForFallback)
                ->where('statusArea', '0')
                ->whereNotNull('DATECHECKOUT')
                ->orderBy('DATECHECKOUT', 'desc')
                ->get(['PO', 'DATECHECKOUT'])
                ->groupBy('PO')
                ->map(fn ($rows) => $rows->first()->DATECHECKOUT);

            $needCheckoutFallback->each(function ($h) use ($legacyCheckoutAtByPo) {
                $fallbackAt = $legacyCheckoutAtByPo->get($h->po_display);
                if ($fallbackAt) {
                    $h->checkout_at = $fallbackAt; // มีแค่เวลา ไม่มีชื่อคน (ระบบเก่าไม่มีเก็บ)
                }
            });
        }
    $groupedBySo = $internalHeads->concat($externalHeads)->concat($legacyHeads)->groupBy('so_id');

    // ── ประกอบกลับเป็น "การ์ดระดับ SO" ตามลำดับเดิมที่ Phase A จัดมาแล้ว (คงลำดับหน้าไว้) ──
    return $pagedSummaries->map(function ($summary) use ($groupedBySo, $billsBySo) {
        $soId   = $summary->so_id;
        $groups = $groupedBySo->get($soId, collect());

        // ── PO ซ้ำกัน (เช่น รับเข้าซ้ำหลายรอบผ่านมือถือ) → เหลือแสดงแค่รายการล่าสุด (done_at มากสุด)
        //    เท่านั้น กันไม่ให้การ์ดโชว์รายการสินค้าเดิมซ้ำหลายก้อน ระบบ checkout ยังทำงานถูกต้องเหมือนเดิม
        //    เพราะฝั่ง backend อัปเดตด้วย po_id ซึ่งครอบคลุมทุกแถวที่ซ้ำกันอยู่แล้ว
        $groups = $groups
            ->groupBy(fn ($g) => $g->type . '|' . $g->po_display)
            ->map(fn ($dupes) => $dupes->sortByDesc(fn ($g) => (string) $g->done_at)->first())
            ->values();

        $billRows  = $billsBySo->get($soId, collect());
        $todoCount = $groups->where('todo', true)->count();

        return (object) [
            'so_id'         => $soId,
            'bills'         => $billRows,
            'latest_time'   => $summary->latest_time,
            'customer_name' => $summary->customer_name,
            'customer_id'   => $summary->customer_id,
            'groups'        => $groups->values(),
            'todo_groups'   => $groups->where('todo', true)->values(),
            'done_groups'   => $groups->where('todo', false)->values(),
            'todo_count'    => $todoCount,
            'total_count'   => $groups->count(),
            'all_done'      => $summary->all_done,
            'not_received'  => $summary->not_received,
        ];
    })->values();
    }

public function checkoutSubmit(Request $request)
{
    $authUser = Auth::guard('web')->user();
    if (!$authUser) {
        return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
    }
    $user = $authUser->name;

    $request->validate([
        'ids'      => 'nullable|array',
        'ids.*'    => 'string',
        'dn_nos'   => 'nullable|array',
        'dn_nos.*' => 'string',
    ]);

    $ids   = $request->input('ids', []);
    $dnNos = array_values(array_unique(array_filter($request->input('dn_nos', []))));

    if (!$ids && !$dnNos) {
        return response()->json(['ok' => false, 'message' => 'ยังไม่ได้เลือกรายการ'], 422);
    }

    $internalIds = [];
    $externalIds = [];
    $legacyIds   = [];
    foreach ($ids as $raw) {
        [$type, $id] = array_pad(explode(':', $raw, 2), 2, null);
        if ($id === null) continue;
        if ($type === 'internal') $internalIds[] = $id;
        if ($type === 'external') $externalIds[] = $id;
        if ($type === 'legacy')   $legacyIds[]   = $id;
    }

    if ($ids && !$internalIds && !$externalIds && !$legacyIds) {
        return response()->json(['ok' => false, 'message' => 'รายการที่เลือกไม่ถูกต้อง'], 422);
    }

    $updated = 0;

    try {
        DB::transaction(function () use ($internalIds, $externalIds, $user, &$updated) {
            if ($internalIds) {
                $updated += internal_po::whereIn('internal_id', $internalIds)
                    ->where('status', internal_po::ST_STORED)
                    ->update([
                        'status'      => internal_po::ST_CHECKOUT,
                        'checkout_by' => $user,
                        'checkout_at' => Carbon::now()->toDateTimeString(),
                    ]);
            }
            if ($externalIds) {
                $updated += PoReceive::whereIn('po_id', $externalIds)
                    ->whereNull('checkout_by')
                    ->update([
                        'checkout_by'   => $user,
                        'checkout_time' => Carbon::now(),
                    ]);
            }
        });

        if ($legacyIds) {
                $updated += $this->checkoutLegacyAndMigrate($legacyIds, $user, $dnNos);
            }
    } catch (\Exception $e) {
        return response()->json(['ok' => false, 'message' => 'ของออกไม่สำเร็จ: ' . $e->getMessage()], 500);
    }

    // ── บันทึกชื่อ/เวลาผู้จัดบิลลง tblbill ทุกใบที่เลือกไว้ (รวมบิลที่ไม่มี PO เชื่อมเลย) ──
    $pickedBills = 0;
    if ($dnNos) {
        try {
            $pickedBills = DB::table('tblbill')
                ->whereIn(self::TBLBILL_DN_COLUMN, $dnNos)
                ->whereNull(self::TBLBILL_PICKER_COLUMN) // เคยบันทึกไปแล้ว ไม่เขียนทับซ้ำ
                ->update([
                    self::TBLBILL_PICKER_COLUMN      => $user,
                    self::TBLBILL_PICKER_TIME_COLUMN => Carbon::now()->toDateTimeString(),
                ]);
        } catch (\Exception $e) {
            Log::warning('checkoutSubmit: บันทึก emp_picker/picker_time ลง tblbill ไม่สำเร็จ: ' . $e->getMessage());
        }
    }

    if ($updated === 0 && $pickedBills === 0) {
        return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ (อาจมีคนบันทึกไปก่อนแล้ว)'], 404);
    }

    $msgParts = [];
    if ($updated > 0) $msgParts[] = 'ของออก ' . $updated . ' ใบ';
    if ($pickedBills > 0) $msgParts[] = 'บันทึกผู้จัดบิล ' . $pickedBills . ' บิล';

    return response()->json(['ok' => true, 'message' => implode(' · ', $msgParts) ?: 'บันทึกสำเร็จ']);
}
    public function status(Request $request)
    {
        $request->validate([
            'PONum' => 'required|string',
            'SONum' => 'required|string',
        ]);

        $poId = $request->input('PONum'); // รูปแบบเดียวกับ po_receives.po_id เช่น 'PO0000-00000'
        $soId = $request->input('SONum');

        $receive = PoReceive::where('po_id', $poId)
            ->where('so_id', $soId)
            ->first();

        $lines = PoReceiveLine::where('po_id', $poId)->get();

        // รวมยอดรับเข้าต่อชื่อสินค้า (อาจมีหลายรอบรับของ) และเก็บรูปแรกที่เจอ
        $items = [];
        foreach ($lines as $line) {
            $name = $line->good_name;

            if (!isset($items[$name])) {
                $items[$name] = [
                    'received_qty' => 0,
                    'photo_url'    => null,
                    'shelf'        => null,
                ];
            }

            $items[$name]['received_qty'] += (float) $line->recv_qty;

            if (!$items[$name]['photo_url'] && $line->photo_path) {
                $items[$name]['photo_url'] = $line->photoUrl();
            }

            if (!$items[$name]['shelf'] && $line->shelf) {
                $items[$name]['shelf'] = $line->shelf;
            }
        }

        return response()->json([
            'ok'     => true,
            'po_id'  => $poId,
            'so_id'  => $soId,
            'status' => $receive->status ?? null, // 'ครบ' | 'บางส่วน' | 'ยกเลิก' | null (ยังไม่รับเข้า)
            'items'  => $items,
        ]);
    }
private function checkoutLegacyAndMigrate(array $legacyIds, string $user, array $dnNos = []): int
{
    if (!$legacyIds) return 0;

    $legacyRows = DB::connection(self::LEGACY_CONNECTION)->table('store')
        ->leftJoin('area', 'area.ID', '=', 'store.Area')
        ->leftJoin('box',  'box.ID',  '=', 'store.BOX')
        ->select('store.*', 'area.areaName', 'box.boxName')
        ->whereIn('store.ID', $legacyIds)
        ->where('store.statusArea', '1')
        ->get();

    if ($legacyRows->isEmpty()) return 0;

    $poNums    = $legacyRows->pluck('PO')->filter()->unique()->values()->all();
    $itemsByPo = $this->fetchLegacyPoItemsBatch($poNums);

    // ── ดึง POref (tblbill.ponum) + customer_name จาก tblbill ตาม billid ที่ส่งมาด้วยตอน submit ──
    // เทียบด้วย so_id คู่กัน เพราะ store ไม่มีคอลัมน์ billid ผูกตรงอยู่
    $billMetaBySoId = $dnNos
        ? DB::table('tblbill')
            ->whereIn(self::TBLBILL_DN_COLUMN, $dnNos)
            ->get(['so_id', self::TBLBILL_POREF_COLUMN, self::TBLBILL_CUSTOMER_COLUMN])
            ->keyBy('so_id')
        : collect();

    $now     = Carbon::now();
    $updated = 0;

    DB::transaction(function () use ($legacyRows, $itemsByPo, $billMetaBySoId, $user, $now, &$updated) {
        foreach ($legacyRows as $row) {
            $poId = 'PO' . $row->PO;
            $soId = $row->SO;
            $meta = $billMetaBySoId->get($soId);

            $receive = PoReceive::firstOrNew([
                'po_id' => $poId,
                'so_id' => $soId,
            ]);
            $receive->status        = 'ครบ';
            $receive->checkout_by   = $user;
            $receive->checkout_time = $now;
            if ($meta) {
                $receive->POref      = $meta->{self::TBLBILL_POREF_COLUMN} ?? $receive->POref;
                $receive->cust_name  = $meta->{self::TBLBILL_CUSTOMER_COLUMN} ?? $receive->cust_name;
            }
            $receive->save();

            if (!PoReceiveLine::where('po_id', $poId)->exists()) {
                foreach ($itemsByPo->get($row->PO, collect()) as $line) {
                    PoReceiveLine::create([
                        'po_id'       => $poId,
                        'good_name'   => $line->item_name,
                        'recv_qty'    => $line->item_quantity,
                        'unit_price'  => null,
                        'shelf'       => $row->areaName,
                        'photo_path'  => null,
                        'received_by' => $row->boxName ?: 'ระบบเก่า',
                        'received_at' => $row->DATEAREA ?? $now,
                    ]);
                }
            }

            DB::connection(self::LEGACY_CONNECTION)->table('store')
                ->where('ID', $row->ID)
                ->update([
                    'statusArea'   => '0',
                    'DATECHECKOUT' => $now->toDateTimeString(),
                ]);

            $updated++;
        }
    });

    Log::info("checkoutLegacyAndMigrate: user={$user} updated={$updated} ids=" . implode(',', $legacyRows->pluck('ID')->all()));

    return $updated;
}
}