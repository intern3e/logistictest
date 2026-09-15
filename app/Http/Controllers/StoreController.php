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
    const MSSQL_CONNECTION  = 'mssql_account03';
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
        return $this->requireLogin($request, $logTag);
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

        // แก้บั๊ก "ดูสินค้า" ไม่ขึ้นข้อมูลสำหรับรายการ legacy ที่ยัง "ไม่ถูก claim"
        // -----------------------------------------------------------------------------
        // จุดที่มา: buildLegacyPendingLocationRows() ส่ง field 'id' ของการ์อดประเภท legacy
        // เป็น store.ID (primary key ตาราง store) ไม่ใช่เลข PO จริง เพราะ id นี้ต้องใช้คู่กับ
        // locationClaim()/legacyClaim() ที่ query จาก store.ID ตรงๆ
        //
        // แต่ที่นี่ (itemsDetailBatch) โค้ดเดิมเอา id นั้นไปใช้เป็น "เลข PO" ตรงๆ เพื่อค้นหา
        // รายละเอียดสินค้าใน fetchLegacyPoItemsBatch() (เทียบกับ POHD.DocuNo / internal_poline.PONum
        // ฯลฯ) ทำให้หาไม่เจอเลย เพราะ store.ID กับเลข PO เป็นคนละค่ากัน -> รายการสินค้าว่างเปล่าเสมอ
        // สำหรับ legacy ที่ยัง "ไม่ถูก claim" (ต่างจากที่ claim แล้ว ซึ่งถูกย้ายไปเป็น PoReceive
        // ประเภท external ที่ id = po_id ที่ถูกต้องอยู่แล้ว จึงไม่มีปัญหานี้)
        //
        // วิธีแก้: resolve store.ID -> เลข PO จริงก่อน ให้ขั้นตอนถัดไปได้ค่าเลข PO ที่ถูกต้อง
        // (ถ้า id ที่ส่งมาบังเอิญเป็นเลข PO จริงอยู่แล้ว ไม่ตรงกับ store.ID ใดเลย โค้ดนี้จะไม่แก้ไขอะไร)
        if ($legacyRows->isNotEmpty()) {
            $legacyIdToPoNum = DB::connection(self::LEGACY_CONNECTION)->table('store')
                ->whereIn('ID', $legacyRows->pluck('id')->unique()->values()->all())
                ->pluck('PO', 'ID');

            $legacyRows = $legacyRows->map(function ($row) use ($legacyIdToPoNum) {
                if ($legacyIdToPoNum->has($row['id'])) {
                    $row['id'] = $legacyIdToPoNum->get($row['id']);
                }
                return $row;
            });
        }
        // -----------------------------------------------------------------------------

        $externalPoNumOf = fn ($poId) => preg_replace('/^PO/', '', $poId);

        $allPoNums = collect()
            ->merge($externalRows->pluck('id')->unique()->map($externalPoNumOf))
            ->merge($legacyRows->pluck('id')->unique())
            ->filter()
            ->unique()
            ->values()
            ->all();

        $legacyDetail = $this->fetchLegacyPoItemsBatch($allPoNums);

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

    private function fetchLegacyPoItemsBatch(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = collect($poNums)->filter()->unique()->values();
        if ($poNums->isEmpty()) return collect();

        $internalStyleNums = $poNums->filter(fn ($num) => str_contains($num, 'A'))->values();
        $normalNums         = $poNums->diff($internalStyleNums)->values();

        $data = collect();

        if ($internalStyleNums->isNotEmpty()) {
            $data = $data->merge($this->fetchInternalPoLineItems($internalStyleNums->all()));
        }

        if ($normalNums->isNotEmpty()) {
            $cacheKey = fn ($num) => 'legacy_po_items:' . $num;

            $normalNums->each(function ($num) use (&$data, $cacheKey) {
                $hit = Cache::get($cacheKey($num));
                if ($hit !== null) $data->put($num, collect($hit));
            });

            $remaining = $normalNums->diff($data->keys())->values();

            if ($remaining->isNotEmpty()) {
                // 1) ดึงจาก MSSQL (POHD/PODT) โดยตรง — เร็ว วิธีเดียวกับหน้า store_location
                $data = $data->merge($this->fetchMssqlPoItems($remaining->all()));

                // 2) PO ที่ MSSQL ดึงไม่ได้ (เช่น prod เชื่อม account03 ไม่ได้/ไม่เสถียร)
                //    -> fallback ยิง HTTP getPODetail เพื่อให้ชื่อสินค้าขึ้นครบเสมอ (กัน "ขึ้นบ้างไม่ขึ้นบ้าง")
                $stillEmpty = $remaining->filter(fn ($num) => !$data->has($num) || $data->get($num)->isEmpty())->values();
                if ($stillEmpty->isNotEmpty()) {
                    $needPooled = collect();
                    foreach ($stillEmpty->chunk(self::LEGACY_PO_ARRAY_BATCH_SIZE) as $chunk) {
                        $batchResult = $this->fetchLegacyPoItemsArrayRequest($chunk->values()->all());
                        if ($batchResult === null) {
                            $needPooled = $needPooled->merge($chunk->values()->all());
                        } else {
                            $data = $data->merge($batchResult);
                        }
                    }
                    if ($needPooled->isNotEmpty()) {
                        $data = $data->merge($this->fetchLegacyPoItemsPooled($needPooled->values()->all()));
                    }
                }

                $remaining->each(function ($num) use ($data, $cacheKey) {
                    $items = $data->get($num);
                    if ($items && $items->isNotEmpty()) {
                        Cache::put($cacheKey($num), $items->all(), now()->addMinutes(10));
                    }
                });

                $emptyNormalNums = $normalNums->filter(fn ($num) => !$data->has($num) || $data->get($num)->isEmpty())->values();
                if ($emptyNormalNums->isNotEmpty()) {
                    $data = $data->merge($this->fetchInternalPoLineItems($emptyNormalNums->all()));
                }
            }
        }

        $poNums->each(function ($num) use ($data) {
            if (!$data->has($num)) $data->put($num, collect());
        });

        return $data;
    }

    /**
     * ดึงชื่อสินค้าของ PO จาก MSSQL (POHD -> PODT) โดยตรง แทนการยิง HTTP getPODetail
     * คืนค่า keyed by เลข PO -> collection ของ {item_name, item_quantity}
     */
    private function fetchMssqlPoItems(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = array_values(array_filter($poNums));
        if (!$poNums) return collect();

        try {
            // DocuNo ใน POHD = 'PO' + เลข PO
            $docuToPo = collect($poNums)->mapWithKeys(fn ($p) => ['PO' . $p => $p]);

            $headers = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                ->whereIn('DocuNo', $docuToPo->keys()->all())
                ->get(['POID', 'DocuNo']);
            if ($headers->isEmpty()) return collect();

            $poidToPo = $headers->mapWithKeys(fn ($h) => [$h->POID => $docuToPo->get($h->DocuNo)]);

            $items = DB::connection(self::MSSQL_CONNECTION)->table('PODT')
                ->whereIn('POID', $poidToPo->keys()->all())
                ->where('CancelFlag', '<>', 'Y')
                ->get(['POID', 'GoodName', 'GoodQty2']);

            return $items->groupBy('POID')->mapWithKeys(fn ($lines, $poid) => [
                $poidToPo->get($poid) => $lines->map(fn ($l) => (object) [
                    'item_name'     => $l->GoodName ?: '—',
                    'item_quantity' => (float) $l->GoodQty2,
                ])->values(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('fetchMssqlPoItems failed: ' . $e->getMessage());
            return collect();
        }
    }

    private function fetchInternalPoLineItems(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = array_values(array_filter($poNums));
        if (!$poNums) return collect();

        return DB::connection(self::LEGACY_CONNECTION)
            ->table('internal_poline')
            ->whereIn('PONum', $poNums)
            ->orderBy('POLineSeq')
            ->get()
            ->groupBy('PONum')
            ->map(fn ($lines) => $lines->map(fn ($l) => (object) [
                'item_name'     => $l->Description ?: '—',
                'item_quantity' => (float) $l->Quantity,
            ]));
    }

    private function fetchLegacyPoItemsArrayRequest(array $poNums): ?\Illuminate\Support\Collection
    {
        try {
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
            return null;
        }

        $ponumKeys = ['PONum', 'PoNum', 'ponum', 'PO_NUM', 'PONUM'];
        $sample    = $lines[0];
        $key       = collect($ponumKeys)->first(fn ($k) => is_array($sample) && array_key_exists($k, $sample));

        if (!$key) {
            Log::warning('fetchLegacyPoItemsArrayRequest: ตอบกลับไม่มีคอลัมน์ระบุ PONum ต่อบรรทัด แยกคืนตาม PO ไม่ได้ ใช้วิธีเดิมแทน');
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
                'finished'    => $claim['finished'],
                'finished_by' => $claim['finished_by'],
                'finished_at' => $claim['finished_at'],
            ];
        })->values();
    }

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

    private function soIdsByBillDate(?string $date): ?array
    {
        if (!$date) return null;

        return DB::table('tblbill')
            ->whereDate('time', $date)
            ->pluck('so_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

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

        $needFallback = $rows
            ->filter(fn ($r) => empty($r->{self::TBLBILL_PICKER_COLUMN}) && !empty($r->{$dnColumn}))
            ->pluck($dnColumn)
            ->unique()
            ->values()
            ->all();

        // ระบบเก่า (fallback เมื่อระบบใหม่ไม่มี emp_picker): ถือว่า "จัดสำเร็จ"
        // ถ้าเลขบิลอยู่ใน BILL_STATUS_HISTORY ที่ DeliveryStatus >= '20' (ผ่านขั้น "ของครบ" แล้ว)
        // status: 10=รอรับของ(ยังไม่นับ), 20=ของครบ, 21=ตีกลับ, 30=บิลออก, 80=ส่งไม่สำเร็จ, 90=ส่งสำเร็จ
        // ชื่อผู้จัดใช้ bills.recNameBill (join มา) ไม่ใช่ ChangedBy (ที่เป็น SYSTEM)
        $legacyPickers = $needFallback
            ? DB::connection(self::LEGACY_CONNECTION)
                ->table('BILL_STATUS_HISTORY as h')
                ->leftJoin('bills as b', 'b.BillNo', '=', 'h.BillNo')
                ->whereIn('h.BillNo', $needFallback)
                ->where('h.DeliveryStatus', '>=', '20')
                ->orderBy('h.ChangedDate')
                ->get(['h.BillNo', 'b.recNameBill', 'b.dateRecBill'])
                ->keyBy('BillNo')
            : collect();

        return $rows
            ->map(function ($row) use ($dnColumn, $legacyPickers) {
                $dnNo = ($dnColumn && !empty($row->{$dnColumn})) ? $row->{$dnColumn} : null;

                $pickedBy = $row->{self::TBLBILL_PICKER_COLUMN} ?? null;
                $pickedAt = $row->{self::TBLBILL_PICKER_TIME_COLUMN} ?? null;
                $isPicked = !empty($pickedBy); // ระบบใหม่: จัดสำเร็จเมื่อมี emp_picker

                // ระบบเก่า (fallback): จัดสำเร็จเมื่อเลขบิลอยู่ใน BILL_STATUS_HISTORY (DeliveryStatus>=20)
                // ชื่อผู้จัดใช้ bills.recNameBill (ไม่ใช่ UpdatedBy/ChangedBy)
                if (!$isPicked && $dnNo && $legacyPickers->has($dnNo)) {
                    $legacy   = $legacyPickers->get($dnNo);
                    $isPicked = true;
                    $pickedBy = $legacy->recNameBill ?: null; // ชื่อผู้จัดจากระบบเก่า
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
                    'picked'        => $isPicked,
                    'picked_by'     => $pickedBy,
                    'picked_at'     => $pickedAt,
                ];
            })
            ->groupBy('so_id');
    }

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
        $legacyHeads   = $this->buildLegacyPendingLocationRows($request); 

        $allHeads = $internalHeads->concat($externalHeads)->concat($legacyHeads);
        if ($poType = $request->input('po_type')) {
            $allHeads = $allHeads->filter(function ($h) use ($poType) {
                $hasA = str_contains((string) $h->po_display, 'A');
                return $poType === 'internal' ? $hasA : !$hasA;
            });
        }

        $allHeads = $allHeads->sort(function ($a, $b) {
            if ($a->todo !== $b->todo) return $a->todo ? -1 : 1;
            return strcmp((string) $b->po_display, (string) $a->po_display);
        })->values();
        $totalTodo = $allHeads->where('todo', true)->count();

        $perPage = self::LOCATION_PER_PAGE;
        $page    = max(1, (int) $request->input('page', 1));
        $heads   = new LengthAwarePaginator(
            $allHeads->forPage($page, $perPage)->values(),
            $allHeads->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // เพิ่มชื่อ Sale (so.createdBy จาก DB เก่า 3e) — ดึงเฉพาะรายการในหน้านี้
        $soIds = collect($heads->items())->pluck('so_id')->filter()->unique()->values()->all();
        $saleBySo = collect();
        if (!empty($soIds)) {
            $saleBySo = DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereIn('SONum', $soIds)
                ->get(['SONum', 'createdBy'])
                ->keyBy('SONum');
        }
        $heads->getCollection()->transform(function ($h) use ($saleBySo) {
            $h->sale = optional($saleBySo->get($h->so_id))->createdBy;
            return $h;
        });

        $locations = $this->recentLocations();

        return view('store.store_location', compact('heads', 'locations', 'creator', 'totalTodo'));
    }

    public function legacyItemsForPo(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            return response()->json(['ok' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ'], 403);
        }

        $request->validate(['po' => 'required|string']);

        // แก้บั๊ก "ดูสินค้า" หาสินค้าไม่เจอสำหรับรายการ type=external (ทั้งสถานะ "กำลังจัดการ" และ
        // "จัดการเสร็จสิ้น") — ปุ่ม "ดูสินค้า" ส่ง po_display มาตรงๆ ซึ่งสำหรับรายการ external จะเป็น
        // PoReceive.po_id ที่ถูกสร้างด้วย 'PO' . เลขPOดิบ เสมอ (ดู legacyClaim()/checkoutLegacyAndMigrate()/
        // migrateLegacyStoreToReceive()) เช่น "PO12345" แต่ fetchLegacyPoItemsBatch()->fetchMssqlPoItems()
        // คาดหวัง "เลข PO ดิบ" (ไม่มี prefix) เพื่อเอาไปต่อเป็น DocuNo = 'PO' + เลขPO เอง ถ้าส่ง "PO12345"
        // เข้าไปตรงๆ จะกลายเป็น 'PO' . 'PO12345' = "POPO12345" หาใน MSSQL ไม่เจอ แล้วก็หาไม่เจอต่อใน
        // fallback อื่นๆ ด้วย (เพราะยังส่ง PONum แบบมี prefix ผิดต่อไป)
        //
        // endpoint พี่น้องกัน itemsDetailBatch() จัดการเรื่องนี้ถูกต้องอยู่แล้วด้วย
        // $externalPoNumOf = fn ($poId) => preg_replace('/^PO/', '', $poId); — เพิ่ม logic เดียวกันที่นี่
        $po = preg_replace('/^PO/', '', $request->input('po'));

        $items = $this->fetchLegacyPoItemsBatch([$po])->get($po, collect());

        return response()->json([
            'ok'    => true,
            'items' => $items->map(fn ($it) => [
                'item_name'     => $it->item_name,
                'item_quantity' => $it->item_quantity,
            ])->values(),
        ]);
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

        $internalIds    = [];
        $externalPoIds  = [];
        $legacyStoreIds = [];
        foreach ($ids as $raw) {
            [$type, $id] = array_pad(explode(':', $raw, 2), 2, null);
            if ($id === null) { $internalIds[] = $raw; continue; }
            if ($type === 'internal') $internalIds[] = $id;
            if ($type === 'external') $externalPoIds[] = $id;
            if ($type === 'legacy')   $legacyStoreIds[] = $id;
        }

        try {
            $updated = DB::transaction(function () use ($internalIds, $externalPoIds, $legacyStoreIds, $authUser, $location) {
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
                if ($legacyStoreIds) {
                    $count += $this->migrateLegacyStoreToReceive($legacyStoreIds, $location, $authUser->name);
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

    private function loadLegacyStoreHeads(?string $soNum, ?array $soIds, ?string $poNum, ?array $restrictSoIds = null)
    {
        $migratedPoNums = $this->migratedLegacyPoNums();

        $q = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->leftJoin('area', 'area.ID', '=', 'store.Area')
            ->leftJoin('box',  'box.ID',  '=', 'store.BOX')
            ->select('store.*', 'area.areaName', 'box.boxName')
            ->whereIn('store.statusArea', ['0', '1'])
            ->whereNotNull('store.Area')
            ->where('store.Area', '<>', '')
            ->where('store.DATEAREA', '>=', self::LEGACY_STORE_MIN_DATE)
            ->when($migratedPoNums, fn ($q2) => $q2->whereNotIn('store.PO', $migratedPoNums));

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
            $q->whereIn('store.SO', $restrictSoIds);
        }

        return $q->orderBy('store.DATEAREA', 'asc')->get();
    }

    public function checkoutDashboard(Request $request)
    {
        $authUser = $this->resolveSsoUser($request, 'store.checkout');

        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        $creator  = $authUser->name;

        // ★ 1. รับค่า filter_status จาก request (ค่าเริ่มต้นเป็น 'pending')
        $filterStatus = $request->input('filter_status', 'pending');

        $hasSoOrPoSearch = $request->filled('SONum') || $request->filled('PONum');
        $isDefaultView   = !$hasSoOrPoSearch && !$request->filled('bill_date');

        if ($hasSoOrPoSearch) {
            $billDate = null;
        } elseif ($isDefaultView) {
            $billDate = Carbon::today()->toDateString();
        } else {
            $billDate = $request->input('bill_date');
        }

        $soNum = $request->input('SONum');
        $poNum = $request->input('PONum');

        $soIds = $this->soIdsByBillDate($billDate);

        $soSummaries = $this->buildSoSummaries($soIds, $soNum, $poNum, $billDate);
        
        // ★ 2. กรองข้อมูลตาม filter_status ที่เลือก
        if ($filterStatus === 'pending') {
            // เก็บเฉพาะรายการที่ยังจัดการไม่เสร็จ (all_done == false)
            $soSummaries = $soSummaries->filter(fn ($s) => !$s->all_done)->values();
        }
        // ถ้าเป็น 'all' จะข้ามขั้นตอนนี้ไป (แสดงทั้งหมด)

        // ★ 3. คำนวณสรุปยอดจากข้อมูลที่ "กรองแล้ว" (เพื่อให้ตัวเลขสถิติตรงกับตารางที่แสดง)
        $daySummary  = $this->buildDaySummary($soSummaries);

        $perPage = self::CHECKOUT_BILLS_PER_PAGE;
        $page    = max(1, (int) $request->input('page', 1));
        
        // ★ 4. ตัดหน้า (Pagination) จากข้อมูลที่กรองแล้ว
        $pagedSummaries = $soSummaries->forPage($page, $perPage)->values();

        // ═══ Phase B (หนัก): โหลดรายละเอียดสินค้า + ยิง legacy HTTP เฉพาะ SO ในหน้านี้เท่านั้น ═══
        $billsForPage = $this->buildBillCards($pagedSummaries, $soNum, $poNum);

        $bills = new LengthAwarePaginator(
            $billsForPage,
            $soSummaries->count(), // ★ ใช้ count ของข้อมูลที่กรองแล้ว
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

        $migratedPoNums = $this->migratedLegacyPoNums();

        $legacyLight = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->whereIn('statusArea', ['0', '1'])
            ->whereNotNull('Area')->where('Area', '<>', '')
            ->where('DATEAREA', '>=', self::LEGACY_STORE_MIN_DATE)
            ->when($migratedPoNums, fn ($q) => $q->whereNotIn('PO', $migratedPoNums))
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

    private function buildBillCards(\Illuminate\Support\Collection $pagedSummaries, ?string $soNum, ?string $poNum): \Illuminate\Support\Collection
    {
        $pageSoIds = $pagedSummaries->pluck('so_id')->values()->all();
        if (!$pageSoIds) return collect();

        $billsBySo = $pagedSummaries->keyBy('so_id')->map->bills;

        $internalHeadsRaw = internal_po::with('lines')
            ->whereIn('status', [internal_po::ST_STORED, internal_po::ST_CHECKOUT])
            ->whereIn('SO_id', $pageSoIds)
            ->when($soNum, fn ($q) => $q->where('SO_id', 'LIKE', '%' . $soNum . '%'))
            ->when($poNum, fn ($q) => $q->where('internal_id', 'LIKE', '%' . $poNum . '%'))
            ->get();

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
                    'checkout_by'   => $h->checkout_by,
                    'checkout_at'   => $h->checkout_at,
                    'status'        => $h->status,
                    'status_color'  => $h->status_color,
                    'todo'          => $h->status === internal_po::ST_STORED,
                ];
            });

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
                    'id'            => $h->po_id,
                    'po_display'    => $h->po_id,
                    'so_id'         => $h->so_id,
                    'customer_name' => null,
                    'items'         => $items->map(fn ($it) => (object) [
                        'item_name'     => $it->good_name,
                        'item_quantity' => $it->recv_qty,
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

        $legacyHeadsRaw = $this->loadLegacyStoreHeads($soNum, null, $poNum, $pageSoIds);

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
                    $h->checkout_at = $fallbackAt;
                }
            });
        }
        
        $groupedBySo = $internalHeads->concat($externalHeads)->concat($legacyHeads)->groupBy('so_id');

        return $pagedSummaries->map(function ($summary) use ($groupedBySo, $billsBySo) {
            $soId   = $summary->so_id;
            $groups = $groupedBySo->get($soId, collect());

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

        $pickedBills = 0;
        if ($dnNos) {
            try {
                $pickedBills = DB::table('tblbill')
                    ->whereIn(self::TBLBILL_DN_COLUMN, $dnNos)
                    ->whereNull(self::TBLBILL_PICKER_COLUMN)
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

        $poId = $request->input('PONum');
        $soId = $request->input('SONum');

        $receive = PoReceive::where('po_id', $poId)
            ->where('so_id', $soId)
            ->first();

        $lines = PoReceiveLine::where('po_id', $poId)->get();

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
            'status' => $receive->status ?? null,
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

                // ❌ ไม่เขียนทับ DB เก่า (3e) อีกต่อไป — ใช้ record ในระบบใหม่ (PoReceive.checkout_time)
                //    เป็นตัวมาร์คว่าเช็คเอาท์แล้ว และ dashboard กรอง PO ที่มีในระบบใหม่ออกอยู่แล้ว
                //    (whereNotIn('PO', $poInNewSystem)) จึงไม่มีทางโผล่ซ้ำ

                $updated++;
            }
        });

        Log::info("checkoutLegacyAndMigrate: user={$user} updated={$updated} ids=" . implode(',', $legacyRows->pluck('ID')->all()));

        return $updated;
    }

    private function migratedLegacyPoNums(): array
    {
        return PoReceive::pluck('po_id')
            ->filter(fn ($id) => str_starts_with($id, 'PO'))
            ->map(fn ($id) => substr($id, 2))
            ->unique()->values()->all();
    }

    private function buildLegacyPendingLocationRows(Request $request): \Illuminate\Support\Collection
    {
        if ($request->filled('location') || $request->filled('item')) {
            return collect();
        }

        // งานที่ "ยังไม่ขึ้นชั้น" = Area ว่าง (ยังไม่ระบุสถานที่เก็บ) + ยังไม่เช็คเอาท์ (DATECHECKOUT ว่าง)
        // ถ้า Area มีค่า = ของขึ้นชั้นแล้ว (แปลชื่อชั้นได้จาก table area) → ไม่ดึงมาแสดงในงานค้าง
        $rows = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->select('store.ID', 'store.PO', 'store.SO', 'store.DATEBOX', 'store.boxS')
            ->where(function ($q) {
                $q->whereNull('store.Area')->orWhere('store.Area', '');
            })
            ->where(function ($q) {
                $q->whereNull('store.DATECHECKOUT')->orWhere('store.DATECHECKOUT', '');
            })
            ->whereNotNull('store.PO')->where('store.PO', '<>', '')
            ->whereNotNull('store.SO')->where('store.SO', '<>', '')
            ->when($request->filled('PONum'), fn ($q) => $q->where('store.PO', 'LIKE', '%' . $request->input('PONum') . '%'))
            ->when($request->filled('SONum'), fn ($q) => $q->where('store.SO', 'LIKE', '%' . $request->input('SONum') . '%'))
            ->get();

        if ($rows->isEmpty()) return collect();

        $poIdsCandidate  = $rows->map(fn ($r) => 'PO' . $r->PO)->unique()->values()->all();
        $alreadyMigrated = PoReceive::whereIn('po_id', $poIdsCandidate)->pluck('po_id')->flip();
        $rows = $rows->reject(fn ($r) => $alreadyMigrated->has('PO' . $r->PO))->values();
        if ($rows->isEmpty()) return collect();
        $rows = $rows->unique('PO')->values();

        $soIdsAll = $rows->pluck('SO')->unique()->values()->all();

        $custBySoId = DB::connection(self::LEGACY_CONNECTION)->table('so')
            ->whereIn('SONum', $soIdsAll)
            ->get(['SONum', 'CustName'])
            ->keyBy('SONum');

        $missingSoIds = collect($soIdsAll)
            ->reject(fn ($soId) => filled(optional($custBySoId->get($soId))->CustName))
            ->values()->all();

        $custBySoIdFallback = $missingSoIds
            ? DB::table('tblbill')
                ->whereIn('so_id', $missingSoIds)
                ->get(['so_id', self::TBLBILL_CUSTOMER_COLUMN])
                ->keyBy('so_id')
            : collect();

        $resolveCustomerName = function ($soId) use ($custBySoId, $custBySoIdFallback) {
            $name = optional($custBySoId->get($soId))->CustName;
            if (filled($name)) return $name;
            return optional($custBySoIdFallback->get($soId))->{self::TBLBILL_CUSTOMER_COLUMN};
        };

        if ($request->filled('customer')) {
            $needle = mb_strtolower($request->input('customer'));
            $rows = $rows->filter(function ($r) use ($resolveCustomerName, $needle) {
                $name = $resolveCustomerName($r->SO);
                return $name && str_contains(mb_strtolower($name), $needle);
            })->values();
            if ($rows->isEmpty()) return collect();
        }

        return $rows->map(function ($r) use ($resolveCustomerName) {
            return (object) [
                'type'          => 'legacy',
                'id'            => $r->ID,
                'po_display'    => $r->PO,
                'so_id'         => $r->SO,
                'customer_name' => $resolveCustomerName($r->SO),
                'items'         => null,
                'total_qty'     => null,
                'location'      => null,
                'packed_by'     => $r->boxS ?: null,
                'packed_at'     => $r->DATEBOX,
                'todo'          => true,
                'claimed'       => false,
                'claimed_by'    => null,
                'claimed_at'    => null,
                'finished'      => false,
                'finished_by'   => null,
                'finished_at'   => null,
            ];
        })->values();
    }

    private function migrateLegacyStoreToReceive(array $legacyStoreIds, string $location, string $user): int
    {
        if (!$legacyStoreIds) return 0;

        $legacyRows = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->whereIn('ID', $legacyStoreIds)
            // ใช้เงื่อนไขเดียวกับ buildLegacyPendingLocationRows() คือ "ยังไม่ขึ้นชั้น
            // (Area ว่าง) + ยังไม่เช็คเอาท์ (DATECHECKOUT ว่าง)" — Area มีค่า = ขึ้นชั้นแล้ว
            ->where(function ($q) {
                $q->whereNull('Area')->orWhere('Area', '');
            })
            ->where(function ($q) {
                $q->whereNull('DATECHECKOUT')->orWhere('DATECHECKOUT', '');
            })
            ->get(['ID', 'PO', 'SO']);

        if ($legacyRows->isEmpty()) return 0;

        $poNums    = $legacyRows->pluck('PO')->filter()->unique()->values()->all();
        $itemsByPo = $this->fetchLegacyPoItemsBatch($poNums);

        $soIds = $legacyRows->pluck('SO')->unique()->values()->all();
        $billMetaBySoId = DB::table('tblbill')
            ->whereIn('so_id', $soIds)
            ->orderBy('time')
            ->get(['so_id', self::TBLBILL_POREF_COLUMN, self::TBLBILL_CUSTOMER_COLUMN])
            ->groupBy('so_id')
            ->map(fn ($rows) => $rows->last());

        $now     = Carbon::now();
        $updated = 0;

        DB::transaction(function () use ($legacyRows, $itemsByPo, $billMetaBySoId, $location, $user, $now, &$updated) {
            foreach ($legacyRows as $row) {
                $poId = 'PO' . $row->PO;

                if (PoReceive::where('po_id', $poId)->exists()) continue;

                $meta = $billMetaBySoId->get($row->SO);

                $receive = PoReceive::create([
                    'po_id'     => $poId,
                    'so_id'     => $row->SO,
                    'status'    => 'ครบ',
                    'POref'     => $meta->{self::TBLBILL_POREF_COLUMN} ?? null,
                    'cust_name' => $meta->{self::TBLBILL_CUSTOMER_COLUMN} ?? null,
                ]);

                $lines = $itemsByPo->get($row->PO, collect());
                if ($lines->isEmpty()) {
                    $lines = collect([(object) ['item_name' => '—', 'item_quantity' => 1]]);
                }

                foreach ($lines as $line) {
                    PoReceiveLine::create([
                        'po_id'       => $poId,
                        'good_name'   => $line->item_name,
                        'recv_qty'    => $line->item_quantity,
                        'unit_price'  => null,
                        'shelf'       => $location,
                        'photo_path'  => null,
                        'received_by' => $user,
                        'received_at' => $now,
                    ]);
                }

                $updated++;
            }
        });

        return $updated;
    }

    public function legacyClaim(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        if (!in_array($authUser->role, ['admin', 'stock', 'store'], true)) {
            return response()->json(['ok' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ'], 403);
        }

        $request->validate(['store_id' => 'required']);
        $storeId = $request->input('store_id');

        try {
            $result = DB::transaction(function () use ($storeId, $authUser) {
                $row = DB::connection(self::LEGACY_CONNECTION)->table('store')
                    ->where('ID', $storeId)
                    // ตรงกับเงื่อนไข "งานที่ยังไม่ขึ้นชั้น (Area ว่าง) + ยังไม่เช็คเอาท์"
                    // เหมือนกับที่ใช้ตอนดึงรายการมาแสดง (buildLegacyPendingLocationRows)
                    ->where(function ($q) {
                        $q->whereNull('Area')->orWhere('Area', '');
                    })
                    ->where(function ($q) {
                        $q->whereNull('DATECHECKOUT')->orWhere('DATECHECKOUT', '');
                    })
                    ->lockForUpdate()
                    ->first(['ID', 'PO', 'SO']);

                if (!$row) {
                    return ['ok' => false, 'message' => 'ไม่พบรายการนี้ หรือถูกเปลี่ยนสถานะไปแล้ว'];
                }

                $poId = 'PO' . $row->PO;

                if (PoReceive::where('po_id', $poId)->exists()) {
                    return ['ok' => false, 'message' => 'PO นี้มีคนกำลังจัดการอยู่แล้ว หรือถูกย้ายไปแล้ว'];
                }

                $itemsByPo = $this->fetchLegacyPoItemsBatch([$row->PO]);
                $lines     = $itemsByPo->get($row->PO, collect());
                if ($lines->isEmpty()) {
                    $lines = collect([(object) ['item_name' => '—', 'item_quantity' => 1]]);
                }

                $soRow    = DB::connection(self::LEGACY_CONNECTION)->table('so')
                    ->where('SONum', $row->SO)->first(['CustName']);
                $custName = filled(optional($soRow)->CustName) ? $soRow->CustName : null;

                $billMeta = DB::table('tblbill')
                    ->where('so_id', $row->SO)
                    ->orderBy('time', 'desc')
                    ->first(['so_id', self::TBLBILL_POREF_COLUMN, self::TBLBILL_CUSTOMER_COLUMN]);

                if (!$custName && $billMeta) {
                    $custName = $billMeta->{self::TBLBILL_CUSTOMER_COLUMN};
                }

                PoReceive::create([
                    'po_id'     => $poId,
                    'so_id'     => $row->SO,
                    'status'    => 'ครบ',
                    'POref'     => $billMeta->{self::TBLBILL_POREF_COLUMN} ?? null,
                    'cust_name' => $custName,
                ]);

                $now = Carbon::now();
                foreach ($lines as $line) {
                    PoReceiveLine::create([
                        'po_id'       => $poId,
                        'good_name'   => $line->item_name,
                        'recv_qty'    => $line->item_quantity,
                        'unit_price'  => null,
                        'shelf'       => null,
                        'photo_path'  => null,
                        'received_by' => $authUser->name,
                        'received_at' => $now,
                    ]);
                }

                PoReceiveLine::where('po_id', $poId)
                    ->update(['do_it' => $authUser->name, 'do_it_time' => $now]);

                return ['ok' => true, 'message' => 'เริ่มจัดการงานแล้ว'];
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }

        return response()->json($result, $result['ok'] ? 200 : 409);
    }
}