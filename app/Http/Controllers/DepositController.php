<?php

namespace App\Http\Controllers;

use App\Models\deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    public function insertdeposit()
    {
        return view('deposit.insertdeposit');
    }

    public function dashboarddeposit()
    {
        return view('deposit.dashboarddeposit');
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
                    'grand_total'      => $validated['grand_total'],
                    'time'             => now(),
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