<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docbills;
use App\Models\docbillsdetail;
use App\Models\SsoTicket;
use App\Models\UserAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocController extends Controller
{
    private function resolveSsoUser(Request $request, string $logTag): UserAuth
    {
        return $this->requireLogin($request, $logTag);
    }

    public function dashboard(Request $request)
    {
        $authUser = $this->resolveSsoUser($request, 'document.dashboard');
        $creator  = $authUser->name;
        return view('document.dashboarddoc', compact('creator'));
    }

    public function dashboarddoc(Request $request)
    {
        $authUser = $this->resolveSsoUser($request, 'document.dashboarddoc');
        $creator  = $authUser->name;

        $date   = $request->get('date');
        $search = trim((string) $request->get('search', ''));
        $message = null;

        if ($search !== '') {
            // ค้นหาเลขที่บิล: หาได้ทุกวัน ไม่จำกัดเฉพาะวันที่เลือกไว้
            $docbill = Docbills::where('doc_id', 'like', '%' . $search . '%')
                        ->orderBy('doc_id', 'desc')
                        ->limit(500)
                        ->get();

            if ($docbill->isEmpty()) {
                $message = 'ไม่พบเลขที่บิลที่ค้นหา';
            }
        } elseif ($date) {
            $docbill = Docbills::whereDate('time', $date)
                        ->orderBy('doc_id', 'desc')
                        ->get();

            if ($docbill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            $docbill = Docbills::orderBy('doc_id', 'desc')->get();
        }

        // โหลดสถานะ "จ่ายงานแล้ว" ของทุกบิลในครั้งเดียว (query เดียว) แทนการเช็คทีละบิลฝั่ง frontend
        $docIds = $docbill->pluck('doc_id')->all();
        $deliveredIds = [];
        if (!empty($docIds)) {
            $deliveredIds = DB::table('transaction_transport')
                ->whereIn('bill_id', $docIds)
                ->whereNull('cancelled_at')   // งานที่ยกเลิกแล้วไม่นับว่าจ่ายแล้ว
                ->distinct()
                ->pluck('bill_id')
                ->flip()
                ->all();
        }

        $docbill->each(function ($item) use ($deliveredIds) {
            $item->has_delivery = isset($deliveredIds[$item->doc_id]);
        });

        return view('document.dashboarddoc', compact('docbill', 'message', 'creator'));
    }

    public function deliveryStatus(Request $request)
    {
        $billId = trim((string) $request->query('bill_id', ''));
        if ($billId === '') {
            return response()->json(['found' => false, 'rows' => []]);
        }

        $rows = DB::table('transaction_transport')
            ->where('bill_id', $billId)
            ->whereNull('cancelled_at')   // ไม่ดึงงานที่ยกเลิกแล้วมาแสดง
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'found'   => $rows->isNotEmpty(),
            'bill_id' => $billId,
            'rows'    => $rows->map(function ($r) {
                $received = !empty($r->check_name) || (string) $r->status === '1';
                return [
                    'name_pick'      => $r->name_pick,
                    'time_pick'      => $r->time_pick,
                    'driver_name'    => $r->driver_name,
                    'transport_name' => $r->transport_name,
                    'check_name'     => $r->check_name,
                    'check_time'     => $r->check_time,
                    'status'         => $r->status,
                    'received'       => $received,
                    'delivery_date'  => $r->delivery_date,
                ];
            })->values(),
        ]);
    }

    public function insertdoc(Request $request)
    {
        $authUser = $this->resolveSsoUser($request, 'document.insertdoc');
        $creator  = $authUser->name;
        return view('document.insertdoc', compact('creator'));
    }

    public function insertDocu(Request $request)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        $creator = $authUser->name;

        DB::beginTransaction();
        try {
            $request->validate([
                'doctype' => 'required|string|max:255',
                'headcom' => 'required|string|max:255',
                'so_id' => 'nullable|string|max:50',
                'solve' => 'nullable|string|max:255',
                'id_com' => 'nullable|string|max:255',
                'com_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_tel' => 'nullable|string|max:255',
                'com_address' => 'required|string|max:255',
                'com_la_long' => 'nullable|string|max:255', // ✅ อนุญาตให้เป็นค่าว่าง
                'datestamp' => 'required|date',
                'statusdeli' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            $currentYear = date('Y') + 543;
            $currentYear = substr($currentYear, -2);
            $currentMonth = date('m');
            $prefix = "SP{$currentYear}{$currentMonth}-";

            $latestBill = Docbills::where('doc_id', 'like', $prefix . '%')
                            ->orderBy(DB::raw('CAST(SUBSTRING(doc_id, 8) AS UNSIGNED)'), 'desc')
                            ->first();

            if ($latestBill) {
                $latestNumber = (int) substr($latestBill->doc_id, -4);
                $nextNumber = $latestNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $doc_id = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $exists = Docbills::where('doc_id', $doc_id)->exists();
            if ($exists) {
                $i = $nextNumber + 1;
                do {
                    $doc_id = $prefix . str_pad($i, 4, '0', STR_PAD_LEFT);
                    $exists = Docbills::where('doc_id', $doc_id)->exists();
                    $i++;
                } while ($exists);
            }

            $item_names = $request->input('item_name', []);
            $item_quantities = $request->input('item_quantity', []);

            $hasItems = collect($item_names)
                ->filter(fn($name) => trim((string) $name) !== '')
                ->isNotEmpty();

            $notes = trim((string) $request->input('notes', ''));

            if (!$hasItems && $notes === '') {
                DB::rollBack();
                return response()->json([
                    'error' => 'กรุณาเพิ่มรายการสินค้า หรือกรอกหมายเหตุ อย่างใดอย่างหนึ่ง'
                ], 422);
            }

            $doc = new Docbills();
            $doc->doc_id = $doc_id;
            $doc->status = 0;
            $doc->statuspdf = 0;
            $doc->statusdeli = 0;
            $doc->id_com = $request->input('id_com');
            $doc->so_id = $request->input('so_id');
            $doc->emp_name = $creator;
            $doc->com_name = $request->input('com_name');
            $doc->contact_name = $request->input('contact_name');
            $doc->contact_tel = $request->input('contact_tel');
            $doc->com_address = $request->input('com_address');
            
            // ✅ แก้ไขจุดที่ 1: การันตีว่าถ้าเป็น null จะเปลี่ยนเป็น '' (Empty String) ทันที
            $doc->com_la_long = $request->input('com_la_long') ?? ''; 
            
            $doc->notes = $notes;
            $doc->datestamp = $request->input('datestamp');
            $doc->doctype = $request->input('doctype');
            $doc->headcom = $request->input('headcom');

            $doc->save();

            if (is_array($item_names) && count($item_names) > 0) {
                foreach ($item_names as $index => $item_name) {
                    if (!empty($item_name)) {
                        $doc_detail = new docbillsdetail();
                        $doc_detail->doc_id = $doc_id;
                        $doc_detail->item_name = $item_name;
                        $doc_detail->quantity = $item_quantities[$index] ?? 0;
                        $doc_detail->save();
                    }
                }
            }
            DB::commit();

            return response()->json([
                'success' => 'สร้างเอกสารสำเร็จ เลขที่เอกสาร:' . $doc_id,
                'doc_id' => $doc_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => 'เกิดข้อผิดพลาด:ใส่ข้อมูลให้ครบถ้วน ' . $e->getMessage()], 500);
        }
    }

    public function editdoc(Request $request, $doc_id)
    {
        $authUser = $this->resolveSsoUser($request, 'document.editdoc');
        $creator  = $authUser->name;

        $doc = Docbills::where('doc_id', $doc_id)->firstOrFail();
        $docDetails = docbillsdetail::where('doc_id', $doc_id)->get();

        return view('document.insertdoc', compact('creator', 'doc', 'docDetails'));
    }

    public function updateDoc(Request $request, $doc_id)
    {
        $authUser = Auth::guard('web')->user();
        if (!$authUser) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        DB::beginTransaction();
        try {
            $request->validate([
                'doctype' => 'required|string|max:255',
                'headcom' => 'required|string|max:255',
                'so_id' => 'nullable|string|max:50',
                'solve' => 'nullable|string|max:255',
                'id_com' => 'nullable|string|max:255',
                'com_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_tel' => 'nullable|string|max:255',
                'com_address' => 'required|string|max:255',
                'com_la_long' => 'nullable|string|max:255', // ✅ อนุญาตให้เป็นค่าว่าง
                'datestamp' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            $doc = Docbills::where('doc_id', $doc_id)->firstOrFail();

            $item_names = $request->input('item_name', []);
            $item_quantities = $request->input('item_quantity', []);

            $hasItems = collect($item_names)
                ->filter(fn($name) => trim((string) $name) !== '')
                ->isNotEmpty();

            $notes = trim((string) $request->input('notes', ''));

            if (!$hasItems && $notes === '') {
                DB::rollBack();
                return response()->json([
                    'error' => 'กรุณาเพิ่มรายการสินค้า หรือกรอกหมายเหตุ อย่างใดอย่างหนึ่ง'
                ], 422);
            }

            $doc->update([
                'id_com' => $request->input('id_com'),
                'so_id' => $request->input('so_id'),
                'com_name' => $request->input('com_name'),
                'contact_name' => $request->input('contact_name'),
                'contact_tel' => $request->input('contact_tel'),
                'com_address' => $request->input('com_address'),
                
                // ✅ แก้ไขจุดที่ 2: การันตีว่าถ้าเป็น null จะเปลี่ยนเป็น '' (Empty String) ทันที
                'com_la_long' => $request->input('com_la_long') ?? '',
                
                'notes' => $notes,
                'datestamp' => $request->input('datestamp'),
                'doctype' => $request->input('doctype'),
                'headcom' => $request->input('headcom'),
            ]);

            docbillsdetail::where('doc_id', $doc_id)->delete();

            if (is_array($item_names) && count($item_names) > 0) {
                foreach ($item_names as $index => $item_name) {
                    if (!empty($item_name)) {
                        $doc_detail = new docbillsdetail();
                        $doc_detail->doc_id = $doc_id;
                        $doc_detail->item_name = $item_name;
                        $doc_detail->quantity = $item_quantities[$index] ?? 0;
                        $doc_detail->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => 'แก้ไขเอกสารสำเร็จ เลขที่เอกสาร:' . $doc_id,
                'doc_id' => $doc_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('updateDoc error: ' . $e->getMessage());
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการแก้ไข: ' . $e->getMessage()], 500);
        }
    }

    public function getDocBillDetail($doc_id)
    {
        if (!Auth::guard('web')->user()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        try {
            $doc_details = docbillsdetail::where('doc_id', $doc_id)->get();

            if ($doc_details->isEmpty()) {
                return response()->json([], 200);
            }

            return response()->json($doc_details, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาด'], 500);
        }
    }

    public function fetchlalong(Request $request)
    {
        if (!Auth::guard('web')->user()) {
            return response()->json(['com_la_long' => null], 401);
        }

        try {
            $id_com = trim((string) $request->input('id_com'));

            if ($id_com === '') {
                return response()->json(['com_la_long' => null]);
            }

            $bill = DB::table('tblbill')
                ->where('customer_id', $id_com)
                ->whereNotNull('customer_la_long')
                ->where('customer_la_long', '!=', '')
                ->where('customer_la_long', 'REGEXP', '^-?[0-9]+([.][0-9]+)?[, ]+-?[0-9]+([.][0-9]+)?$')
                ->orderBy('time', 'desc')
                ->first();

            return response()->json([
                'com_la_long' => $bill->customer_la_long ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('fetchlalong error: ' . $e->getMessage());
            return response()->json(['com_la_long' => null], 500);
        }
    }

    public function searchCustAndVendor(Request $request)
    {
        if (!Auth::guard('web')->user()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        $searchKey = trim((string) $request->input('keySearch'));
        if (mb_strlen($searchKey) < 3) {
            return response()->json(['error' => 'โปรดกรอกคำค้นหาอย่างน้อย 3 ตัวอักษร']);
        }

        $conn = 'mssql_account03';

        $customers = DB::connection($conn)->table('EMCust')
            ->where(function ($q) use ($searchKey) {
                $q->where('CustName', 'LIKE', "%{$searchKey}%")
                  ->orWhere('CustCode', 'LIKE', "%{$searchKey}%");
            })
            ->select('CustCode', 'CustName', 'CustAddr1', 'CustAddr2', 'District', 'Amphur', 'PostCode', 'ContAddr1', 'ContAddr2', 'ContDistrict', 'ContAmphur', 'ContProvince', 'ContPostCode')
            ->limit(100)
            ->get();

        $vendors = DB::connection($conn)->table('EMVendor')
            ->where(function ($q) use ($searchKey) {
                $q->where('VendorName', 'LIKE', "%{$searchKey}%")
                  ->orWhere('VendorCode', 'LIKE', "%{$searchKey}%");
            })
            ->select('VendorCode', 'VendorName', 'VendorAddr1', 'VendorAddr2', 'District', 'Amphur', 'PostCode', 'ContAddr1', 'ContAddr2', 'ContDistrict', 'ContAmphur', 'ContProvince', 'ContPostCode')
            ->limit(100)
            ->get();

        return response()->json([
            'Customer' => $customers,
            'Supplier' => $vendors,
        ]);
    }

    public function savePdfBill(Request $request)
    {
        if (!Auth::guard('web')->user()) {
            return response()->json(['success' => false, 'error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        try {
            $request->validate([
                'doc_id' => 'required|string|max:255',
                'pdf' => 'required|file|mimes:pdf',
            ]);

            $doc_id = $request->input('doc_id');
            $file = $request->file('pdf');

            $path = $file->storeAs('temporary_bill', $doc_id . '.pdf', 'public');

            Docbills::where('doc_id', $doc_id)->update(['statuspdf' => 1]);

            return response()->json(['success' => true, 'path' => $path]);
        } catch (\Exception $e) {
            Log::error('savePdfBill error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}