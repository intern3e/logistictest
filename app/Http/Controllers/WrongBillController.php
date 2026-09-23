<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Docbills;
use App\Models\transaction_delivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * หน้า /wrongbill — ระบบแก้ "สินค้าผิด" (ของผิด) สำหรับ Sale
 *
 * หลักการ:
 *   - ดึงบิลที่รับเข้ามาแล้วสถานะ "สินค้าผิด" (transaction_transport.status = 'สินค้าผิด')
 *   - Sale ตัดสินใจต่อบิลว่าจะ:
 *       (ก) เก็บของเข้าสต็อก  -> solve = 'เก็บเข้าสต็อก'  (ถือว่าเคลียร์ทันที)
 *       (ข) เปิดบิลใหม่ไปส่ง   -> solve = <เลขบิลใหม่>     (จับ 2 เลขบิลเป็นงานเดียวกัน)
 *   - เก็บการตัดสินใจไว้ในคอลัมน์ solve ของบิลนั้น (tblbill.solve / docbills.solve)
 *   - ถ้าบิลใหม่ส่ง "จัดส่งสำเร็จ" หรือ "ค้างบิล" -> ถือว่าบิลของผิดเดิมเคลียร์แล้ว
 *
 * การมองเห็น (เหมือน /shelfsale): admin เห็นทั้งหมดทันที, role อื่นต้องเลือกตัวกรองก่อนถึงจะเห็น
 */
class WrongBillController extends Controller
{
    const DELI_STATUS_WRONG = 'สินค้าผิด';
    const DELI_STATUS_HOLD  = 'ค้างบิล';
    const SOLVE_STOCK       = 'เก็บเข้าสต็อก';
    /** สถานะที่ถือว่าบิล "เคลียร์" เมื่อบิลใหม่ไปถึงสถานะนี้ */
    const CLEARED_STATUSES  = ['จัดส่งสำเร็จ', 'ค้างบิล'];

    /** role ที่ตัดสินใจแก้ของผิดได้ (Sale + admin) */
    private function solverRoles(): array
    {
        return ['admin', 'sale', 'sale_assistant', 'support'];
    }

    /**
     * หน้า /wrongbill — โหลดแค่ตัวกรอง (admin โหลดข้อมูลทันที, role อื่นต้องเลือกตัวกรองก่อน)
     */
    public function index()
    {
        $user    = $this->requireLogin();
        $role    = $user->role ?? '';
        $creator = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');

        $autoLoad = $role === 'admin';                          // admin โหลดทั้งหมดทันที
        $canSolve = in_array($role, $this->solverRoles(), true); // สิทธิ์ตัดสินใจแก้ของผิด

        // dropdown Sale — ดึงจาก tblbill.sale_name (cache 30 นาที)
        $saleOptions = Cache::remember('wrongbill_sale_options', 1800, function () {
            return Bill::whereNotNull('sale_name')->where('sale_name', '!=', '')
                ->distinct()->orderBy('sale_name')->pluck('sale_name')->values();
        });

        return view('sale.dashboardwrong', compact('creator', 'autoLoad', 'canSolve', 'saleOptions'));
    }

    /**
     * ดึงข้อมูลบิลของผิด (AJAX)
     *   filter: sale, customer (รหัส/ชื่อ), bill (เลขบิล), status (pending|tracking|cleared|all)
     */
    public function data(Request $request)
    {
        $user    = $this->requireLogin();
        $role    = $user->role ?? '';
        $isAdmin = $role === 'admin';

        $fSale = trim((string) $request->input('sale', ''));
        $fCust = trim((string) $request->input('customer', ''));
        $fBill = trim((string) $request->input('bill', ''));
        $fStat = trim((string) $request->input('status', 'open'));  // ค่าเริ่มต้น: ยังไม่เคลียร์

        // role อื่น (ไม่ใช่ admin) ต้องเลือกตัวกรองอย่างน้อย 1 อย่างก่อน
        if (!$isAdmin && $fSale === '' && $fCust === '' && $fBill === '') {
            return response()->json([
                'ok'      => true,
                'rows'    => [],
                'message' => 'กรุณาเลือกตัวกรอง (Sale / ลูกค้า / เลขบิล) ก่อนค้นหา',
            ]);
        }

        // ===== 1) ดึงงานที่สถานะ "สินค้าผิด" และ "ค้างบิล" =====
        $wrongs = transaction_delivery::whereIn('status', [self::DELI_STATUS_WRONG, self::DELI_STATUS_HOLD])
            ->orderByDesc('check_time')
            ->get();

        if ($wrongs->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        $ids = $wrongs->pluck('bill_id')->filter()->unique()->values();

        // resolve เลขบิล/ลูกค้า/Sale/solve จาก tblbill + docbills
        $billsBySoDetail = Bill::whereIn('so_detail_id', $ids)
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'sale_name', 'solve', 'statusdeli', 'NG'])
            ->keyBy('so_detail_id');
        $docs = Docbills::whereIn('doc_id', $ids)
            ->get(['doc_id', 'id_com', 'com_name', 'contact_name', 'solve', 'statusdeli', 'NG'])
            ->keyBy('doc_id');

