<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Docbills;
use App\Models\transaction_delivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * หน้ารับเข้าบิล (/billreceive) — ระบบใหม่แทน panel ขวาในหน้า oil
 * ดึงงานจาก transaction_transport ตรง ๆ, กรองตาม time_pick (วันที่จ่ายงาน),
 * ค้นหาเลขบิลแบบไม่สนวันที่, รับเข้าทีละบิล (สำเร็จ/ค้างบิล/ส่งวันใหม่/สินค้าผิด)
 * สิทธิ์: role admin, store, accounting เท่านั้น
 */
class BillreceiveController extends Controller
{
    const DELI_STATUS_OK    = 'จัดส่งสำเร็จ';
    const DELI_STATUS_WRONG = 'สินค้าผิด';
    const DELI_STATUS_HOLD  = 'ค้างบิล';

    private const ERP_CONNECTION = 'mysql_3e';

    private function editorRoles(): array
    {
        return ['admin', 'store', 'accounting'];
    }

    private function requireEditorPage(Request $request)
    {
        if (!Auth::guard('web')->check()) return redirect()->guest(route('login'));
        if (!in_array(Auth::guard('web')->user()->role ?? '', $this->editorRoles(), true)) {
            abort(403, 'เฉพาะ admin, store, accounting เท่านั้นที่เข้าหน้านี้ได้');
        }
        return null;
    }

    private function requireEditorApi()
    {
        $user = Auth::guard('web')->user();
        if (!$user) return [null, response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401)];
        if (!in_array($user->role ?? '', $this->editorRoles(), true)) {
            return [null, response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการ (admin/store/accounting เท่านั้น)'], 403)];
        }
        return [$user, null];
    }

    /** role ที่ "เข้าดู" หน้านี้ได้ (รับเข้า/เปลี่ยนคนขับ = editor เท่านั้น) */
    private function viewerRoles(): array
    {
        return ['admin', 'store', 'accounting', 'sale', 'sale_assistant', 'support'];
    }

    private function canEdit($user = null): bool
    {
        $user = $user ?: Auth::guard('web')->user();
        return $user && in_array($user->role ?? '', $this->editorRoles(), true);
    }

