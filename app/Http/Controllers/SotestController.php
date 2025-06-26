<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Pobills;
use App\Models\Docbills;
use Illuminate\Support\Facades\Log;

class SotestController extends Controller
{
    public function dashboard(Request $request)
    {
        $date = $request->get('date');
        $message = null;

        // ดึง Bill
        $bill = Bill::when($date, function ($query) use ($date) {
                    return $query->whereDate('time', $date);
                })
                ->orderBy('so_detail_id', 'desc')
                ->get();

        // ดึง Pobills
        $pobill = Pobills::when($date, function ($query) use ($date) {
                    return $query->whereDate('time', $date);
                })
                ->orderBy('po_detail_id', 'desc')
                ->get();

        // ดึง Docbills
        $docbill = Docbills::when($date, function ($query) use ($date) {
                    return $query->whereDate('datestamp', $date);
                })
                ->orderBy('doc_id', 'desc')
                ->get();

        // ตรวจสอบว่าไม่มีข้อมูลเลยทั้ง 3 อย่าง
        if ($bill->isEmpty() && $pobill->isEmpty() && $docbill->isEmpty()) {
            $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
        }

        // ส่งออกไปที่ view
        return view('sale.Sotest', compact('bill', 'pobill', 'docbill', 'message'));
    }
}
