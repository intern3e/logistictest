<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tblsos;
use App\Models\tblcustomer;
use App\Models\bill_detail;
use App\Models\so_item_id;
use App\Models\Bill;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    
    public function showLoginForm()
    {
        return view('admin.loginadmin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_admin' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only('id_admin', 'password');
    
        if ($credentials['id_admin'] === '2' && $credentials['password'] === '2') {
            session([
                'logged_in' => true,
                'id_admin' => $credentials['id_admin'], 
            ]);
            return redirect()->route('admin.dashboardadmin')->with('success', 'ล็อกอินสำเร็จ!');
        }
    
        return back()->withErrors(['admin.loginadmin' => 'ID หรือรหัสผ่านไม่ถูกต้อง']);
    }
    public function dashboard(Request $request)
    {
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
        
        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }
    
        return view('admin.dashboardadmin', compact('bill', 'message'));
    }
    public function dashboardpdf(Request $request)
    {
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
        
        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }
    
        return view('admin.dashboardadminpdf', compact('bill', 'message'));
    }

    public function adminroute(Request $request)
    {
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
        
        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }
    
        return view('admin.adminroute', compact('bill', 'message'));
    }

    public function history(Request $request)
    {
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message
        
        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('time', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.history', compact('bill', 'message'));
    }

    public function logoutadmin()
{
    session()->flush(); // ลบข้อมูลในเซสชัน
    return redirect()->route("admin.loginadmin")->with('success', 'คุณได้ออกจากระบบเรียบร้อยแล้ว!');
}
public function updateStatus(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['status' => 1]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateStatuspdf(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['statuspdf' => 1]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateStatuspdfback(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update([
                'statuspdf' => 1,
                'status' => 0
            ]);


        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
// app/Http/Controllers/BillController.php
public function updateBillIssue(Request $request)
{
    $request->validate([
        'so_detail_id' => 'required',
        'bill_issue_no' => 'required|string|max:255',
    ]);

    $bill = Bill::where('so_detail_id', $request->so_detail_id)->first();

    if (!$bill) {
        return response()->json(['message' => 'ไม่พบข้อมูล so_detail_id'], 404);
    }

    $bill->bill_issue_no = $request->bill_issue_no;
    $bill->save();

    return response()->json(['message' => 'อัปเดตสำเร็จ']);
}

public function updateStatuspdf2(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
                ->update([
                        'statuspdf' => 2,
                        'status' => 1
                    ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateDeliveryDate(Request $request) 
{
    try {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validator = Validator::make($request->all(), [
            'so_detail_id' => 'required',
            'new_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
            ], 422);
        }

        // ตรวจสอบก่อนว่ามีข้อมูลในฐานข้อมูลหรือไม่
        $existing = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->first();

        if (!$existing) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลที่ต้องการอัปเดต'
            ], 404);
        }

        // อัปเดตข้อมูล
        $updated = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->update([
                'date_of_dali' => $request->new_date,
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
        // บันทึกข้อผิดพลาดลง log
        \Log::error('Error updating delivery date: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], 500);
    }
}
public function upload(Request $request)
{
    $request->validate([
        'pdffile' => 'required|mimes:pdf|max:10240' 
    ]);

    $file = $request->file('pdffile');

    // ดึงชื่อไฟล์ต้นฉบับ เช่น 46805-00708.pdf
    $originalName = $file->getClientOriginalName();

    // กำหนด path ปลายทาง เช่น storage/app/public/doc_document
    $destinationPath = storage_path('app/public/doc_document');

    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0777, true);
    }

    // ย้ายไฟล์โดยไม่เปลี่ยนชื่อ
    $file->move($destinationPath, $originalName);

    return back()->with('success', 'อัปโหลดไฟล์ ' . $originalName . ' เรียบร้อยแล้ว!');
}
public function uploadBillIssue(Request $request)
{
    $request->validate([
        'bill_issue_no' => 'required|string',
        'pdffilebillissue' => 'required|mimes:pdf|max:10240' 
    ]);

    $billIssueNo = $request->bill_issue_no;
    $file = $request->file('pdffilebillissue');

    $filename = $billIssueNo . '.pdf';

    // จัดเก็บไฟล์ใน storage/app/public/billissue_document
    $file->storeAs('public/billissue_document', $filename);

    return back()->with('message', '✅ อัปโหลดไฟล์สำเร็จ');
}
public function updateStatuspdfcan(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['statuspdf' => '6']);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
}