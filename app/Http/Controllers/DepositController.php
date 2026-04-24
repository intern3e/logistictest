<?php

namespace App\Http\Controllers;

use App\Models\deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DepositController extends Controller
{
    public function insertdeposit()
    {
        return view('deposit.insertdeposit');
    }

    public function dashboarddeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));
        $keyword   = trim($request->get('keyword', ''));
        $createBy  = trim($request->get('create_by', ''));

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

        // filter: ผู้สร้าง
        if ($createBy !== '') {
            $query->where('emp_name', $createBy);
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

    public function botdeposit()
    {
        return view('deposit.dotdeposit');
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