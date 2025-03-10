<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\docbills;
use App\Models\docbillsdetail;
use Illuminate\Support\Facades\DB;

class doccontroller extends Controller
{
    public function dashboard(Request $request)
    {
        return view('document.dashboarddoc');
    }
    public function dashboarddoc()
    {
        $docbill = docbills::all();

        return view('document.dashboarddoc', compact('docbill'));
    }
    public function insertdoc()
    {
        return view('document.insertdoc');
    }
    public function insertDocu(Request $request)
    {
        DB::beginTransaction(); // เริ่มต้นการทำธุรกรรมในฐานข้อมูล

        try {
            // Validation ข้อมูลจากฟอร์ม
            $request->validate([
                'so_id' => 'required|string|max:255',
                'customer_id' => 'required|string|max:255',
                'customer_address' => 'required|string|max:255',
                'customer_la_long' => 'required|string|max:255',
                'emp_name' => 'required|string|max:255',
                'sale_name' => 'required|string|max:255',
                'date_of_dali' => 'required|date',
                'notes' => 'nullable|string',
                'item_id' => 'required|array',
                'item_id.*' => 'string',
                'item_name' => 'required|array',
                'item_name.*' => 'string',
                'item_quantity' => 'required|array',
                'item_quantity.*' => 'integer|min:1',
                'item_unit_price' => 'required|array',
                'item_unit_price.*' => 'numeric|min:0',
                'status' => 'required|array',
            ]);

            // สร้างบิลใหม่
            $bill = new DocBills();
            $bill->so_id = $request->input('so_id');
            $bill->status = 0;  // สถานะเริ่มต้น
            $bill->customer_id = $request->input('customer_id');
            $bill->customer_tel = $request->input('customer_tel');
            $bill->customer_address = $request->input('customer_address');
            $bill->customer_la_long = $request->input('customer_la_long');
            $bill->notes = $request->input('notes');
            $bill->date_of_dali = $request->input('date_of_dali');
            $bill->emp_name = $request->input('emp_name');
            $bill->sale_name = $request->input('sale_name');
            $bill->save(); // บันทึกบิลลงฐานข้อมูล

            // รับข้อมูลจากฟอร์มที่เกี่ยวกับสินค้า
            $so_detail_id = $bill->id; // ใช้ ID ของบิลที่ถูกบันทึก

            $item_ids = $request->input('item_id');
            $item_names = $request->input('item_name');
            $item_quantities = $request->input('item_quantity');
            $item_unit_prices = $request->input('item_unit_price');
            $status_checked = $request->input('status', []);

            // ลูปผ่านรายการสินค้าเพื่อบันทึกรายละเอียด
            foreach ($status_checked as $index => $value) {
                $bill_detail = new DocBillsDetail();
                $bill_detail->so_detail_id = $so_detail_id;
                $bill_detail->so_id = $request->input('so_id');
                $bill_detail->item_id = $item_ids[$index];
                $bill_detail->item_name = $item_names[$index];
                $bill_detail->quantity = $item_quantities[$index];
                $bill_detail->unit_price = $item_unit_prices[$index];
                $bill_detail->save(); // บันทึกรายละเอียดสินค้า
            }

            DB::commit(); // คอมมิตธุรกรรมในฐานข้อมูล
            return response()->json(['success' => 'เปิดบิลสำเร็จ']); // ส่งข้อความกลับ
        } catch (\Exception $e) {
            DB::rollBack(); // หากเกิดข้อผิดพลาดให้ย้อนกลับ
            return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500); // ส่งข้อความผิดพลาด
        }
    }
}

