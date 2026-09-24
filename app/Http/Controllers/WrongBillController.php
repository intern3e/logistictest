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
 * หน้า /wrongbill — แก้งาน "ของผิด (สินค้าผิด)" และ "ค้างบิล" สำหรับ Sale
 *
 * วิธีแก้ (เก็บใน tblbill.solve + solve_by + solve_at):
 *   ── ของผิด (สินค้าผิด) ──
 *     1) ส่งใหม่เลขบิลเดิม  -> solve = 'ส่งใหม่'          : soft-cancel รอบที่ผิด คืนงานไปหน้าจ่ายงาน (delivery)
 *                             เคลียร์เมื่อรอบใหม่ของบิลเดิม "จัดส่งสำเร็จ"
 *     2) เปลี่ยนเลขบิล       -> solve = 'เปลี่ยนบิล:<เลขบิลใหม่>' : เคลียร์เมื่อบิลใหม่ "จัดส่งสำเร็จ"
 *   ── ค้างบิล ──
 *     เปิดเอกสารชั่วคราวไปรับกลับ -> solve = 'เอกสารชั่วคราว:<เลขเอกสาร>' : เคลียร์เมื่อเอกสารนั้น "จัดส่งสำเร็จ"
 *
 * สิทธิ์:
 *   - sale เห็นเฉพาะงานของตัวเอง
 *   - support, sale_assistant, accounting, admin เห็นได้ทุกงาน
 *   - stock, store เข้าไม่ได้
 */
class WrongBillController extends Controller
{
    const ST_WRONG   = 'สินค้าผิด';
    const ST_HOLD    = 'ค้างบิล';
    const ST_SUCCESS = 'จัดส่งสำเร็จ';

    /** role ที่เข้าหน้านี้ได้ (stock/store เข้าไม่ได้) */
    private function viewerRoles(): array
    {
        return ['admin', 'support', 'sale_assistant', 'accounting', 'sale'];
    }

    /** role ที่เห็นได้ "ทุกงาน" (sale เห็นเฉพาะของตัวเอง) */
    private function seeAllRoles(): array
    {
        return ['admin', 'support', 'sale_assistant', 'accounting'];
    }

    public function index()
    {
        $user = $this->requireLogin();
        $role = $user->role ?? '';

        if (!in_array($role, $this->viewerRoles(), true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        $creator  = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');
        $isSale   = $role === 'sale';                       // เห็นเฉพาะงานตัวเอง
        $seeAll   = in_array($role, $this->seeAllRoles(), true);
        $autoLoad = true;                                    // โหลดทันที (sale = ของตัวเอง, อื่น ๆ = ทั้งหมด)
        $canSolve = true;                                    // เข้าได้ = แก้ได้

        // dropdown Sale (เฉพาะ role ที่เห็นทุกงาน)
        $saleOptions = $seeAll
            ? Cache::remember('wrongbill_sale_options', 1800, function () {
                return Bill::whereNotNull('sale_name')->where('sale_name', '!=', '')
                    ->distinct()->orderBy('sale_name')->pluck('sale_name')->values();
            })
            : collect();

        return view('sale.dashboardwrong', compact(
            'creator', 'autoLoad', 'canSolve', 'saleOptions', 'isSale', 'seeAll'
        ) + ['loginName' => $creator]);
    }

    /**
     * ดึงข้อมูล (AJAX)
     *   type   = wrong | hold | all   (ประเภทปัญหา)
     *   status = open | fixed | cleared | all
     */
    public function data(Request $request)
    {
        $user = $this->requireLogin();
        $role = $user->role ?? '';
        if (!in_array($role, $this->viewerRoles(), true)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $fSale = trim((string) $request->input('sale', ''));
        $fCust = trim((string) $request->input('customer', ''));
        $fBill = trim((string) $request->input('bill', ''));
        $fType = trim((string) $request->input('type', 'all'));     // wrong|hold|all
        $fStat = trim((string) $request->input('status', 'open'));  // open|fixed|cleared|all

        // sale เห็นเฉพาะงานของตัวเอง — บังคับ filter ด้วยชื่อตัวเอง
        if (!in_array($role, $this->seeAllRoles(), true)) {
            $fSale = $user->name ?? '';
        }

        // ===== 1) งานที่ยัง "ไม่แก้" = transaction_transport (ไม่ถูกยกเลิก) สถานะ สินค้าผิด/ค้างบิล =====
        $activeProblems = transaction_delivery::whereIn('status', [self::ST_WRONG, self::ST_HOLD])
            ->orderByDesc('check_time')
            ->get();

        $activeIds = $activeProblems->pluck('bill_id')->filter()->unique()->values()->all();

        // ===== 2) งานที่ "แก้แล้ว" (solve_at ถูกตั้งจากหน้านี้) =====
        $fixedBills = Bill::whereNotNull('solve_at')
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'sale_name', 'solve', 'solve_by', 'solve_at', 'statusdeli']);
        $fixedDocs = Docbills::whereNotNull('solve_at')
            ->get(['doc_id', 'id_com', 'com_name', 'contact_name', 'solve', 'solve_by', 'solve_at', 'statusdeli']);

        // resolve บิล/เอกสาร ของงานที่ยังไม่แก้
        $billsById = Bill::whereIn('so_detail_id', $activeIds)
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'sale_name', 'solve', 'solve_by', 'solve_at', 'statusdeli'])
            ->keyBy('so_detail_id');
        $docsById = Docbills::whereIn('doc_id', $activeIds)
            ->get(['doc_id', 'id_com', 'com_name', 'contact_name', 'solve', 'solve_by', 'solve_at', 'statusdeli'])
            ->keyBy('doc_id');

