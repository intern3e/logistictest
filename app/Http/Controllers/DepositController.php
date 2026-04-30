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

        $query = deposit::query();

        if ($soKeyword !== '') {
            $query->where('so_id', 'like', "%{$soKeyword}%");
        }

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('sale_name', 'like', "%{$keyword}%")
                  ->orWhere('contactso', 'like', "%{$keyword}%")
                  ->orWhere('customer_id', 'like', "%{$keyword}%")
                  ->orWhere('deposit_bill_id', 'like', "%{$keyword}%");
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
     * แสดงใบมัดจำในรูปแบบฟอร์มเอกสาร (พิมพ์/PDF)
     * Route: GET /deposit/bill/{deposit_bill_id}
     */
    public function showBill($deposit_bill_id)
    {
        $items = deposit::where('deposit_bill_id', $deposit_bill_id)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            abort(404, 'ไม่พบใบมัดจำเลขที่ ' . $deposit_bill_id);
        }

        // ใช้ row แรกเป็น header info
        $header = $items->first();

        // คำนวณยอดรวม
        $totalDeposit = $items->sum('dep_price');
        $grandTotal   = (float) $header->grand_total;
        $netRemaining = max(0, $grandTotal - $totalDeposit);

        return view('deposit.billform', compact(
            'items', 'header', 'totalDeposit', 'grandTotal', 'netRemaining', 'deposit_bill_id'
        ));
    }

    /**
     * อัปเดตสถานะใบมัดจำ (รอยืนยัน ↔ ยืนยัน)
     */
    public function updateStatus(Request $request)
    {
        $changedBy = strtolower(trim($request->input('changed_by', '')));

        if (!in_array($changedBy, $this->adminUsers)) {
            return response()->json([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์เปลี่ยนสถานะ',
            ], 403);
        }

        $newStatus = $request->input('new_status');
        $allowed   = ['รอยืนยัน', 'ยืนยัน'];

        if (!in_array($newStatus, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'สถานะไม่ถูกต้อง',
            ], 422);
        }

        $depositId = $request->input('deposit_id');
        $soId      = $request->input('so_id');

        if (empty($depositId) && empty($soId)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีข้อมูลใบมัดจำที่จะอัปเดต',
            ], 422);
        }

        try {
            $query = deposit::query();

            if (!empty($depositId)) {
                $query->where('id', $depositId);
            } else {
                $query->where('so_id', $soId);
            }

            // ===== เตรียมข้อมูลที่จะ update =====
            $updateData = [
                'status' => $newStatus,
            ];

            // ถ้าเปลี่ยนเป็น "ยืนยัน" → บันทึก time_check
            // ถ้าเปลี่ยนกลับเป็น "รอยืนยัน" → ล้าง time_check
            if ($newStatus === 'ยืนยัน') {
                $updateData['time_check'] = now();
            } else {
                $updateData['time_check'] = null;
            }

            $affected = $query->update($updateData);

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรายการที่จะอัปเดต',
                ], 404);
            }

            Log::info('Deposit status updated', [
                'so_id'      => $soId,
                'deposit_id' => $depositId,
                'new_status' => $newStatus,
                'changed_by' => $changedBy,
                'time_check' => $updateData['time_check'],
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'อัปเดตสถานะเรียบร้อย',
                'time_check' => $updateData['time_check'],
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
     * บันทึกว่าใบมัดจำถูกพิมพ์แล้ว
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
     */
    public function botdeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));

        $query = deposit::query()
            ->where('status', 'ยืนยัน');

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

    /**
     * สร้าง deposit_bill_id แบบ running ต่อเดือน
     * รูปแบบ: RD + YY(พ.ศ.2หลัก) + MM + - + 5หลัก  เช่น RD6904-00003
     *
     * ใช้ DB transaction + lockForUpdate กันการสร้างเลขซ้ำเมื่อกดพร้อมกัน
     *
     * @return string
     */
    private function generateDepositBillId()
    {
        $now      = Carbon::now();
        $yearBE   = $now->year + 543;             // ค.ศ. → พ.ศ.
        $yy       = substr((string)$yearBE, -2);  // 2หลักท้าย
        $mm       = $now->format('m');
        $prefix   = "RD{$yy}{$mm}-";              // เช่น RD6904-

        // หาเลขล่าสุดของ prefix นี้ พร้อม lock เพื่อกัน race condition
        $latest = DB::table('deposit')
            ->where('deposit_bill_id', 'like', $prefix . '%')
            ->orderByDesc('deposit_bill_id')
            ->lockForUpdate()
            ->value('deposit_bill_id');

        if ($latest) {
            // ตัดส่วนหลัง '-' มาเป็นตัวเลข แล้ว +1
            $lastNum = (int) substr($latest, strlen($prefix));
            $next    = $lastNum + 1;
        } else {
            $next = 1;
        }

        $running = str_pad((string)$next, 5, '0', STR_PAD_LEFT);
        return $prefix . $running;
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

        $netGrandTotal = (float)$validated['grand_total'];

        DB::beginTransaction();
        try {
            // ===== สร้าง deposit_bill_id (1 ใบ ต่อ 1 SO) =====
            $depositBillId = $this->generateDepositBillId();

            $inserted = [];

            foreach ($validated['deposits'] as $dep) {
                if ((float)$dep['percent'] <= 0 && (float)$dep['amount'] <= 0) {
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
                    'deposit_bill_id'  => $depositBillId,
                    'time_check'       => null,
                    'deposit_bill'     => null,
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

            Log::info('Deposit created', [
                'so_id'           => $validated['so_id'],
                'deposit_bill_id' => $depositBillId,
                'count'           => count($inserted),
            ]);

            return response()->json([
                'success'         => true,
                'message'         => 'บันทึกใบมัดจำเรียบร้อยแล้ว',
                'so_id'           => $validated['so_id'],
                'deposit_bill_id' => $depositBillId,
                'inserted_ids'    => $inserted,
                'count'           => count($inserted),
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