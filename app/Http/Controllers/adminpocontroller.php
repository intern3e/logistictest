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
    public function historypo(Request $request)
    {
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
        
        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $pobill = Pobills::whereDate('time', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('po_detail_id', 'desc')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($pobill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $pobill = Pobills::orderBy('po_detail_id', 'desc')
                        ->get();
        }

        return view('po.historypo', compact('pobill', 'message'));
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