    private function requireViewerPage(Request $request)
    {
        if (!Auth::guard('web')->check()) return redirect()->guest(route('login'));
        if (!in_array(Auth::guard('web')->user()->role ?? '', $this->viewerRoles(), true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }
        return null;
    }

    private function requireViewerApi()
    {
        $user = Auth::guard('web')->user();
        if (!$user) return [null, response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401)];
        if (!in_array($user->role ?? '', $this->viewerRoles(), true)) {
            return [null, response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403)];
        }
        return [$user, null];
    }

    private function userName($user = null): string
    {
        $user = $user ?: Auth::guard('web')->user();
        return $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? '-');
    }

    public function index(Request $request)
    {
        if ($resp = $this->requireViewerPage($request)) return $resp;
        return view('driver.billreceive', [
            'loggedInName'       => $this->userName(),
            'canEdit'            => $this->canEdit(),                        // admin/store/accounting = แก้ได้
            'deliveryMethods'    => config('delivery.methods', []),
            'responsiblePersons' => config('delivery.responsible_persons', []),
        ]);
    }

    /**
     * ดึงรายการงานจาก transaction_transport
     *   - q (เลขบิล) มีค่า -> ค้นหาแบบไม่สนวันที่
     *   - ไม่งั้น -> กรองตาม time_pick = date
     */
    public function data(Request $request)
    {
        [$user, $err] = $this->requireViewerApi();   // viewer เห็นได้, editor เท่านั้นที่กดรับเข้า/เปลี่ยนคนขับ
        if ($err) return $err;

        $q      = trim((string) $request->input('q', ''));
        $cust   = trim((string) $request->input('cust', ''));
        $cname  = trim((string) $request->input('cname', ''));
        $driver = trim((string) $request->input('driver', ''));
        $status = trim((string) $request->input('status', ''));   // ok|hold|wrong|pending|''
        $date   = $request->input('date');

        // มีตัวกรองใด ๆ (เลขบิล/รหัส/ชื่อลูกค้า/คนขับ/สถานะ) -> ไม่สนวันที่
        $ignoreDate = ($q !== '' || $cust !== '' || $cname !== '' || $driver !== '' || $status !== '');

        $query = transaction_delivery::query();

        if ($ignoreDate) {
            // โหมดค้นหา/กรอง -> ดึงแถวที่ถูกยกเลิก (cancelled) มาด้วย เพื่อให้เห็น "ประวัติรอบเก่า"
            // (เช่น รอบแรกสินค้าผิด/ส่งใหม่ -> soft-cancel -> จ่ายใหม่ -> รอบใหม่สำเร็จ)
            // หน้ารายวัน (ไม่มีตัวกรอง) ยังคงแสดงเฉพาะงาน active ตามเดิม
            $query->withoutGlobalScope('notCancelled');
            // สร้างชุด bill_id ที่ตรงแต่ละเงื่อนไข (เลขบิล/รหัส/ชื่อลูกค้า) แล้ว intersect
            $sets = [];
            if ($q !== '') {
                $sets[] = array_values(array_unique(array_merge(
                    Bill::where('billid', 'LIKE', "%{$q}%")->pluck('so_detail_id')->all(),
                    Docbills::where('doc_id', 'LIKE', "%{$q}%")->pluck('doc_id')->all()
                )));
            }
            if ($cust !== '') {
                $sets[] = array_values(array_unique(array_merge(
                    Bill::where('customer_id', 'LIKE', "%{$cust}%")->pluck('so_detail_id')->all(),
                    Docbills::where('id_com', 'LIKE', "%{$cust}%")->pluck('doc_id')->all()
                )));
            }
            if ($cname !== '') {
                $sets[] = array_values(array_unique(array_merge(
                    Bill::where('customer_name', 'LIKE', "%{$cname}%")->pluck('so_detail_id')->all(),
                    Docbills::where('com_name', 'LIKE', "%{$cname}%")->pluck('doc_id')->all()
                )));
            }
            if (!empty($sets)) {
                $billIds = array_shift($sets);
                foreach ($sets as $s) $billIds = array_values(array_intersect($billIds, $s));
                if (empty($billIds)) {
                    return response()->json(['ok' => true, 'rows' => []]);
                }
                $query->whereIn('bill_id', $billIds);
            }
            // กรองคนขับ (driver_name อยู่บน transaction_transport ตรง ๆ)
            if ($driver !== '') {
                $query->where('driver_name', 'LIKE', "%{$driver}%");
            }
            // status/คนขับ อย่างเดียว (ไม่มี bill/cust/cname) -> โหลดข้ามวัน (จำกัดจำนวน) แล้วกรองหลัง group
            $query->orderByDesc('time_pick')->limit(3000);
        } else {
            if (!$date) $date = Carbon::now()->toDateString();
            $query->whereDate('time_pick', $date)->orderByDesc('time_pick');
        }

        $deliveries = $query->get();
        if ($deliveries->isEmpty()) {
            return response()->json(['ok' => true, 'rows' => []]);
        }

        $ids = $deliveries->pluck('bill_id')->filter()->unique()->values();

        // time_pick ล่าสุดของแต่ละ bill_id (ทุกวัน) — ใช้ตรวจว่างานถูก "จ่ายใหม่" ไปวันหลังแล้วหรือยัง
        $latestByBill = transaction_delivery::whereIn('bill_id', $ids)
            ->get(['bill_id', 'time_pick'])
            ->groupBy('bill_id')
            ->map(function ($g) { return $g->max('time_pick'); });

        // resolve เลขบิล/ลูกค้า จาก 3 แหล่ง
        $billsBySoDetail = Bill::whereIn('so_detail_id', $ids)
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'transport_type'])
            ->keyBy('so_detail_id');
        $docs = Docbills::whereIn('doc_id', $ids)
            ->get(['doc_id', 'id_com', 'com_name'])
            ->keyBy('doc_id');

        // จัดกลุ่มเป็น 1 แถวต่อ 1 บิล — ดึงเฉพาะ บิล (บริษัท/เอกชน) + บิลชั่วคราว (doc)
        // ตัดงานไปรับของเอง (PONum ที่ไม่ใช่บิล/เอกสาร) ออก
        $grouped = [];
        foreach ($deliveries as $d) {
            $billId = $d->bill_id;
            if ($billsBySoDetail->has($billId)) {
                $b     = $billsBySoDetail->get($billId);
                // ประเภทขนส่ง: private = ขนส่งเอกชน, นอกนั้น (รวม null) = ส่งโดยบริษัท
                $type  = (($b->transport_type ?? '') === 'private') ? 'private' : 'company';
                $no    = (string) $b->billid;
                $key   = 'bill:' . $b->billid;
                $custC = (string) ($b->customer_id ?? '');
                $custN = (string) ($b->customer_name ?? '');
                $soId  = (string) ($b->so_id ?? '');
            } elseif ($docs->has($billId)) {
                $doc   = $docs->get($billId);
                $type  = 'doc';
                $no    = (string) $billId;
                $key   = 'doc:' . $billId;
                $custC = (string) ($doc->id_com ?? '');
                $custN = (string) ($doc->com_name ?? '');
                $soId  = '';
            } else {
                // งานไปรับของเอง (PO) -> ไม่ดึงมาหน้านี้
                continue;
            }

            // แยกเป็น 1 แถวต่อ "รอบจ่าย" (bill + วันที่ time_pick) — งานที่ถูกจ่ายใหม่ไปวันอื่น
            // จะเป็นคนละแถว ทำให้ค้นเลขบิลแล้วเห็นทั้งงานเดิม(ที่ถูกจ่ายใหม่) และงานใหม่(พร้อมผล)
            $dispatchDate = optional($d->time_pick)->format('Y-m-d') ?: 'nodate';
            $groupKey     = $key . '|' . $dispatchDate;

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'job_key'       => $key,   // ใช้ resolve tblbill/docbills (ระดับบิล)
                    'type'          => $type,
                    'bill_no'       => $no,
                    'so_id'         => $soId,
                    'customer_code' => $custC,
                    'customer_name' => $custN,
                    '_rows'         => collect(),
                ];
            }
            $grouped[$groupKey]['_rows']->push($d);
        }

        $rows = collect($grouped)->map(function ($g) use ($latestByBill) {
            $rowsCol = $g['_rows'];
            // ใช้แถวล่าสุด (time_pick) เป็นตัวแทนข้อมูลการจ่ายงาน/สถานะ
            $first = $rowsCol->sortByDesc('time_pick')->first();

            // ตรวจว่างานนี้ถูกจ่ายใหม่ไปวันหลังหรือยัง (มีแถว time_pick ใหม่กว่าตัวแทน)
            $repTime = $first->time_pick;
            $reTo = null;
            foreach ($rowsCol->pluck('bill_id')->unique() as $bid) {
                $latest = $latestByBill->get($bid);
                if ($latest && $repTime && $latest->gt($repTime)) {
                    if (!$reTo || $latest->gt($reTo)) $reTo = $latest;
                }
            }

            return [
                'job_key'        => $g['job_key'],
                'tx_ids'         => $rowsCol->pluck('id')->values()->all(),   // id ของรอบจ่ายนี้เท่านั้น
                'type'           => $g['type'],
                'bill_no'        => $g['bill_no'],
                'so_id'          => $g['so_id'],
                'customer_code'  => $g['customer_code'],
                'customer_name'  => $g['customer_name'],
                'name_pick'      => (string) ($first->name_pick ?? ''),   // ผู้จ่ายงาน
                'time_pick'      => optional($first->time_pick)->format('Y-m-d H:i'),
                'driver_name'    => (string) ($first->driver_name ?? ''),
                'transport_name' => (string) ($first->transport_name ?? ''),
                'delivery_date'  => optional($first->delivery_date)->format('Y-m-d'),
                'status'         => (string) ($first->status ?? ''),
                'note'           => (string) ($first->note ?? ''),
                'check_name'     => (string) ($first->check_name ?? ''),
                'check_time'     => optional($first->check_time)->format('Y-m-d H:i'),
                'confirmed'      => !empty($first->check_time),
                'redispatched_to'=> $reTo ? $reTo->format('Y-m-d') : null,   // ถูกจ่ายใหม่ไปวันที่ ...
                'cancelled'      => !empty($first->cancelled_at),            // แถวประวัติ (รอบเก่าที่ถูกแทนที่) -> อ่านอย่างเดียว
                'cancelled_by'   => (string) ($first->cancelled_by ?? ''),
                'cancelled_at'   => optional($first->cancelled_at)->format('Y-m-d H:i'),
            ];
        })->values();

        // กรองสถานะ (หลัง group ใช้สถานะของแถวตัวแทน)
        if ($status !== '') {
            $rows = $rows->filter(function ($r) use ($status) {
                $s = trim((string) $r['status']);
                $key = $s === 'จัดส่งสำเร็จ' ? 'ok'
                     : ($s === 'ค้างบิล' ? 'hold'
                     : ($s === 'สินค้าผิด' ? 'wrong' : 'pending'));
                return $key === $status;
            })->values();
        }

        if ($ignoreDate) {
            // โหมดกรอง -> เรียงวันที่ล่าสุดไปอดีต
            $rows = $rows->sortByDesc(function ($r) { return $r['time_pick'] ?? ''; })->values();
        } else {
            // โหมดรายวัน -> จัดกลุ่มตามขนส่ง (transport_name) ให้อยู่ติดกัน แล้วตามบิล/รอบจ่าย (ขนส่งว่างท้ายสุด)
            $rows = $rows->sortBy(function ($r) {
                $t = trim((string) $r['transport_name']);
                return ($t === '' ? '1|' : '0|' . $t) . '||' . $r['bill_no'] . '|' . ($r['time_pick'] ?? '');
            })->values();
        }

        // แสดงหน้าละ 100 รายการ
        $rows = $rows->take(100)->values();

        // เชื่อมโยงใบชั่วคราว (doc) -> บิลค้างที่ผูกไว้ (รายการสินค้าใน doc_detail ที่เป็นเลขบิลสถานะ 'ค้างบิล')
        $docNos = $rows->where('type', 'doc')->pluck('bill_no')->filter()->unique()->values()->all();
        $linkedByDoc = [];
        if (!empty($docNos)) {
            $details = DB::table('doc_detail')->whereIn('doc_id', $docNos)->get(['doc_id', 'item_name']);
            $cand = $details->pluck('item_name')->map(function ($s) { return trim((string) $s); })
                ->filter()->unique()->values()->all();
            $holdSet = [];
            if (!empty($cand)) {
                $holdSet = Bill::whereIn('billid', $cand)->where('statusdeli', 'ค้างบิล')
                    ->pluck('billid')->map(function ($b) { return (string) $b; })->unique()->flip()->all();
            }
            foreach ($details as $dt) {
                $name = trim((string) $dt->item_name);
                if ($name !== '' && isset($holdSet[$name])) $linkedByDoc[(string) $dt->doc_id][] = $name;
            }
            foreach ($linkedByDoc as $k => $v) $linkedByDoc[$k] = array_values(array_unique($v));
        }
        $rows = $rows->map(function ($r) use ($linkedByDoc) {
            $r['linked_bills'] = ($r['type'] === 'doc' && isset($linkedByDoc[$r['bill_no']]))
                ? $linkedByDoc[$r['bill_no']] : [];
            return $r;
        })->values();

        return response()->json(['ok' => true, 'rows' => $rows]);
    }

    /**
     * รับเข้าทีละบิล
     *   action = ok | hold | wrong | redo
     *   redo -> เปลี่ยน name_pick=ผู้ล็อกอิน, time_pick/delivery_date=วันที่เลือก, คืนสถานะรอจ่าย
     */
    public function confirm(Request $request)
    {
        [$user, $err] = $this->requireEditorApi();
        if ($err) return $err;

        $validated = $request->validate([
            'job_key'   => 'required|string',
            'action'    => 'required|string|in:ok,hold,wrong,redo',
            'note'      => 'nullable|string|max:1000',
            'redo_date' => 'nullable|date',
            'tx_ids'    => 'nullable|array',       // id ของ "รอบจ่าย" ที่จะทำ (ไม่กระทบรอบอื่น)
            'tx_ids.*'  => 'integer',
        ]);

        [$type, $rawId] = array_pad(explode(':', $validated['job_key'], 2), 2, null);
        if (!$rawId || !in_array($type, ['bill', 'doc', 'unknown'], true)) {
            return response()->json(['ok' => false, 'message' => 'รูปแบบงานไม่ถูกต้อง'], 422);
        }

        // ระดับบิล (ใช้ update tblbill/docbills)
        $billid = ($type === 'bill') ? $rawId : null;

        // รายการที่จะทำ: ถ้าส่ง tx_ids มา -> เฉพาะรอบจ่ายนั้น ไม่งั้น fallback ทั้งบิล
        if (!empty($validated['tx_ids'])) {
            $deliveries = transaction_delivery::whereIn('id', $validated['tx_ids'])->get();
        } elseif ($type === 'bill') {
            $soIds = Bill::where('billid', $billid)->pluck('so_detail_id');
            if ($soIds->isEmpty()) {
                return response()->json(['ok' => false, 'message' => 'ไม่พบเลขบิลนี้'], 404);
            }
            $deliveries = transaction_delivery::whereIn('bill_id', $soIds)->get();
        } else {
            $deliveries = transaction_delivery::where('bill_id', $rawId)->get();
        }
        if ($deliveries->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการจ่ายงานนี้'], 404);
        }

        $userName = $this->userName($user);
        $now      = Carbon::now();

        // ===== ส่งใหม่: คืนงานกลับไปหน้าจ่ายงาน (deliverytrack) เพื่อจ่ายให้คนขับใหม่ =====
        // ไม่กำหนดวันที่ที่นี่แล้ว -> soft-cancel งานจ่ายเดิม (cancelled_at) ; global scope จะซ่อนงานนี้
        // ทำให้งานกลับไปโผล่ในหน้าจ่ายงานขนส่ง แล้วค่อยจ่ายคนขับ/เลือกวันใหม่ที่นั่น
        if ($validated['action'] === 'redo') {
            DB::transaction(function () use ($deliveries, $userName, $now) {
                foreach ($deliveries as $d) {
                    // เก็บประวัติ: งานนี้เคยไปวันไหน คนขับใคร ผู้จ่ายงานใคร แล้วไม่สำเร็จ (ต้องส่งใหม่)
                    $wentDate = $d->delivery_date
                        ? Carbon::parse($d->delivery_date)->format('d/m/Y')
                        : (optional($d->time_pick)->format('d/m/Y') ?: '-');

                    $d->status       = 'ส่งใหม่';          // ประวัติ: ไม่สำเร็จ ต้องส่งใหม่
                    $d->check_name   = $userName;
                    $d->check_time   = $now;
                    $d->cancelled_at = $now;               // คืนงานไปหน้าจ่ายงาน (ซ่อนจากงาน active) แต่ยังเก็บเป็นประวัติ
                    $d->cancelled_by = $userName;
                    $d->note = 'ส่งใหม่ (ไม่สำเร็จ) เคยไปวันที่ ' . $wentDate
                             . ' · คนขับ ' . ($d->driver_name ?: '-')
                             . ' · จ่ายโดย ' . ($d->name_pick ?: '-')
                             . ' · สั่งส่งใหม่โดย ' . $userName . ' ' . $now->format('Y-m-d H:i');
                    $d->save();
                }
            });

            return response()->json([
                'ok'      => true,
                'action'  => 'redo',
                'message' => 'คืนงานไปหน้าจ่ายงานแล้ว — ไปจ่ายให้คนขับใหม่ได้ที่หน้าจ่ายงานขนส่ง',
            ]);
        }

        // ===== สำเร็จ / ค้างบิล / สินค้าผิด =====
        $statusMap = [
            'ok'    => self::DELI_STATUS_OK,
            'hold'  => self::DELI_STATUS_HOLD,
            'wrong' => self::DELI_STATUS_WRONG,
        ];
        $status = $statusMap[$validated['action']];
        $note   = trim((string) ($validated['note'] ?? ''));

        // ค้างบิล และ สินค้าผิด ต้องมีหมายเหตุ
        if (in_array($validated['action'], ['wrong', 'hold'], true) && $note === '') {
            $msg = $validated['action'] === 'hold' ? 'กรุณากรอกหมายเหตุค้างบิล' : 'กรุณากรอกหมายเหตุสินค้าผิด';
            return response()->json(['ok' => false, 'message' => $msg], 422);
        }

        DB::transaction(function () use ($deliveries, $status, $note, $userName, $now, $type, $billid, $rawId, $validated) {
            foreach ($deliveries as $d) {
                $d->status     = $status;
                $d->check_name = $userName;
                $d->check_time = $now;
                if ($note !== '') $d->note = $note;
                $d->save();
            }

            // อัปเดตไส้ในบิล/เอกสาร (statusdeli / NG) — งานไปรับเอง (unknown) ไม่มีไส้ใน
            $writeNg = in_array($validated['action'], ['wrong', 'hold'], true);
            if ($type === 'bill') {
                $upd = ['statusdeli' => $status];
                if ($writeNg) $upd['NG'] = $note;
                DB::table('tblbill')->where('billid', $billid)->update($upd);
            } elseif ($type === 'doc') {
                $upd = ['statusdeli' => $status];
                if ($writeNg) $upd['NG'] = $note;
                DB::table('docbills')->where('doc_id', $rawId)->update($upd);
            }
        });

        return response()->json([
            'ok'         => true,
            'action'     => $validated['action'],
            'status'     => $status,
            'check_name' => $userName,
            'check_time' => $now->format('Y-m-d H:i'),
            'message'    => 'บันทึกสถานะ "' . $status . '" แล้ว',
        ]);
    }

    /**
     * เปลี่ยนคนขับ/ขนส่ง แล้วบันทึกว่างานนี้ "จัดส่งสำเร็จ"
     *   - soft-cancel รอบเดิม + สร้าง row ใหม่ (status=จัดส่งสำเร็จ) พร้อมจดว่าเปลี่ยนจากใครเป็นใคร โดยใคร
     *   - เฉพาะ admin/store/accounting
     */
    public function changeDriver(Request $request)
    {
        [$user, $err] = $this->requireEditorApi();
        if ($err) return $err;

        $validated = $request->validate([
            'job_key'        => 'required|string',
            'tx_ids'         => 'required|array|min:1',
            'tx_ids.*'       => 'integer',
            'driver_name'    => 'nullable|string|max:255',
            'transport_name' => 'nullable|string|max:255',
        ]);

        [$type, $rawId] = array_pad(explode(':', $validated['job_key'], 2), 2, null);
        if (!$rawId || !in_array($type, ['bill', 'doc', 'unknown'], true)) {
            return response()->json(['ok' => false, 'message' => 'รูปแบบงานไม่ถูกต้อง'], 422);
        }
        $billid = ($type === 'bill') ? $rawId : null;

        $newDriver    = trim((string) ($validated['driver_name'] ?? ''));
        $newTransport = trim((string) ($validated['transport_name'] ?? ''));
        if ($newDriver === '' && $newTransport === '') {
            return response()->json(['ok' => false, 'message' => 'กรุณาเลือกคนขับหรือขนส่งใหม่'], 422);
        }

        $deliveries = transaction_delivery::whereIn('id', $validated['tx_ids'])->get();
        if ($deliveries->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการจ่ายงานนี้'], 404);
        }

        $userName = $this->userName($user);
        $now      = Carbon::now();
        $rep      = $deliveries->sortByDesc('time_pick')->first();
        $oldDriver      = $rep->driver_name ?: '-';
        $oldTransport   = $rep->transport_name ?: '-';
        $finalDriver    = $newDriver !== ''    ? $newDriver    : $oldDriver;
        $finalTransport = $newTransport !== '' ? $newTransport : $oldTransport;

        $parts = [];
        if ($finalDriver !== $oldDriver)       $parts[] = 'คนขับจาก ' . $oldDriver . ' เป็น ' . $finalDriver;
        if ($finalTransport !== $oldTransport) $parts[] = 'ขนส่งจาก ' . $oldTransport . ' เป็น ' . $finalTransport;
        $changeNote = 'เปลี่ยน' . (empty($parts) ? 'คนขับ/ขนส่ง' : implode(' · ', $parts)) . ' โดย ' . $userName;

        DB::transaction(function () use ($deliveries, $rep, $userName, $now, $finalDriver, $finalTransport, $changeNote, $type, $billid, $rawId) {
            // soft-cancel รอบเดิม (ถูกแทนที่ด้วยรอบใหม่)
            foreach ($deliveries as $d) {
                $d->cancelled_at = $now;
                $d->cancelled_by = $userName;
                $d->save();
            }
            // สร้างรอบใหม่ = จัดส่งสำเร็จ ด้วยคนขับ/ขนส่งใหม่
            transaction_delivery::create([
                'bill_id'        => $rep->bill_id,
                'name_pick'      => $rep->name_pick,
                'time_pick'      => $rep->time_pick,
                'transport_name' => $finalTransport,
                'driver_name'    => $finalDriver,
                'delivery_date'  => $rep->delivery_date,
                'status'         => self::DELI_STATUS_OK,
                'check_name'     => $userName,
                'check_time'     => $now,
                'note'           => $changeNote,
                'id_transport'   => $rep->id_transport,
            ]);
            // อัปเดตไส้ในบิล/เอกสาร -> จัดส่งสำเร็จ
            if ($type === 'bill') {
                DB::table('tblbill')->where('billid', $billid)->update(['statusdeli' => self::DELI_STATUS_OK]);
            } elseif ($type === 'doc') {
                DB::table('docbills')->where('doc_id', $rawId)->update(['statusdeli' => self::DELI_STATUS_OK]);
            }
        });

        Log::info("billreceive.changeDriver {$validated['job_key']} -> {$finalDriver} / {$finalTransport} by {$userName}");
        return response()->json([
            'ok'      => true,
            'message' => 'เปลี่ยนเป็นคนขับ "' . $finalDriver . '" / ขนส่ง "' . $finalTransport . '" และบันทึกจัดส่งสำเร็จแล้ว',
        ]);
    }
}
