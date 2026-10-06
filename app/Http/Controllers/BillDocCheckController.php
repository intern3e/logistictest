<?php

namespace App\Http\Controllers;

use App\Models\BillDocCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ตรวจเอกสารบิล vs ข้อมูลในระบบ (งานบัญชี)
 *
 * แนวคิด:
 *   - sync เลขบิลทั้งเดือนจากระบบ (ERP) ลงตาราง bill_doc_checks  -> ได้ "รายการบิลที่ควรมี"
 *   - คนติ๊ก หรือ bot/OCR ภายนอกยิงเข้ามาว่า "เจอเอกสารใบไหน"      -> has_document = true
 *   - รายงาน: เลขบิลที่ยังไม่มีเอกสาร / ข้อมูลไม่ตรง / มีเอกสารแต่ไม่มีในระบบ
 *
 * ยังไม่ทำ OCR ในระบบ — OCR ทำข้างนอกแล้วยิงผลเข้าทาง botMatch()
 */
class BillDocCheckController extends Controller
{
    const ERP_CONNECTION    = 'mssql_account03';   // SOInvHD / SOInvDT (บิลขาย)
    const LEGACY_CONNECTION = 'mysql_3e';          // so (ลูกค้า)
    const VAT_RATE          = 1.07;
    const AMOUNT_TOLERANCE  = 0.05;                // ผลต่างยอดที่ยอมรับว่า "ตรง"

    /** สิทธิ์เข้าใช้ — เฉพาะ admin เท่านั้น */
    private function canUse($user): bool
    {
        return ($user->role ?? '') === 'admin';
    }

    /** หน้าเว็บ — โหลดแค่ตัวกรอง */
    public function index(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        // รายชื่อเดือนที่เคย sync ไว้ + เดือนปัจจุบัน
        $periods = BillDocCheck::select('period')->distinct()->orderByDesc('period')->pluck('period')->all();
        $thisPeriod = Carbon::now()->format('Y-m');
        if (!in_array($thisPeriod, $periods, true)) array_unshift($periods, $thisPeriod);

        return view('account.bill_doc_check', [
            'periods'    => $periods,
            'thisPeriod' => $thisPeriod,
            'loginName'  => $user->name ?? '',
            'canManage'  => true,
        ]);
    }

    /** ดึงรายการบิลของเดือน (+ตัวกรองประเภท/สถานะ) สำหรับ render ตาราง */
    public function data(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $period = $this->normPeriod($request->input('period'));
        $type   = trim((string) $request->input('type', ''));   // '' | สินค้า | บริการ | none(ไม่ระบุ)
        $status = trim((string) $request->input('status', ''));  // '' | has | missing | mismatch | doc_no_system
        $q      = trim((string) $request->input('q', ''));

        $query = BillDocCheck::where('period', $period)->where('bill_no', 'LIKE', '4%');

        if ($type === 'none')      $query->whereNull('bill_type');
        elseif ($type !== '')      $query->where('bill_type', $type);

        if ($status === 'has')               $query->where('has_document', true);
        elseif ($status === 'missing')       $query->where('has_document', false)->where('not_found', false)->where('in_system', true);
        elseif ($status === 'notfound')      $query->where('not_found', true);
        elseif ($status === 'notsigned')     $query->where('not_signed', true);
        elseif ($status === 'mismatch')      $query->where('match_status', BillDocCheck::M_MISMATCH);
        elseif ($status === 'doc_no_system') $query->where('match_status', BillDocCheck::M_DOC_NO_SYS);
        elseif ($status === 'cancelled')     $query->where('cancelled', true);
        elseif ($status === 'noted')         $query->whereNotNull('note')->where('note', '<>', '');

        if ($q !== '') {
            if (ctype_digit($q)) {
                // พิมพ์ตัวเลขล้วน = เลขรันนิ่ง -> แสดงตั้งแต่เลขนี้เป็นต้นไป (เลขท้ายหลัง '-')
                $query->whereRaw("CAST(SUBSTRING_INDEX(bill_no, '-', -1) AS UNSIGNED) >= ?", [(int) $q]);
            } else {
                $query->where('bill_no', 'LIKE', "%{$q}%");   // ค้นแบบมีตัวอักษร = substring
            }
        }

        // ตรวจแล้ว (พบ หรือ ไม่พบ) ลงไปอยู่ล่างสุด ; ที่ยังไม่ตรวจอยู่บน
        $models = $query->orderByRaw('(has_document OR not_found) asc')->orderBy('bill_no')->limit(5000)->get();

        // รายละเอียดจากระบบ logistic (tblbill + transaction_transport) เชื่อมด้วย bill_issue_no = เลขบิล (ERP InvNo)
        $detail = $this->logisticDetail($models->pluck('bill_no')->all());

        $rows = $models->map(fn ($r) => $this->rowOut($r, $detail[$r->bill_no] ?? null));

        return response()->json([
            'ok'      => true,
            'period'  => $period,
            'rows'    => $rows,
            'summary' => $this->summaryFor($period),
        ]);
    }

