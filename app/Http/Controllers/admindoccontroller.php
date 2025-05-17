<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Docbills;
use Illuminate\Support\Facades\Validator;

class admindoccontroller extends Controller
{
    public function dashboard(Request $request)
        {
            $docbill = docbills::all();  // Fetch the data
            return view('document.dashboarddoc', compact('docbill'));  // Pass data to the view
        }

    public function dashboarddoc(Request $request)
    {
        $docbill = Docbills::orderBy('doc_id', 'desc')->get();
        $message = null;

        return view('document.admindoc', compact('docbill', 'message'));
    }

    public function dashboarddocroute(Request $request)
        {
            $date = $request->get('date');
            $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
            
            // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือกs
            if ($date) {
                $docbill = Docbills::whereDate('time', $date)  
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

            return view('document.admindocroute', compact('docbill', 'message'));
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
        public function statuspdfdoc(Request $request)
        {
            $docDetailIds  = $request->input('docDetailIds');
        
            // Update the status of the selected SO details to 1
            DB::table('docbills')
                ->whereIn('doc_id', $docDetailIds )
                ->update(['statuspdf' => 1]);
        
            return response()->json(['success' => true]);
        }
    public function updateStatusdocback(Request $request)
        {
            $doc_id = $request->input('doc_id');  // ← เปลี่ยนตรงนี้

            if (empty($doc_id)) {
                return response()->json(['success' => false, 'message' => 'No doc_id provided'], 400);
            }

            try {
                \Log::info('Updating doc_id list:', $doc_id);

                DB::table('docbills')
                    ->whereIn('doc_id', $doc_id)
                    ->update([
                        'statuspdf' => "0",
                        'status' => "0"
                    ]);

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                \Log::error('Update failed:', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

 public function updateDeliveryDate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'doc_id' => 'required|string',
                'new_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
                ], 422);
            }

            $existing = DB::table('docbills')->where('doc_id', $request->doc_id)->first();

            if (!$existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลที่ต้องการอัปเดต'
                ], 404);
            }

            $updated = DB::table('docbills')
                ->where('doc_id', $request->doc_id)
                ->update([
                    'datestamp' => $request->new_date,
                    'time' => now()
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'อัปเดตวันที่ส่งของเรียบร้อยแล้ว'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถอัปเดตข้อมูลได้'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating delivery date: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
}

