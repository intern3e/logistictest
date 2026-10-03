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
        $seeAll     = in_array($user->role ?? '', ['admin', 'store', 'stock', 'sale', 'sale_assistant', 'support', 'accounting'], true);
        // แสดงคอลัมน์ "ชื่อลูกค้า" แทน "Sale" สำหรับ sale/support/sale_assistant
        $isSaleView = in_array($user->role ?? '', ['sale', 'sale_assistant', 'support'], true);
        // ล็อกช่อง Sale = ชื่อตัวเอง เฉพาะ role ที่ถูกบังคับเห็นเฉพาะงานตัวเอง (นอกกลุ่ม seeAll)
        $lockSale   = !$seeAll;
        $autoLoad   = ($user->role ?? '') === 'admin';   // admin โหลดของทุก Sale ทันทีที่เข้าหน้า
        $loginName  = $user->name ?? '';
        $canSeePrice = in_array($user->role ?? '', ['admin', 'sale', 'sale_assistant', 'support'], true);  // เห็นมูลค่า
        $canManage   = in_array($user->role ?? '', ['admin', 'store', 'stock'], true);                     // ย้ายชั้น/เช็คเอาท์ เฉพาะ admin/store/stock
        // คอลัมน์ "เช็คเอาท์": แสดงให้ "ทุก role ทุกแถว" (รวม admin/store/stock) เพื่อดูประวัติของที่เช็คเอ้าแล้ว
        //   ตอนค้นด้วย PO/SO ; ฝั่ง manage มีคอลัมน์ "จัดการ" (ปุ่ม) แยกอีกคอลัมน์
        $showCheckout = true;

        // dropdown Sale — เหมือนเดิม (ดึงจาก 3e so) แต่ cache 30 นาที กัน groupBy เต็มตารางทุกครั้ง
        $saleOptions = Cache::remember('shelfsale_sale_options', 1800, function () {
            return DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereNotNull('createdBy')->where('createdBy', '!=', '')
                ->groupBy('createdBy')
                ->pluck('createdBy')
                ->sort()->values();
        });

        $shelfOptions = collect(self::SHELF_OPTIONS);

        return view('sale.dashboardshelf', compact('saleOptions', 'shelfOptions', 'creator', 'isSaleView', 'lockSale', 'autoLoad', 'loginName', 'canSeePrice', 'canManage', 'showCheckout'));
    }

    /**
     * ดึงข้อมูลตามตัวกรอง (AJAX) — กรองด้วย "ชั้น" และ/หรือ "Sale" เท่านั้น (เหมือน filter เดิม)
     * ลำดับ: logistic (po_receives_line) + 3e (store) -> ข้อมูลลูกค้า/Sale จาก 3e so
     */
    public function data(Request $request)
    {
        $user = $this->requireLogin();

        $fShelf  = trim((string) $request->input('shelf', ''));
        $fSale   = trim((string) $request->input('sale', ''));
        $fSo     = trim((string) $request->input('so', ''));
        $fPo     = trim((string) $request->input('po', ''));

        // admin/store/stock/accounting และ sale/sale_assistant/support เห็นทุก Sale — role อื่นเท่านั้นที่ถูกบังคับเห็นเฉพาะงานของตัวเอง
        $seeAll = in_array($user->role ?? '', ['admin', 'store', 'stock', 'sale', 'sale_assistant', 'support', 'accounting'], true);
        if (!$seeAll) {
            $fSale = $user->name ?? '';
        }

        $isAdmin   = ($user->role ?? '') === 'admin';
        $hasFilter = $fShelf !== '' || $fSale !== '' || $fSo !== '' || $fPo !== '';

        // แสดง "ของที่เคยเช็คเอาท์" เฉพาะตอนค้นด้วย PO หรือ SO เท่านั้น (ไม่งั้นดึงของเก่าที่เช็คเอาท์แล้วเป็นแสนแถว)
        //   ค้น PO/SO -> รวมทั้งที่ยังไม่เช็คเอาท์ + เช็คเอาท์แล้ว ; นอกนั้น -> เฉพาะที่ยังไม่เช็คเอาท์
        $fStatus = ($fPo !== '' || $fSo !== '') ? 'all' : 'pending';

        // role อื่น (ไม่ใช่ admin) ต้องมีตัวกรองก่อน — admin โหลดของทุก Sale (รอเช็คเอาท์) ได้เลย
        if (!$isAdmin && !$hasFilter) {
            return response()->json([
                'ok'      => true,
                'rows'    => [],
                'message' => 'กรุณาเลือกตัวกรอง (ชั้น / Sale / SO / PO) ก่อนค้นหา',
            ]);
        }

        // --- ของใหม่: logistic po_receives_line (บนชั้น) — resolve "รอบ" (header) ต่อ line ด้วย po_receive_id ---
        //   รองรับเคส PO เช็คเอาท์รอบแรกไปแล้ว + ของมาใหม่เป็นรอบใหม่ (คนละ header) แยกสถานะเช็คเอาท์ได้ถูกต้อง
        $newQ = PoReceiveLine::whereNotNull('shelf')->where('shelf', '!=', '');
        if ($fShelf !== '') $newQ->where('shelf', 'LIKE', "%{$fShelf}%");
        if ($fPo !== '')    $newQ->where('po_id', 'LIKE', "%{$fPo}%");
        if ($fSo !== '') {
            // po_id ที่ header มี so_id ตรงคำค้น — ใช้จับ line เก่าที่ so_id ว่าง (so อยู่บน header)
            //   ป้องกันบั๊ก: เดิม orWhereNull('so_id') ดึงทุก line ที่ so_id ว่างมาปนทุกการค้นหา
            $soMatchPoIds = PoReceive::where('so_id', 'LIKE', "%{$fSo}%")
                ->pluck('po_id')->filter()->unique()->values()->all();
            $newQ->where(function ($q) use ($fSo, $soMatchPoIds) {
                $q->where('so_id', 'LIKE', "%{$fSo}%");
                if (!empty($soMatchPoIds)) {
                    $q->orWhere(function ($w) use ($soMatchPoIds) {
                        $w->whereNull('so_id')->whereIn('po_id', $soMatchPoIds);
                    });
                }
            });
        }
        $newLines = $newQ->get();

        // map line -> header : มี po_receive_id ใช้ตรง ๆ
        $hdrById = collect();
        $fkIds = $newLines->pluck('po_receive_id')->filter()->unique()->values()->all();
        if (!empty($fkIds)) {
            $hdrById = PoReceive::whereIn('id', $fkIds)->get()->keyBy('id');
        }
        // orphan (po_receive_id ว่าง = ข้อมูลก่อนมีฟีเจอร์): resolve ด้วย po_id+so_id
        //   ต้องเลือก header ที่ "ยังไม่ถูกจับจอง" ด้วย line อื่น (เก่าสุด) — ไม่งั้นจะไปจับรอบใหม่ที่ยังไม่เช็คเอาท์ผิด
        //   (เดิม keyBy('po_id') เก็บ header ตัวท้าย ทำให้ line รอบเก่าที่เช็คเอาท์แล้วโชว์เป็น "ยังไม่เช็คเอาท์")
        $hdrsByPo = collect();
        $fallbackPoIds = $newLines->filter(fn ($l) => empty($l->po_receive_id))
            ->pluck('po_id')->filter()->unique()->values()->all();
        if (!empty($fallbackPoIds)) {
            $hdrsByPo = PoReceive::whereIn('po_id', $fallbackPoIds)->get()->groupBy('po_id');
        }
        $claimedHeaderIds = array_flip($fkIds);   // header ที่มี line ผูกด้วย po_receive_id แล้ว
        $resolveHeader = function ($line) use ($hdrById, $hdrsByPo, $claimedHeaderIds) {
            if ($line->po_receive_id) return $hdrById->get($line->po_receive_id);
            $cands = ($hdrsByPo->get($line->po_id) ?? collect())
                ->filter(function ($h) use ($line) {
                    return (string) ($h->so_id ?? '') === (string) ($line->so_id ?? '')
                        || ($h->so_id ?? '') === '' || ($line->so_id ?? '') === '';
                })
                ->sortBy('id')->values();
            $unclaimed = $cands->reject(fn ($h) => isset($claimedHeaderIds[$h->id]))->values();
            return $unclaimed->first() ?: $cands->first();
        };

        $newItems = $newLines->map(function ($line) use ($resolveHeader) {
                $h = $resolveHeader($line);
                return (object) [
                    'so'          => $line->so_id ?: optional($h)->so_id,
                    'po'          => $line->po_id ?: optional($h)->po_id,
                    'shelf'       => $line->shelf,
                    'received_at' => $line->received_at,
                    'good_name'   => $line->good_name,
                    'recv_qty'    => $line->recv_qty,
                    'line_id'     => $line->id,
                    'po_receive_id' => $line->po_receive_id,
                    'checkout_by' => optional($h)->checkout_by,
                    'checkout_at' => optional($h)->checkout_time,
                    '_has_header' => (bool) $h,
                    '_checked'    => filled(optional($h)->checkout_time),
                ];
            })
            // header ถูกยกเลิก/ไม่พบ (global scope notCancelled) -> ตัดออก (เหมือนเดิมที่ whereHas ตัด)
            ->filter(fn ($it) => $it->_has_header)
            // กรองตามสถานะเช็คเอาท์ของ "รอบ" นั้น ๆ
            ->filter(function ($it) use ($fStatus) {
                if ($fStatus === 'checkedout') return $it->_checked;
                if ($fStatus === 'all') return true;
                return !$it->_checked;   // pending (รอเช็คเอาท์)
            })
            ->values();

        // --- ของใหม่ (PO ภายใน รหัส A): internal_po + internal_poline (บนชั้น = มี location) ---
        //   PO ภายในระบบใหม่เก็บแยกจาก po_receives จึงต้องดึงเพิ่มเอง
        //   ชื่อสินค้าดึงจาก internal_poline ตอนท้าย (เส้นทางเดียวกับงานเก่า) -> ตั้ง line_id=null กัน id ชนกับ po_receives_line
        $intQ = \App\Models\internal_po::query()
            ->whereIn('status', [\App\Models\internal_po::ST_STORED, \App\Models\internal_po::ST_CHECKOUT])
            ->whereNotNull('location')->where('location', '!=', '');
        if ($fShelf !== '') $intQ->where('location', 'LIKE', "%{$fShelf}%");
        if ($fPo !== '')    $intQ->where('internal_id', 'LIKE', "%{$fPo}%");
        if ($fSo !== '')    $intQ->where('SO_id', 'LIKE', "%{$fSo}%");
        if ($fStatus === 'checkedout') {
            $intQ->whereNotNull('checkout_at');
        } elseif ($fStatus !== 'all') {   // pending (ยังไม่เช็คเอาท์)
            $intQ->whereNull('checkout_at');
        }
        $internalHeads = $intQ->get();
        $internalItems = $internalHeads->map(function ($h) {
            $recvAt = $h->location_at ?: ($h->pick_at ?: ($h->timestamp ?? null));
            return (object) [
                'so'            => $h->SO_id,
                'po'            => $h->internal_id,
                'shelf'         => $h->location,
                'received_at'   => filled($recvAt) ? Carbon::parse($recvAt) : null,
                'good_name'     => null,   // ดึงชื่อจาก internal_poline ตอนท้าย
                'recv_qty'      => null,
                'line_id'       => null,
                'po_receive_id' => null,
                'checkout_by'   => $h->checkout_by,
                'checkout_at'   => filled($h->checkout_at) ? Carbon::parse($h->checkout_at) : null,
                'cust_id'       => $h->customer_code,
                'cust_name'     => $h->customer_name,
                'sale'          => $h->create_by,
            ];
        })->values();
        // set PO ภายใน (clean) สำหรับกันซ้ำกับ 3e store
        $internalPoSet = array_flip(
            $internalHeads->pluck('internal_id')->filter()
                ->map(fn ($p) => preg_replace('/^PO/i', '', (string) $p))
                ->all()
        );
        // มูลค่า PO ภายใน = ผลรวม internal_poline.item_total (PO ภายในไม่มีใน MSSQL POHD)
        $internalPriceByPo = [];
        if ($internalHeads->isNotEmpty()) {
            $intIds = $internalHeads->pluck('internal_id')->filter()->unique()->values()->all();
            $sums = DB::table('internal_poline')
                ->whereIn('internal_id', $intIds)
                ->select('internal_id', DB::raw('SUM(item_total) as total'))
                ->groupBy('internal_id')
                ->pluck('total', 'internal_id');
            foreach ($sums as $iid => $tot) {
                $internalPriceByPo[preg_replace('/^PO/i', '', (string) $iid)] = (float) $tot;
            }
        }

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
                ->whereNotNull('Area')->where('Area', '<>', '');
            // กรองตามสถานะเช็คเอาท์ (ของเก่าดูจาก DATECHECKOUT)
            if ($fStatus === 'checkedout') {
                $legQ->whereNotNull('DATECHECKOUT')->where('DATECHECKOUT', '<>', '');
            } elseif ($fStatus === 'all') {
                // ไม่กรอง — เอาทั้งที่เช็คเอาท์แล้วและยังไม่เช็คเอาท์
            } else { // pending
                $legQ->where(function ($q) {
                    $q->whereNull('DATECHECKOUT')->orWhere('DATECHECKOUT', '');
                });
            }
            if (!empty($shelfAreaIds)) $legQ->whereIn('Area', $shelfAreaIds);
            if ($fPo !== '') $legQ->where('PO', 'LIKE', "%{$fPo}%");
            if ($fSo !== '') $legQ->where('SO', 'LIKE', "%{$fSo}%");

            // จำกัดผลลัพธ์ของเก่ากันดึงมหาศาลในโหมด checkout (ของที่เช็คเอาท์แล้วมีเป็นแสน)
            $legacyRows = $legQ->limit(3000)->get(['SO', 'PO', 'Area', 'DATEAREA', 'DATECHECKOUT'])
                ->reject(function ($row) use ($poInNewSet, $internalPoSet) {
                    $clean = preg_replace('/^PO/i', '', (string) $row->PO);
                    return isset($poInNewSet[$clean]) || isset($internalPoSet[$clean]);
                })
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
                'po_receive_id' => null,
                'checkout_by' => null,   // ระบบเก่า: ไม่มีชื่อผู้เช็คเอาท์ (แสดงแค่เวลา)
                'checkout_at' => filled($row->DATECHECKOUT) ? Carbon::parse($row->DATECHECKOUT) : null,
            ]);
        }

        $items = $newItems->concat($internalItems)->concat($legacyItems)->sortByDesc('received_at')->values();

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
        $orderedByPo = [];   // [cleanPo][normname] => จำนวนที่สั่ง (PODT.GoodQty2)
        try {
            $poDocuNos = $items->pluck('po')->filter()
                ->map(fn ($p) => 'PO' . preg_replace('/^PO/i', '', (string) $p))
                ->unique()->values()->all();
            if (!empty($poDocuNos)) {
                // มูลค่า PO ดึงจากคอลัมน์ NetAmnt (ยอดสุทธิ) ตามที่กำหนด
                $priceByDocu = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                    ->whereIn('DocuNo', $poDocuNos)->get(['DocuNo', 'NetAmnt'])->keyBy('DocuNo');
            }

            $soDocuNos = array_map(fn ($s) => 'SO' . $s, $soNums);
            if (!empty($soDocuNos)) {
                $shipByDocu = DB::connection(self::MSSQL_CONNECTION)->table('SOHD')
                    ->whereIn('DocuNo', $soDocuNos)->get(['DocuNo', 'ShipDate'])->keyBy('DocuNo');
            }

            // จำนวนที่ "สั่ง" (PODT.GoodQty2) ต่อ PO + ชื่อสินค้า — ใช้แสดง "รับจริง/สั่ง" และทำ PDF
            if (!empty($poDocuNos)) {
                $allHeads = DB::connection(self::MSSQL_CONNECTION)->table('POHD')
                    ->whereIn('DocuNo', $poDocuNos)->get(['POID', 'DocuNo']);
                $poidClean = [];
                foreach ($allHeads as $h) {
                    $poidClean[$h->POID] = preg_replace('/^PO/i', '', (string) $h->DocuNo);
                }
                if (!empty($poidClean)) {
                    $allDt = DB::connection(self::MSSQL_CONNECTION)->table('PODT')
                        ->whereIn('POID', array_keys($poidClean))
                        ->where('CancelFlag', '<>', 'Y')
                        ->get(['POID', 'GoodName', 'GoodQty2']);
                    foreach ($allDt as $line) {
                        $clean = $poidClean[$line->POID] ?? null;
                        if ($clean === null) continue;
                        $nk = mb_strtolower(preg_replace('/\s+/', ' ', trim((string) $line->GoodName)));
                        $orderedByPo[$clean][$nk] = ($orderedByPo[$clean][$nk] ?? 0) + (float) $line->GoodQty2;
                    }
                }
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
                        ->get(['POID', 'GoodName', 'GoodQty2']);
                    foreach ($dt as $line) {
                        $clean = $poidToClean[$line->POID] ?? null;
                        if ($clean === null) continue;
                        $namesByPo[$clean][] = ['name' => trim((string) $line->GoodName), 'qty' => $line->GoodQty2];
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
                    ->get(['PONum', 'Description', 'Quantity']);
                foreach ($internalLines as $ln) {
                    $clean = preg_replace('/^PO/i', '', (string) $ln->PONum);
                    if (isset($posWithPodt[$clean])) continue;
                    $name = trim((string) $ln->Description);
                    if ($name !== '') $namesByPo[$clean][] = ['name' => $name, 'qty' => $ln->Quantity];
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
                        ->get(['internal_id', 'item_name', 'item_quantity']);
                    foreach ($logiLines as $ln) {
                        $clean = preg_replace('/^PO/i', '', (string) $ln->internal_id);
                        $name  = trim((string) $ln->item_name);
                        if ($name !== '') $namesByPo[$clean][] = ['name' => $name, 'qty' => $ln->item_quantity];
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('shelfsale logistic.internal_poline failed: ' . $e->getMessage());
                }
            }
        }

        // ระบุว่าชื่อสินค้า (งานเก่า) เป็นของ SO ใด จาก token "S.<so>=<qty>" ที่ฝังในชื่อ
        //   - ไม่มี token S.<...> เลย = ใช้ร่วมทุก SO (แสดงทุกแถว)
        //   - มี token = แสดงเฉพาะ SO ที่ตรง (เทียบด้วยตัวเลขล้วน รองรับทั้งรูปเต็ม 69/018052 และย่อ 019208)
        $soBelongs = function ($name, $soNum) {
            if (!preg_match_all('/S\.([0-9\/]+)/u', (string) $name, $m)) {
                return true;
            }
            $soDigits = preg_replace('/\D/', '', (string) $soNum);
            if ($soDigits === '') return true;
            foreach ($m[1] as $tok) {
                $tokDigits = preg_replace('/\D/', '', $tok);
                if ($tokDigits === '') continue;
                if ($tokDigits === $soDigits
                    || str_ends_with($soDigits, $tokDigits)
                    || str_ends_with($tokDigits, $soDigits)) {
                    return true;
                }
            }
            return false;
        };

        // ดึง "จำนวน" ของงานเก่าจาก token "S.<so>=<qty>" ในชื่อ (เลือก qty ของ SO ที่ตรง)
        //   ถ้าไม่มี token = คืน null (แสดง '-')
        $extractQty = function ($name, $soNum) {
            if (!preg_match_all('/S\.([0-9\/]+)\s*=\s*([0-9]+(?:\.[0-9]+)?)/u', (string) $name, $m, PREG_SET_ORDER)) {
                return null;
            }
            $soDigits = preg_replace('/\D/', '', (string) $soNum);
            foreach ($m as $mm) {
                $tokDigits = preg_replace('/\D/', '', $mm[1]);
                if ($tokDigits === '') continue;
                if ($soDigits === '' || $tokDigits === $soDigits
                    || str_ends_with($soDigits, $tokDigits)
                    || str_ends_with($tokDigits, $soDigits)) {
                    return $mm[2];
                }
            }
            return null;
        };

        // ===== รวมเป็น 1 แถวต่อ 1 (PO + SO + รอบ) — แยกตาม "รอบรับเข้า" (po_receive_id) เพื่อไม่รวมรอบเก่า+ใหม่เป็นแถวเดียว =====
        $rows = $items->groupBy(fn ($it) => preg_replace('/^PO/i', '', (string) $it->po) . '|' . (string) $it->so . '|' . (string) ($it->po_receive_id ?? ''))
            ->map(function ($group) use ($priceByDocu, $shipByDocu, $namesByPo, $orderedByPo, $now, $soBelongs, $extractQty, $internalPriceByPo) {
                $first    = $group->first();
                $cleanPo  = preg_replace('/^PO/i', '', (string) $first->po);
                // จำนวนที่สั่งของสินค้าชื่อนี้ใน PO นี้ (PODT.GoodQty2)
                $orderedOf = function ($name) use ($orderedByPo, $cleanPo) {
                    $nk = mb_strtolower(preg_replace('/\s+/', ' ', trim((string) $name)));
                    return $orderedByPo[$cleanPo][$nk] ?? null;
                };
                $shelves  = $group->pluck('shelf')->filter()->unique()->values();
                $isLegacy = $group->every(fn ($it) => empty($it->line_id));
                $docu     = 'PO' . $cleanPo;

                // PO ภายใน: ใช้มูลค่าจากผลรวม item_total ; PO ภายนอก: ใช้ NetAmnt จาก MSSQL POHD
                $price = isset($internalPriceByPo[$cleanPo])
                    ? $internalPriceByPo[$cleanPo]
                    : (float) (optional($priceByDocu->get($docu))->NetAmnt ?? 0);
                $ship  = optional($shipByDocu->get('SO' . $first->so))->ShipDate;
                $dueDays = null;
                if (filled($ship)) {
                    $dueDays = (int) $now->copy()->startOfDay()->diffInDays(Carbon::parse($ship)->startOfDay(), false);
                }

                if ($isLegacy && !empty($namesByPo[$cleanPo])) {
                    $shelfForLegacy = $shelves->first() ?: '';
                    // งานเก่า: ชื่อสินค้าเป็นระดับ PO -> กรองเฉพาะที่เป็นของ SO นี้ (ตาม token S.<so>)
                    $filtered = collect($namesByPo[$cleanPo])
                        ->filter(fn ($it) => $soBelongs($it['name'] ?? '', $first->so))
                        ->values();
                    // เผื่อ token ไม่ตรงรูปแบบจนกรองหมด -> แสดงทั้งหมดกันข้อมูลหาย
                    if ($filtered->isEmpty()) $filtered = collect($namesByPo[$cleanPo])->values();
                    $soForQty = $first->so;
                    $products = $filtered->map(function ($it) use ($shelfForLegacy, $soForQty, $extractQty, $orderedOf) {
                        $nm = $it['name'] ?? '-';
                        // จำนวน: token S.<so>=qty ในชื่อก่อน (ต่อ SO) ไม่งั้นใช้ qty จากแหล่งข้อมูล (PODT/internal)
                        $qty = $extractQty($nm, $soForQty);
                        if ($qty === null) $qty = $it['qty'] ?? null;
                        return ['name' => $nm ?: '-', 'shelf' => $shelfForLegacy, 'qty' => $qty, 'ordered' => $orderedOf($nm), 'line_id' => null];
                    })->values();
                } else {
                    $products = $group->map(fn ($it) => [
                        'name' => $it->good_name ?: '-', 'shelf' => $it->shelf ?: '',
                        'qty' => $it->recv_qty, 'ordered' => $orderedOf($it->good_name), 'line_id' => $it->line_id,
                    ])->values();
                }

                // ข้อมูลเช็คเอาท์ (ระบบใหม่: ใคร+เมื่อ, ระบบเก่า: เฉพาะเวลา)
                $coAt = $first->checkout_at ?? null;
                return [
                    'so'          => $first->so ?: '-',
                    'po'          => $cleanPo ?: '-',
                    // ชั้นวาง: แสดงรายชื่อชั้นทั้งหมดของ SO นี้ (ไม่ใช้คำว่า "หลายชั้น")
                    'shelf'       => $shelves->isNotEmpty() ? $shelves->implode(', ') : '-',
                    'cust_id'     => $first->cust_id ?: '-',
                    'cust_name'   => $first->cust_name ?: '-',
                    'sale'        => $first->sale ?: '-',
                    'ship_date'   => filled($ship) ? Carbon::parse($ship)->format('d/m/Y') : null,
                    'due_days'    => $dueDays,
                    'price'       => $price,
                    'products'    => $products,
                    'item_count'  => $products->count(),
                    'is_checkedout' => filled($coAt),
                    'checkout_by'   => $first->checkout_by ?: null,
                    'checkout_at'   => filled($coAt) ? Carbon::parse($coAt)->format('d/m/Y H:i') : null,
                    'po_receive_id' => $first->po_receive_id ?? null,   // รอบ (header) — ใช้เช็คเอาท์แยกรอบ
                    '_ship_ts'    => filled($ship) ? Carbon::parse($ship)->timestamp : null,
                ];
            })->values();

        // เรียง: "ยังไม่เช็คเอาท์" ขึ้นก่อน -> ตามด้วย "เช็คเอาท์แล้ว" ; ในกลุ่มเรียงกำหนดส่งไกลสุดขึ้นก่อน
        $rows = $rows->sortBy(function ($r) {
            $base = $r['is_checkedout'] ? 1e13 : 0;
            return $base - ($r['_ship_ts'] ?? -1);
        })->values();

        // PO ซ้ำ (โผล่หลายแถว = หลาย SO/หลายรอบ): ทุกแถวยังแสดงราคาปกติ
        //   แต่ "มูลค่าทั้งหมด" คิดแต่ละ PO ครั้งเดียว (ไม่บวกซ้ำ)
        $seenPo     = [];
        $totalValue = 0.0;
        foreach ($rows as $r) {
            $po = (string) ($r['po'] ?? '');
            if ($po !== '' && $po !== '-') {
                if (isset($seenPo[$po])) continue;   // PO ซ้ำ -> ไม่บวกยอดรวมซ้ำ
                $seenPo[$po] = true;
            }
            $totalValue += (float) ($r['price'] ?? 0);
        }

        return response()->json([
            'ok'          => true,
            'rows'        => $rows,
            'total_value' => $totalValue,
        ]);
    }
}
