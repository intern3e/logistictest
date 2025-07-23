<?php

namespace App\Http\Controllers;
use App\Models\Bill;
use App\Models\docBills;
use App\Models\pobills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class checkbillController extends Controller
{
    public function dashboardsolve(Request $request)
{
    $bills = Bill::orderBy('so_detail_id', 'desc')->get();
    $poBills = pobills::orderBy('po_detail_id', 'desc')->get();
    $docBills = docBills::orderBy('doc_id', 'desc')->get();
    $items = $bills->concat($poBills)->concat($docBills);

    return view('checkbill.dashboardcheckbillsolve', compact('items'));

}
public function dashboard(Request $request)
{
    $bills = Bill::orderBy('so_detail_id', 'desc')->get();
    $poBills = pobills::orderBy('po_detail_id', 'desc')->get();
    $docBills = docBills::orderBy('doc_id', 'desc')->get();
    // all
    $items = $bills->concat($poBills)->concat($docBills);

    return view('checkbill.dashboardcheckbill', compact('items'));

}
public function updatestatusdeli(Request $request)
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

        $item->statusdeli = '1'; 
        $item->save();

        return response()->json(['status' => 'success', 'message' => 'อัปเดตสถานะจัดส่งสำเร็จ']);
    } catch (\Throwable $e) {
        \Log::error('Update statusdeli Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], 500);
    }
}


}