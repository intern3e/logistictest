<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tblsos;
use App\Models\tblcustomer;
use App\Models\bill_detail;
use App\Models\so_item_id;
use App\Models\Bill;
use Illuminate\Support\Facades\Log;
use App\Models\BillDetail;
use function Laravel\Prompts\alert;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use FPDF;
use Barryvdh\DomPDF\Facade as PDF;

class SaleController extends Controller
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
            'emp_name' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only('emp_name', 'password');
    
        if ($credentials['emp_name'] === '1' && $credentials['password'] === '1') {
            session([
                'logged_in' => true,
                'emp_name' => $credentials['emp_name'], 
            ]);
            return redirect()->route('sale.dashboard')->with('success', 'ล็อกอินสำเร็จ!');
        }
    
        return back()->withErrors(['sale.loginsale' => 'SO หรือรหัสผ่านไม่ถูกต้อง']);
    }

public function dashboard(Request $request)
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

        return view('sale.dashboard', compact('bill', 'message'));
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
public function fetchFormType(Request $request)
{
    $customer_id = $request->input('customer_id');
    $bill = DB::table('tblbill')
                ->where('customer_id', $customer_id)
                ->orderBy('time', 'desc') // หรือจะใช้ 'so_detail_id' ก็ได้ ถ้าเพิ่มขึ้นเรื่อยๆ
                ->first(); // ดึงแถวล่าสุด

    if ($bill) {
        return response()->json([
            'formtype' => $bill->formtype,
            'customer_la_long' => $bill->customer_la_long
        ]);
    } else {
        return response()->json(['formtype' => null]); // ถ้าไม่พบข้อมูล
    }
}

  public function insert(Request $request)
{date_default_timezone_set('Asia/Bangkok');

    DB::beginTransaction();
    try {
        $request->validate([
            'so_id' => 'required|string|max:255',
            'ponum' => 'string|max:255',
            'billtype' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_tel' => 'nullable|string|max:255',
            'customer_address' => 'required|string|max:255',
            'customer_la_long' => 'required|string|max:255',
            'emp_name' => 'required|string|max:255',
            'sale_name' => 'required|string|max:255',
            'date_of_dali' => 'required|date',
            'notes' => 'nullable|string',
            'billid' => 'required|string|max:255',
            'item_id' => 'required|array',
            'item_id.*' => 'string',
            'item_name' => 'required|array',
            'item_name.*' => 'string',
            'item_quantity' => 'required|array',
            'item_quantity.*' => 'string',
            'unit_price' => 'required|array',
            'unit_price.*' => 'string',
            'status' => 'nullable|array',
            'statuspdf' => 'nullable|array',
            'POdocument' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        // สร้าง so_detail_id แบบ 3E(เลขท้ายพ.ศ.)(เดือน)X0001
 $currentYear = date('y');           // เช่น 25 (ปี ค.ศ. 2025)
$currentMonth = date('m');          // เช่น 05
$currentTime = date('Hi');          // เช่น 1620
$prefix = "{$currentYear}{$currentMonth}-{$currentTime}-"; // เช่น 2505-1620-

// หาเลข running number ล่าสุดที่มี prefix ตรงกัน
$latestBill = Bill::where('so_detail_id', 'like', $prefix . '%')
    ->orderBy(DB::raw('CAST(SUBSTRING(so_detail_id, -4) AS UNSIGNED)'), 'desc')
    ->first();

if ($latestBill) {
    $latestNumber = (int) substr($latestBill->so_detail_id, -4);
    $nextNumber = $latestNumber + 1;
} else {
    $nextNumber = 1;
}

// สร้าง so_detail_id ที่สมบูรณ์
$so_detail_id = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

// ตรวจสอบว่ามี so_detail_id นี้อยู่แล้วหรือไม่
$exists = Bill::where('so_detail_id', $so_detail_id)->exists();
if ($exists) {
    $i = $nextNumber + 1;
    do {
        $so_detail_id = $prefix . str_pad($i, 4, '0', STR_PAD_LEFT);
        $exists = Bill::where('so_detail_id', $so_detail_id)->exists();
        $i++;
    } while ($exists);
}
        $customer_id = $request->input('customer_id');
        $formType = $request->input('formtype');
        $customer_la_long = $request->input('customer_la_long');



        // **🔹 Insert into Bills**
        $bill = new Bill();
        $bill->so_detail_id = $so_detail_id; // ใช้ so_detail_id ที่สร้างขึ้นใหม่
        $bill->so_id = $request->input('so_id');
        $bill->ponum = $request->input('ponum');
        $bill->status = 0;
        $bill->statuspdf = 0;
        $bill->customer_id = $request->input('customer_id');
        $bill->customer_name = $request->input('customer_name');
        $bill->customer_tel = $request->input('customer_tel');
        $bill->customer_address = $request->input('customer_address');
        $bill->customer_la_long = $request->input('customer_la_long');
        $bill->notes = $request->input('notes');
        $bill->date_of_dali = $request->input('date_of_dali');
        $bill->emp_name = $request->input('emp_name');
        $bill->sale_name = $request->input('sale_name');
        $bill->billtype = $request->input('billtype');
        $bill->formtype = $request->input('formtype');
        $bill->billid = $request->input('billid');
            
        if ($request->hasFile('POdocument')) {
            $file = $request->file('POdocument');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());

            // เปลี่ยนนามสกุลเป็น .pdf เสมอ เพราะ frontend แปลงมาแล้ว
            $filename = $so_detail_id . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.pdf';

            $path = 'public/po_documents';
            $file->storeAs($path, $filename);

            $bill->POdocument = $filename;
        }
        // บันทึกข้อมูล bill
        $bill->save();
        
        $item_ids = $request->input('item_id');
        $item_names = $request->input('item_name');
        $item_quantities = $request->input('item_quantity');
        $unit_price = $request->input('unit_price');
        $status_checked = $request->input('status', []);

        // **🔹 Insert into Bill Details**
        foreach ($item_ids as $index => $item_id) {
            if (!isset($status_checked[$index])) {
                continue;  // ข้ามถ้าผู้ใช้ไม่ได้ติ๊กเลือก
            }
            $bill_detail = new Bill_detail();
            $bill_detail->so_detail_id = $so_detail_id; // ใช้ $so_detail_id ที่สร้างใหม่
            $bill_detail->so_id = $request->input('so_id');
            $bill_detail->item_id = $item_ids[$index];
            $bill_detail->item_name = $item_names[$index];
            $bill_detail->quantity = $item_quantities[$index];
            $bill_detail->unit_price = $unit_price[$index];
            $bill_detail->save();
        }
        DB::commit();
        return response()->json(['success' => 'เปิดบิลสำเร็จ เลขที่บิล:' . $so_detail_id]);
        Log::info('so_detail_id: ' . $so_detail_id);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error($e->getMessage());
        return response()->json(['error' => 'เกิดข้อผิดพลาด:ใส่ข้อมูลให้ครบถ้วน ' . $e->getMessage()], 500);
    }
}      
public function updateBill(Request $request) {
        Log::info('📥 รับข้อมูลจาก JavaScript:', $request->all());
    
        $so_detail_id = $request->so_detail_id;
        $items = $request->items;
    
        foreach ($items as $item) {
            Log::info("🔄 อัปเดต item_id: {$item['item_id']} จำนวน: {$item['quantity']}");
    
            DB::table('bill_detail')
                ->where('so_detail_id', $so_detail_id)
                ->where('item_id', $item['item_id'])
                ->update(['quantity' => $item['quantity']]);
        }
    
        Log::info('✅ อัปเดตเสร็จสิ้น');
        return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    }
    
public function deleteBill($so_detail_id)
    {
        try {
            // หาบิลที่มี so_detail_id ตรงกัน
            $bill = Bill::where('so_detail_id', $so_detail_id)->first();
            
            if (!$bill) {
                return response()->json(['error' => 'ไม่พบบิล'], 404);
            }
    
            // ลบรายการสินค้าจาก bill_detail ที่มี so_detail_id ตรงกับบิล
            bill_detail::where('so_detail_id', $so_detail_id)->delete();
    
            // ลบบิลจาก tblbill โดยใช้ so_detail_id
            Bill::where('so_detail_id', $so_detail_id)->delete();
    
            return response()->json(['success' => 'ลบบิลสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

}

