<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tblsos;
use App\Models\tblcustomer;
use function Laravel\Prompts\table;

class salecontroller extends Controller
{

    public function home()
    {
        return view('home');
    }


    public function showLoginForm()
    {
        return view('sale.loginsale');
    }


    public function login(Request $request)
    {
        $request->validate([
            'so_number' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only('so_number', 'password');
    
        if ($credentials['so_number'] === '1' && $credentials['password'] === '1') {
            session([
                'logged_in' => true,
                'so_number' => $credentials['so_number'], // เก็บเลข so_number
            ]);
            return redirect()->route('sale.dashboard')->with('success', 'ล็อกอินสำเร็จ!');
        }
    
        return back()->withErrors(['sale.loginsale' => 'SO หรือรหัสผ่านไม่ถูกต้อง']);
    }


    public function dashboard()
    {
        return view('sale.dashboard');
    }


    public function insertdata()
    {
        return view('sale.insertdata');
    }


    public function logout()
{
    session()->flush(); // ลบข้อมูลในเซสชัน
    return redirect()->route("sale.loginsale")->with('success', 'คุณได้ออกจากระบบเรียบร้อยแล้ว!');
}




public function popup()
{
    return view('sale.txt');  
}


public function findData(Request $request)
{
    // รับค่า 'so_number' จากฟอร์ม
    $sonumber = $request->input('so_number');

    // ค้นหาข้อมูลจากตาราง tblsos ตาม 'so_number'
    $so = tblsos::where('so_id', $sonumber)->first();

    if ($so) {
        // ดึง 'customer_id' จาก tblsos
        $customer_id = $so->customer_id;

        // ใช้ 'customer_id' เพื่อค้นหาชื่อจากตาราง tblcustomer
        $customer = tblcustomer::where('customer_id', $customer_id)->first();

        // ตรวจสอบว่าพบข้อมูลลูกค้าหรือไม่
        if ($customer) {
            $customer_name = $customer->customer_name;
        } else {
            $customer_name = 'ไม่พบข้อมูลลูกค้า';
        }

        // ส่งข้อมูลไปยัง view
        return view('sale.insertdata', compact('so', 'customer_name'));
    } else {
        // ถ้าไม่พบข้อมูล SO ที่ตรงกับหมายเลขที่กรอก
        return redirect()->back()->with('error', 'ไม่พบข้อมูล SO ที่ตรงกับหมายเลขที่กรอก');
    }
}
}