    /** สรุปภาพรวมของเดือน (การ์ด + รายงาน) */
    public function report(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }
        $period = $this->normPeriod($request->input('period'));
        return response()->json(['ok' => true, 'period' => $period, 'summary' => $this->summaryFor($period)]);
    }

    /** หน้า PDF สรุปยอด (A4) — พิมพ์/บันทึกเป็น PDF จากเบราว์เซอร์ */
    public function reportPdf(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }
        $period = $this->normPeriod($request->input('period'));

        $base = BillDocCheck::where('period', $period)->where('bill_no', 'LIKE', '4%');
        $agg = function ($q) {
            return [
                'count'  => (clone $q)->count(),
                'before' => (float) (clone $q)->sum('amount_before_vat'),
                'vat'    => (float) (clone $q)->sum('vat_amount'),
                'after'  => (float) (clone $q)->sum('amount'),
            ];
        };
        // ยอดขาย = บิลที่ไม่ยกเลิก "และไม่ใช่ไม่พบเอกสาร" (ไม่พบเอกสาร = ไม่เอามาคิดยอด)
        $valid     = (clone $base)->where('cancelled', false)->where('not_found', false);
        $cancel    = (clone $base)->where('cancelled', true);
        $data = [
            'period'      => $period,
            'period_thai' => $this->periodThai($period),
            'printed_by'  => $user->name ?? '',
            'printed_at'  => Carbon::now()->addYears(543)->format('d/m/Y H:i'),
            'goods'       => $agg((clone $valid)->where('bill_type', self::serviceOrGoods('goods'))),
            'service'     => $agg((clone $valid)->where('bill_type', self::serviceOrGoods('service'))),
            'untyped'     => $agg((clone $valid)->whereNull('bill_type')),
            'valid_total' => $agg((clone $valid)),
            // ยกเลิก แยกเป็น ขายสินค้า / บริการ
            'cancelled'         => $agg((clone $cancel)),
            'cancelled_goods'   => $agg((clone $cancel)->where('bill_type', self::serviceOrGoods('goods'))),
            'cancelled_service' => $agg((clone $cancel)->where('bill_type', self::serviceOrGoods('service'))),
            'has_document' => (clone $valid)->where('has_document', true)->count(),
            'missing_doc'  => (clone $valid)->where('has_document', false)->count(),
            'not_found'    => (clone $base)->where('cancelled', false)->where('not_found', true)->count(),
            'not_signed'   => (clone $valid)->where('not_signed', true)->count(),
        ];
        return view('account.bill_doc_report', $data);
    }

    private static function serviceOrGoods(string $w): string
    {
        return $w === 'service' ? BillDocCheck::TYPE_SERVICE : BillDocCheck::TYPE_GOODS;
    }

    /** YYYY-MM -> "เดือน ปีพ.ศ." เช่น 2026-10 -> ตุลาคม 2569 */
    private function periodThai(string $period): string
    {
        $m = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
              'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        [$y, $mo] = array_map('intval', explode('-', $period));
        return ($m[$mo] ?? $period) . ' ' . ($y + 543);
    }

    /**
     * sync เลขบิลทั้งเดือนจาก ERP (SOInvHD + SOInvDT)
     *   - อัปเดตเฉพาะ "ฝั่งระบบ" (so_no/amount/customer/date/cancelled) ไม่แตะสถานะเอกสารที่ติ๊กไว้
     */
    public function syncErp(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $period = $this->normPeriod($request->input('period'));
        try {
            // ★ กรองด้วย "เลขบิล" ไม่ใช่วันที่: เลขบิล = 4YYMM-xxxxx (4 + ปีพ.ศ.2หลัก + เดือน)
            //   เลือกเดือน 2026-09 -> prefix 46909 ; เอาเฉพาะบิลขายขึ้นต้น 4 (ตัด RD อัตโนมัติ)
            $prefix = $this->periodToInvPrefix($period);   // เช่น 46910

            // ★ เบา DB: ดึง SOInvHD ตารางเดียว ไม่ join (NetAmnt/VATAmnt/Cust มีในตัว) กรองด้วย InvNo prefix (ใช้ index)
            $invs = DB::connection(self::ERP_CONNECTION)->table('SOInvHD')
                ->where('InvNo', 'LIKE', $prefix . '-%')
                ->selectRaw('SOInvID, RTRIM(InvNo) as inv_no, RTRIM(SONo) as sono, DocuDate as docu_date, RTRIM(DocuStatus) as docu_status, NetAmnt as amount, VATAmnt as vat, InvCancRemark as cancel_reason, RTRIM(CustID) as cust_id, RTRIM(CustName) as cust_name')
                ->orderBy('InvNo')
                ->get();

            if ($invs->isEmpty()) {
                return response()->json(['ok' => true, 'synced' => 0, 'message' => "ไม่พบบิล {$prefix}-xxxxx ในระบบ", 'summary' => $this->summaryFor($period)]);
            }

            // รหัสลูกค้า = CustCode (เช่น CUS-16714.3) จาก EMCust (map จาก SOInvHD.CustID ที่เป็นเลขภายใน)
            $custCodeById = [];
            $custIds = $invs->pluck('cust_id')->map(fn ($x) => trim((string) $x))->filter()->unique()->values()->all();
            foreach (array_chunk($custIds, 1000) as $chunk) {
                $cm = DB::connection(self::ERP_CONNECTION)->table('EMCust')
                    ->whereIn('CustID', $chunk)
                    ->selectRaw('RTRIM(CustID) as cid, RTRIM(CustCode) as code')
                    ->get();
                foreach ($cm as $c) $custCodeById[(string) $c->cid] = trim((string) $c->code);
            }

            // ชุด GoodID ที่เป็น "บริการ" (EMGood.GoodTypeFlag = 'S') — มีแค่ไม่กี่สิบ โหลดครั้งเดียว
            $serviceGoodIds = [];
            try {
                $serviceGoodIds = array_flip(
                    DB::connection(self::ERP_CONNECTION)->table('EMGood')
                        ->where('GoodTypeFlag', 'S')->pluck('GoodID')->map(fn ($x) => (int) $x)->all()
                );
            } catch (\Throwable $e) {
                Log::warning('billcheck load service goods failed: ' . $e->getMessage());
            }

            // ไส้ในบิล (ชื่อ/จำนวน/ยอด + GoodID ไว้แยกประเภท) — ถามทีเดียวด้วย whereIn SOInvID (chunk 1000)
            $soInvIds = $invs->pluck('SOInvID')->filter()->unique()->values()->all();
            $itemsById = [];
            foreach (array_chunk($soInvIds, 1000) as $chunk) {
                $dt = DB::connection(self::ERP_CONNECTION)->table('SOInvDT')
                    ->whereIn('SOInvID', $chunk)
                    ->selectRaw('SOInvID, GoodID, RTRIM(GoodName) as good_name, GoodQty2 as qty, GoodPrice2 as price, GoodAmnt as amnt')
                    ->orderBy('SOInvID')->orderBy('ListNo')
                    ->get();
                foreach ($dt as $d) {
                    $itemsById[$d->SOInvID][] = [
                        'good_id' => (int) $d->GoodID,
                        'name'    => trim((string) $d->good_name),
                        'qty'     => (float) $d->qty,
                        'price'   => round((float) $d->price, 2),
                        'amount'  => round((float) $d->amnt, 2),
                    ];
                }
            }

            // เหตุผลยกเลิก / Description: อยู่ตาราง SOInvHDRemark (ListNo, SOInvID, Remark)
            //   ดึงเฉพาะบิลที่ยกเลิก (DocuStatus='C') -> เบา แล้วต่อข้อความตาม ListNo
            $cancelReasonById = [];
            $cancelledInvIds = $invs->filter(fn ($r) => strtoupper(trim((string) $r->docu_status)) === 'C')
                ->pluck('SOInvID')->filter()->unique()->values()->all();
            foreach (array_chunk($cancelledInvIds, 1000) as $chunk) {
                $rem = DB::connection(self::ERP_CONNECTION)->table('SOInvHDRemark')
                    ->whereIn('SOInvID', $chunk)
                    ->selectRaw('SOInvID, ListNo, RTRIM(Remark) as remark')
                    ->orderBy('SOInvID')->orderBy('ListNo')
                    ->get();
                foreach ($rem as $rm) {
                    $txt = trim((string) $rm->remark);
                    if ($txt === '') continue;
                    $cancelReasonById[$rm->SOInvID][] = $txt;
                }
            }

            // bulk upsert — update ฝั่งระบบ + ประเภท(auto) + ไส้ใน/VAT ไม่แตะสถานะเอกสารที่ติ๊ก/bot
            $now = now()->toDateTimeString();
            $values = [];
            foreach ($invs as $r) {
                $billNo = trim((string) $r->inv_no);
                if ($billNo === '') continue;
                $soId   = preg_replace('/^SO/i', '', (string) $r->sono);
                $st     = strtoupper(trim((string) $r->docu_status));
                // เหตุผลยกเลิกจาก SOInvHDRemark (ต่อหลายบรรทัด) ; fallback InvCancRemark
                $reason = !empty($cancelReasonById[$r->SOInvID])
                    ? implode(' / ', $cancelReasonById[$r->SOInvID])
                    : trim((string) ($r->cancel_reason ?? ''));
                $lines  = $itemsById[$r->SOInvID] ?? [];

                // ประเภท: มีสินค้า (GoodID ไม่อยู่ในชุดบริการ) อย่างน้อย 1 รายการ = สินค้า ; ทุกบรรทัดเป็นบริการ = บริการ
                $billType = null;
                if (!empty($lines)) {
                    $hasGoods = false;
                    foreach ($lines as $ln) {
                        if (!isset($serviceGoodIds[$ln['good_id']])) { $hasGoods = true; break; }
                    }
                    $billType = $hasGoods ? BillDocCheck::TYPE_GOODS : BillDocCheck::TYPE_SERVICE;
                }

                $afterVat  = round((float) $r->amount, 2);
                $vat       = round((float) ($r->vat ?? 0), 2);
                $beforeVat = round($afterVat - $vat, 2);

                $values[] = [
                    'period'            => $period,
                    'bill_no'           => $billNo,
                    'so_no'             => $soId ?: null,
                    'bill_type'         => $billType,
                    'bill_date'         => $r->docu_date ? Carbon::parse($r->docu_date)->toDateString() : null,
                    // รหัสลูกค้า = CustCode (CUS-xxxxx) ; ถ้าไม่เจอใน EMCust ใช้เลข CustID เดิม
                    'customer_id'       => ($custCodeById[trim((string) ($r->cust_id ?? ''))] ?? null) ?: (trim((string) ($r->cust_id ?? '')) ?: null),
                    'customer_name'     => trim((string) ($r->cust_name ?? '')) ?: null,
                    'amount'            => $afterVat,
                    'amount_before_vat' => $beforeVat,
                    'vat_amount'        => $vat,
                    'items'             => json_encode($lines, JSON_UNESCAPED_UNICODE),
                    'cancelled'         => $st === 'C' ? 1 : 0,
                    'sys_status'        => $st ?: null,
                    'cancel_reason'     => $reason !== '' ? $reason : null,
                    'in_system'         => 1,
                    'updated_at'        => $now,
                    'created_at'        => $now,
                ];
            }

            $updateCols = ['so_no', 'bill_type', 'bill_date', 'customer_id', 'customer_name',
                           'amount', 'amount_before_vat', 'vat_amount', 'items',
                           'cancelled', 'sys_status', 'cancel_reason', 'in_system', 'updated_at'];
            $n = 0;
            foreach (array_chunk($values, 500) as $chunk) {
                // upsert: ชนกับ unique(period,bill_no) -> update เฉพาะ $updateCols (ไม่แตะ has_document/match_status/doc_*)
                DB::table('bill_doc_checks')->upsert($chunk, ['period', 'bill_no'], $updateCols);
                $n += count($chunk);
            }

            // ล้างแถวเก่าของเดือนนี้ที่ไม่ใช่บิลขึ้นต้น 4 (เช่น CC/CN/RD ที่หลงมาจากการ sync เวอร์ชันก่อน) — เฉพาะ logistic
            DB::table('bill_doc_checks')->where('period', $period)->where('bill_no', 'NOT LIKE', '4%')->delete();

            return response()->json([
                'ok'      => true,
                'synced'  => $n,
                'message' => "sync บิลเดือน {$period} จำนวน {$n} ใบ",
                'summary' => $this->summaryFor($period),
            ]);
        } catch (\Throwable $e) {
            Log::warning('billcheck syncErp failed: ' . $e->getMessage());
            return response()->json([
                'ok'      => false,
                'message' => 'ดึงข้อมูลจาก ERP ไม่สำเร็จ: ' . $e->getMessage(),
            ], 502);
        }
    }

    /** คนติ๊ก/ยกเลิกติ๊ก ว่าพบเอกสาร (manual) */
    public function tick(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }
        $data = $request->validate([
            'period'  => 'required|string',
            'bill_no' => 'required|string',
            'has'     => 'required|boolean',
        ]);
        $period = $this->normPeriod($data['period']);

        $row = BillDocCheck::where('period', $period)->where('bill_no', trim($data['bill_no']))->first();
        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบเลขบิลนี้ในรายการของเดือน (ยัง sync หรือเปล่า?)'], 404);
        }

        if ($data['has']) {
            // คนยืนยันด้วยตา = ถือว่าตรง (เคลียร์ "ไม่พบ")
            $row->has_document = true;
            $row->not_found    = false;
            $row->match_status = $row->in_system ? BillDocCheck::M_MATCHED : BillDocCheck::M_DOC_NO_SYS;
            $row->check_source = 'manual';
            $row->checked_by   = $user->name;
            $row->checked_at   = now();
        } else {
            $row->has_document = false;
            $row->not_signed   = false;   // ไม่พบแล้ว -> เคลียร์ "ไม่ได้เซ็น"
            $row->match_status = $row->in_system ? BillDocCheck::M_MISSING_DOC : BillDocCheck::M_DOC_NO_SYS;
            $row->check_source = 'manual';
            $row->checked_by   = $user->name;
            $row->checked_at   = now();
        }
        $row->save();

        return response()->json(['ok' => true, 'row' => $this->rowOut($row), 'summary' => $this->summaryFor($period)]);
    }

    /** ติ๊ก/ยกเลิก "พบแต่ไม่ได้เซ็นบิล" — ติ๊ก = ถือว่าพบเอกสารแล้ว (has_document) แต่ยังไม่เซ็น */
    public function markNotSigned(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }
        $data = $request->validate([
            'period'  => 'required|string',
            'bill_no' => 'required|string',
            'val'     => 'required|boolean',
        ]);
        $period = $this->normPeriod($data['period']);
        $row = BillDocCheck::where('period', $period)->where('bill_no', trim($data['bill_no']))->first();
        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบเลขบิลนี้ในรายการของเดือน'], 404);
        }

        if ($data['val']) {
            $row->not_signed   = true;
            $row->has_document = true;    // พบเอกสารแล้ว แต่ไม่เซ็น
            $row->not_found    = false;
            $row->match_status = $row->in_system ? BillDocCheck::M_MATCHED : BillDocCheck::M_DOC_NO_SYS;
        } else {
            $row->not_signed   = false;
        }
        $row->check_source = 'manual';
        $row->checked_by   = $user->name;
        $row->checked_at   = now();
        $row->save();

        return response()->json(['ok' => true, 'row' => $this->rowOut($row), 'summary' => $this->summaryFor($period)]);
    }

    /** ติ๊ก/ยกเลิก "ไม่พบบิล" (ยืนยันหาแล้วไม่เจอ) — ติ๊กไม่พบ = เคลียร์ "พบเอกสาร" */
    public function markNotFound(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }
        $data = $request->validate([
            'period'  => 'required|string',
            'bill_no' => 'required|string',
            'val'     => 'required|boolean',
        ]);
        $period = $this->normPeriod($data['period']);

        $row = BillDocCheck::where('period', $period)->where('bill_no', trim($data['bill_no']))->first();
        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบเลขบิลนี้ในรายการของเดือน'], 404);
        }

        if ($data['val']) {
            $row->not_found    = true;
            $row->has_document = false;   // ไม่พบ = ไม่มีเอกสาร
            $row->not_signed   = false;
            $row->match_status = $row->in_system ? BillDocCheck::M_MISSING_DOC : BillDocCheck::M_DOC_NO_SYS;
            $row->check_source = 'manual';
            $row->checked_by   = $user->name;
            $row->checked_at   = now();
        } else {
            $row->not_found    = false;
            $row->check_source = 'manual';
            $row->checked_by   = $user->name;
            $row->checked_at   = now();
        }
        $row->save();

        return response()->json(['ok' => true, 'row' => $this->rowOut($row), 'summary' => $this->summaryFor($period)]);
    }

    /** เพิ่ม/แก้ไข/ลบ หมายเหตุ — มีหมายเหตุ = ติ๊ก "พบเอกสาร" อัตโนมัติ */
    public function setNote(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }
        $data = $request->validate([
            'period'  => 'required|string',
            'bill_no' => 'required|string',
            'note'    => 'nullable|string|max:5000',
        ]);
        $period = $this->normPeriod($data['period']);

        $row = BillDocCheck::where('period', $period)->where('bill_no', trim($data['bill_no']))->first();
        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบเลขบิลนี้ในรายการของเดือน'], 404);
        }

        $note = trim((string) ($data['note'] ?? ''));
        $row->note = $note !== '' ? $note : null;

        if ($note !== '') {
            // มีหมายเหตุ -> ติ๊กว่าพบเอกสารอัตโนมัติ (ถ้ายังไม่ติ๊ก) + เคลียร์ "ไม่พบ"
            if (!$row->has_document) {
                $row->has_document = true;
                $row->match_status = $row->in_system ? BillDocCheck::M_MATCHED : BillDocCheck::M_DOC_NO_SYS;
            }
            $row->not_found    = false;
            $row->check_source = 'manual';
            $row->checked_by   = $user->name;
            $row->checked_at   = now();
        }
        $row->save();

        return response()->json(['ok' => true, 'row' => $this->rowOut($row), 'summary' => $this->summaryFor($period)]);
    }

    /** ตั้งประเภทบิล (สินค้า/บริการ) แบบ manual */
    public function setType(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }
        $data = $request->validate([
            'period'  => 'required|string',
            'bill_no' => 'required|string',
            'type'    => 'nullable|in:สินค้า,บริการ',
        ]);
        $period = $this->normPeriod($data['period']);
        $row = BillDocCheck::where('period', $period)->where('bill_no', trim($data['bill_no']))->first();
        if (!$row) return response()->json(['ok' => false, 'message' => 'ไม่พบเลขบิล'], 404);

        $row->bill_type = $data['type'] ?: null;
        $row->save();
        return response()->json(['ok' => true, 'row' => $this->rowOut($row)]);
    }

    /**
     * ★ endpoint ให้ bot/OCR ภายนอกยิงผลการอ่านเอกสารเข้ามา (ใช้ที่ routes/api.php)
     *   body: { period, bill_no, doc_amount?, doc_date?, doc_customer?, doc_file?, bill_type?, confidence?, source? }
     *   - จับคู่กับแถวในระบบ แล้วตัดสิน matched / mismatch / doc_no_system ด้วยโค้ด (ไม่ให้ AI ตัดสิน)
     */
    public function botMatch(Request $request)
    {
        // ป้องกันเบื้องต้นด้วย token (ตั้งใน .env: BILLCHECK_BOT_TOKEN) — ถ้าไม่ตั้งไว้จะไม่บังคับ (dev)
        $token = config('services.billcheck.bot_token', env('BILLCHECK_BOT_TOKEN'));
        if ($token && !hash_equals((string) $token, (string) $request->header('X-Bot-Token'))) {
            return response()->json(['ok' => false, 'message' => 'invalid token'], 401);
        }

        $data = $request->validate([
            'period'       => 'required|string',
            'bill_no'      => 'required|string',
            'doc_amount'   => 'nullable|numeric',
            'doc_date'     => 'nullable|date',
            'doc_customer' => 'nullable|string|max:255',
            'doc_file'     => 'nullable|string|max:255',
            'bill_type'    => 'nullable|in:สินค้า,บริการ',
            'confidence'   => 'nullable|numeric',
            'source'       => 'nullable|string|max:20',
        ]);

        $period = $this->normPeriod($data['period']);
        $billNo = trim($data['bill_no']);

        $row = BillDocCheck::firstOrNew(['period' => $period, 'bill_no' => $billNo]);
        $isNew = !$row->exists;
        if ($isNew) {
            // เจอเอกสารแต่ไม่มีในระบบ (ยังไม่ sync เจอ)
            $row->in_system = false;
        }

        $row->has_document = true;
        $row->check_source = $data['source'] ?? 'bot';
        $row->confidence   = $data['confidence'] ?? null;
        $row->doc_amount   = $data['doc_amount'] ?? null;
        $row->doc_date     = $data['doc_date'] ?? null;
        $row->doc_customer = $data['doc_customer'] ?? null;
        $row->doc_file     = $data['doc_file'] ?? $row->doc_file;
        if (!empty($data['bill_type'])) $row->bill_type = $data['bill_type'];
        $row->checked_by   = $data['source'] ?? 'bot';
        $row->checked_at   = now();

        $row->match_status = $this->computeMatch($row);
        $row->save();

        return response()->json(['ok' => true, 'match_status' => $row->match_status, 'row' => $this->rowOut($row)]);
    }

    /**
     * กฎตัดสินการจับคู่ (โค้ดล้วน ไม่ใช้ AI):
     *   - ไม่มีในระบบ               -> doc_no_system
     *   - ยอดตรง (±tolerance) และวันที่ตรง (ถ้ามี) -> matched
     *   - นอกนั้น                    -> mismatch (ส่งคนตรวจ)
     */
    private function computeMatch(BillDocCheck $row): string
    {
        if (!$row->in_system) return BillDocCheck::M_DOC_NO_SYS;

        $amountOk = $row->doc_amount !== null && $row->amount !== null
            && abs((float) $row->doc_amount - (float) $row->amount) <= self::AMOUNT_TOLERANCE;

        $dateOk = true;
        if ($row->doc_date && $row->bill_date) {
            $dateOk = Carbon::parse($row->doc_date)->isSameDay(Carbon::parse($row->bill_date));
        } elseif ($row->doc_date && !$row->bill_date) {
            $dateOk = true; // ไม่มีวันที่ฝั่งระบบให้เทียบ
        }

        return ($amountOk && $dateOk) ? BillDocCheck::M_MATCHED : BillDocCheck::M_MISMATCH;
    }

    /** สรุปตัวเลขของเดือน */
    private function summaryFor(string $period): array
    {
        $base = BillDocCheck::where('period', $period)->where('bill_no', 'LIKE', '4%');

        $total      = (clone $base)->count();
        $inSystem   = (clone $base)->where('in_system', true)->count();
        $hasDoc     = (clone $base)->where('has_document', true)->count();
        $missingDoc = (clone $base)->where('in_system', true)->where('has_document', false)->count();
        $mismatch   = (clone $base)->where('match_status', BillDocCheck::M_MISMATCH)->count();
        $docNoSys   = (clone $base)->where('match_status', BillDocCheck::M_DOC_NO_SYS)->count();
        $cancelled  = (clone $base)->where('cancelled', true)->count();

        $byType = function ($type) use ($period) {
            $q = BillDocCheck::where('period', $period)->where('bill_no', 'LIKE', '4%');
            $type === 'none' ? $q->whereNull('bill_type') : $q->where('bill_type', $type);
            return [
                'total'   => (clone $q)->count(),
                'has'     => (clone $q)->where('has_document', true)->count(),
                'missing' => (clone $q)->where('in_system', true)->where('has_document', false)->count(),
            ];
        };

        return [
            'total'        => $total,
            'in_system'    => $inSystem,
            'has_document' => $hasDoc,
            'missing_doc'  => $missingDoc,
            'mismatch'     => $mismatch,
            'doc_no_system'=> $docNoSys,
            'cancelled'    => $cancelled,
            'goods'        => $byType(BillDocCheck::TYPE_GOODS),
            'service'      => $byType(BillDocCheck::TYPE_SERVICE),
            'untyped'      => $byType('none'),
        ];
    }

    /**
     * รายละเอียดจากระบบ logistic ต่อเลขบิล (ERP InvNo) :
     *   tblbill.billid = เลขบิล -> ผู้เปิด(emp_name)+เวลา(time), so_detail_id, รับบิล(status_bill*)
     *   transaction_transport.bill_id = so_detail_id -> ขนส่ง/คนขับ/รับเข้า(check_name,check_time,status)
     *   ถ้า billid ซ้ำ -> เอาแถวล่าสุด (time ใหม่สุด)
     */
    private function logisticDetail(array $billNos): array
    {
        $billNos = array_values(array_filter(array_unique($billNos)));
        if (empty($billNos)) return [];

        // 1) tblbill (เอาล่าสุดต่อ billid)
        $byBill = [];
        $soDetailIds = [];
        foreach (array_chunk($billNos, 1000) as $chunk) {
            $rows = DB::table('tblbill')
                ->whereIn('billid', $chunk)
                ->orderBy('time')   // วนทับ -> แถวเวลาใหม่สุด(ล่าสุด)ชนะ
                ->get(['billid', 'emp_name', 'time', 'so_detail_id', 'transport_type',
                       'status_bill', 'status_bill_by', 'status_bill_time']);
            foreach ($rows as $b) {
                $key = trim((string) $b->billid);
                if ($key === '') continue;
                $byBill[$key] = $b;
                if (!empty($b->so_detail_id)) $soDetailIds[] = $b->so_detail_id;
            }
        }

        // 2) transaction_transport (ล่าสุดต่อ bill_id = so_detail_id) — แหล่งหลัก
        $bySoDetail = [];
        $soDetailIds = array_values(array_unique($soDetailIds));
        foreach (array_chunk($soDetailIds, 1000) as $chunk) {
            $tx = DB::table('transaction_transport')
                ->whereIn('bill_id', $chunk)
                ->orderBy('id')
                ->get(['bill_id', 'transport_name', 'driver_name', 'name_pick', 'time_pick',
                       'check_name', 'check_time', 'status']);
            foreach ($tx as $t) $bySoDetail[$t->bill_id] = $t;
        }

        // 3) bill_status_history (db3e) — fallback เมื่อไม่เจอใน transaction_transport ; คีย์ด้วย BillNo = เลขบิล
        $histByBill = [];
        try {
            foreach (array_chunk($billNos, 1000) as $chunk) {
                $hs = DB::connection(self::ERP_CONNECTION)->table('bill_status_history')
                    ->whereIn('BillNo', $chunk)
                    ->orderBy('BillInDate')   // วนทับ -> ล่าสุดชนะ
                    ->get(['BillNo', 'BillInReason', 'BillInByDesc', 'BillInBy', 'BillInDate', 'DeliveryMethodDesc', 'ChangedBy', 'ChangedDate']);
                foreach ($hs as $h) {
                    $k = trim((string) $h->BillNo);
                    if ($k !== '') $histByBill[$k] = $h;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('billcheck bill_status_history fallback failed: ' . $e->getMessage());
        }

        $fmt = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y H:i') : null;
        $out = [];
        foreach ($billNos as $key) {
            $b  = $byBill[$key] ?? null;
            $tx = ($b && !empty($b->so_detail_id)) ? ($bySoDetail[$b->so_detail_id] ?? null) : null;
            $h  = $histByBill[$key] ?? null;
            if (!$b && !$tx && !$h) continue;   // ไม่มีข้อมูลเลย -> ไม่แสดง

            // ขนส่ง/คนขับ/รับเข้า : ใช้ transaction_transport เป็นหลัก ถ้าไม่เจอค่อยใช้ bill_status_history
            if ($tx) {
                $transport = trim((string) $tx->transport_name) ?: (trim((string) ($b->transport_type ?? '')) ?: null);
                $driver    = trim((string) $tx->driver_name) ?: null;
                $recvBy    = trim((string) $tx->check_name) ?: null;
                $recvAt    = $fmt($tx->check_time ?? null);
                $recvSt    = trim((string) $tx->status) ?: null;
                $src       = 'transport';
            } elseif ($h) {
                $transport = trim((string) ($h->DeliveryMethodDesc ?? '')) ?: null;
                $driver    = null;   // bill_status_history ไม่มีคนขับ
                $recvBy    = trim((string) ($h->BillInByDesc ?: ($h->BillInBy ?? ''))) ?: null;
                $recvAt    = $fmt($h->BillInDate ?? null);
                $recvSt    = trim((string) ($h->BillInReason ?? '')) ?: null;
                $src       = 'history';
            } else {
                $transport = trim((string) ($b->transport_type ?? '')) ?: null;
                $driver = $recvBy = $recvAt = $recvSt = null;
                $src = 'tblbill';
            }

            $out[$key] = [
                'opener'         => $b ? (trim((string) ($b->emp_name ?? '')) ?: null) : null,
                'opened_at'      => $b ? $fmt($b->time ?? null) : null,
                'transport'      => $transport,
                'driver'         => $driver,
                'received_by'    => $recvBy,
                'received_at'    => $recvAt,
                'receive_status' => $recvSt,
                'received_src'   => $src,
                'bill_received_by'  => $b ? (trim((string) ($b->status_bill_by ?? '')) ?: null) : null,
                'bill_received_at'  => $b ? $fmt($b->status_bill_time ?? null) : null,
                'bill_received_st'  => $b ? (trim((string) ($b->status_bill ?? '')) ?: null) : null,
            ];
        }
        return $out;
    }

    private function rowOut(BillDocCheck $r, ?array $detail = null): array
    {
        return [
            'detail'        => $detail,
            'id'            => $r->id,
            'bill_no'       => $r->bill_no,
            'so_no'         => $r->so_no,
            'bill_type'     => $r->bill_type,
            'bill_date'     => optional($r->bill_date)->format('d/m/Y'),
            'customer_id'   => $r->customer_id,
            'customer_name' => $r->customer_name,
            'amount'        => $r->amount !== null ? (float) $r->amount : null,
            'amount_before_vat' => $r->amount_before_vat !== null ? (float) $r->amount_before_vat : null,
            'vat_amount'    => $r->vat_amount !== null ? (float) $r->vat_amount : null,
            'items'         => is_array($r->items) ? $r->items : [],
            'cancelled'     => (bool) $r->cancelled,
            'sys_status'    => $r->sys_status,
            'sys_status_th' => $this->statusTh($r->sys_status),
            'cancel_reason' => $r->cancel_reason,
            'in_system'     => (bool) $r->in_system,
            'has_document'  => (bool) $r->has_document,
            'not_found'     => (bool) $r->not_found,
            'not_signed'    => (bool) $r->not_signed,
            'match_status'  => $r->match_status,
            'check_source'  => $r->check_source,
            'confidence'    => $r->confidence !== null ? (float) $r->confidence : null,
            'doc_amount'    => $r->doc_amount !== null ? (float) $r->doc_amount : null,
            'doc_date'      => optional($r->doc_date)->format('d/m/Y'),
            'doc_customer'  => $r->doc_customer,
            'note'          => $r->note,
            'checked_by'    => $r->checked_by,
            'checked_at'    => optional($r->checked_at)->format('d/m/Y H:i'),
        ];
    }

    /** แปลง DocuStatus (ERP) เป็นข้อความไทย */
    private function statusTh(?string $st): string
    {
        switch (strtoupper(trim((string) $st))) {
            case 'C': return 'ยกเลิก';
            case 'Y': return 'ปกติ';
            case 'P': return 'บางส่วน';
            case 'N': return 'ยังไม่สมบูรณ์';
            default:  return $st ?: '-';
        }
    }

    private function normPeriod($period): string
    {
        $period = trim((string) $period);
        if (preg_match('/^\d{4}-\d{2}$/', $period)) return $period;
        return Carbon::now()->format('Y-m');
    }

    /** period (YYYY-MM ค.ศ.) -> prefix เลขบิล 4YYMM (4 + ปีพ.ศ.2หลัก + เดือน) เช่น 2026-10 -> 46910 */
    private function periodToInvPrefix(string $period): string
    {
        [$y, $m] = array_map('intval', explode('-', $period));
        $beYY = ($y + 543) % 100;
        return sprintf('4%02d%02d', $beYY, $m);
    }
}
