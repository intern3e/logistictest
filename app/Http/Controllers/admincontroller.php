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
                'statuspdf' => 0,
                'status' => 0
            ]);


        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateBillId(Request $request)
{
    $request->validate([
        'so_detail_id' => 'required|exists:tblbill,so_detail_id',
        'billid' => 'required|string|max:50'
    ]);

    try {
        $affectedRows = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->update(['billid' => $request->billid]);

        if ($affectedRows === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No records updated. Maybe bill ID is the same or SO Detail ID not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'affectedRows' => $affectedRows,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update bill ID',
            'error' => $e->getMessage()
        ], 500);
    }
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
            ->update(['statuspdf' => 2]);

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
                'time' => now() // หรือใช้ $request->new_date หากไม่ต้องการเวลา ณ ขณะนั้น
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
}