        // ===== รวมเป็น 1 แถวต่อ 1 บิล (key = bill:<billid> / doc:<doc_id>) =====
        $rows = [];   // key => row array

        // 2.1 งานยังไม่แก้
        foreach ($activeProblems as $d) {
            $bid = $d->bill_id;
            if ($billsById->has($bid)) {
                $b = $billsById->get($bid);
                if (!empty($b->solve_at)) continue;   // แก้ไปแล้ว -> ไปโผล่ในกลุ่ม fixed
                $key = 'bill:' . $b->billid;
                if (!isset($rows[$key])) {
                    $rows[$key] = $this->baseRow('bill', $b->billid, $b->so_id, $b->customer_id, $b->customer_name, $b->sale_name);
                    $rows[$key]['problem']    = $d->status;
                    $rows[$key]['reason']     = (string) ($d->note ?? '');
                    $rows[$key]['wrong_by']   = (string) ($d->check_name ?? '');
                    $rows[$key]['wrong_time'] = optional($d->check_time)->format('Y-m-d H:i');
                    $rows[$key]['driver']     = (string) ($d->driver_name ?? '');
                }
            } elseif ($docsById->has($bid)) {
                $doc = $docsById->get($bid);
                if (!empty($doc->solve_at)) continue;
                $key = 'doc:' . $bid;
                if (!isset($rows[$key])) {
                    $rows[$key] = $this->baseRow('doc', $bid, '', $doc->id_com, $doc->com_name, $doc->contact_name);
                    $rows[$key]['problem']    = $d->status;
                    $rows[$key]['reason']     = (string) ($d->note ?? '');
                    $rows[$key]['wrong_by']   = (string) ($d->check_name ?? '');
                    $rows[$key]['wrong_time'] = optional($d->check_time)->format('Y-m-d H:i');
                    $rows[$key]['driver']     = (string) ($d->driver_name ?? '');
                }
            }
        }

        // 2.2 งานแก้แล้ว (bill)
        foreach ($fixedBills as $b) {
            $key = 'bill:' . $b->billid;
            if (!isset($rows[$key])) {
                $rows[$key] = $this->baseRow('bill', $b->billid, $b->so_id, $b->customer_id, $b->customer_name, $b->sale_name);
            }
            $this->applySolve($rows[$key], $b->solve, $b->solve_by, $b->solve_at);
        }
        // 2.3 งานแก้แล้ว (doc)
        foreach ($fixedDocs as $doc) {
            $key = 'doc:' . $doc->doc_id;
            if (!isset($rows[$key])) {
                $rows[$key] = $this->baseRow('doc', $doc->doc_id, '', $doc->id_com, $doc->com_name, $doc->contact_name);
            }
            $this->applySolve($rows[$key], $doc->solve, $doc->solve_by, $doc->solve_at);
        }

