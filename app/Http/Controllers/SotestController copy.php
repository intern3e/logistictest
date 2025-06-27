<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Facades\Log;

class SotestController extends Controller
{
    public function dashboard(Request $request)
    {
        $date = $request->get('date');
        $message = null;

        // ดึงเฉพาะ Bill ที่ status == 1 และ statuspdf == 1
        $query = Bill::where('status', 1)
                     ->where('statuspdf', 1);

        // ถ้าระบุวันที่ ให้กรองตามวันที่
        if ($date) {
            $query->whereDate('time', $date);
        }

        // เรียงลำดับและดึงข้อมูล
        $bill = $query->orderBy('so_detail_id', 'desc')->get();

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($bill->isEmpty()) {
            $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
        }

        return view('sale.Sotest', compact('bill', 'message'));
    }
}
