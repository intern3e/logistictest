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


    $cust = DB::table('custdetail')->where('idcust', $customer_id)->first();
    $formtype = $cust->formtype ?? null;


    $bill = DB::table('tblbill')
                ->where('customer_id', $customer_id)
                ->orderBy('time', 'desc')
                ->first();

    $customer_la_long = $bill->customer_la_long ?? '';

    // ✅ 3. ถ้า formtype ยังไม่เจอ → ลองใช้จาก tblbill
    if (!$formtype && $bill) {
        $formtype = $bill->formtype ?? null;
    }

    return response()->json([
        'formtype' => $formtype,
        'customer_la_long' => $customer_la_long
    ]);
}


 public function insert(Request $request)
{
    date_default_timezone_set('Asia/Bangkok');

    DB::beginTransaction();
    try {
        $request->validate([
            'so_id' => 'required|string|max:255',
            'solve' => 'nullable|string|max:255',
            'ponum' => 'nullable|string|max:255',
            'billtype' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_tel' => 'nullable|string|max:255',
            'customer_address' => 'required|string|max:255',
            'customer_la_long' => 'required|string|max:255',
            'emp_name' => 'required|string|max:255',
            'sale_name' => 'required|string|max:255',
            'contactso' => 'required|string|max:255',
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
            'statusdeli' => 'nullable',
            'statuspdf' => 'nullable|array',
            'POdocument' => 'nullable|file|mimes:pdf|max:20480',
            'formtype' => ['required', 'string', 'max:255', 'not_in:ไม่มีข้อมูล'],
            'formtype.not_in' => 'กรุณาเลือกประเภทฟอร์มให้ถูกต้อง',
        ]);

        // 🔸 รหัสเดือนปัจจุบัน: 2505 (yyMM)
        $prefix = date('ym'); // เช่น 2505

        // 🔸 ดึงเลขล่าสุดในเดือนเดียวกัน
        $searchPattern = $prefix . '-%'; // ค้นหาเฉพาะเดือนนั้น
        $latestBill = Bill::where('so_detail_id', 'like', $searchPattern)
            ->orderBy(DB::raw('CAST(SUBSTRING(so_detail_id, -4) AS UNSIGNED)'), 'desc')
            ->first();

        if ($latestBill) {
            $latestNumber = (int) substr($latestBill->so_detail_id, -4);
            $nextNumber = $latestNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // 🔸 ใช้ datetime stamp แยกเพื่อให้ไม่ซ้ำ แต่ไม่เกี่ยวกับการรันเลข
        $datetimePart = date('dHi'); // เช่น 29120133

        // 🔸 สร้าง so_detail_id: 2505-29120133-0001
        $so_detail_id = "{$prefix}-{$datetimePart}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // 🔸 ป้องกันการชน (ซ้ำ) กรณีเขียนซ้อนเร็วมาก
        while (Bill::where('so_detail_id', $so_detail_id)->exists()) {
            $nextNumber++;
            $so_detail_id = "{$prefix}-{$datetimePart}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // 🔸 Insert into Bills
        $bill = new Bill();
        $bill->so_detail_id = $so_detail_id;
        $bill->so_id = $request->input('so_id');
        $bill->ponum = $request->input('ponum');
        $bill->status = 0;
        $bill->statuspdf = 0;
        $bill->statusdeli = 0;
        $bill->customer_id = $request->input('customer_id');
        $bill->customer_name = $request->input('customer_name');
        $bill->contactso = $request->input('contactso');
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

        // 🔸 จัดการไฟล์เอกสาร
        if ($request->hasFile('POdocument')) {
            $file = $request->file('POdocument');
            $originalName = $file->getClientOriginalName();
            $filename = $so_detail_id .'.pdf';

            $file->storeAs('public/po_documents', $filename);
            $bill->POdocument = $filename;
        }

        $bill->save();
        DB::table('custdetail')
            ->where('idcust', $request->input('customer_id'))
            ->update(['formtype' => $request->input('formtype')]);
        // 🔸 Insert into Bill Details
        $item_ids = $request->input('item_id');
        $item_names = $request->input('item_name');
        $item_quantities = $request->input('item_quantity');
        $unit_price = $request->input('unit_price');
        $status_checked = $request->input('status', []);

        foreach ($item_ids as $index => $item_id) {
            if (!isset($status_checked[$index])) {
                continue; // ถ้าไม่ได้ติ๊กเลือก
            }

            $bill_detail = new Bill_detail();
            $bill_detail->so_detail_id = $so_detail_id;
            $bill_detail->so_id = $request->input('so_id');
            $bill_detail->item_id = $item_ids[$index];
            $bill_detail->item_name = $item_names[$index];
            $bill_detail->quantity = $item_quantities[$index];
            $bill_detail->unit_price = $unit_price[$index];
            $bill_detail->save();
        }

        DB::commit();
        Log::info('so_detail_id: ' . $so_detail_id);
        return response()->json(['success' => 'เปิดบิลสำเร็จ เลขที่บิล: ' . $so_detail_id]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error($e->getMessage());
        return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
    }
}public function updateBill(Request $request) {
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
public function fetchContactSo(Request $request)
{
    $bill = Bill::where('customer_id', $request->customer_id)
                ->orderBy('time', 'desc') 
                ->first();

    return response()->json([
        'contactso' => $bill?->contactso,
    ]);
}
 public function checkBillId(Request $request)
    {
        $billid = $request->input('billid');

        $existingBill = Bill::where('billid', $billid)->first();

        if ($existingBill) {
            return response()->json([
                'exists' => true,
                'emp_name' => $existingBill->emp_name,
                'billid' => $existingBill->billid,
            ]);
        }

        return response()->json([
            'exists' => false,
        ]);
    }

}


