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

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'job_key'       => $key,
                    'type'          => $type,
                    'bill_no'       => $no,
                    'so_id'         => $soId,
                    'customer_code' => $custC,
                    'customer_name' => $custN,
                    '_rows'         => collect(),
                ];
            }
            $grouped[$key]['_rows']->push($d);
        }

        $rows = collect($grouped)->map(function ($g) {
            $rowsCol = $g['_rows'];
            // ใช้แถวล่าสุด (time_pick) เป็นตัวแทนข้อมูลการจ่ายงาน/สถานะ
            $first = $rowsCol->sortByDesc('time_pick')->first();
            return [
                'job_key'        => $g['job_key'],
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
            ];
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
        ]);

        [$type, $rawId] = array_pad(explode(':', $validated['job_key'], 2), 2, null);
        if (!$rawId || !in_array($type, ['bill', 'doc', 'unknown'], true)) {
            return response()->json(['ok' => false, 'message' => 'รูปแบบงานไม่ถูกต้อง'], 422);
        }

        // หา transaction_transport ที่เกี่ยวข้องกับบิลนี้
        $billid = null;
        if ($type === 'bill') {
            $billid = $rawId;
            $soIds  = Bill::where('billid', $billid)->pluck('so_detail_id');
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

        // ===== ส่งวันใหม่: เปลี่ยนผู้จ่ายงาน/วันจ่ายเป็นของผู้ล็อกอิน + วันที่เลือก =====
        if ($validated['action'] === 'redo') {
            if (empty($validated['redo_date'])) {
                return response()->json(['ok' => false, 'message' => 'กรุณาเลือกวันที่จะส่งใหม่'], 422);
            }
            $newDate = Carbon::parse($validated['redo_date']);
            $newTime = $newDate->copy()->setTime((int) $now->format('H'), (int) $now->format('i'), 0);

            DB::transaction(function () use ($deliveries, $userName, $newDate, $newTime) {
                foreach ($deliveries as $d) {
                    $d->name_pick     = $userName;
                    $d->time_pick     = $newTime;
                    $d->delivery_date = $newDate->toDateString();
                    $d->status        = '0';   // กลับเป็นรอส่ง (ยังไม่ยืนยันผล)
                    $d->check_name    = null;
                    $d->check_time    = null;
                    $d->save();
                }
            });

            return response()->json([
                'ok'      => true,
                'action'  => 'redo',
                'message' => 'ส่งใหม่วันที่ ' . $newDate->format('d/m/Y') . ' โดย ' . $userName,
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
