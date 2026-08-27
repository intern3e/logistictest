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
    /**
     * ตรวจ ticket SSO (client_key '3e') แล้ว login ให้อัตโนมัติถ้ายังไม่มี session
     * ไม่มี session เลย -> redirect ไปหน้า login
     * (ใช้วิธีเดียวกับ StoreController::resolveSsoUser — copy มาไว้ในไฟล์นี้โดยตรง ไม่แก้ไฟล์อื่น)
     */
    private function resolveSsoUser(Request $request, string $logTag): UserAuth
    {
        $ticket = $request->input('ticket');

        if ($ticket && !Auth::guard('web')->check()) {
            $ticketRecord = SsoTicket::where('ticket', $ticket)
                ->where('client_key', '3e')
                ->first();

            if ($ticketRecord && $ticketRecord->markAsUsed()) {
                $user = UserAuth::find($ticketRecord->id_emp);
                if ($user && $user->is_active) {
                    Auth::guard('web')->login($user);
                    Log::info("{$logTag}: SSO login success user={$user->id_emp}");
                } else {
                    Log::warning("{$logTag}: ticket valid but user not found/inactive id_emp={$ticketRecord->id_emp}");
                }
            } else {
                Log::warning("{$logTag}: invalid or expired ticket={$ticket}");
            }
        }

        if (!Auth::guard('web')->check()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->guest(route('login'))
            );
        }

        return Auth::guard('web')->user();
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
        // ไม่จำกัด role — เข้าได้ทุก role ที่ login ผ่านระบบนี้แล้ว

        $date = $request->get('date');
        $message = null;

        if ($date) {
            $docbill = Docbills::whereDate('time', $date)
                        ->orderBy('doc_id', 'desc')
                        ->get();

            if ($docbill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            $docbill = Docbills::orderBy('doc_id', 'desc')->get();
        }

        return view('document.dashboarddoc', compact('docbill', 'message', 'creator'));
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
                // 'emp_name' ตัดออก — ไม่รับจาก client แล้ว ดึงจาก session ที่ login ไว้เท่านั้น
                'doctype' => 'required|string|max:255',
                'headcom' => 'required|string|max:255',
                'so_id' => 'nullable|string|max:50',
                'solve' => 'nullable|string|max:255',
                'id_com' => 'nullable|string|max:255',
                'com_name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_tel' => 'nullable|string|max:255',
                'com_address' => 'required|string|max:255',
                'com_la_long' => 'required|string|max:255',
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
                    $exists = Docbills::where('docid', $doc_id)->exists();
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
            $doc->emp_name = $creator;   // <-- มาจาก session login เท่านั้น ปลอมไม่ได้แล้ว
            $doc->com_name = $request->input('com_name');
            $doc->contact_name = $request->input('contact_name');
            $doc->contact_tel = $request->input('contact_tel');
            $doc->com_address = $request->input('com_address');
            $doc->com_la_long = $request->input('com_la_long');
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

    public function getDocBillDetail($doc_id)
    {
        if (!Auth::guard('web')->user()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        try {
            $doc_details = Docbillsdetail::where('doc_id', $doc_id)->get();

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

            return response()->json(['success' => true, 'path' => $path]);
        } catch (\Exception $e) {
            Log::error('savePdfBill error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}