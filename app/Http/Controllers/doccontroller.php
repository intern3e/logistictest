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
        $validator = Validator::make($request->all(), [
            'so_id' => 'nullable',
            'doctype' => 'required',
            'emp_name' => 'required',
            'contact_name' => 'required',
            'customer_name' => 'required',
            'customer_tel' => 'required|string',
            'customer_address' => 'nullable|string',
            'customer_la_long' => 'required|string',
            'revdate' => 'required',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        $currentYear = date('Y') + 543;
        $currentYear = substr($currentYear, -2); 
        $currentMonth = date('m'); // เดือนปัจจุบัน 2 หลัก (เช่น 04)
        $prefix = "T{$currentYear}{$currentMonth}X"; // สร้าง prefix เช่น 3E6804X
        
        // หาเลข running number ล่าสุด
        $latestBill =Docbills::where('doc_id', 'like', $prefix . '%')
                        ->orderBy(DB::raw('CAST(SUBSTRING(doc_id, 8) AS UNSIGNED)'), 'desc')
                        ->first();
        
        if ($latestBill) {
            // ถ้ามีแล้ว ดึงเลขล่าสุดและเพิ่มอีก 1
            $latestNumber = (int) substr($latestBill->doc_id, -4);
            $nextNumber = $latestNumber + 1;
        } else {
            // ถ้ายังไม่มี เริ่มที่ 1
            $nextNumber = 1;
        }
        
        // สร้าง doc_id ในรูปแบบที่ต้องการ (เช่น )
        $doc_id = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // ตรวจสอบว่ามี doc_id นี้อยู่แล้วหรือไม่ เพื่อป้องกันการซ้ำ
        $exists = Docbills::where('doc_id', $doc_id)->exists();
        if ($exists) {
            // ถ้ามีแล้ว ให้เพิ่มเลขต่อไปเรื่อยๆ จนกว่าจะไม่ซ้ำ
            $i = $nextNumber + 1;
            do {
                $doc_id= $prefix . str_pad($i, 4, '0', STR_PAD_LEFT);
                $exists = Docbills::where('doc_id', $doc_id)->exists();
                $i++;
            } while ($exists);
        }

        try {
            DB::table('docbills')->insert([
                'so_id' => $request->so_id,
                'doctype' => $request->doctype,
                'doc_id' => $doc_id,
                'emp_name' => $request->emp_name,
                'status' => 0,
                'contact_name' => $request->contact_name,
                'customer_name' => $request->customer_name,
                'customer_tel' => $request->customer_tel,
                'customer_address' => $request->customer_address,
                'customer_la_long' => $request->customer_la_long,   
                'revdate' => $request->revdate,
                'notes' => $request->notes,
            ]);

            return response()->json(['success' => 'บันทึกข้อมูลสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()], 500);
        }
    }
    public function getdocBillDetail($doc_id)
    {
        try {
            $docbillDetails = docbills::where('doc_id', $doc_id)->get();
            
            if ($docbillDetails->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($docbillDetails);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
}
