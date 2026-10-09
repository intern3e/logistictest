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

    /** role ที่เห็นได้ "ทุกงาน" (sale เห็นเฉพาะของตัวเอง) — ที่เหลือทุก role เข้าดูได้หมด */
    private function seeAllRoles(): array
    {
        return ['admin', 'support', 'sale_assistant', 'accounting', 'stock', 'store'];
    }

    /** จัดการได้ (บล็อก/ปลดบล็อก/ส่งใหม่/เคลียร์): เฉพาะ admin หรือผู้ใช้ชื่อ jun */
    private function canManage($user): bool
    {
        if (($user->role ?? '') === 'admin') return true;
        return strtolower(trim((string) ($user->name ?? ''))) === 'jun';
    }

    public function index()
    {
        $user = $this->requireLogin();           // ล็อกอินแล้วเข้าได้ทุก role
        $role = $user->role ?? '';

        $creator  = $user->name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');
        $isSale   = $role === 'sale';                       // เห็นเฉพาะงานตัวเอง
        $seeAll   = $role !== 'sale';                       // ทุก role ยกเว้น sale เห็นทั้งหมด
        $autoLoad = true;                                    // โหลดทันที
        $canSolve = $this->canManage($user);                // จัดการได้เฉพาะ admin

        // dropdown Sale (เฉพาะ role ที่เห็นทุกงาน)
        $saleOptions = $seeAll
            ? Cache::remember('wrongbill_sale_options', 1800, function () {
                return Bill::whereNotNull('sale_name')->where('sale_name', '!=', '')
                    ->distinct()->orderBy('sale_name')->pluck('sale_name')->values();
            })
            : collect();

        return view('sale.dashboardwrong', compact(
            'creator', 'autoLoad', 'canSolve', 'saleOptions', 'isSale', 'seeAll'
        ) + [
            'loginName'          => $creator,
            'deliveryMethods'    => config('delivery.methods', []),
            'responsiblePersons' => config('delivery.responsible_persons', []),
        ]);
    }

    /**
     * ดึงข้อมูล (AJAX)
     *   type   = wrong | hold | all   (ประเภทปัญหา)
     *   status = open | fixed | cleared | all
     */
    public function data(Request $request)
    {
        $user = $this->requireLogin();           // ล็อกอินแล้วเข้าได้ทุก role
        $role = $user->role ?? '';

        $fSale = trim((string) $request->input('sale', ''));
        $fCust = trim((string) $request->input('customer', ''));
        $fBill = trim((string) $request->input('bill', ''));
        $fType = trim((string) $request->input('type', 'all'));     // wrong|hold|all
        $fStat = trim((string) $request->input('status', 'open'));  // open|fixed|cleared|done|all

        // sale เห็นเฉพาะงานของตัวเอง — บังคับ filter ด้วยชื่อตัวเอง
        if ($role === 'sale') {
            $fSale = $user->name ?? '';
        }

        // ===== 1) งานที่ยัง "ไม่แก้" = transaction_transport (ไม่ถูกยกเลิก) สถานะ สินค้าผิด/ค้างบิล =====
        $activeProblems = transaction_delivery::whereIn('status', [self::ST_WRONG, self::ST_HOLD])
            ->orderByDesc('check_time')
            ->get();

        $activeIds = $activeProblems->pluck('bill_id')->filter()->unique()->values()->all();

        // ===== 2) งานที่ "แก้แล้ว" (solve_at ถูกตั้งจากหน้านี้) =====
        $fixedBills = Bill::whereNotNull('solve_at')
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'sale_name', 'emp_name', 'solve', 'solve_by', 'solve_at', 'statusdeli']);
        $fixedDocs = Docbills::whereNotNull('solve_at')
            ->get(['doc_id', 'so_id', 'id_com', 'com_name', 'contact_name', 'emp_name', 'solve', 'solve_by', 'solve_at', 'statusdeli']);

        // resolve บิล/เอกสาร ของงานที่ยังไม่แก้
        $billsById = Bill::whereIn('so_detail_id', $activeIds)
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'sale_name', 'emp_name', 'solve', 'solve_by', 'solve_at', 'statusdeli'])
            ->keyBy('so_detail_id');
        $docsById = Docbills::whereIn('doc_id', $activeIds)
            ->get(['doc_id', 'so_id', 'id_com', 'com_name', 'contact_name', 'emp_name', 'solve', 'solve_by', 'solve_at', 'statusdeli'])
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
                    $rows[$key] = $this->baseRow('bill', $b->billid, $b->so_id, $b->customer_id, $b->customer_name, $b->sale_name, $b->emp_name);
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
                    $rows[$key] = $this->baseRow('doc', $bid, $doc->so_id ?? '', $doc->id_com, $doc->com_name, $doc->contact_name, $doc->emp_name);
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
                $rows[$key] = $this->baseRow('bill', $b->billid, $b->so_id, $b->customer_id, $b->customer_name, $b->sale_name, $b->emp_name);
            }
            $rows[$key]['_bill_id'] = $b->so_detail_id;   // ใช้ดึง note เดิมของงานที่แก้แล้ว
            $this->applySolve($rows[$key], $b->solve, $b->solve_by, $b->solve_at);
        }
        // 2.3 งานแก้แล้ว (doc)
        foreach ($fixedDocs as $doc) {
            $key = 'doc:' . $doc->doc_id;
            if (!isset($rows[$key])) {
                $rows[$key] = $this->baseRow('doc', $doc->doc_id, $doc->so_id ?? '', $doc->id_com, $doc->com_name, $doc->contact_name, $doc->emp_name);
            }
            $rows[$key]['_bill_id'] = $doc->doc_id;       // doc: bill_id = doc_id
            $this->applySolve($rows[$key], $doc->solve, $doc->solve_by, $doc->solve_at);
        }

        // งานที่แก้แล้วยังไม่มี note/ผู้แจ้ง -> ดึงจาก transaction_transport เดิมของบิลนั้น (รวมที่ยกเลิกแล้ว)
        $needNote = [];
        foreach ($rows as $key => $r) {
            if (($r['reason'] ?? '') === '' && !empty($r['_bill_id'] ?? null)) {
                $needNote[(string) $r['_bill_id']][] = $key;
            }
        }
        if (!empty($needNote)) {
            $noteRows = DB::table('transaction_transport')   // ไม่ผ่าน global scope -> รวมแถวที่ยกเลิกด้วย
                ->whereIn('bill_id', array_keys($needNote))
                ->whereIn('status', [self::ST_WRONG, self::ST_HOLD])
                ->orderByDesc('check_time')
                ->get(['bill_id', 'note', 'check_name', 'check_time']);
            $noteByBill = [];
            foreach ($noteRows as $nr) {
                $bid = (string) $nr->bill_id;
                if (!isset($noteByBill[$bid])) $noteByBill[$bid] = $nr;   // อันล่าสุด (เรียง desc)
            }
            foreach ($needNote as $bid => $keys) {
                if (!isset($noteByBill[$bid])) continue;
                $nr = $noteByBill[$bid];
                foreach ($keys as $key) {
                    $rows[$key]['reason'] = (string) ($nr->note ?? '');
                    if (($rows[$key]['wrong_by'] ?? '') === '')   $rows[$key]['wrong_by']   = (string) ($nr->check_name ?? '');
                    if (($rows[$key]['wrong_time'] ?? '') === '') $rows[$key]['wrong_time'] = $nr->check_time ? Carbon::parse($nr->check_time)->format('Y-m-d H:i') : '';
                }
            }
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

        // ===== auto-block: ของผิดที่ยัง open = บล็อกไว้ก่อน (approved=0) จนกว่า admin จะอนุมัติ =====
        //   ทำบนชุดเต็ม (ก่อน filter) — open ที่ยังไม่มี rule -> insert approved=0 ; งานที่แก้แล้ว -> ลบ rule (ปลดบล็อก)
        //   server_update so/show อ่าน flag นี้อย่างเดียว (ไม่คำนวณเลขบิลเอง)
        //   บล็อกจับตาม so เท่านั้น — บิลชั่วคราวที่ไม่มี so จะไม่ถูกบล็อก (เตะออกอย่างเดียว)
        $keyOf = function ($r) {
            return (string) ($r['so_id'] ?? '');
        };
        //   เฉพาะ "สินค้าผิด" เท่านั้นที่บล็อก — ค้างบิลไม่บล็อก (เปิดเอกสารชั่วคราวแล้วจะหายเอง)
        $openKeys = $rows->filter(fn ($r) => $r['state'] === 'open' && $r['problem'] === self::ST_WRONG)->map($keyOf)->filter()->unique()->values()->all();
        $resolvedKeys = $rows->filter(fn ($r) => !($r['state'] === 'open' && $r['problem'] === self::ST_WRONG))->map($keyOf)->filter()->unique()->values()->all();
        $resolvedKeys = array_values(array_diff($resolvedKeys, $openKeys));   // ถ้ายังมี open อยู่ ไม่ลบ
        try {
            if (!empty($openKeys)) {
                $existing = DB::table('wrong_so_approvals')->whereIn('so_id', $openKeys)->pluck('so_id')->all();
                $toInsert = array_values(array_diff($openKeys, $existing));
                if (!empty($toInsert)) {
                    $now = now();
                    DB::table('wrong_so_approvals')->insert(array_map(fn ($k) => [
                        'so_id' => $k, 'approved' => 0, 'approved_by' => null, 'approved_at' => null,
                        'created_at' => $now, 'updated_at' => $now,
                    ], $toInsert));
                }
            }
            if (!empty($resolvedKeys)) {
                DB::table('wrong_so_approvals')->whereIn('so_id', $resolvedKeys)->delete();
            }
        } catch (\Throwable $e) { /* ไม่ให้หน้าพังเพราะ sync */ }

        // ===== filter =====
        if ($fSale !== '') $rows = $rows->filter(fn ($r) => stripos((string) $r['sale'], $fSale) !== false)->values();
        if ($fCust !== '') $rows = $rows->filter(fn ($r) =>
            stripos((string) $r['customer_code'], $fCust) !== false || stripos((string) $r['customer_name'], $fCust) !== false
        )->values();
        if ($fBill !== '') $rows = $rows->filter(fn ($r) =>
            stripos((string) $r['bill_no'], $fBill) !== false || stripos((string) $r['solve_target'], $fBill) !== false
        )->values();

        // นับจำนวนต่อหมวด (หลัง filter sale/cust/bill, ก่อน filter type/status) — ใช้โชว์ badge บนแท็บ
        $counts = [
            'wrong' => $rows->filter(fn ($r) => $r['problem'] === self::ST_WRONG && $r['state'] === 'open')->count(),
            'hold'  => $rows->filter(fn ($r) => $r['problem'] === self::ST_HOLD && $r['state'] === 'open')->count(),
            'done'  => $rows->filter(fn ($r) => in_array($r['state'], ['fixed', 'cleared'], true))->count(),
        ];

        if ($fType === 'wrong') $rows = $rows->filter(fn ($r) => $r['problem'] === self::ST_WRONG)->values();
        elseif ($fType === 'hold') $rows = $rows->filter(fn ($r) => $r['problem'] === self::ST_HOLD)->values();

        // ชนิดบิล: bill = บิลส่งของ (tblbill) , doc = บิลชั่วคราว (docbills)
        $fKind = trim((string) $request->input('kind', 'all'));
        if ($fKind === 'bill')     $rows = $rows->filter(fn ($r) => $r['type'] === 'bill')->values();
        elseif ($fKind === 'doc')  $rows = $rows->filter(fn ($r) => $r['type'] === 'doc')->values();

        if ($fStat === 'open')        $rows = $rows->filter(fn ($r) => $r['state'] === 'open')->values();
        elseif ($fStat === 'fixed')   $rows = $rows->filter(fn ($r) => $r['state'] === 'fixed')->values();
        elseif ($fStat === 'cleared') $rows = $rows->filter(fn ($r) => $r['state'] === 'cleared')->values();
        elseif ($fStat === 'done')    $rows = $rows->filter(fn ($r) => in_array($r['state'], ['fixed', 'cleared'], true))->values();   // แก้ไขแล้ว (รวม fixed+cleared)

        // เรียง: ยังไม่แก้ก่อน -> แก้แล้วรอผล -> เคลียร์แล้ว ; ในกลุ่มเรียงตามเวลาที่ผิดล่าสุด
        $order = ['open' => 0, 'fixed' => 1, 'cleared' => 2];
        $rows = $rows->sortBy(fn ($r) => ($order[$r['state']] ?? 9) . '|' . (9999999999 - strtotime($r['wrong_time'] ?: ($r['solve_at'] ?: '1970-01-01'))))->values();

        // ===== สถานะอนุมัติ/บล็อก (ของผิด) — จับตาม so เท่านั้น =====
        //   บิลที่มี so -> บล็อก/ปลดบล็อกได้ ; บิลชั่วคราวที่ไม่มี so -> ไม่มีบล็อก (เตะออกอย่างเดียว)
        $rows = $rows->map(function ($r) {
            $r['approve_key'] = (string) ($r['so_id'] ?? '');
            return $r;
        });
        $keys = $rows->pluck('approve_key')->filter()->unique()->values()->all();
        $appr = collect();
        if (!empty($keys)) {
            $appr = DB::table('wrong_so_approvals')->whereIn('so_id', $keys)->get()->keyBy('so_id');
        }
        $canManage = $this->canManage($user);
        $rows = $rows->map(function ($r) use ($appr, $canManage) {
            $hasSo = ($r['so_id'] ?? '') !== '';
            $a = $hasSo ? $appr->get($r['approve_key']) : null;
            $r['has_so']       = $hasSo;                          // มี so ไหม (มี = บล็อกได้)
            $r['has_rule']     = (bool) $a;                       // มีการตั้งค่าไว้ไหม
            $r['approved']     = (bool) ($a->approved ?? false);  // อนุมัติแล้ว (จัดส่งได้)
            $r['blocked']      = $a && empty($a->approved);       // ตั้งค่าบล็อก + ยังไม่อนุมัติ
            $r['approved_by']  = $a->approved_by ?? null;
            $r['approved_at']  = isset($a->approved_at) && $a->approved_at ? \Carbon\Carbon::parse($a->approved_at)->format('d/m/Y H:i') : null;
            $r['can_manage']   = $canManage;   // admin เท่านั้น: บล็อก/ปลดบล็อก/เตะออก
            return $r;
        })->values();

        return response()->json(['ok' => true, 'rows' => $rows, 'counts' => $counts]);
    }

    /** เปิด/ปิด อนุมัติของผิด ราย SO (admin เท่านั้น) — toggle ได้ */
    public function toggleApprove(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canManage($user)) {
            return response()->json(['ok' => false, 'message' => 'เฉพาะ admin เท่านั้น'], 403);
        }
        $data = $request->validate([
            'so_id'    => 'required|string|max:50',
            'approved' => 'required|boolean',
        ]);
        $soId = trim($data['so_id']);
        $approved = (bool) $data['approved'];
        $actor = $user->name ?? null;
        $now = now();

        // บันทึกผู้ทำ+เวลาเสมอ (ทั้งตอนบล็อกและอนุมัติ) — approved=1 จัดส่งได้, approved=0 บล็อก
        DB::table('wrong_so_approvals')->updateOrInsert(
            ['so_id' => $soId],
            [
                'approved'    => $approved ? 1 : 0,
                'approved_by' => $actor,
                'approved_at' => $now,
                'updated_at'  => $now,
                'created_at'  => $now,
            ]
        );

        return response()->json([
            'ok'          => true,
            'approved'    => $approved,
            'blocked'     => !$approved,
            'has_rule'    => true,
            'approved_by' => $actor,
            'approved_at' => $now->format('d/m/Y H:i'),
        ]);
    }

    /**
     * เตะออกจากของผิด (admin เท่านั้น) — มาร์คงานว่าแก้แล้ว (ย้ายไปหมวด "แก้ไขแล้ว")
     *   bill  -> tblbill.solve_at ; doc -> docbills.solve_at ; แล้วลบ rule บล็อกของ so (ปลดบล็อก)
     *   body: { job_key: "bill:<billid>" | "doc:<doc_id>" }
     */
    public function dismiss(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canManage($user)) {
            return response()->json(['ok' => false, 'message' => 'เฉพาะ admin เท่านั้น'], 403);
        }
        $data = $request->validate(['job_key' => 'required|string|max:120']);
        $jobKey = trim($data['job_key']);
        $pos = strpos($jobKey, ':');
        if ($pos === false) {
            return response()->json(['ok' => false, 'message' => 'job_key ไม่ถูกต้อง'], 422);
        }
        $type = substr($jobKey, 0, $pos);
        $no   = substr($jobKey, $pos + 1);
        $actor = $user->name ?? '';
        $now = now();
        $soId = null;

        if ($type === 'bill') {
            $b = Bill::where('billid', $no)->first(['so_detail_id', 'so_id']);
            if (!$b) return response()->json(['ok' => false, 'message' => 'ไม่พบบิล'], 404);
            $soId = $b->so_id;
            Bill::where('so_detail_id', $b->so_detail_id)->update([
                'solve' => 'เคลียร์ข้อมูล', 'solve_by' => $actor, 'solve_at' => $now,
            ]);
        } elseif ($type === 'doc') {
            $d = Docbills::where('doc_id', $no)->first(['doc_id', 'so_id']);
            if (!$d) return response()->json(['ok' => false, 'message' => 'ไม่พบเอกสาร'], 404);
            $soId = $d->so_id;
            Docbills::where('doc_id', $no)->update([
                'solve' => 'เคลียร์ข้อมูล', 'solve_by' => $actor, 'solve_at' => $now,
            ]);
        } else {
            return response()->json(['ok' => false, 'message' => 'ชนิดงานไม่ถูกต้อง'], 422);
        }

        // ปลดบล็อก: ลบ rule ของ so นี้ (ถ้ามี) — เตะออกแล้วไม่ต้องบล็อก
        if (!empty($soId)) {
            DB::table('wrong_so_approvals')->where('so_id', $soId)->delete();
        }

        return response()->json(['ok' => true, 'message' => 'เตะออกจากของผิดแล้ว']);
    }

    private function baseRow($type, $billNo, $soId, $custCode, $custName, $sale, $empName = ''): array
    {
        return [
            'job_key'       => $type . ':' . $billNo,
            'type'          => $type,
            'bill_no'       => (string) $billNo,
            'so_id'         => (string) $soId,
            'customer_code' => (string) $custCode,
            'customer_name' => (string) $custName,
            'sale'          => (string) $sale,
            'emp_name'      => (string) $empName,   // ผู้เปิดบิล (tblbill/docbills.emp_name)
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

        if (mb_strpos($solve, 'เคลียร์') === 0 || mb_strpos($solve, 'เตะออก') === 0) {
            $row['solve_method'] = 'dismiss';
            $row['solve_target'] = '';
        } elseif (mb_strpos($solve, 'ส่งใหม่') === 0) {
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
        if (!$this->canManage($user)) {
            return response()->json(['ok' => false, 'message' => 'เฉพาะ admin เท่านั้น'], 403);
        }

        $validated = $request->validate([
            'job_key'  => 'required|string',
            'mode'     => 'required|string|in:resend,changebill,tempdoc',
            'target'   => 'nullable|string|max:100',
            // ส่งใหม่เลขบิลเดิม: เลือกวิธี (เหมือนหน้า billreceive)
            //   return = คืนไปหน้าจ่ายงานขนส่ง, assign = จ่ายใหม่ที่นี่เลย (คนขับ/ขนส่ง/วันที่)
            'redo_mode'      => 'nullable|string|in:return,assign',
            'redo_driver'    => 'nullable|string|max:255',
            'redo_transport' => 'nullable|string|max:255',
            'redo_date'      => 'nullable|date',
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

        // หมายเหตุ: ปิดการ "ยกเลิกการแก้ไข" (mode=clear) แล้ว — แก้ไปแล้วย้อนกลับไม่ได้

        // ── ส่งใหม่เลขบิลเดิม (ของผิด/ค้างบิล) : soft-cancel รอบเดิม แล้ว ──
        //   - return (ค่าเดิม): คืนงานไปหน้าจ่ายงานขนส่ง ให้เลือกคนขับ/วันใหม่ที่นั่น
        //   - assign: จ่ายงานใหม่ที่นี่เลย (ผู้รับผิดชอบ / วิธีการจัดส่ง / วันที่ไปส่ง) -> สร้างแถวจ่ายงานใหม่ทันที
        if ($validated['mode'] === 'resend') {
            // รองรับทั้งบิล (tblbill) และเอกสารชั่วคราว SP (docbills) — รวม SP ที่ไม่ได้เชื่อม SO
            $redoMode     = $validated['redo_mode'] ?? 'return';
            $newDriver    = trim((string) ($validated['redo_driver'] ?? ''));
            $newTransport = trim((string) ($validated['redo_transport'] ?? ''));
            $newDate      = $validated['redo_date'] ?? null;

            if ($redoMode === 'assign') {
                if ($newTransport === '') {
                    return response()->json(['ok' => false, 'message' => 'กรุณาเลือกวิธีการจัดส่ง'], 422);
                }
                if (!$newDate) {
                    return response()->json(['ok' => false, 'message' => 'กรุณาเลือกวันที่ไปส่ง'], 422);
                }
                if ($newTransport === 'เซลล์ไปส่งเอง' && $newDriver === '') {
                    return response()->json(['ok' => false, 'message' => 'เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเองด้วย'], 422);
                }
                if ($newTransport !== 'เซลล์ไปส่งเอง' && $newDriver !== ''
                    && !in_array($newDriver, config('delivery.responsible_persons', []), true)) {
                    return response()->json(['ok' => false, 'message' => 'กรุณาเลือกผู้รับผิดชอบจากรายการที่มีให้เท่านั้น'], 422);
                }
            }

            // bill: delivery.bill_id = tblbill.so_detail_id ; doc: delivery.bill_id = doc_id ตรง ๆ
            $billIds = ($type === 'bill')
                ? Bill::where('billid', $rawId)->pluck('so_detail_id')->all()
                : [$rawId];
            DB::transaction(function () use ($billIds, $userName, $now, $item, $redoMode, $newDriver, $newTransport, $newDate) {
                $deliveries = transaction_delivery::whereIn('bill_id', $billIds)
                    ->whereIn('status', [self::ST_WRONG, self::ST_HOLD])->get();
                foreach ($deliveries as $d) {
                    $wentDate = $d->delivery_date ? Carbon::parse($d->delivery_date)->format('d/m/Y')
                        : (optional($d->time_pick)->format('d/m/Y') ?: '-');
                    $d->status       = 'ส่งใหม่';
                    $d->check_name   = $userName;
                    $d->check_time   = $now;
                    $d->cancelled_at = $now;
                    $d->cancelled_by = $userName;
                    $d->note         = 'ของผิด/ค้างบิล -> ส่งใหม่เลขบิลเดิม (เคยไปวันที่ ' . $wentDate . ' · คนขับ ' . ($d->driver_name ?: '-') . ') สั่งโดย ' . $userName
                                     . ($redoMode === 'assign'
                                        ? ' · จ่ายใหม่ให้ ' . ($newDriver ?: '-') . ' / ' . $newTransport . ' วันที่ ' . Carbon::parse($newDate)->format('d/m/Y')
                                        : '');
                    $d->save();
                }

                // assign: จ่ายงานใหม่ 1 แถวต่อ bill_id (เหมือนหน้าจ่ายงาน) — ข้ามถ้ายังมีงาน active
                if ($redoMode === 'assign') {
                    foreach ($deliveries->pluck('bill_id')->filter()->unique() as $billId) {
                        if (transaction_delivery::where('bill_id', $billId)->exists()) continue;
                        transaction_delivery::create([
                            'bill_id'        => $billId,
                            'name_pick'      => $userName,
                            'time_pick'      => $now,
                            'delivery_date'  => $newDate,
                            'transport_name' => $newTransport,
                            'driver_name'    => $newDriver !== '' ? $newDriver : null,
                            'check_name'     => null,
                            'check_time'     => null,
                            'status'         => '0',
                            'note'           => null,
                        ]);
                    }
                }

                $item->solve    = 'ส่งใหม่';
                $item->solve_by = $userName;
                $item->solve_at = $now;
                $item->save();
            });
            Log::info("wrongbill.solve resend({$redoMode}) {$validated['job_key']} by {$userName}");
            return response()->json(['ok' => true, 'message' => $redoMode === 'assign'
                ? ('จ่ายงานใหม่ (เลขบิลเดิม) ให้ ' . ($newDriver ?: $newTransport) . ' วันที่ ' . Carbon::parse($newDate)->format('d/m/Y') . ' แล้ว · จะเคลียร์เมื่อรอบใหม่ส่งสำเร็จ')
                : 'คืนงานไปหน้าจ่ายงานขนส่งแล้ว — ไปจ่ายให้คนขับใหม่ (เลขบิลเดิม) · จะเคลียร์เมื่อรอบใหม่ส่งสำเร็จ']);
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