        $rows = collect($rows)->values();
        if ($rows->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        // ===== หา "สถานะจัดส่ง" ของบิลเดิม + บิล/เอกสารปลายทาง เพื่อตัดสินว่าเคลียร์ =====
        $this->resolveCleared($rows);

        // ===== state + สี =====
        $rows = $rows->map(function ($r) {
            // problem type สำหรับ fixed ที่ไม่มี transport active -> เดาจากวิธีแก้
            if (empty($r['problem'])) {
                $r['problem'] = ($r['solve_method'] === 'tempdoc') ? self::ST_HOLD : self::ST_WRONG;
            }
            if ($r['cleared'])                     $r['state'] = 'cleared';
            elseif (!empty($r['solve_method']))    $r['state'] = 'fixed';    // แก้แล้ว รอผล
            else                                   $r['state'] = 'open';     // ยังไม่แก้

            $r['border'] = $this->borderColor($r);
            return $r;
        });

        // ===== filter =====
        if ($fSale !== '') $rows = $rows->filter(fn ($r) => stripos((string) $r['sale'], $fSale) !== false)->values();
        if ($fCust !== '') $rows = $rows->filter(fn ($r) =>
            stripos((string) $r['customer_code'], $fCust) !== false || stripos((string) $r['customer_name'], $fCust) !== false
        )->values();
        if ($fBill !== '') $rows = $rows->filter(fn ($r) =>
            stripos((string) $r['bill_no'], $fBill) !== false || stripos((string) $r['solve_target'], $fBill) !== false
        )->values();

        if ($fType === 'wrong') $rows = $rows->filter(fn ($r) => $r['problem'] === self::ST_WRONG)->values();
        elseif ($fType === 'hold') $rows = $rows->filter(fn ($r) => $r['problem'] === self::ST_HOLD)->values();

        if ($fStat === 'open')        $rows = $rows->filter(fn ($r) => $r['state'] === 'open')->values();
        elseif ($fStat === 'fixed')   $rows = $rows->filter(fn ($r) => $r['state'] === 'fixed')->values();
        elseif ($fStat === 'cleared') $rows = $rows->filter(fn ($r) => $r['state'] === 'cleared')->values();

        // เรียง: ยังไม่แก้ก่อน -> แก้แล้วรอผล -> เคลียร์แล้ว ; ในกลุ่มเรียงตามเวลาที่ผิดล่าสุด
        $order = ['open' => 0, 'fixed' => 1, 'cleared' => 2];
        $rows = $rows->sortBy(fn ($r) => ($order[$r['state']] ?? 9) . '|' . (9999999999 - strtotime($r['wrong_time'] ?: ($r['solve_at'] ?: '1970-01-01'))))->values();

        return response()->json(['ok' => true, 'rows' => $rows]);
    }

    private function baseRow($type, $billNo, $soId, $custCode, $custName, $sale): array
    {
        return [
            'job_key'       => $type . ':' . $billNo,
            'type'          => $type,
            'bill_no'       => (string) $billNo,
            'so_id'         => (string) $soId,
            'customer_code' => (string) $custCode,
            'customer_name' => (string) $custName,
            'sale'          => (string) $sale,
            'problem'       => '',
            'reason'        => '',
            'wrong_by'      => '',
            'wrong_time'    => '',
            'driver'        => '',
            'solve'         => '',
            'solve_by'      => '',
            'solve_at'      => '',
            'solve_method'  => '',   // resend | changebill | tempdoc | ''
            'solve_target'  => '',   // เลขบิล/เอกสารปลายทาง
            'cleared'       => false,
            'state'         => 'open',
            'border'        => '',
        ];
    }

    /** ตีความค่า solve เป็น method + target */
    private function applySolve(array &$row, $solve, $solveBy, $solveAt): void
    {
        $solve = trim((string) $solve);
        $row['solve']    = $solve;
        $row['solve_by'] = (string) $solveBy;
        $row['solve_at'] = $solveAt ? Carbon::parse($solveAt)->format('Y-m-d H:i') : '';

        if ($solve === '' || $solveAt === null) { $row['solve_method'] = ''; return; }

        if (mb_strpos($solve, 'ส่งใหม่') === 0) {
            $row['solve_method'] = 'resend';
            $row['solve_target'] = '';
        } elseif (mb_strpos($solve, 'เปลี่ยนบิล:') === 0) {
            $row['solve_method'] = 'changebill';
            $row['solve_target'] = trim(mb_substr($solve, mb_strlen('เปลี่ยนบิล:')));
        } elseif (mb_strpos($solve, 'เอกสารชั่วคราว:') === 0) {
            $row['solve_method'] = 'tempdoc';
            $row['solve_target'] = trim(mb_substr($solve, mb_strlen('เอกสารชั่วคราว:')));
        } else {
            // ค่าเก่า/อื่น ๆ — ถือเป็นเปลี่ยนบิล ถ้าไม่ใช่คำสั่งพิเศษ
            $row['solve_method'] = 'changebill';
            $row['solve_target'] = $solve;
        }
    }

