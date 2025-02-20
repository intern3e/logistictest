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

class admincontroller extends Controller
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
                        ->with('customer')
                        ->get();
            
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            } 
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->with('customer')
                        ->get();
        }
    
        return view('admin.dashboardadmin', compact('bill', 'message'));
    }


    public function history()
    {
        $bill = Bill::orderBy('so_detail_id', 'desc')->get();
        return view('admin.history', compact('bill'));
    }
    public function logoutadmin()
{
    session()->flush(); // ลบข้อมูลในเซสชัน
    return redirect()->route("admin.loginadmin")->with('success', 'คุณได้ออกจากระบบเรียบร้อยแล้ว!');
}
public function getBillDetail($so_detail_id)
    {
        $billDetails = Bill_Detail::where('so_detail_id', $so_detail_id)->get();
        
        return response()->json($billDetails);
    }

    public function updateStatus(Request $request)
    {
        $soDetailIds = $request->input('soDetailIds');
    
        // Update the status of the selected SO details to 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['status' => 1]);
    
        return response()->json(['success' => true]);
    }

}
