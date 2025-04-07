<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Docbills;

class admindoccontroller extends Controller
{
    public function dashboard(Request $request)
    {
        $docbill = docbills::all();  // Fetch the data
        return view('document.dashboarddoc', compact('docbill'));  // Pass data to the view
    }

    public function dashboarddoc()
    {
        $docbill = docbills::all();  // Fetch the data
        return view('document.admindoc', compact('docbill'));  // Pass data to the view
    }
    public function historydoc(Request $request)
    {
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
        
        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือกs
        if ($date) {
            $docbill = Docbills::whereDate('time', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('doc_id', 'desc')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($docbill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $docbill = Docbills::orderBy('doc_id', 'desc')
                        ->get();
        }

        return view('document.historydoc', compact('docbill', 'message'));
    }
   
    public function updateStatus(Request $request)
    {
        $docDetailIds  = $request->input('docDetailIds');
    
        // Update the status of the selected SO details to 1
        DB::table('docbills')
            ->whereIn('doc_id', $docDetailIds )
            ->update(['status' => 1]);
    
        return response()->json(['success' => true]);
    }
    
}
