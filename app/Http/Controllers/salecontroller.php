<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tblsos;
use App\Models\tblcustomer;
use App\Models\so_item_id;
use App\Models\Bill;
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
        // ดึงข้อมูลบิลพร้อมกับข้อมูลลูกค้า
        $bill = Bill::with('customer')->get();
        return view('sale.dashboard', compact('bill'));
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


// Show the form
public function showForm()
{
    return view('sale.insertdata');
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

        // ค้นหาข้อมูลลูกค้า
        $customer = tblcustomer::where('customer_id', $customer_id)->first();

        // ค้นหาสินค้าทั้งหมดที่เกี่ยวข้องกับ so_id
        $so_items = so_item_id::where('so_id', $sonumber)->get(); // ดึงข้อมูลทั้งหมด

        // ตรวจสอบว่าพบข้อมูลลูกค้าหรือไม่
        if ($customer) {
            $customer_name = $customer->customer_name;
            $customer_tel = $customer->customer_tel;
            $customer_address = $customer->customer_address;
            $customer_la_long = $customer->customer_la_long;
        } else {
            $customer_name = 'ไม่พบข้อมูลลูกค้า';
            $customer_tel = '-'; 
            $customer_address = '-';
            $customer_la_long = '-';
        }

        // ตรวจสอบว่ามีสินค้าไหม
        if ($so_items->isEmpty()) {
            $items = [['item_id' => 'ไม่พบข้อมูลสินค้า']];
        } else {
            $items = $so_items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,  
                    'item_quantity' => $item->item_quantity,    
                    'item_unit_price' => $item->item_unit_price  
                ];
            })->toArray(); 
        }
        
        // ส่งข้อมูลไปยัง View
        return view('sale.insertdata', compact('so', 'customer_name', 'customer_tel', 'customer_address', 'customer_la_long', 'items'));
    } else {
        // ถ้าไม่พบข้อมูล SO ที่ตรงกับหมายเลขที่กรอก
        return redirect()->route('sodetail')->with('error', 'ไม่พบข้อมูล SO ที่ตรงกับหมายเลขที่กรอก');
    }
}

public function insert(Request $request)
{
    try {
        // ตรวจสอบความถูกต้องของข้อมูลที่ส่งมา
        $request->validate([
            'so_id' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'date_of_dali' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // ดึงข้อมูลจาก request
        $so_id = $request->input('so_id');
        $customer_id = $request->input('customer_id');
        $date_of_dali = $request->input('date_of_dali');
        $notes = $request->input('notes');

        // ตรวจสอบว่า SO มีอยู่ในฐานข้อมูลหรือไม่
        $so = tblsos::where('so_id', $so_id)->first();
        if (!$so) {
            return response()->json(['error' => 'ไม่พบข้อมูล SO'], 400);
        }

        // สร้างบิลใหม่
        $bill = new Bill();
        $bill->timestamps = false;
        $bill->so_id = $so_id;
        $bill->status = 0; // 0 = รออนุมัติ
        $bill->customer_id = $customer_id;
        $bill->notes = $notes;
        $bill->date_of_dali = $date_of_dali;
        $bill->save();


        return response()->json(['success' => 'เปิดบิลสำเร็จ']);

    } catch (\Exception $e) {
        return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
    }
}


}

