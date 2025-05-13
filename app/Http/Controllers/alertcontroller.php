<?php

namespace App\Http\Controllers;
use App\Models\Bill;
use Illuminate\Http\Request;

class alertcontroller extends Controller
{
    public function dashboard(Request $request)
    {

        $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->with('customer')
                        ->get();
        $items = $bill; // เพิ่มบรรทัดนี้
        return view('alert.alertsale', compact('bill'));
    }
public function updateNG(Request $request)
{
    // ตรวจสอบว่าได้ส่ง so_detail_id มาหรือไม่
    $soDetailId = $request->input('so_detail_id');

    // ค้นหาข้อมูลในฐานข้อมูลโดยใช้ so_detail_id
    $item = bill::where('so_detail_id', $soDetailId)->first();

    if ($item) {
        // หากพบข้อมูล ให้ทำการอัปเดตค่า NG เป็น null
        $item->NG = null;
        $item->save();
        return response()->json(['status' => 'success', 'message' => 'เสร็จสิ้น']);
        window.location.reload();
    }

    // หากไม่พบข้อมูล
    return response()->json(['status' => 'error', 'message' => 'so_detail_id not found']);
}


}
