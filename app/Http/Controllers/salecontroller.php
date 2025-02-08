<?php

namespace App\Http\Controllers;
use App\Models\bill;
use App\Models\tblorder;
use App\Models\tblso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


public function searchSo(Request $request)
{
    // รับค่า so_id จากคำขอ (JavaScript ส่งเป็น JSON)
    $so_number = $request->so_id;

    // ค้นหาข้อมูลจากฐานข้อมูลที่มี so_id ตรงกับค่าที่ได้รับ
    $order = tblso::where('so_id', $so_number)->first();

    if ($order) {
        // ถ้ามีข้อมูล เลือกข้อมูลที่ต้องการส่งกลับ
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    } else {
        // ถ้าไม่พบข้อมูล
        return response()->json([
            'success' => false,
            'message' => 'ไม่พบเลขที่ SO นี้ในระบบ'
        ]);
    }
}}