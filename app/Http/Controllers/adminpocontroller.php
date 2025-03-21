<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pobills;
use Illuminate\Support\Facades\DB;

class AdminpoController extends Controller
{
    public function dashboard(Request $request)
    {
        $pobill = Pobills::all();  // Fetch the data
        return view('po.adminpo', compact('pobill'));  // Pass data to the view
    }

    public function dashboardpo()
    {
        $pobill = Pobills::all();  // Fetch the data
        return view('po.adminpo', compact('pobill'));  // Pass data to the view
    }
    public function historypo()
    {
        $pobill = poBills::orderBy('po_detail_id', 'desc')->get();
        return view('po.historypo', compact('pobill'));
    }
   
    public function updateStatus(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $poDetailIds = $request->input('poDetailIds');
    if (empty($poDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No PO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('pobills')
            ->whereIn('po_detail_id', $poDetailIds)
            ->update(['status' => 1]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
    
}