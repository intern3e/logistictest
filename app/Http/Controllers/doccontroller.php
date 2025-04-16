<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docbills;
use App\Models\docbillsdetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class DocController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('document.dashboarddoc');
    }

    public function dashboarddoc(Request $request)
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

        return view('document.dashboarddoc', compact('docbill', 'message'));
    }

    public function insertdoc()
    {
        return view('document.insertdoc');
    }
    public function insertDocu(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'emp_name' => 'required|string|max:255',
                'doctype' => 'required|string|max:255',
                'com_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_tel' => 'nullable|string|max:255',
                'com_address' => 'required|string|max:255',
                'com_la_long' => 'required|string|max:255',
                'time' => 'required|date',
                'notes' => 'nullable|string',
            ]);
            $currentYear = date('Y') + 543;
            $currentYear = substr($currentYear, -2); 
            $currentMonth = date('m'); 
            $prefix = "T{$currentYear}{$currentMonth}X"; 
            
            $latestBill = Docbills::where('doc_id', 'like', $prefix . '%')
                            ->orderBy(DB::raw('CAST(SUBSTRING(doc_id, 8) AS UNSIGNED)'), 'desc')
                            ->first();
            
            if ($latestBill) {
                $latestNumber = (int) substr($latestBill->doc_id, -4);
                $nextNumber = $latestNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            $doc_id = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    
           
            $exists = Docbills::where('doc_id', $doc_id)->exists();
            if ($exists) {
                $i = $nextNumber + 1;
                do {
                    $doc_id = $prefix . str_pad($i, 4, '0', STR_PAD_LEFT);
                    $exists = Docbills::where('docid', $doc_id)->exists();
                    $i++;
                } while ($exists);
            }
    
            // **🔹 Insert into Bills**
            $doc = new Docbills();
            $doc->doc_id = $doc_id; // ใช้ so_detail_id ที่สร้างขึ้นใหม่
            $doc->status = 0;
            $doc->emp_name = $request->input('emp_name');
            $doc->com_name = $request->input('com_name');
            $doc->contact_name = $request->input('contact_name');
            $doc->contact_tel = $request->input('contact_tel');
            $doc->com_address = $request->input('com_address');
            $doc->com_la_long = $request->input('com_la_long');
            $doc->notes = $request->input('notes');
            $doc->time = $request->input('time');
            $doc->doctype = $request->input('doctype'); 

            $doc->save();
            $item_names = $request->input('item_name', []);
            $item_quantities = $request->input('item_quantity', []);
            $status_checked = $request->input('status', []);

            if (is_array($item_names) && count($item_names) > 0) {
                foreach ($item_names as $index => $item_name) {
                    if (!empty($item_name)) {
                        $doc_detail = new docbillsdetail();
                        $doc_detail->doc_id = $doc_id;
                        $doc_detail->item_name = $item_name;
                        $doc_detail->quantity = $item_quantities[$index] ?? 0;
                        $doc_detail->save();
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => 'เปิดบิลสำเร็จ เลขที่บิล:' . $doc_id]);
            Log::info('doc_id: ' . $doc_id);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => 'เกิดข้อผิดพลาด:ใส่ข้อมูลให้ครบถ้วน ' . $e->getMessage()], 500);
        }
    }

    public function getDocBillDetail($doc_id)
{
    try {
        // ดึงข้อมูลรายละเอียดของบิลจาก docbillsdetail
        $doc_details = Docbillsdetail::where('doc_id', $doc_id)->get();
        
        if ($doc_details->isEmpty()) {
            return response()->json([], 200); // ส่งคืน array ว่าง ถ้าไม่มีข้อมูล
        }

        return response()->json($doc_details, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'เกิดข้อผิดพลาด'], 500);
    }
}
}
