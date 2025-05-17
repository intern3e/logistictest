<?php

namespace App\Http\Controllers;
use App\Models\Bill;
use App\Models\docBills;
use App\Models\pobills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class alertcontroller extends Controller
{
    public function dashboard(Request $request)
{
    // ดึงข้อมูลจาก tblbill
    $bills = Bill::orderBy('so_detail_id', 'desc')->get();

    // ดึงข้อมูลจาก pobills (สมมติ model ชื่อ PoBill)
    $poBills = pobills::orderBy('po_detail_id', 'desc')->get();

    // ดึงข้อมูลจาก docbills (สมมติ model ชื่อ DocBill)
    $docBills = docBills::orderBy('doc_id', 'desc')->get();

    // รวมข้อมูล 3 collection เข้าด้วยกัน
    $items = $bills->concat($poBills)->concat($docBills);

    return view('alert.alertsale', compact('items'));
}
  public function updateNG(Request $request)
    {
        try {
            $id = $request->input('id');
            $table = $request->input('table');

            if (!$id || !$table) {
                return response()->json(['status' => 'error', 'message' => 'Missing parameters'], 400);
            }

            switch ($table) {
                case 'tblbill':
                    $item = Bill::where('so_detail_id', $id)->first();
                    break;
                case 'pobills':
                    $item = PoBills::where('po_detail_id', $id)->first();
                    break;
                case 'docbills':
                    $item = DocBills::where('doc_id', $id)->first();
                    break;
                default:
                    return response()->json(['status' => 'error', 'message' => 'ไม่รู้จักชื่อตาราง'], 400);
            }

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'ไม่พบข้อมูล'], 404);
            }

            $item->NG = null;
            $item->save();

            return response()->json(['status' => 'success', 'message' => 'ล้าง NG สำเร็จ']);
        } catch (\Throwable $e) {
            \Log::error('Update NG Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

 public function dashboardaccount(Request $request)
    {

        $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->with('customer')
                        ->get();
        $items = $bill; // เพิ่มบรรทัดนี้
        return view('alert.alertaccount', compact('bill'));
    }
public function finish(Request $request)
{
    // ตรวจสอบว่าได้ส่ง so_detail_id มาหรือไม่
    $soDetailId = $request->input('so_detail_id');

    // ค้นหาข้อมูลในฐานข้อมูลโดยใช้ so_detail_id
    $item = bill::where('so_detail_id', $soDetailId)->first();

    if ($item) {
        // หากพบข้อมูล ให้ทำการอัปเดตค่า NG เป็น null
        $item->statuspdf = 4;
        $item->save();
        return response()->json(['status' => 'success', 'message' => 'เสร็จสิ้น']);
    
    }

    // หากไม่พบข้อมูล
    return response()->json(['status' => 'error', 'message' => 'so_detail_id not found']);
}
public function getBillDetail(Request $request, $id)
{
    try {
        $results = collect();

        // ค้นหาใน bill_detail (tblbill)
        $tblbill = DB::table('bill_detail')->where('so_detail_id', $id)->get();

        // ค้นหาใน pobills_detail (pobills)
        $pobills = DB::table('pobills_detail')->where('po_detail_id', $id)->get();

        // ค้นหาใน doc_detail (docbills)
        $docbills = DB::table('doc_detail')->where('doc_id', $id)->get();

        // รวมข้อมูลทั้งหมด
        $results = $results->merge($tblbill)->merge($pobills)->merge($docbills);

        return response()->json($results);

    } catch (\Exception $e) {
        \Log::error('getBillDetail error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

}
