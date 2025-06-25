<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Pobills;
use Illuminate\Support\Facades\Log;

class SotestController extends Controller
{
    public function dashboard(Request $request)
    {
        $date = $request->get('date');
        $message = null;

        // ดึงข้อมูลจาก Bill ตามวันที่ หรือทั้งหมด
        $bill = Bill::when($date, function ($query) use ($date) {
                    return $query->whereDate('time', $date);
                })
                ->orderBy('so_detail_id', 'desc')
                ->get();

        // ดึงข้อมูลจาก Pobills ตามวันที่ หรือทั้งหมด
        $pobill = Pobills::when($date, function ($query) use ($date) {
                    return $query->whereDate('time', $date);
                })
                ->orderBy('po_detail_id', 'desc')
                ->get();

        // ตรวจสอบว่าทั้งสองว่างหรือไม่
        if ($bill->isEmpty() && $pobill->isEmpty()) {
            $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
        }

        return view('sale.Sotest', compact('bill', 'pobill', 'message'));
    }
}
