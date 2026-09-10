<?php

namespace App\Http\Controllers;

use App\Models\PoReceive;
use App\Models\PoReceiveLine;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShelfsaleController extends Controller
{
    /** ชื่อ connection ของฐานข้อมูลระบบเก่า (3e) — ใช้ "อ่านอย่างเดียว" */
    const LEGACY_CONNECTION = 'mysql_3e';

    public function index()
    {
        $user    = $this->requireLogin();
        $creator = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');

        // PO ทุกตัวที่มีอยู่ในระบบใหม่แล้ว (ไม่ว่าสถานะจะเป็นอะไร)
        // ใช้กันไม่ให้ของเก่าโผล่มาซ้ำ/ขัดแย้งกับของใหม่
        // NOTE: match กันด้วย po_id (po_id ของทั้งสองตารางเป็นค่าเดียวกัน = เลข PO จริง)
        $poInNewSystem = PoReceive::pluck('po_id')->filter()->all();

        // --- ของใหม่ (หลัก): DB logistic ---
        // งานจะหายไปก็ต่อเมื่อถูกเช็คเอาท์ (header.checkout_time ไม่ว่าง) เท่านั้น
        $newItems = PoReceiveLine::whereNotNull('shelf')
            ->where('shelf', '!=', '')
            ->whereHas('header', function ($q) {
                $q->whereNull('checkout_time');
            })
            ->with('header')
            ->get()
            ->map(function ($line) {
                return (object) [
                    'so'          => $line->header->so_id ?? null,
                    'po'          => $line->header->po_id ?? null,
                    'shelf'       => $line->shelf,
                    'received_at' => $line->received_at,
                ];
            });

        // --- ของเก่า (fallback เฉพาะ PO ที่ยังไม่เข้าระบบใหม่เลย): DB 3e, table store ---
        // งานจะหายไปก็ต่อเมื่อถูกเช็คเอาท์ (DATECHECKOUT ไม่ว่าง) เท่านั้น
        $legacyItems = Store::where('DATECHECKOUT', '')   // ค่าจริงเก็บเป็น empty string
            ->whereNotNull('areaS')
            ->where('areaS', '!=', '')
            ->whereNotIn('PO', $poInNewSystem)
            ->get()
            ->map(function ($row) {
                return (object) [
                    'so'          => $row->SO,
                    'po'          => $row->PO,
                    'shelf'       => $row->areaS,
                    'received_at' => filled($row->DATEAREA) ? Carbon::parse($row->DATEAREA) : null,
                ];
            });

        $items = $newItems->concat($legacyItems)
            ->sortByDesc('received_at')
            ->values();

        // --- ดึงข้อมูลลูกค้า + Sale จาก table so (DB เก่า 3e) ด้วย SO ครั้งเดียว ---
        // อ่านอย่างเดียว (SELECT) ไม่มีการ update/delete ฐานข้อมูลเก่า
        $soNumbers = $items->pluck('so')->filter()->unique()->values()->all();

        $soInfo = collect();
        if (!empty($soNumbers)) {
            $soInfo = DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereIn('SONum', $soNumbers)
                ->get(['SONum', 'CustID', 'CustName', 'createdBy'])
                ->keyBy('SONum');
        }

        // แปะ รหัสลูกค้า / ชื่อลูกค้า / Sale(createdBy) ให้แต่ละแถว
        $now = Carbon::now();
        $items = $items->map(function ($item) use ($soInfo, $now) {
            $so = $soInfo->get($item->so);

            $item->cust_id   = $so->CustID   ?? null;
            $item->cust_name = $so->CustName ?? null;
            $item->sale      = $so->createdBy ?? null;

            // จำนวนวันตั้งแต่รับเข้า (ปัดลงเป็นจำนวนวันเต็ม)
            $item->days_in = $item->received_at
                ? (int) $item->received_at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay())
                : null;

            return $item;
        });

        // รายชื่อ Sale สำหรับ dropdown ฟิลเตอร์
        $saleOptions = $items->pluck('sale')->filter()->unique()->sort()->values();

        return view('sale.dashboardshelf', compact('items', 'saleOptions', 'creator'));
    }
}
