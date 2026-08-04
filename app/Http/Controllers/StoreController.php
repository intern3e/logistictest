<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use App\Models\internal_poline;
use App\Models\PoReceive;
use App\Models\PoReceiveLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    const ALLOWED_USERS = ['test101'];

    /** ชื่อ connection ของฐานข้อมูลระบบเก่า (ตาราง store / area / box) — ดู config/database.php */
    const LEGACY_CONNECTION = 'mysql_3e';
    const LEGACY_PO_DETAIL_URL = 'http://server_update:8000/api/getPODetail';
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
 * ดึงรายละเอียด PO จากระบบเก่า (getPODetail) แบบ concurrent แต่จำกัดจำนวนพร้อมกันต่อ batch
 * (เซิร์ฟเวอร์ปลายทางรับ connection พร้อมกันเยอะๆ ไม่ไหว จะ reset connection ถ้ายิงทีเดียว 100+ ตัว)
 */
private function fetchLegacyPoItemsBatch(array $poNums): \Illuminate\Support\Collection
{
    $poNums = collect($poNums)->filter()->unique()->values();
    if ($poNums->isEmpty()) return collect();

    $concurrency = 30; // ← ปรับจาก 10 เป็น 30 ตามที่ขอ

    $data   = collect();
    $failed = collect();

    foreach ($poNums->chunk($concurrency) as $chunk) {
        [$chunkData, $chunkFailed] = $this->fetchLegacyPoItemsChunk($chunk->values()->all());
        $data   = $data->merge($chunkData);
        $failed = $failed->merge($chunkFailed);
    }

    if ($failed->isNotEmpty()) {
        usleep(300000);
        foreach ($failed->chunk(10) as $chunk) { // retry รอบสองใช้ concurrency ต่ำกว่า (10) กันซ้ำ error
            [$chunkData, $stillFailed] = $this->fetchLegacyPoItemsChunk($chunk->values()->all());
            $data = $data->merge($chunkData);
            if ($stillFailed->isNotEmpty()) {
                Log::warning('fetchLegacyPoItemsBatch: ล้มเหลวแม้ retry แล้ว: ' . $stillFailed->implode(', '));
            }
        }
    }

    $poNums->each(function ($num) use ($data) {
        if (!$data->has($num)) $data->put($num, collect());
    });

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
    /** จำนวนใบต่อหน้าของหน้าของออก (ด่าน 3) */
    const CHECKOUT_PER_PAGE = 100;

    /** จำนวนใบต่อหน้าของหน้าระบุตำแหน่ง (ด่าน 2) */
    const LOCATION_PER_PAGE = 100;

    /**
     * ชื่อคอลัมน์เก็บ "ชื่อลูกค้า" ในตาราง tblbill (database logistic)
     * ใช้สำหรับดึงชื่อลูกค้าของรายการ ภายนอก/ระบบเก่า ที่ตัวตารางเองไม่ได้เก็บชื่อลูกค้าไว้
     * (รายการ "ภายใน" ดึงชื่อลูกค้าจาก internal_po.customer_name ตรงๆ อยู่แล้ว ไม่เกี่ยวกับตรงนี้)
     */
    const TBLBILL_CUSTOMER_COLUMN = 'customer_name';

    private function allowed(?string $user): bool
    {
        return in_array($user, self::ALLOWED_USERS, true);
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
     * ใช้คอลัมน์ time (DATETIME, created) เป็นตัวแทนวันที่เปิด SO
     * ⚠️ ถ้าความหมายจริงต้องใช้คอลัมน์อื่น (เช่น date_of_dali) เปลี่ยนตรงนี้ที่เดียว
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
     * ดึงชื่อลูกค้าของ SO ที่ระบุ จากตาราง tblbill (database logistic) — ใช้เติมชื่อลูกค้าให้ทุกรายการ
     * (ภายใน/ภายนอก/ระบบเก่า) โดยหาแทนด้วยเลข SO ทั้งหมด แทนที่จะดึงจากคอลัมน์ customer_name ของแต่ละแหล่งเอง
     * คืนค่า array แบบ [so_id => ชื่อลูกค้า]
     */
    private function customerNamesBySo(array $soIds): array
    {
        if (!$soIds) return [];

        return DB::table('tblbill')
            ->whereIn('so_id', $soIds)
            ->whereNotNull(self::TBLBILL_CUSTOMER_COLUMN)
            ->where(self::TBLBILL_CUSTOMER_COLUMN, '<>', '')
            ->pluck(self::TBLBILL_CUSTOMER_COLUMN, 'so_id')
            ->toArray();
    }

    /* ==================== ด่าน 2: ระบุตำแหน่ง (เฉพาะภายใน — ภายนอกระบุชั้นวางตอนรับเข้าแล้ว) ==================== */
    public function locationDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $todoStatus = internal_po::ST_FINISH;
        $statuses   = [
            internal_po::ST_FINISH,
            internal_po::ST_STORED,
            internal_po::ST_CHECKOUT,
        ];

        $query = $this->buildLocationQuery($request, $statuses);

        // นับจำนวน "ยังไม่ได้จัดตำแหน่ง" จากผลลัพธ์ทั้งหมดที่ตรงกับ filter (ก่อนตัดหน้า)
        $totalTodo = (clone $query)->where('status', $todoStatus)->count();

        $heads = $query
            ->orderByRaw('FIELD(status, ?) DESC', [$todoStatus])
            ->orderBy('internal_id')
            ->paginate(self::LOCATION_PER_PAGE)
            ->withQueryString();

        $locations = $this->recentLocations();

        return view('store.store_location', compact('heads', 'locations', 'creator', 'totalTodo'));
    }

    public function locationSubmit(Request $request)
    {
        $request->validate([
            'ids'      => 'required|array|min:1',
            'ids.*'    => 'string',
            'user'     => 'required|string|max:100',
            'location' => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $ids      = $request->input('ids');
        $location = $request->input('location');

        try {
            $updated = DB::transaction(function () use ($ids, $user, $location) {
                return internal_po::whereIn('internal_id', $ids)
                    ->where('status', internal_po::ST_FINISH)
                    ->update([
                        'status'      => internal_po::ST_STORED,
                        'location_by' => $user,
                        'location'    => $location,
                        'location_at' => Carbon::now()->toDateTimeString(),
                    ]);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ระบุตำแหน่งไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ระบุตำแหน่ง ' . $updated . ' ใบ']);
    }

    /* ==================== ระบบเก่า: ตาราง store (database "3e") ==================== */
    /**
     * ดึงรายการที่ "มีตำแหน่งแล้ว แต่ยังไม่ได้ checkout"
     * เงื่อนไข: statusArea = '1' (ยังอยู่ในสต็อก) และ Area ถูกระบุแล้ว (ไม่ว่าง)
     */
    private function loadLegacyStoreHeads(Request $request, ?array $soIds, ?string $poNum)
    {
        $q = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->leftJoin('area', 'area.ID', '=', 'store.Area')
            ->leftJoin('box',  'box.ID',  '=', 'store.BOX')
            ->select('store.*', 'area.areaName', 'box.boxName')
            ->whereIn('store.statusArea', ['0', '1']) // ดึงทั้ง "รอเอาออก" (1) และ "เอาออกแล้ว" (0)
            ->whereNotNull('store.Area')
            ->where('store.Area', '<>', '');

        if ($request->filled('SONum')) {
            $q->where('store.SO', 'LIKE', '%' . $request->input('SONum') . '%');
        }
        if ($poNum) {
            $q->where('store.PO', 'LIKE', '%' . $poNum . '%');
        }
        if ($soIds !== null) {
            $q->whereIn('store.SO', $soIds);
        }

        return $q->orderBy('store.DATEAREA', 'asc')->get();
    }
    /* ==================== ด่าน 3: ของออก (รวม internal_po + PoReceive + store (ระบบเก่า)) ====================
     * หมายเหตุสำคัญ: หน้านี้ "ไม่โหลดข้อมูลอัตโนมัติ" ตอนเข้าหน้าแรก
     * ผู้ใช้ต้องระบุเงื่อนไขค้นหาอย่างน้อยหนึ่งอย่างก่อน (วันที่เปิด SO หรือเลข SO หรือเลข PO)
     * ถึงจะยิง query จริง — ป้องกันไม่ให้ query ทั้ง 3 แหล่งข้อมูลพร้อมกันโดยไม่จำเป็น
     */
    public function checkoutDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $billDate = $request->input('bill_date'); // format YYYY-MM-DD จาก <input type="date">
        $poNum    = $request->input('PONum');

        // ต้องมีเงื่อนไขค้นหาอย่างน้อย 1 อย่าง: วันที่เปิด SO / เลข SO / เลข PO
        $searched = $request->filled('SONum') || $request->filled('PONum') || $request->filled('bill_date');

        if (!$searched) {
            $heads = new LengthAwarePaginator(
                collect(),
                0,
                self::CHECKOUT_PER_PAGE,
                1,
                [
                    'path'  => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('store.store_checkout', [
                'heads'     => $heads,
                'creator'   => $creator,
                'totalTodo' => 0,
                'total'     => 0,
                'billDate'  => $billDate,
                'searched'  => false,
            ]);
        }

        $soIds = $this->soIdsByBillDate($billDate); // null = ไม่กรอง, array = กรองตาม tblbill

        // ── ภายใน: internal_po ที่ระบุตำแหน่งแล้ว (ST_STORED) หรือเอาของออกแล้ว (ST_CHECKOUT) ──
        $internalHeads = internal_po::with('lines')
            ->whereIn('status', [internal_po::ST_STORED, internal_po::ST_CHECKOUT])
            ->when($request->filled('SONum'), function ($q) use ($request) {
                $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
            })
            ->when($poNum, function ($q) use ($poNum) {
                $q->where('internal_id', 'LIKE', '%' . $poNum . '%');
            })
            ->when($soIds !== null, function ($q) use ($soIds) {
                $q->whereIn('SO_id', $soIds);
            })
            ->get()
            ->map(function ($h) {
                $items = $h->lines;
                return (object) [
                    'type'          => 'internal',
                    'id'            => $h->internal_id,      // ใช้เป็น key ส่งกลับตอนกด "ของออก"
                    'po_display'    => $h->internal_id,      // เลข PO ที่แสดงในคอลัมน์ "PO ภายใน"
                    'so_id'         => $h->SO_id,
                    'customer_name' => null, // ดึงชื่อลูกค้าจาก tblbill ผ่านเลข SO เหมือนกันทุกแหล่งข้อมูล (ดูด้านล่าง)
                    'items'         => $items->map(fn ($it) => (object) [
                        'item_name'     => $it->item_name,
                        'item_quantity' => $it->item_quantity,
                    ]),
                    'location'      => $h->location,
                    'done_by'       => $h->location_by,
                    'done_at'       => $h->location_at,
                    'status'        => $h->status,
                    'status_color'  => $h->status_color,
                    'todo'          => $h->status === internal_po::ST_STORED,
                ];
            });

        // ── ภายนอก: PoReceive (รับเข้าจาก mobile) ──
        $externalHeads = PoReceive::with('lines')
            ->when($request->filled('SONum'), function ($q) use ($request) {
                $q->where('so_id', 'LIKE', '%' . $request->input('SONum') . '%');
            })
            ->when($poNum, function ($q) use ($poNum) {
                $q->where('po_id', 'LIKE', '%' . $poNum . '%');
            })
            ->when($soIds !== null, function ($q) use ($soIds) {
                $q->whereIn('so_id', $soIds);
            })
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
                    'customer_name' => null, // po_receives ไม่มีคอลัมน์ชื่อลูกค้า ดึงจาก tblbill ผ่าน so_id แทน (ดูด้านล่าง)
                    'items'         => $items->map(fn ($it) => (object) [
                        'item_name'     => $it->good_name,
                        'item_quantity' => $it->recv_qty,
                    ]),
                    'location'      => optional($first)->shelf,
                    'done_by'       => optional($first)->received_by,
                    'done_at'       => optional($first)->received_at,
                    'status'        => $todo ? 'รับเข้าแล้ว (รอของออก)' : 'เอาของออกแล้ว',
                    'status_color'  => $todo ? 'orange' : 'green',
                    'todo'          => $todo,
                ];
            });

        // ── ระบบเก่า: store (database "3e") — มีตำแหน่งแล้ว ยังไม่ checkout ──
        $legacyHeads = $this->loadLegacyStoreHeads($request, $soIds, $poNum)
            ->map(function ($h) {
                $todo = $h->statusArea === '1';
                return (object) [
                    'type'          => 'legacy',
                    'id'            => $h->ID,
                    'po_display'    => $h->PO ?: '—',
                    'so_id'         => $h->SO,
                    'customer_name' => null,
                    'items'         => collect(), // ดึงทีหลังผ่าน apis.store.legacyPoItemsBatch
                    'location'      => $todo ? $h->areaName : ($h->areaS ?: $h->areaName),
                    'done_by'       => $todo ? ($h->boxName ?: '—') : ($h->boxS ?: '—'),
                    'done_at'       => $todo ? $h->DATEAREA : $h->DATECHECKOUT,
                    'status'        => $todo ? 'รอเอาออก (ระบบเก่า)' : 'เอาของออกแล้ว (ระบบเก่า)',
                    'status_color'  => $todo ? 'orange' : 'green',
                    'todo'          => $todo,
                ];
            });

        $allHeads = $internalHeads->concat($externalHeads)->concat($legacyHeads);

        // ── เติมชื่อลูกค้าให้ทุกรายการ โดยดึงจาก tblbill ผ่านเลข SO (ใช้เลข SO หาแทนทุกแหล่งข้อมูล) ──
        $missingSoIds = $allHeads
            ->filter(fn ($h) => empty($h->customer_name) && !empty($h->so_id))
            ->pluck('so_id')
            ->unique()
            ->values()
            ->all();

        if ($missingSoIds) {
            $customerBySo = $this->customerNamesBySo($missingSoIds);
            $allHeads->each(function ($h) use ($customerBySo) {
                if (empty($h->customer_name) && !empty($customerBySo[$h->so_id] ?? null)) {
                    $h->customer_name = $customerBySo[$h->so_id];
                }
            });
        }

        // ── เรียงตาม SO ก่อน (ให้ใบที่ SO เดียวกันอยู่ติดกัน) แล้วค่อยเรียงรอเอาออกไว้บน ──
        $allHeads = $allHeads->sort(function ($a, $b) {
            if ($a->todo !== $b->todo) {
                return $a->todo ? -1 : 1; // todo = true มาก่อน
            }
            return strcmp((string) $a->so_id, (string) $b->so_id);
        })->values();

        // ── สรุปยอดจากผลลัพธ์ทั้งหมดที่กรองแล้ว (ก่อนตัดหน้า) ──
        $totalTodo = $allHeads->where('todo', true)->count();
        $total     = $allHeads->count();

        // ── ตัดหน้า (pagination) หน้าละ 100 ใบ ──
        $perPage = self::CHECKOUT_PER_PAGE;
        $page    = max(1, (int) $request->input('page', 1));
        $heads   = new LengthAwarePaginator(
            $allHeads->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('store.store_checkout', compact(
            'heads', 'creator', 'totalTodo', 'total', 'billDate', 'searched'
        ));
    }

    public function checkoutSubmit(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'string',
            'user'  => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        // แยก id ตามชนิด: "internal:6907-A0001" / "external:PO123" / "legacy:123"
        $internalIds = [];
        $externalIds = [];
        $legacyIds   = [];
        foreach ($request->input('ids') as $raw) {
            [$type, $id] = array_pad(explode(':', $raw, 2), 2, null);
            if ($id === null) continue;
            if ($type === 'internal') $internalIds[] = $id;
            if ($type === 'external') $externalIds[] = $id;
            if ($type === 'legacy')   $legacyIds[]   = $id;
        }

        if (!$internalIds && !$externalIds && !$legacyIds) {
            return response()->json(['ok' => false, 'message' => 'รายการที่เลือกไม่ถูกต้อง'], 422);
        }

        $updated = 0;

        try {
            // ── ธุรกรรมของฐานข้อมูลใหม่ (logistic): internal_po + PoReceive ──
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

            // ── ธุรกรรมแยกต่างหากของฐานข้อมูลเก่า (3e): store ──
            // แยก transaction เพราะคนละ connection/คนละ database กับด้านบน
            if ($legacyIds) {
                DB::connection(self::LEGACY_CONNECTION)->transaction(function () use ($legacyIds, &$updated) {
                    $conn = DB::connection(self::LEGACY_CONNECTION);
                    $now  = Carbon::now()->toDateTimeString();

                    $rows = $conn->table('store')
                        ->leftJoin('area', 'area.ID', '=', 'store.Area')
                        ->leftJoin('box',  'box.ID',  '=', 'store.BOX')
                        ->select('store.ID', 'area.areaName', 'box.boxName')
                        ->whereIn('store.ID', $legacyIds)
                        ->where('store.statusArea', '1')
                        ->get();

                    foreach ($rows as $r) {
                        $updated += $conn->table('store')
                            ->where('ID', $r->ID)
                            ->where('statusArea', '1')
                            ->update([
                                'statusArea'   => '0',
                                'DATECHECKOUT' => $now,
                                'boxS'         => $r->boxName,
                                'areaS'        => $r->areaName,
                            ]);
                    }
                });
            }
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ของออกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ของออก ' . $updated . ' ใบ']);
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
}