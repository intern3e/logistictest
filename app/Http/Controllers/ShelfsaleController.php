<?php

namespace App\Http\Controllers;

use App\Models\PoReceive;
use App\Models\PoReceiveLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShelfsaleController extends Controller
{
    /** connection ฐานข้อมูลระบบเก่า 3e (อ่านอย่างเดียว) */
    const LEGACY_CONNECTION = 'mysql_3e';
    /** connection MSSQL account03 (อ่านอย่างเดียว) — "ดึงท้ายสุด" หลังคัดกรองแล้วเท่านั้น */
    const MSSQL_CONNECTION  = 'mssql_account03';

    /**
     * หน้า /shelfsale — โหลดแค่ตัวกรอง (ไม่ดึงข้อมูลตอนเปิดหน้า)
     * ข้อมูลจะแสดงก็ต่อเมื่อผู้ใช้เลือกตัวกรองแล้วกดค้นหา (เรียก data())
     */
    public function index()
    {
        $user    = $this->requireLogin();
        $creator = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');

        // dropdown Sale — cache 30 นาที กัน groupBy เต็มตาราง so ทุกครั้งที่เปิดหน้า
        $saleOptions = Cache::remember('shelfsale_sale_options', 1800, function () {
            return DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereNotNull('createdBy')->where('createdBy', '!=', '')
                ->groupBy('createdBy')
                ->pluck('createdBy')
                ->sort()->values();
        });

        return view('sale.dashboardshelf', compact('saleOptions', 'creator'));
    }

    /**
     * ดึงข้อมูลตามตัวกรอง (AJAX) — ลำดับการดึง: ถูก -> แพง
     *   1) ของบนชั้นจาก logistic (po_receives_line) + 3e (store)  -> กรองด้วย shelf/po/วันที่ ใน SQL
     *   2) ข้อมูลลูกค้า/Sale จาก 3e so (whereIn)                    -> กรองด้วย customer/sale
     *   3) (ท้ายสุด) MSSQL account03: ราคา(SumGoodAmnt), กำหนดส่ง(ShipDate), ชื่อสินค้าเก่า(PODT)
     *      เฉพาะแถวที่ "ผ่านตัวกรองแล้ว" เท่านั้น เพื่อให้แตะ account03 น้อยที่สุด
     */
    public function data(Request $request)
    {
        $this->requireLogin();

        $fShelf   = trim((string) $request->input('shelf', ''));
        $fSale    = trim((string) $request->input('sale', ''));
        $fSo      = trim((string) $request->input('so', ''));
        $fPo      = trim((string) $request->input('po', ''));
        $fCust    = trim((string) $request->input('customer', ''));
        $fFrom    = $request->input('date_from');
        $fTo      = $request->input('date_to');
        $fOverdue = $request->input('overdue') == '1';

        // ต้องเลือกตัวกรองอย่างน้อย 1 อย่าง ถึงจะดึง (กันโหลดทั้งหมด)
        $hasFilter = $fShelf !== '' || $fSale !== '' || $fSo !== '' || $fPo !== ''
            || $fCust !== '' || filled($fFrom) || filled($fTo) || $fOverdue;
        if (!$hasFilter) {
            return response()->json([
                'ok'      => true,
                'rows'    => [],
                'message' => 'กรุณาเลือกตัวกรองอย่างน้อย 1 อย่างก่อนค้นหา',
            ]);
        }

        // ---------- 1) ของใหม่: logistic po_receives_line ----------
        $newQ = PoReceiveLine::whereNotNull('shelf')
            ->where('shelf', '!=', '')
            ->whereHas('header', fn ($q) => $q->whereNull('checkout_time'))
            ->with('header');
        if ($fShelf !== '') $newQ->where('shelf', 'LIKE', "%{$fShelf}%");
        if ($fPo !== '')    $newQ->where('po_id', 'LIKE', "%{$fPo}%");
        if (filled($fFrom)) $newQ->where('received_at', '>=', $fFrom . ' 00:00:00');
        if (filled($fTo))   $newQ->where('received_at', '<=', $fTo . ' 23:59:59');

        $newItems = $newQ->get()->map(fn ($line) => (object) [
            'so'          => optional($line->header)->so_id,
            'po'          => optional($line->header)->po_id,
            'shelf'       => $line->shelf,
            'received_at' => $line->received_at,
            'good_name'   => $line->good_name,
            'is_legacy'   => false,
        ]);

        // ---------- 2) ของเก่า: 3e store (กรอง po ระบบใหม่ใน PHP ไม่ใช้ NOT IN) ----------
        $poInNewSet = array_flip(
            PoReceive::pluck('po_id')->filter()
                ->map(fn ($p) => preg_replace('/^PO/i', '', (string) $p))
                ->all()
        );

        $legQ = DB::connection(self::LEGACY_CONNECTION)->table('store')
            ->where(function ($q) {
                $q->whereNull('DATECHECKOUT')->orWhere('DATECHECKOUT', '');
            })
            ->whereNotNull('areaS')->where('areaS', '!=', '');
        if ($fShelf !== '') $legQ->where('areaS', 'LIKE', "%{$fShelf}%");
        if ($fPo !== '')    $legQ->where('PO', 'LIKE', "%{$fPo}%");
        if (filled($fFrom)) $legQ->where('DATEAREA', '>=', $fFrom . ' 00:00:00');
        if (filled($fTo))   $legQ->where('DATEAREA', '<=', $fTo . ' 23:59:59');

        $legacyItems = $legQ->get(['SO', 'PO', 'areaS', 'DATEAREA'])
            ->reject(fn ($row) => isset($poInNewSet[preg_replace('/^PO/i', '', (string) $row->PO)]))
            ->map(fn ($row) => (object) [
                'so'          => $row->SO,
                'po'          => $row->PO,
                'shelf'       => $row->areaS,
                'received_at' => filled($row->DATEAREA) ? Carbon::parse($row->DATEAREA) : null,
                'good_name'   => null,      // ของเก่าไม่มีชื่อสินค้า -> ไปดึงจาก PODT ตอนท้าย
                'is_legacy'   => true,
            ]);

        $items = $newItems->concat($legacyItems)->sortByDesc('received_at')->values();

        // กรองด้วยเลข SO (มีในมือแล้ว)
        if ($fSo !== '') {
            $items = $items->filter(fn ($it) => stripos((string) $it->so, $fSo) !== false)->values();
        }

        if ($items->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        // ---------- 2.5) ลูกค้า/Sale จาก 3e so ----------
        $soNums = $items->pluck('so')->filter()->unique()->values()->all();
        $soInfo = collect();
        if (!empty($soNums)) {
            $soInfo = DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereIn('SONum', $soNums)
                ->get(['SONum', 'CustID', 'CustName', 'createdBy'])
                ->keyBy('SONum');
        }

        $now = Carbon::now();
        $items = $items->map(function ($it) use ($soInfo, $now) {
            $so = $soInfo->get($it->so);
            $it->cust_id   = optional($so)->CustID;
            $it->cust_name = optional($so)->CustName;
            $it->sale      = optional($so)->createdBy;
            $it->days_in   = $it->received_at
                ? (int) $it->received_at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay())
                : null;
            return $it;
        });

        // กรองด้วยลูกค้า / Sale / งานค้าง (หลังได้ข้อมูล so แล้ว)
        if ($fCust !== '') {
            $items = $items->filter(function ($it) use ($fCust) {
                return stripos((string) $it->cust_name, $fCust) !== false
                    || stripos((string) $it->cust_id, $fCust) !== false;
            })->values();
        }
        if ($fSale !== '') {
            $items = $items->filter(fn ($it) => stripos((string) $it->sale, $fSale) !== false)->values();
        }
        if ($fOverdue) {
            $items = $items->filter(fn ($it) => $it->days_in !== null && $it->days_in >= 4)->values();
        }

        if ($items->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        // ---------- 3) ท้ายสุด: MSSQL account03 เฉพาะแถวที่ผ่านตัวกรองแล้ว ----------
        $priceByDocu = collect();   // ราคา รวมต่อ PO
        $shipByDocu  = collect();   // กำหนดส่งต่อ SO
        $namesByPo   = [];          // ชื่อสินค้าเก่าต่อ PO (legacy)

        try {
            // 3.1 ราคา (POHD.SumGoodAmnt) — DocuNo = 'PO' + เลข PO
            $poDocuNos = $items->pluck('po')->filter()
                ->map(fn ($p) => 'PO' . preg_replace('/^PO/i', '', (string) $p))
                ->unique()->values()->all();
            if (!empty($poDocuNos)) {
                $priceByDocu = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                    ->whereIn('DocuNo', $poDocuNos)
                    ->get(['DocuNo', 'SumGoodAmnt'])
                    ->keyBy('DocuNo');
            }

            // 3.2 กำหนดส่ง (SOHD.ShipDate) — DocuNo = 'SO' + เลข SO
            $soDocuNos = array_map(fn ($s) => 'SO' . $s, $soNums);
            if (!empty($soDocuNos)) {
                $shipByDocu = DB::connection(self::MSSQL_CONNECTION)->table('SOHD')
                    ->whereIn('DocuNo', $soDocuNos)
                    ->get(['DocuNo', 'ShipDate'])
                    ->keyBy('DocuNo');
            }

            // 3.3 ชื่อสินค้าของงานเก่า (PODT) — เฉพาะ PO ที่เป็น legacy และผ่านตัวกรอง
            $legacyDocus = $items->filter(fn ($it) => $it->is_legacy)
                ->pluck('po')->filter()
                ->map(fn ($p) => 'PO' . preg_replace('/^PO/i', '', (string) $p))
                ->unique()->values()->all();
            if (!empty($legacyDocus)) {
                $heads = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                    ->whereIn('DocuNo', $legacyDocus)
                    ->get(['POID', 'DocuNo']);
                if ($heads->isNotEmpty()) {
                    $poidToClean = [];
                    foreach ($heads as $h) {
                        $poidToClean[$h->POID] = preg_replace('/^PO/i', '', (string) $h->DocuNo);
                    }
                    $dt = DB::connection(self::MSSQL_CONNECTION)->table('PODT')
                        ->whereIn('POID', array_keys($poidToClean))
                        ->where('CancelFlag', '<>', 'Y')
                        ->get(['POID', 'GoodName']);
                    foreach ($dt as $line) {
                        $clean = $poidToClean[$line->POID] ?? null;
                        if ($clean === null) continue;
                        $namesByPo[$clean][] = trim((string) $line->GoodName);
                    }
                }
            }
        } catch (\Throwable $e) {
            // เชื่อม account03 ไม่ได้ — ไม่ทำให้ทั้งหน้าพัง แค่ไม่มีราคา/กำหนดส่ง/ชื่อสินค้าเก่า
            \Illuminate\Support\Facades\Log::warning('shelfsale MSSQL failed: ' . $e->getMessage());
        }

        // ---------- ประกอบผลลัพธ์ ----------
        $rows = $items->map(function ($it) use ($priceByDocu, $shipByDocu, $namesByPo, $now) {
            $docu  = 'PO' . preg_replace('/^PO/i', '', (string) $it->po);
            $price = (float) (optional($priceByDocu->get($docu))->SumGoodAmnt ?? 0);

            $ship     = optional($shipByDocu->get('SO' . $it->so))->ShipDate;
            $dueDays  = null;
            if (filled($ship)) {
                $dueDays = (int) $now->copy()->startOfDay()->diffInDays(Carbon::parse($ship)->startOfDay(), false);
            }

            $cleanPo  = preg_replace('/^PO/i', '', (string) $it->po);
            $product  = $it->good_name;
            if (!$product && isset($namesByPo[$cleanPo])) {
                $product = implode(', ', array_slice($namesByPo[$cleanPo], 0, 20));
            }

            return [
                'so'          => $it->so ?: '-',
                'po'          => $it->po ?: '-',
                'shelf'       => $it->shelf ?: '',
                'cust_id'     => $it->cust_id ?: '-',
                'cust_name'   => $it->cust_name ?: '-',
                'sale'        => $it->sale ?: '-',
                'product'     => $product ?: '-',
                'price'       => $price,
                'received_at' => $it->received_at ? $it->received_at->format('d/m/Y H:i') : null,
                'days_in'     => $it->days_in,
                'ship_date'   => filled($ship) ? Carbon::parse($ship)->format('d/m/Y') : null,
                'due_days'    => $dueDays,
                'is_legacy'   => $it->is_legacy,
            ];
        })->values();

        return response()->json(['ok' => true, 'rows' => $rows]);
    }
}