    /** เติม cleared ให้ทุกแถว (batch หา status ปลายทาง) */
    private function resolveCleared(&$rows): void
    {
        // เลขบิล/เอกสารปลายทาง (changebill/tempdoc)
        $targets = $rows->pluck('solve_target')->filter()->unique()->values()->all();
        // บิลเดิม (resend) — ตรวจจากรอบล่าสุดที่ยังไม่ยกเลิกของ so_detail_id ของบิลเดิม
        $resendBillNos = $rows->filter(fn ($r) => $r['solve_method'] === 'resend' && $r['type'] === 'bill')
            ->pluck('bill_no')->filter()->unique()->values()->all();

        // สถานะจัดส่งของ "เลขบิล" ปลายทาง (tblbill.statusdeli) + doc
        $billDeli = [];
        if (!empty($targets)) {
            foreach (Bill::whereIn('billid', $targets)->get(['billid', 'statusdeli']) as $b) {
                $billDeli[(string) $b->billid] = (string) $b->statusdeli;
            }
            foreach (Docbills::whereIn('doc_id', $targets)->get(['doc_id', 'statusdeli']) as $d) {
                if (!isset($billDeli[(string) $d->doc_id])) $billDeli[(string) $d->doc_id] = (string) $d->statusdeli;
            }
        }
        // fallback: สถานะล่าสุดจาก transaction_transport ของเลขปลายทาง (เผื่อ statusdeli ไม่อัปเดต)
        // map billid -> so_detail_id
        $targetInner = [];
        if (!empty($targets)) {
            $soByBillid = Bill::whereIn('billid', $targets)->pluck('billid', 'so_detail_id'); // [so_detail_id => billid]
            $innerIds   = $soByBillid->keys()->merge($targets)->unique()->values()->all();     // doc: bill_id = doc_id
            if (!empty($innerIds)) {
                $tx = transaction_delivery::whereIn('bill_id', $innerIds)->orderBy('check_time')->get(['bill_id', 'status']);
                foreach ($tx as $t) {
                    $no = $soByBillid->get($t->bill_id) ?? (string) $t->bill_id;
                    $targetInner[(string) $no] = (string) $t->status;   // เก็บอันล่าสุด (loop เรียงเวลา)
                }
            }
        }

        // สถานะรอบล่าสุด (ไม่ยกเลิก) ของบิลเดิม (resend)
        $resendLatest = [];
        if (!empty($resendBillNos)) {
            $soByBillid = Bill::whereIn('billid', $resendBillNos)->pluck('billid', 'so_detail_id'); // [so_detail_id => billid]
            $innerIds   = $soByBillid->keys()->all();
            if (!empty($innerIds)) {
                $tx = transaction_delivery::whereIn('bill_id', $innerIds)->orderBy('check_time')->get(['bill_id', 'status']);
                foreach ($tx as $t) {
                    $no = $soByBillid->get($t->bill_id);
                    if ($no) $resendLatest[(string) $no] = (string) $t->status;   // อันล่าสุด (ยังไม่ยกเลิก, global scope)
                }
            }
        }

        $rows->transform(function ($r) use ($billDeli, $targetInner, $resendLatest) {
            $cleared = false;
            if ($r['solve_method'] === 'resend' && $r['type'] === 'bill') {
                $st = $resendLatest[$r['bill_no']] ?? null;
                $cleared = ($st === self::ST_SUCCESS);
            } elseif (in_array($r['solve_method'], ['changebill', 'tempdoc'], true) && $r['solve_target'] !== '') {
                $st = $billDeli[$r['solve_target']] ?? ($targetInner[$r['solve_target']] ?? null);
                $cleared = ($st === self::ST_SUCCESS);
            }
            $r['cleared'] = $cleared;
            return $r;
        });
    }

    private function borderColor(array $r): string
    {
        if ($r['state'] === 'cleared') return '#2e7d32';           // เขียว = จบ
        if ($r['state'] === 'fixed')   return '#2853d5';           // ฟ้า = แก้แล้ว รอผล
        // ยังไม่แก้ -> ตามประเภทปัญหา
        return ($r['problem'] === self::ST_HOLD) ? '#ed6c02' : '#c62828';   // ค้างบิล=ส้ม, ของผิด=แดง
    }

