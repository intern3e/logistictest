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
    /** connection MSSQL account03 (อ่านอย่างเดียว) — ดึงราคา/กำหนดส่ง/ชื่อสินค้าเก่า "ท้ายสุด" หลังคัดกรอง */
    const MSSQL_CONNECTION  = 'mssql_account03';

    /** รายชื่อชั้นวาง — ชุดเดียวกับหน้า mobile-app (po/mobile_app.blade.php SHELF_OPTIONS) */
    const SHELF_OPTIONS = [
        "1A01","1Kโบว์","1กวาง","1กี้","1ตี๋/พลอย","1ต่าย","1ท๊อป","1นภา",
        "1นุ/เต้น","1นุช","1นุ่น","1น้อย/มล","1น้ำ/กิ๊ฟ","1ฟอง","1ฟิล์ม",
        "1ภิรุณ","1มุก","1หนิง","1หมิง","1หมู/ต๋อง","1เจี๊ยบ","1เชร์",
        "1เนย","1เอก","1แยม","1แอม","1โจ",
        "A11","A12","A13","A14","A21","A22","A23","A24",
        "A31","A32","A33","A34","A41","A42","A43","A44",
        "aom stock","B1",
        "C1","C10","C11","C12","C13","C14","C15","C16",
        "C2","C3","C4","C5","C6","C7","C8","C9","Cท่อ",
        "LASADA",
        "Qกรมศุลเล็","Qจัดแล้วรอ","Qติดปัญหา","Qบิลสด","QรอครบSO","Qสหกรณ์","Qแก้ไข","Qแดง",
        "top stock","Z55",
        "ขมจ่ายแล้ว","ของเกิน","คืนstock","ช.เดช","ช.โอ","ชัย-เดช","ชัย1","ชัย2",
        "ด.1","ด.10","ด.11","ด.12","ด.2","ด.3","ด.4","ด.5","ด.6","ด.7","ด.8","ด.9",
        "ทำคืน","ท๊อปบน","ปอ-ฮิคาริ","ปิดรับบิล","ปี69","พู่",
        "ว๊าล","ว๊าลแก้ไข","หน้าออฟฟิศ","หยกรอบิล","หยกรอเคลีย","หลังออฟฟิศ",
    ];

    /**
     * หน้า /shelfsale — โหลดแค่ตัวกรอง (ไม่ดึงข้อมูลตอนเปิดหน้า)
     * ข้อมูลจะแสดงก็ต่อเมื่อเลือกตัวกรอง (ชั้น/Sale) แล้วกดค้นหา -> เรียก data()
     */
    public function index()
    {
        $user    = $this->requireLogin();
        $creator = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');

        // สิทธิ์การมองเห็น: admin/store/stock และ sale/sale_assistant/support เห็นทุกชั้นทุก Sale (ไม่ล็อกเฉพาะชื่อตัวเอง)
        $seeAll     = in_array($user->role ?? '', ['admin', 'store', 'stock', 'sale', 'sale_assistant', 'support'], true);
        // แสดงคอลัมน์ "ชื่อลูกค้า" แทน "Sale" สำหรับ sale/support/sale_assistant
        $isSaleView = in_array($user->role ?? '', ['sale', 'sale_assistant', 'support'], true);
        // ล็อกช่อง Sale = ชื่อตัวเอง เฉพาะ role ที่ถูกบังคับเห็นเฉพาะงานตัวเอง (นอกกลุ่ม seeAll)
        $lockSale   = !$seeAll;
        $autoLoad   = ($user->role ?? '') === 'admin';   // admin โหลดของทุก Sale ทันทีที่เข้าหน้า
        $loginName  = $user->name ?? '';
        $canSeePrice = in_array($user->role ?? '', ['admin', 'sale', 'sale_assistant', 'support'], true);  // เห็นมูลค่า
        $canManage   = in_array($user->role ?? '', ['admin', 'store', 'stock'], true);                     // ย้ายชั้น/เช็คเอาท์ เฉพาะ admin/store/stock

        // dropdown Sale — เหมือนเดิม (ดึงจาก 3e so) แต่ cache 30 นาที กัน groupBy เต็มตารางทุกครั้ง
        $saleOptions = Cache::remember('shelfsale_sale_options', 1800, function () {
            return DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereNotNull('createdBy')->where('createdBy', '!=', '')
                ->groupBy('createdBy')
                ->pluck('createdBy')
                ->sort()->values();
        });

        $shelfOptions = collect(self::SHELF_OPTIONS);

        return view('sale.dashboardshelf', compact('saleOptions', 'shelfOptions', 'creator', 'isSaleView', 'lockSale', 'autoLoad', 'loginName', 'canSeePrice', 'canManage'));
    }

    /**
     * ดึงข้อมูลตามตัวกรอง (AJAX) — กรองด้วย "ชั้น" และ/หรือ "Sale" เท่านั้น (เหมือน filter เดิม)
     * ลำดับ: logistic (po_receives_line) + 3e (store) -> ข้อมูลลูกค้า/Sale จาก 3e so
     */
    public function data(Request $request)
    {
        $user = $this->requireLogin();

        $fShelf = trim((string) $request->input('shelf', ''));
        $fSale  = trim((string) $request->input('sale', ''));
        $fSo    = trim((string) $request->input('so', ''));
        $fPo    = trim((string) $request->input('po', ''));

        // admin/store/stock และ sale/sale_assistant/support เห็นทุก Sale — role อื่นเท่านั้นที่ถูกบังคับเห็นเฉพาะงานของตัวเอง
        $seeAll = in_array($user->role ?? '', ['admin', 'store', 'stock', 'sale', 'sale_assistant', 'support'], true);
        if (!$seeAll) {
            $fSale = $user->name ?? '';
        }

        // ต้องเลือกตัวกรองอย่างน้อย 1 อย่างก่อน (กันโหลดทั้งหมด) — ยกเว้น admin ที่โหลดของทุก Sale ได้เลย
        $isAdmin = ($user->role ?? '') === 'admin';
        if (!$isAdmin && $fShelf === '' && $fSale === '' && $fSo === '' && $fPo === '') {
            return response()->json([
                'ok'      => true,
                'rows'    => [],
                'message' => 'กรุณาเลือกตัวกรอง (ชั้น / Sale / SO / PO) ก่อนค้นหา',
            ]);
        }

        // --- ของใหม่: logistic po_receives_line (บนชั้น + ยังไม่เช็คเอาท์) ---
        $newQ = PoReceiveLine::whereNotNull('shelf')
            ->where('shelf', '!=', '')
            ->whereHas('header', function ($q) use ($fSo) {
                $q->whereNull('checkout_time');
                if ($fSo !== '') $q->where('so_id', 'LIKE', "%{$fSo}%");
            })
            ->with('header');
        if ($fShelf !== '') $newQ->where('shelf', 'LIKE', "%{$fShelf}%");
        if ($fPo !== '')    $newQ->where('po_id', 'LIKE', "%{$fPo}%");

        $newItems = $newQ->get()->map(fn ($line) => (object) [
            'so'          => optional($line->header)->so_id,
            'po'          => optional($line->header)->po_id,
            'shelf'       => $line->shelf,
            'received_at' => $line->received_at,
            'good_name'   => $line->good_name,
            'line_id'     => $line->id,
        ]);

        // --- ของเก่า: 3e store — ของ "บนชั้น" ใช้คอลัมน์ Area (เป็น id ของชั้น) + ยังไม่เช็คเอาท์ ---
        //   Area = รหัสชั้น -> แปลชื่อชั้นจากตาราง area (areaName)
        $poInNewSet = array_flip(
            PoReceive::pluck('po_id')->filter()
                ->map(fn ($p) => preg_replace('/^PO/i', '', (string) $p))
                ->all()
        );

        // ถ้ากรองด้วยชื่อชั้น -> หา id ชั้นที่ชื่อ match ก่อน แล้วค่อยกรอง store.Area IN ids
        $shelfAreaIds = null;
        if ($fShelf !== '') {
            $shelfAreaIds = DB::connection(self::LEGACY_CONNECTION)->table('area')
                ->where('areaName', 'LIKE', "%{$fShelf}%")
                ->pluck('ID')->values()->all();
        }

        $legacyItems = collect();
        // ถ้ากรองชั้นแล้วไม่เจอ id ชั้นที่ชื่อ match -> ไม่มี legacy (ข้าม query)
        if (!($fShelf !== '' && empty($shelfAreaIds))) {
            $legQ = DB::connection(self::LEGACY_CONNECTION)->table('store')
                ->whereIn('statusArea', ['0', '1'])
                ->whereNotNull('Area')->where('Area', '<>', '')
                ->where(function ($q) {
                    $q->whereNull('DATECHECKOUT')->orWhere('DATECHECKOUT', '');
                });
            if (!empty($shelfAreaIds)) $legQ->whereIn('Area', $shelfAreaIds);
            if ($fPo !== '') $legQ->where('PO', 'LIKE', "%{$fPo}%");
            if ($fSo !== '') $legQ->where('SO', 'LIKE', "%{$fSo}%");

            $legacyRows = $legQ->get(['SO', 'PO', 'Area', 'DATEAREA'])
                ->reject(fn ($row) => isset($poInNewSet[preg_replace('/^PO/i', '', (string) $row->PO)]))
                ->values();

            // แปลรหัสชั้น (Area) -> ชื่อชั้น (areaName)
            $areaNames = collect();
            $areaIds   = $legacyRows->pluck('Area')->filter()->unique()->values()->all();
            if (!empty($areaIds)) {
                $areaNames = DB::connection(self::LEGACY_CONNECTION)->table('area')
                    ->whereIn('ID', $areaIds)->pluck('areaName', 'ID');
            }

            $legacyItems = $legacyRows->map(fn ($row) => (object) [
                'so'          => $row->SO,
                'po'          => $row->PO,
                'shelf'       => $areaNames[$row->Area] ?? '',
                'received_at' => filled($row->DATEAREA) ? Carbon::parse($row->DATEAREA) : null,
                'good_name'   => null,   // ของเก่าไม่มีชื่อสินค้า -> ดึงจาก PODT ตอนท้าย
                'line_id'     => null,
            ]);
        }

        $items = $newItems->concat($legacyItems)->sortByDesc('received_at')->values();

        if ($items->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        // --- ลูกค้า / Sale จาก 3e so ---
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
            // งานภายในถือข้อมูลลูกค้า/Sale ของตัวเองอยู่แล้ว — ใช้ค่าจาก 3e so ถ้ามี ไม่งั้นคงค่าเดิม
            $it->cust_id   = optional($so)->CustID    ?? ($it->cust_id ?? null);
            $it->cust_name = optional($so)->CustName  ?? ($it->cust_name ?? null);
            $it->sale      = optional($so)->createdBy ?? ($it->sale ?? null);
            $it->days_in   = $it->received_at
                ? (int) $it->received_at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay())
                : null;
            return $it;
        });

        // กรองด้วย Sale (หลังได้ข้อมูล so)
        if ($fSale !== '') {
            $items = $items->filter(fn ($it) => stripos((string) $it->sale, $fSale) !== false)->values();
        }

        if ($items->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        // ===== ท้ายสุด: MSSQL account03 เฉพาะแถวที่ผ่านตัวกรองแล้ว =====
        //   ราคา (POHD.SumGoodAmnt) / กำหนดส่ง (SOHD.ShipDate) / ชื่อสินค้าของงานเก่า (PODT)
        $priceByDocu = collect();
        $shipByDocu  = collect();
        $namesByPo   = [];
        try {
            $poDocuNos = $items->pluck('po')->filter()
                ->map(fn ($p) => 'PO' . preg_replace('/^PO/i', '', (string) $p))
                ->unique()->values()->all();
            if (!empty($poDocuNos)) {
                $priceByDocu = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                    ->whereIn('DocuNo', $poDocuNos)->get(['DocuNo', 'SumGoodAmnt'])->keyBy('DocuNo');
            }

            $soDocuNos = array_map(fn ($s) => 'SO' . $s, $soNums);
            if (!empty($soDocuNos)) {
                $shipByDocu = DB::connection(self::MSSQL_CONNECTION)->table('SOHD')
                    ->whereIn('DocuNo', $soDocuNos)->get(['DocuNo', 'ShipDate'])->keyBy('DocuNo');
            }

            // ชื่อสินค้าของงานเก่า (PODT) เฉพาะ PO ที่เป็น legacy (ไม่มี line_id)
            $legacyDocus = $items->filter(fn ($it) => empty($it->line_id))
                ->pluck('po')->filter()
                ->map(fn ($p) => 'PO' . preg_replace('/^PO/i', '', (string) $p))
                ->unique()->values()->all();
            if (!empty($legacyDocus)) {
                $heads = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                    ->whereIn('DocuNo', $legacyDocus)->get(['POID', 'DocuNo']);
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
            \Illuminate\Support\Facades\Log::warning('shelfsale MSSQL failed: ' . $e->getMessage());
        }

        // ===== ชื่อสินค้าของงาน "รหัส A" (เช่น 6905-A0347) — ดึงจาก 3e.internal_poline (PONum -> Description) =====
        //   งานเหล่านี้ไม่มีใน MSSQL PODT ชื่อสินค้าจึงต้องดึงจากตาราง internal_poline ในฐาน 3e
        $legacyCleanPos = $items->filter(fn ($it) => empty($it->line_id))
            ->pluck('po')->filter()
            ->map(fn ($p) => preg_replace('/^PO/i', '', (string) $p))
            ->unique()->values()->all();
        if (!empty($legacyCleanPos)) {
            try {
                $posWithPodt = array_flip(array_keys($namesByPo));   // PO ที่มีชื่อจาก PODT แล้ว (ไม่ต้องซ้ำ)
                $internalLines = DB::connection(self::LEGACY_CONNECTION)->table('internal_poline')
                    ->whereIn('PONum', $legacyCleanPos)
                    ->orderBy('POLineSeq')
                    ->get(['PONum', 'Description']);
                foreach ($internalLines as $ln) {
                    $clean = preg_replace('/^PO/i', '', (string) $ln->PONum);
                    if (isset($posWithPodt[$clean])) continue;
                    $name = trim((string) $ln->Description);
                    if ($name !== '') $namesByPo[$clean][] = $name;
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('shelfsale 3e.internal_poline failed: ' . $e->getMessage());
            }

            // fallback: PO ที่ยังไม่มีชื่อ (ทั้ง PODT และ 3e.internal_poline ไม่มี) -> ดึงจาก logistic.internal_poline (internal_id -> item_name)
            $stillMissing = array_values(array_filter($legacyCleanPos, fn ($p) => empty($namesByPo[$p])));
            if (!empty($stillMissing)) {
                try {
                    $logiLines = DB::table('internal_poline')   // default connection = ฐาน logistic
                        ->whereIn('internal_id', $stillMissing)
                        ->get(['internal_id', 'item_name']);
                    foreach ($logiLines as $ln) {
                        $clean = preg_replace('/^PO/i', '', (string) $ln->internal_id);
                        $name  = trim((string) $ln->item_name);
                        if ($name !== '') $namesByPo[$clean][] = $name;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('shelfsale logistic.internal_poline failed: ' . $e->getMessage());
                }
            }
        }

        // ===== รวมเป็น 1 แถวต่อ 1 (PO + SO) — ถ้ารหัส PO,SO เดียวกัน แสดงแถวเดียว =====
        $rows = $items->groupBy(fn ($it) => preg_replace('/^PO/i', '', (string) $it->po) . '|' . (string) $it->so)
            ->map(function ($group) use ($priceByDocu, $shipByDocu, $namesByPo, $now) {
                $first    = $group->first();
                $cleanPo  = preg_replace('/^PO/i', '', (string) $first->po);
                $shelves  = $group->pluck('shelf')->filter()->unique()->values();
                $isLegacy = $group->every(fn ($it) => empty($it->line_id));
                $docu     = 'PO' . $cleanPo;

                $price = (float) (optional($priceByDocu->get($docu))->SumGoodAmnt ?? 0);
                $ship  = optional($shipByDocu->get('SO' . $first->so))->ShipDate;
                $dueDays = null;
                if (filled($ship)) {
                    $dueDays = (int) $now->copy()->startOfDay()->diffInDays(Carbon::parse($ship)->startOfDay(), false);
                }

                if ($isLegacy && !empty($namesByPo[$cleanPo])) {
                    $shelfForLegacy = $shelves->first() ?: '';
                    $products = collect($namesByPo[$cleanPo])->map(fn ($n) => [
                        'name' => $n ?: '-', 'shelf' => $shelfForLegacy, 'line_id' => null,
                    ])->values();
                } else {
                    $products = $group->map(fn ($it) => [
                        'name' => $it->good_name ?: '-', 'shelf' => $it->shelf ?: '', 'line_id' => $it->line_id,
                    ])->values();
                }

                return [
                    'so'         => $first->so ?: '-',
                    'po'         => $cleanPo ?: '-',
                    'shelf'      => $shelves->count() === 1 ? $shelves->first()
                                    : ($shelves->count() > 1 ? 'หลายชั้น' : '-'),
                    'cust_id'    => $first->cust_id ?: '-',
                    'cust_name'  => $first->cust_name ?: '-',
                    'sale'       => $first->sale ?: '-',
                    'ship_date'  => filled($ship) ? Carbon::parse($ship)->format('d/m/Y') : null,
                    'due_days'   => $dueDays,
                    'price'      => $price,
                    'products'   => $products,
                    'item_count' => $products->count(),
                    '_ship_ts'   => filled($ship) ? Carbon::parse($ship)->timestamp : null,
                ];
            })->values();

        // เรียง "กำหนดส่งไกลสุด (ล่าสุด) ขึ้นก่อน" — ไม่มีกำหนดส่งไว้ท้ายสุด
        $rows = $rows->sortByDesc(fn ($r) => $r['_ship_ts'] ?? -1)->values();

        $totalValue = (float) $rows->sum('price');

        return response()->json([
            'ok'          => true,
            'rows'        => $rows,
            'total_value' => $totalValue,
        ]);
    }
}
