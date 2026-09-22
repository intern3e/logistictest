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

    private function userName($user = null): string
    {
        $user = $user ?: Auth::guard('web')->user();
        return $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? '-');
    }

    public function index(Request $request)
    {
        if ($resp = $this->requireEditorPage($request)) return $resp;
        return view('driver.billreceive', [
            'loggedInName' => $this->userName(),
        ]);
    }

    /**
     * ดึงรายการงานจาก transaction_transport
     *   - q (เลขบิล) มีค่า -> ค้นหาแบบไม่สนวันที่
     *   - ไม่งั้น -> กรองตาม time_pick = date
     */
    public function data(Request $request)
    {
        [$user, $err] = $this->requireEditorApi();
        if ($err) return $err;

        $q    = trim((string) $request->input('q', ''));
        $date = $request->input('date');

        $query = transaction_delivery::query();

        if ($q !== '') {
            // ค้นด้วยเลขบิล -> ไม่สนวันที่ (จับทั้ง billid ใน tblbill, doc_id, และ bill_id ตรง ๆ)
            $soDetailIds = Bill::where('billid', 'LIKE', "%{$q}%")->pluck('so_detail_id')->all();
            $docIds      = Docbills::where('doc_id', 'LIKE', "%{$q}%")->pluck('doc_id')->all();
            $ids         = array_values(array_unique(array_merge($soDetailIds, $docIds)));
            $query->where(function ($w) use ($ids, $q) {
                if (!empty($ids)) $w->whereIn('bill_id', $ids);
                $w->orWhere('bill_id', 'LIKE', "%{$q}%");
            });
        } else {
            if (!$date) $date = Carbon::now()->toDateString();
            $query->whereDate('time_pick', $date);
        }

        $deliveries = $query->orderByDesc('time_pick')->get();
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
            ];
        })->values();

        // เรียง: จัดกลุ่มตามขนส่ง (transport_name) ให้อยู่ติดกัน -> แล้วตามบิล -> รอบจ่าย (เก่าก่อน)
        // ขนส่งว่างดันไปท้ายสุด
        $rows = $rows->sortBy(function ($r) {
            $t = trim((string) $r['transport_name']);
            return ($t === '' ? '1|' : '0|' . $t) . '||' . $r['bill_no'] . '|' . ($r['time_pick'] ?? '');
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

        // ===== ส่งวันใหม่: สร้าง row ใหม่ (เหมือนระบบเก่า) ไม่แก้ row เดิม =====
        if ($validated['action'] === 'redo') {
            if (empty($validated['redo_date'])) {
                return response()->json(['ok' => false, 'message' => 'กรุณาเลือกวันที่จะส่งใหม่'], 422);
            }
            $newDate = Carbon::parse($validated['redo_date']);
            $newTime = $newDate->copy()->setTime(9, 0, 0);   // เริ่ม 09:00 เหมือนระบบเก่า

            DB::transaction(function () use ($deliveries, $userName, $newTime) {
                // 1 so_detail_id เดิม -> สร้างงานส่งใหม่ 1 แถว โดยผู้จ่ายงาน = ผู้ล็อกอิน
                foreach ($deliveries as $d) {
                    $origDate = $d->delivery_date
                        ? Carbon::parse($d->delivery_date)->format('Y-m-d')
                        : (optional($d->time_pick)->format('Y-m-d') ?: '-');
                    $note = 'มีการให้ไปส่งใหม่จาก วันที่ ' . $origDate
                          . ' โดย ' . $userName . ' ' . $newTime->format('Y-m-d H:i:s');

                    transaction_delivery::create([
                        'bill_id'        => $d->bill_id,
                        'name_pick'      => $userName,
                        'time_pick'      => $newTime,
                        'transport_name' => $d->transport_name,
                        'driver_name'    => $d->driver_name,
                        'delivery_date'  => null,
                        'check_name'     => null,
                        'check_time'     => null,
                        'status'         => '0',
                        'note'           => $note,
                    ]);
                }
            });

            return response()->json([
                'ok'      => true,
                'action'  => 'redo',
                'message' => 'สร้างงานส่งใหม่วันที่ ' . $newDate->format('d/m/Y') . ' โดย ' . $userName . ' แล้ว',
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

        if ($validated['action'] === 'wrong' && $note === '') {
            return response()->json(['ok' => false, 'message' => 'กรุณากรอกหมายเหตุสินค้าผิด'], 422);
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
            if ($type === 'bill') {
                $upd = ['statusdeli' => $status];
                if ($validated['action'] === 'wrong') $upd['NG'] = $note;
                DB::table('tblbill')->where('billid', $billid)->update($upd);
            } elseif ($type === 'doc') {
                $upd = ['statusdeli' => $status];
                if ($validated['action'] === 'wrong') $upd['NG'] = $note;
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
}
