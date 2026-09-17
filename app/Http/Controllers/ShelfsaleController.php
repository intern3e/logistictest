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

        // dropdown Sale — เหมือนเดิม (ดึงจาก 3e so) แต่ cache 30 นาที กัน groupBy เต็มตารางทุกครั้ง
        $saleOptions = Cache::remember('shelfsale_sale_options', 1800, function () {
            return DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereNotNull('createdBy')->where('createdBy', '!=', '')
                ->groupBy('createdBy')
                ->pluck('createdBy')
                ->sort()->values();
        });

        $shelfOptions = collect(self::SHELF_OPTIONS);

        return view('sale.dashboardshelf', compact('saleOptions', 'shelfOptions', 'creator'));
    }

    /**
     * ดึงข้อมูลตามตัวกรอง (AJAX) — กรองด้วย "ชั้น" และ/หรือ "Sale" เท่านั้น (เหมือน filter เดิม)
     * ลำดับ: logistic (po_receives_line) + 3e (store) -> ข้อมูลลูกค้า/Sale จาก 3e so
     */
    public function data(Request $request)
    {
        $this->requireLogin();

        $fShelf = trim((string) $request->input('shelf', ''));
        $fSale  = trim((string) $request->input('sale', ''));

        // ต้องเลือกตัวกรองอย่างน้อย 1 อย่างก่อน (กันโหลดทั้งหมด)
        if ($fShelf === '' && $fSale === '') {
            return response()->json([
                'ok'      => true,
                'rows'    => [],
                'message' => 'กรุณาเลือกตัวกรอง (ชั้น หรือ Sale) ก่อนค้นหา',
            ]);
        }

        // --- ของใหม่: logistic po_receives_line (บนชั้น + ยังไม่เช็คเอาท์) ---
        $newQ = PoReceiveLine::whereNotNull('shelf')
            ->where('shelf', '!=', '')
            ->whereHas('header', fn ($q) => $q->whereNull('checkout_time'))
            ->with('header');
        if ($fShelf !== '') $newQ->where('shelf', 'LIKE', "%{$fShelf}%");

        $newItems = $newQ->get()->map(fn ($line) => (object) [
            'so'          => optional($line->header)->so_id,
            'po'          => optional($line->header)->po_id,
            'shelf'       => $line->shelf,
            'received_at' => $line->received_at,
        ]);

        // --- ของเก่า: 3e store (กรอง po ระบบใหม่ใน PHP ไม่ใช้ NOT IN) ---
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

        $legacyItems = $legQ->get(['SO', 'PO', 'areaS', 'DATEAREA'])
            ->reject(fn ($row) => isset($poInNewSet[preg_replace('/^PO/i', '', (string) $row->PO)]))
            ->map(fn ($row) => (object) [
                'so'          => $row->SO,
                'po'          => $row->PO,
                'shelf'       => $row->areaS,
                'received_at' => filled($row->DATEAREA) ? Carbon::parse($row->DATEAREA) : null,
            ]);

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
            $it->cust_id   = optional($so)->CustID;
            $it->cust_name = optional($so)->CustName;
            $it->sale      = optional($so)->createdBy;
            $it->days_in   = $it->received_at
                ? (int) $it->received_at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay())
                : null;
            return $it;
        });

        // กรองด้วย Sale (หลังได้ข้อมูล so)
        if ($fSale !== '') {
            $items = $items->filter(fn ($it) => stripos((string) $it->sale, $fSale) !== false)->values();
        }

        $rows = $items->map(fn ($it) => [
            'so'          => $it->so ?: '-',
            'po'          => $it->po ?: '-',
            'shelf'       => $it->shelf ?: '',
            'cust_id'     => $it->cust_id ?: '-',
            'cust_name'   => $it->cust_name ?: '-',
            'sale'        => $it->sale ?: '-',
            'received_at' => $it->received_at ? $it->received_at->format('d/m/Y H:i') : null,
            'days_in'     => $it->days_in,
        ])->values();

        return response()->json(['ok' => true, 'rows' => $rows]);
    }
}
