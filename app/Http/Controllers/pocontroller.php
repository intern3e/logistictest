<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pobills;
use App\Models\PobillsDetail;

class PoController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('po.dashboardpo');
    }
    public function dashboardpo()
    {
        $pobill = Pobills::all();

        return view('po.dashboardpo', compact('pobill'));
    }

    public function insertpo()
    {
        return view('po.insertpo');
    }

    public function insertpobill(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'po_id' => 'required|string|max:255',
                'store_name' => 'required|string|max:255',
                'store_address' => 'required|string|max:255',
                'store_la_long' => 'required|string|max:255',
                'emp_name' => 'required|string|max:255',
                'recvDate' => 'required|date',
                'notes' => 'nullable|string',
                'item_id' => 'required|array',
                'item_id.*' => 'string',
                'item_name' => 'required|array',
                'item_name.*' => 'string',
                'item_quantity' => 'required|array',
                'item_quantity.*' => 'integer|min:1',
                'item_unit_price' => 'required|array',
                'item_unit_price.*' => 'numeric|min:0',
                'cartype' => 'required|string|max:255',
                'store_tel' => 'nullable|string|max:255' ,
                'status' => 'required|max:255', 
            ]);
    
            // Save to Pobills table
            $pobill = new Pobills();
            $pobill->po_id = $request->input('po_id');
            $pobill->status = $request->input('status');
            $pobill->cartype = $request->input('cartype');
            $pobill->store_name = $request->input('store_name'); 
            $pobill->store_tel = $request->input('store_tel');  
            $pobill->store_address = $request->input('store_address');
            $pobill->store_la_long = $request->input('store_la_long');
            $pobill->notes = $request->input('notes');
            $pobill->recvDate = $request->input('recvDate');
            $pobill->emp_name = $request->input('emp_name');
            $pobill->save();
    
            // Get Pobills ID
            $po_detail_id = $pobill->id;
    
            // Get items data
            $item_ids = $request->input('item_id');
            $item_names = $request->input('item_name');
            $item_quantities = $request->input('item_quantity');
            $item_unit_prices = $request->input('item_unit_price');
            // Save items to Pobills_Detail table
            foreach ($item_ids as $index => $item_id) {
                $pobill_detail = new PobillsDetail();
                $pobill_detail->po_detail_id = $po_detail_id;
                $pobill_detail->po_id = $request->input('po_id');
                $pobill_detail->item_id = $item_id;
                $pobill_detail->item_name = $item_names[$index];
                $pobill_detail->quantity = $item_quantities[$index];
                $pobill_detail->unit_price = $item_unit_prices[$index];
                $pobill_detail->save();
            }
    
            DB::commit();
            return response()->json(['success' => 'เปิดบิลสำเร็จ']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
    public function getpoBillDetail($po_detail_id)
    {
        try {
            $pobillDetails = PobillsDetail::where('po_detail_id', $po_detail_id)->get();
            
            if ($pobillDetails->isEmpty()) {
                return response()->json(["message" => "ไม่มีข้อมูล"], 200);
            }

            return response()->json($pobillDetails);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }


}   