        // ===== 2) จัดกลุ่มเป็น 1 แถวต่อ 1 บิล (ใช้แถวของผิดล่าสุดเป็นตัวแทน) =====
        $grouped = [];
        foreach ($wrongs as $d) {
            $billId = $d->bill_id;
            if ($billsBySoDetail->has($billId)) {
                $b     = $billsBySoDetail->get($billId);
                $type  = 'bill';
                $key   = 'bill:' . $b->billid;
                $no    = (string) $b->billid;
                $custC = (string) ($b->customer_id ?? '');
                $custN = (string) ($b->customer_name ?? '');
                $sale  = (string) ($b->sale_name ?? '');
                $soId  = (string) ($b->so_id ?? '');
                $solve = trim((string) ($b->solve ?? ''));
            } elseif ($docs->has($billId)) {
                $doc   = $docs->get($billId);
                $type  = 'doc';
                $key   = 'doc:' . $billId;
                $no    = (string) $billId;
                $custC = (string) ($doc->id_com ?? '');
                $custN = (string) ($doc->com_name ?? '');
                $sale  = (string) ($doc->contact_name ?? '');
                $soId  = '';
                $solve = trim((string) ($doc->solve ?? ''));
            } else {
                // งานไปรับของเอง (PO) — ไม่ดึงมาหน้านี้
                continue;
            }

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'job_key'       => $key,
                    'type'          => $type,
                    'bill_no'       => $no,
                    'so_id'         => $soId,
                    'customer_code' => $custC,
                    'customer_name' => $custN,
                    'sale'          => $sale,
                    'solve'         => $solve,
                    '_rows'         => collect(),
                ];
            }
            $grouped[$key]['_rows']->push($d);
        }

        if (empty($grouped)) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        // ===== 3) สถานะการส่งของ "บิลใหม่" (solve = เลขบิลใหม่) เพื่อดูว่าเคลียร์หรือยัง =====
        $newBillNos = collect($grouped)
            ->pluck('solve')
            ->filter(fn ($s) => $s !== '' && $s !== self::SOLVE_STOCK)
            ->unique()->values()->all();

        $newBillStatus = [];   // เลขบิลใหม่ -> สถานะส่ง
        if (!empty($newBillNos)) {
            // จาก tblbill (billid) และ docbills (doc_id)
            foreach (Bill::whereIn('billid', $newBillNos)->get(['billid', 'statusdeli']) as $b) {
                if (trim((string) $b->statusdeli) !== '') $newBillStatus[(string) $b->billid] = (string) $b->statusdeli;
            }
            foreach (Docbills::whereIn('doc_id', $newBillNos)->get(['doc_id', 'statusdeli']) as $d) {
                if (!isset($newBillStatus[(string) $d->doc_id]) && trim((string) $d->statusdeli) !== '') {
                    $newBillStatus[(string) $d->doc_id] = (string) $d->statusdeli;
                }
            }
            // fallback: สถานะล่าสุดใน transaction_transport ของบิลใหม่ (map เลขบิล -> bill_id ภายใน)
            $stillUnknown = array_values(array_filter($newBillNos, fn ($n) => !isset($newBillStatus[$n])));
            if (!empty($stillUnknown)) {
                $soDetailByBillid = Bill::whereIn('billid', $stillUnknown)
                    ->pluck('billid', 'so_detail_id');   // [so_detail_id => billid]
                $innerIds = $soDetailByBillid->keys()
                    ->merge($stillUnknown)               // doc: bill_id = doc_id = เลขบิลตรง ๆ
                    ->unique()->values()->all();
                if (!empty($innerIds)) {
                    $latest = transaction_delivery::whereIn('bill_id', $innerIds)
                        ->orderBy('check_time')
                        ->get(['bill_id', 'status', 'check_time']);
                    foreach ($latest as $t) {
                        $billNo = $soDetailByBillid->get($t->bill_id) ?? (string) $t->bill_id;
                        if (trim((string) $t->status) !== '') $newBillStatus[(string) $billNo] = (string) $t->status;
                    }
                }
            }
        }

        // ===== 4) สร้างแถว + คำนวณสถานะเคลียร์ =====
        $rows = collect($grouped)->map(function ($g) use ($newBillStatus) {
            $rowsCol = $g['_rows'];
            $first   = $rowsCol->sortByDesc('check_time')->first();

            $solve = $g['solve'];
            // สถานะการแก้: pending (ยังไม่ตัดสินใจ) / stock (เก็บเข้าสต็อก) / newbill (เปิดบิลใหม่)
            if ($solve === '') {
                $solveMode  = 'pending';
                $newStatus  = null;
                $cleared    = false;
            } elseif ($solve === self::SOLVE_STOCK) {
                $solveMode  = 'stock';
                $newStatus  = null;
                $cleared    = true;                     // เก็บเข้าสต็อก = เคลียร์
            } else {
                $solveMode  = 'newbill';
                $newStatus  = $newBillStatus[$solve] ?? null;
                $cleared    = in_array($newStatus, self::CLEARED_STATUSES, true);
            }

            // state สรุปสำหรับกรอง/แสดงผล
            if ($cleared)                    $state = 'cleared';
            elseif ($solveMode === 'newbill') $state = 'tracking';   // เปิดบิลใหม่แล้ว รอส่ง
            else                              $state = 'pending';     // ยังไม่ตัดสินใจ

            return [
                'job_key'       => $g['job_key'],
                'type'          => $g['type'],
                'bill_no'       => $g['bill_no'],
                'so_id'         => $g['so_id'],
                'customer_code' => $g['customer_code'],
                'customer_name' => $g['customer_name'],
                'sale'          => $g['sale'],
                'deli_status'   => (string) ($first->status ?? ''),   // สินค้าผิด หรือ ค้างบิล
                'reason'        => (string) ($first->note ?? ''),
                'wrong_by'      => (string) ($first->check_name ?? ''),
                'wrong_time'    => optional($first->check_time)->format('Y-m-d H:i'),
                'driver_name'   => (string) ($first->driver_name ?? ''),
                'solve'         => $solve,
                'solve_mode'    => $solveMode,
                'new_bill'      => $solveMode === 'newbill' ? $solve : '',
                'new_status'    => $newStatus,
                'cleared'       => $cleared,
                'state'         => $state,
            ];
        })->values();

        // ===== 5) กรอง =====
        if ($fSale !== '') {
            $rows = $rows->filter(fn ($r) => stripos((string) $r['sale'], $fSale) !== false)->values();
        }
        if ($fCust !== '') {
            $rows = $rows->filter(fn ($r) =>
                stripos((string) $r['customer_code'], $fCust) !== false ||
                stripos((string) $r['customer_name'], $fCust) !== false
            )->values();
        }
        if ($fBill !== '') {
            $rows = $rows->filter(fn ($r) =>
                stripos((string) $r['bill_no'], $fBill) !== false ||
                stripos((string) $r['new_bill'], $fBill) !== false
            )->values();
        }
        // สถานะ: open (pending+tracking) / pending / tracking / cleared / all
        if ($fStat === 'open') {
            $rows = $rows->filter(fn ($r) => !$r['cleared'])->values();
        } elseif (in_array($fStat, ['pending', 'tracking', 'cleared'], true)) {
            $rows = $rows->filter(fn ($r) => $r['state'] === $fStat)->values();
        }
        // fStat === 'all' -> ไม่กรอง

        // เรียง: ยังไม่แก้ก่อน -> ตามด้วยของผิดล่าสุด
        $order = ['pending' => 0, 'tracking' => 1, 'cleared' => 2];
        $rows = $rows->sortBy(fn ($r) => ($order[$r['state']] ?? 9) . '|' . (9999999999 - strtotime($r['wrong_time'] ?? '1970-01-01')))->values();

        // สรุปจำนวนแต่ละสถานะ (ก่อนกรอง status — เพื่อโชว์ badge) : คำนวณจาก grouped ทั้งหมด
        return response()->json([
            'ok'   => true,
            'rows' => $rows,
        ]);
    }

    /**
     * บันทึกการตัดสินใจแก้ของผิด
     *   mode = stock   -> solve = 'เก็บเข้าสต็อก'
     *   mode = newbill -> solve = <เลขบิลใหม่>
     */
    public function solve(Request $request)
    {
        $user = $this->requireLogin();
        if (!in_array($user->role ?? '', $this->solverRoles(), true)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการ (เฉพาะ Sale/admin)'], 403);
        }

        $validated = $request->validate([
            'job_key'  => 'required|string',
            'mode'     => 'required|string|in:stock,newbill,clear',
            'new_bill' => 'nullable|string|max:100',
        ]);

        [$type, $rawId] = array_pad(explode(':', $validated['job_key'], 2), 2, null);
        if (!$rawId || !in_array($type, ['bill', 'doc'], true)) {
            return response()->json(['ok' => false, 'message' => 'รูปแบบงานไม่ถูกต้อง'], 422);
        }

        // หา record บิล (tblbill ค้นด้วย billid, docbills ค้นด้วย doc_id)
        if ($type === 'bill') {
            $item = Bill::where('billid', $rawId)->first();
        } else {
            $item = Docbills::where('doc_id', $rawId)->first();
        }
        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบบิลนี้'], 404);
        }

        if ($validated['mode'] === 'stock') {
            $solve = self::SOLVE_STOCK;
        } elseif ($validated['mode'] === 'clear') {
            $solve = '';   // ยกเลิกการตัดสินใจ (กลับไปรอแก้)
        } else {
            $newBill = trim((string) ($validated['new_bill'] ?? ''));
            if ($newBill === '') {
                return response()->json(['ok' => false, 'message' => 'กรุณากรอกเลขบิลใหม่'], 422);
            }
            if ($newBill === (string) $rawId) {
                return response()->json(['ok' => false, 'message' => 'เลขบิลใหม่ต้องไม่ใช่เลขบิลเดิม'], 422);
            }
            // เตือน (ไม่บล็อก) ถ้าไม่พบเลขบิลใหม่ในระบบ
            $exists = Bill::where('billid', $newBill)->exists() || Docbills::where('doc_id', $newBill)->exists();
            $solve  = $newBill;
            if (!$exists) {
                $item->solve = $solve;
                $item->save();
                Log::info("wrongbill.solve: {$validated['job_key']} -> newbill={$newBill} (ยังไม่พบในระบบ) by " . ($user->name ?? $user->id_emp));
                return response()->json([
                    'ok'      => true,
                    'warning' => true,
                    'message' => 'บันทึกเลขบิลใหม่ "' . $newBill . '" แล้ว (หมายเหตุ: ยังไม่พบเลขบิลนี้ในระบบ — จะเคลียร์อัตโนมัติเมื่อบิลนี้ส่งสำเร็จ/ค้างบิล)',
                ]);
            }
        }

        $item->solve = $solve;
        $item->save();

        Log::info("wrongbill.solve: {$validated['job_key']} -> solve=" . ($solve === '' ? '(clear)' : $solve) . ' by ' . ($user->name ?? $user->id_emp));

        $msg = $validated['mode'] === 'stock'
            ? 'บันทึก "เก็บเข้าสต็อก" แล้ว — บิลนี้เคลียร์แล้ว'
            : ($validated['mode'] === 'clear'
                ? 'ยกเลิกการตัดสินใจแล้ว — บิลกลับไปสถานะรอแก้'
                : 'ผูกกับบิลใหม่ "' . $solve . '" แล้ว — จะเคลียร์อัตโนมัติเมื่อบิลใหม่ส่งสำเร็จ/ค้างบิล');

        return response()->json(['ok' => true, 'message' => $msg]);
    }
}
