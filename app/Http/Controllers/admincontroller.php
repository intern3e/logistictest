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
    public function dashboard()
    {
        $bill = Bill::orderBy('so_detail_id', 'desc')->get();
        return view('admin.dashboardadmin', compact('bill'));
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



}
