<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ng_shipment;

class DeliverytrackController extends Controller
{
    public function index(Request $request)
    {
        $date   = $request->get('date', '');
        $driver = $request->get('driver', '');
        $status = $request->get('status', 'ng');

        $query = ng_shipment::query()->latest('ng_date')->latest('id');

        // ✅ เปลี่ยน whereDate เป็น where ตรงๆ + รองรับทั้ง 2 format
        if ($date) {
            $query->where(function($q) use ($date) {
                $q->where('ng_date', $date)
                ->orWhereDate('ng_date', $date);
            });
        }
        
        if ($driver) $query->where('driver_name', $driver);
        if ($status) $query->where('status', $status);

        $shipments = $query->get();
        $drivers   = ng_shipment::distinct()->orderBy('driver_name')
                        ->pluck('driver_name')->filter()->values();

        return view('driver.deliverytrack', compact('shipments', 'drivers', 'date', 'driver', 'status'));
    }
    public function saveNewBill(Request $request, $id)
    {
        $request->validate([
            'new_bill_no' => 'required|string|max:50',
        ]);

        $shipment = ng_shipment::findOrFail($id);

        // ถ้าเป็น pending แล้ว → ล็อก ไม่ให้แก้
        if ($shipment->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'รายการนี้บันทึกแล้ว ไม่สามารถแก้ไขได้',
            ], 422);
        }

        $shipment->new_bill_no = trim($request->input('new_bill_no'));
        $shipment->status      = 'pending';
        $shipment->save();

        return response()->json([
            'success'     => true,
            'message'     => 'บันทึกเลขบิลใหม่เรียบร้อย',
            'new_bill_no' => $shipment->new_bill_no,
        ]);
    }
}