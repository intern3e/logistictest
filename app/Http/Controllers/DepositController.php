<?php

namespace App\Http\Controllers;

use App\Models\deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DepositController extends Controller
{
    /**
     * รายชื่อ admin ที่สามารถเปลี่ยนสถานะใบมัดจำได้
     */
    private $adminUsers = ['kanitin2', 'dev'];

    public function insertdeposit()
    {
        return view('deposit.insertdeposit');
    }

    public function dashboarddeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));
        $keyword   = trim($request->get('keyword', ''));
        // หมายเหตุ: create_by ใช้สำหรับระบุ "ใครเข้าระบบ" (แสดงชื่อมุมขวาบน + เช็คสิทธิ์ admin)
        //          ❌ ไม่ใช้สำหรับ filter ข้อมูล มิฉะนั้นจะไม่เห็นรายการเลย

        // ดึงข้อมูลทั้งหมด ไม่ group - แสดงทุกแถวแยกกัน
        $query = deposit::query();

        // filter: เลขใบสั่งขาย
        if ($soKeyword !== '') {
            $query->where('so_id', 'like', "%{$soKeyword}%");
        }

        // filter: ชื่อลูกค้า / Sale / ผู้ติดต่อ / รหัสลูกค้า
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('sale_name', 'like', "%{$keyword}%")
                  ->orWhere('contactso', 'like', "%{$keyword}%")
                  ->orWhere('customer_id', 'like', "%{$keyword}%");
            });
        }

        $deposits = $query
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('deposit.dashboarddeposit', compact('deposits'));
    }

    /**
     * ดึงรายละเอียดใบมัดจำทั้งหมดของ so_id นั้น (ใช้ใน Modal)
     */
    public function detail($so_id)
    {
        $items = deposit::where('so_id', $so_id)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'items'   => $items,
        ]);
    }

    /**
     * อัปเดตสถานะใบมัดจำ (รอยืนยัน ↔ ยืนยัน)
     * Route: POST /deposit/update-status
     *
     * Body (JSON):
     *  - so_id      : เลขใบสั่งขาย
     *  - deposit_id : ID ของแถวมัดจำ (ใช้ระบุแถวที่จะอัปเดต)
     *  - new_status : "รอยืนยัน" หรือ "ยืนยัน"
     *  - changed_by : ชื่อผู้ใช้ที่กดเปลี่ยน (จาก URL ?create_by=...)
     */
    public function updateStatus(Request $request)
    {
        // ===== 1. ตรวจสอบสิทธิ์ admin (kanitin2, dev เท่านั้น) =====
        $changedBy = strtolower(trim($request->input('changed_by', '')));

        if (!in_array($changedBy, $this->adminUsers)) {
            return response()->json([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์เปลี่ยนสถานะ',
            ], 403);
        }

        // ===== 2. ตรวจสอบค่า new_status =====
        $newStatus = $request->input('new_status');
        $allowed   = ['รอยืนยัน', 'ยืนยัน'];

        if (!in_array($newStatus, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'สถานะไม่ถูกต้อง',
            ], 422);
        }

        // ===== 3. หาแถวที่จะอัปเดต =====
        $depositId = $request->input('deposit_id');
        $soId      = $request->input('so_id');

        if (empty($depositId) && empty($soId)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีข้อมูลใบมัดจำที่จะอัปเดต',
            ], 422);
        }

        try {
            // ใช้ deposit_id ก่อนถ้ามี (แม่นยำกว่า), ไม่งั้น fallback ไปใช้ so_id
            $query = deposit::query();

            if (!empty($depositId)) {
                $query->where('id', $depositId);
            } else {
                $query->where('so_id', $soId);
            }

            $affected = $query->update([
                'status' => $newStatus,
            ]);

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรายการที่จะอัปเดต',
                ], 404);
            }

            // log การเปลี่ยนสถานะเผื่อ audit ภายหลัง
            Log::info('Deposit status updated', [
                'so_id'      => $soId,
                'deposit_id' => $depositId,
                'new_status' => $newStatus,
                'changed_by' => $changedBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตสถานะเรียบร้อย',
            ]);

        } catch (\Throwable $e) {
            Log::error('Update deposit status failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * บันทึกว่าใบมัดจำถูกพิมพ์แล้ว (อัปเดต print_time + bill_no)
     * Route: POST /deposit/mark-printed
     */
    public function markPrinted(Request $request)
    {
        $depositId = $request->input('deposit_id');
        $soId      = $request->input('so_id');
        $billNo    = trim($request->input('bill_no', ''));
        $printedBy = trim($request->input('printed_by', 'unknown'));

        if (empty($depositId) && empty($soId)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีข้อมูลใบมัดจำ',
            ], 422);
        }

        try {
            $query = deposit::query();

            if (!empty($depositId)) {
                $query->where('id', $depositId);
            } else {
                $query->where('so_id', $soId);
            }

            $updateData = [
                'print_time' => now(),
            ];

            // ถ้ามี bill_no ส่งมาด้วย ก็เก็บใน status_bill (ปรับชื่อ column ตามจริงถ้าต่าง)
            if ($billNo !== '') {
                $updateData['status_bill'] = $billNo;
            }

            $affected = $query->update($updateData);

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรายการที่จะอัปเดต',
                ], 404);
            }

            Log::info('Deposit marked as printed', [
                'so_id'      => $soId,
                'deposit_id' => $depositId,
                'bill_no'    => $billNo,
                'printed_by' => $printedBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกการพิมพ์เรียบร้อย',
            ]);

        } catch (\Throwable $e) {
            Log::error('Mark printed failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * บันทึกว่าใบมัดจำหลายรายการถูกพิมพ์แล้ว (bulk)
     * Route: POST /deposit/mark-printed-bulk
     */
    public function markPrintedBulk(Request $request)
    {
        $ids = $request->input('deposit_ids', []);
        $printedBy = trim($request->input('printed_by', 'unknown'));

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีรายการที่จะบันทึก',
            ], 422);
        }

        try {
            $affected = deposit::whereIn('id', $ids)
                ->update(['print_time' => now()]);

            Log::info('Deposit bulk marked as printed', [
                'deposit_ids' => $ids,
                'count'       => $affected,
                'printed_by'  => $printedBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => "บันทึก {$affected} รายการสำเร็จ",
                'count'   => $affected,
            ]);

        } catch (\Throwable $e) {
            Log::error('Mark printed bulk failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * หน้า botdeposit — แสดงเฉพาะใบมัดจำที่ status = "ยืนยัน"
     * (ข้อมูลจะมาที่หน้านี้ต่อเมื่อ admin กดยืนยันที่หน้า dashboarddeposit)
     */
    public function botdeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));

        $query = deposit::query()
            ->where('status', 'ยืนยัน');

        // filter: เลขใบสั่งขาย
        if ($soKeyword !== '') {
            $query->where('so_id', 'like', "%{$soKeyword}%");
        }

        $deposits = $query
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('deposit.botdeposit', compact('deposits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_id'              => 'required|string|max:50',
            'sell_date'          => 'nullable|string',
            'customer_id'        => 'nullable|string|max:50',
            'customer_name'      => 'nullable|string|max:255',
            'contactso'          => 'required|string|max:255',
            'customer_tel'       => 'nullable|string|max:50',
            'customer_address'   => 'nullable|string',
            'emp_name'           => 'nullable|string|max:150',
            'sale_name'          => 'nullable|string|max:150',
            'grand_total'        => 'required|numeric|min:0',
            'deposits'           => 'required|array|min:1',
            'deposits.*.type'    => 'required|in:product,service,shipping',
            'deposits.*.percent' => 'required|numeric|min:0|max:100',
            'deposits.*.amount'  => 'required|numeric|min:0',
        ]);

        $dateDep = null;
        if (!empty($validated['sell_date'])) {
            $parts = explode('-', $validated['sell_date']);
            if (count($parts) === 3) {
                if (strlen($parts[0]) === 2) {
                    $dateDep = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                } else {
                    $dateDep = $validated['sell_date'];
                }
            }
        }

        $totalDeposit = 0;
        foreach ($validated['deposits'] as $dep) {
            if ((float)$dep['percent'] > 0) {
                $totalDeposit += (float)$dep['amount'];
            }
        }

        $netGrandTotal = (float)$validated['grand_total'];

        DB::beginTransaction();
        try {
            $inserted = [];

            foreach ($validated['deposits'] as $dep) {
                if ((float)$dep['percent'] <= 0) {
                    continue;
                }

                $row = deposit::create([
                    'so_id'            => $validated['so_id'],
                    'date_dep'         => $dateDep,
                    'customer_id'      => $validated['customer_id']      ?? null,
                    'customer_name'    => $validated['customer_name']    ?? null,
                    'contactso'        => $validated['contactso'],
                    'customer_tel'     => $validated['customer_tel']     ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'sale_name'        => $validated['sale_name']        ?? null,
                    'emp_name'         => $validated['emp_name']         ?? 'Guest',
                    'dep_type'         => $dep['type'],
                    'dep_per'          => $dep['percent'],
                    'dep_price'        => $dep['amount'],
                    'grand_total'      => $netGrandTotal,
                    'time'             => now(),
                    'print_time'       => null,
                    'status'           => 'รอยืนยัน',
                    'status_bill'      => null,
                ]);

                $inserted[] = $row->id;
            }

            if (empty($inserted)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีรายการมัดจำที่ถูกต้อง',
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'บันทึกใบมัดจำเรียบร้อยแล้ว',
                'so_id'        => $validated['so_id'],
                'inserted_ids' => $inserted,
                'count'        => count($inserted),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Deposit store failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage(),
            ], 500);
        }
    }
}