    /**
     * บันทึกวิธีแก้
     *   mode = resend | changebill | tempdoc | clear
     */
    public function solve(Request $request)
    {
        $user = $this->requireLogin();
        $role = $user->role ?? '';
        if (!in_array($role, $this->viewerRoles(), true)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $validated = $request->validate([
            'job_key'  => 'required|string',
            'mode'     => 'required|string|in:resend,changebill,tempdoc,clear',
            'target'   => 'nullable|string|max:100',
        ]);

        [$type, $rawId] = array_pad(explode(':', $validated['job_key'], 2), 2, null);
        if (!$rawId || !in_array($type, ['bill', 'doc'], true)) {
            return response()->json(['ok' => false, 'message' => 'รูปแบบงานไม่ถูกต้อง'], 422);
        }

        $item = ($type === 'bill')
            ? Bill::where('billid', $rawId)->first()
            : Docbills::where('doc_id', $rawId)->first();
        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบบิล/เอกสารนี้'], 404);
        }

        $userName = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');
        $now      = Carbon::now();

        // ยกเลิกการแก้ (กลับไปสถานะรอแก้)
        if ($validated['mode'] === 'clear') {
            $item->solve = null; $item->solve_by = null; $item->solve_at = null;
            $item->save();
            return response()->json(['ok' => true, 'message' => 'ยกเลิกการแก้ไขแล้ว — กลับไปสถานะรอแก้']);
        }

        // ── ส่งใหม่เลขบิลเดิม (ของผิด) : soft-cancel รอบที่ผิด คืนงานไปหน้าจ่ายงาน ──
        if ($validated['mode'] === 'resend') {
            if ($type !== 'bill') {
                return response()->json(['ok' => false, 'message' => 'ส่งใหม่เลขบิลเดิมใช้ได้กับบิลเท่านั้น'], 422);
            }
            $soIds = Bill::where('billid', $rawId)->pluck('so_detail_id');
            DB::transaction(function () use ($soIds, $userName, $now, $item) {
                $deliveries = transaction_delivery::whereIn('bill_id', $soIds)
                    ->where('status', self::ST_WRONG)->get();
                foreach ($deliveries as $d) {
                    $wentDate = $d->delivery_date ? Carbon::parse($d->delivery_date)->format('d/m/Y')
                        : (optional($d->time_pick)->format('d/m/Y') ?: '-');
                    $d->status       = 'ส่งใหม่';
                    $d->check_name   = $userName;
                    $d->check_time   = $now;
                    $d->cancelled_at = $now;
                    $d->cancelled_by = $userName;
                    $d->note         = 'ของผิด -> ส่งใหม่เลขบิลเดิม (เคยไปวันที่ ' . $wentDate . ' · คนขับ ' . ($d->driver_name ?: '-') . ') สั่งโดย ' . $userName;
                    $d->save();
                }
                $item->solve    = 'ส่งใหม่';
                $item->solve_by = $userName;
                $item->solve_at = $now;
                $item->save();
            });
            Log::info("wrongbill.solve resend {$validated['job_key']} by {$userName}");
            return response()->json(['ok' => true, 'message' => 'คืนงานไปหน้าจ่ายงานขนส่งแล้ว — ไปจ่ายให้คนขับใหม่ (เลขบิลเดิม) · จะเคลียร์เมื่อรอบใหม่ส่งสำเร็จ']);
        }

        // ── เปลี่ยนเลขบิล / เปิดเอกสารชั่วคราว : เก็บเลขปลายทาง ──
        $target = trim((string) ($validated['target'] ?? ''));
        if ($target === '') {
            return response()->json(['ok' => false, 'message' => 'กรุณากรอกเลข' . ($validated['mode'] === 'tempdoc' ? 'เอกสารชั่วคราว' : 'บิลใหม่')], 422);
        }
        if ($target === (string) $rawId) {
            return response()->json(['ok' => false, 'message' => 'เลขปลายทางต้องไม่ใช่เลขเดิม'], 422);
        }

        $prefix = $validated['mode'] === 'tempdoc' ? 'เอกสารชั่วคราว:' : 'เปลี่ยนบิล:';
        $item->solve    = $prefix . $target;
        $item->solve_by = $userName;
        $item->solve_at = $now;
        $item->save();

        Log::info("wrongbill.solve {$validated['mode']} {$validated['job_key']} -> {$target} by {$userName}");
        $msg = $validated['mode'] === 'tempdoc'
            ? 'ผูกเอกสารชั่วคราว "' . $target . '" แล้ว — จะเคลียร์เมื่อเอกสารนี้จัดส่งสำเร็จ'
            : 'เปลี่ยนเป็นบิลใหม่ "' . $target . '" แล้ว — จะเคลียร์เมื่อบิลใหม่จัดส่งสำเร็จ';
        return response()->json(['ok' => true, 'message' => $msg]);
    }
}
