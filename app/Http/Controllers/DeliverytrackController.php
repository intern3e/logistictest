<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Docbills;
use App\Models\transaction_delivery;
use App\Models\PoReceive;
use App\Models\PooutsideCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliverytrackController extends Controller
{
    protected string $erpConnection = 'mysql_3e';
    protected string $account03Connection = 'mssql_account03';

    protected array $selfPickupMethods = ['รับเองรถใหญ่', 'รับเองมอเตอร์ไซด์'];
    protected string $selfPickupStartDate = '2026-01-01';

    protected array $deliveryMethods = [
        'มอเตอร์ไซต์กบ', 'มอเตอร์ไซด์ในเมือง', 'มอเตอร์ไซค์ - พระราม 2', 'เซลล์ไปส่งเอง',
        '3ฒย 478', '3ฉมง 3059', '2ฒธ 1621', '2ฒธ 1620', '3ฒก 6071', '2ฒฏ 3017',
        '4ฒฎ 5861', '2ฒศ 6762', '2ฉธ 1619', '6 ล้อ', 'laramove', 'สุราษฎร์ทัวร์ เอ็กเพรส',
        'แท็กซี่คอนซูม 02-6230110', 'AT SPEED 02-233-6062', 'PM 081-564-5920',
        'ป้าติ๊ก P.P 083-082-1026', 'ข้ามสมุทรขนส่ง 02-887-0368', 'ระยองพัฒนา 02-2229296',
        'นิวอุดร ขนส่ง 085-4830094', '999ขนส่ง 087-053 5488', 'นิ่มซี่เส็ง 02-282-7936',
        'สยามเฟริส 02-954-3601', 'เจ๊ แต๋ว 02-623-3919', 'นครพนมขนส่ง 02-448-2065',
        'สกุลทองขนส่ง 02-2225595', 'NTC 02-611-9582', 'PM PL 02-214-0629',
        'ศรีราชาทัวร์ 02-391-5188', 'พัฒนาเอ็กสเพรส 02-223-3831', 'ชวาลกิต ขนส่ง 02-8894747-9',
        'ลูกค้ารับเอง', 'เกียรติสกุลทรานสปอร์ต สาย 2 064-7894452,064-7893653',
        'ประจวบทองขัยขนส่ง สาย 2 02-4481976-7, 086-3679602', 'สี่สหายขนส่ง (1988) 02-4516712-6',
        'ไอที ทรานสปอร์ต 089-6491111', 'เอ็มเอส เอ็กซ์เพรส 086-1217672, 089-0990782',
        'เอส.ดี. เอ็กซ์เพรส 02-2144341, 2165846', 'KERRY', 'Grab', 'ขนส่ง SD EXPRESS',
        'TB พาร์ท', 'ขนส่ง โกโลด', 'ขนส่ง BS', 'ขนส่ง มะม่วง', 'ขนส่ง PJ', 'ขนส่ง คู่บุญ', 'ธนมัยสาย2',
    ];

    protected array $responsiblePersons = ['บอย', 'แซม', 'กบ', 'joey', 'yuth', 'แฟงค์', 'เก่ง', 'แมน', 'เอ', 'กอลฟ์', 'บังเดช', 'เอ้'];

    /**
     * สิทธิ์เข้าใช้งานหน้าจ่ายงาน/สรุปงาน:
     *   - role: admin, store, stock, accounting
     *   - หรือ ผู้ใช้เฉพาะบุคคล: ชื่อ "FILM"
     */
    private function hasDeliveryAccess($user): bool
    {
        if (!$user) return false;
        if (in_array($user->role, ['admin', 'store', 'stock', 'accounting'], true)) return true;
        return strcasecmp(trim((string) ($user->name ?? '')), 'FILM') === 0;
    }

    /**
     * สิทธิ์ "ยกเลิกงาน" ในหน้าสรุป: เฉพาะผู้ใช้ชื่อ FILM หรือ role admin
     */
    private function canCancelAssignment($user): bool
    {
        if (!$user) return false;
        if (($user->role ?? '') === 'admin') return true;
        return strcasecmp(trim((string) ($user->name ?? '')), 'FILM') === 0;
    }

    private function checkAccess()
    {
        if (!Auth::guard('web')->check()) return redirect()->guest(route('login'));
        $user = Auth::guard('web')->user();
        if (!$this->hasDeliveryAccess($user)) abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        return null;
    }

    private function loggedInName()
    {
        $user = Auth::guard('web')->user();
        return $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? '-');
    }

    public function index(Request $request)
    {
        if ($resp = $this->checkAccess()) return $resp;

        $bills = Bill::whereNotNull('emp_picker')->where('emp_picker', '!=', '')->orderBy('time')->get();
        $docbills = Docbills::where('status', '0')->orderBy('time')->get();
        $poJobs = $this->getPendingSelfPickupPOs();

        $relevantIds = $bills->pluck('so_detail_id')->merge($docbills->pluck('doc_id'))->merge($poJobs->pluck('PONum'));

        $dispatched = $this->chunkedWhereInGet(fn () => transaction_delivery::query()->orderBy('id'), 'bill_id', $relevantIds)->keyBy('bill_id');

        $bills    = $bills->reject(fn ($b) => $dispatched->has($b->so_detail_id))->values();
        $docbills = $docbills->reject(fn ($d) => $dispatched->has($d->doc_id))->values();
        $poJobs   = $poJobs->reject(fn ($p) => $dispatched->has($p->PONum))->values();

        // แยกบิลตามประเภทขนส่ง: private = 'private', นอกนั้น (รวม null) = company
        $companyBills = $bills->filter(fn ($b) => ($b->transport_type ?: 'company') !== 'private')->values();
        $privateBills = $bills->filter(fn ($b) => $b->transport_type === 'private')->values();

        $activeTransport = $request->input('transport') === 'private' ? 'private' : 'company';

        // ส่งบิลทั้งสองฝั่ง (company/private) ไปพร้อมกัน แล้วให้ฝั่ง JS สลับโชว์เอง
        // เพื่อไม่ต้องรีโหลดหน้า+รัน query ข้ามฐาน (ERP/SQL Server) ใหม่ทุกครั้งที่กดสลับขนส่ง
        return view('driver.delivery', [
            'companyBillGroups'  => $this->groupBillsByCustomer($companyBills),
            'privateBillGroups'  => $this->groupBillsByCustomer($privateBills),
            'docGroups'          => $this->groupDocsByCustomer($docbills),
            'poGroups'           => $this->groupPoByVendor($poJobs),
            'responsiblePersons' => $this->responsiblePersons,
            'deliveryMethods'    => $this->deliveryMethods,
            'loggedInName'       => $this->loggedInName(),
            'activeTransport'    => $activeTransport,
            'companyCount'       => $companyBills->count(),
            'privateCount'       => $privateBills->count(),
        ]);
    }

    private function getPendingSelfPickupPOs()
    {
        $poLists = DB::connection($this->erpConnection)->table('polist')
            ->whereIn('DeliveryMethod', $this->selfPickupMethods)
            ->where('DeliveryDate', '>=', $this->selfPickupStartDate)
            ->whereRaw("UPPER(TRIM(COALESCE(POstatus, ''))) != 'COMPLETED'")
            ->orderBy('SONum')->orderBy('PONum')->get();

        if ($poLists->isEmpty()) return collect();

        $poIdsWithPrefix = $poLists->pluck('PONum')->map(fn ($p) => 'PO' . $p)->unique()->values();

        $poReceives = $this->chunkedWhereInGet(fn () => PoReceive::query(), 'po_id', $poIdsWithPrefix)
            ->keyBy(fn ($r) => $r->so_id . '|' . preg_replace('/^PO/', '', $r->po_id));

        $cancelled = $this->chunkedWhereInGet(fn () => PooutsideCancelled::query(), 'po_id', $poIdsWithPrefix)
            ->keyBy(fn ($r) => $r->so_id . '|' . preg_replace('/^PO/', '', $r->po_id));

        $result = collect();
        foreach ($poLists as $po) {
            $key = $po->SONum . '|' . $po->PONum;
            $status = $this->resolvePOStatus($po, $poReceives->get($key), $cancelled->has($key));
            if (!$this->isPoPending($status)) continue;
            $po->status_color = $status['color'];
            $po->status_label = $status['label'];
            $result->push($po);
        }
        return $this->attachVendorAddressAndItems($result);
    }

    private function attachVendorAddressAndItems($poJobs)
    {
        if ($poJobs->isEmpty()) return $poJobs;
        $docuNos = $poJobs->pluck('PONum')->map(fn ($p) => 'PO' . $p)->unique()->values();

        try {
            $headers = $this->chunkedWhereInGet(
                fn () => DB::connection($this->account03Connection)->table('POHD')
                    ->leftJoin('EMVendor', 'POHD.VendorID', '=', 'EMVendor.VendorID')
                    ->select('POHD.POID', 'POHD.DocuNo', 'EMVendor.VendorAddr1', 'EMVendor.VendorAddr2', 'EMVendor.District', 'EMVendor.Amphur', 'EMVendor.Province', 'EMVendor.PostCode'),
                'POHD.DocuNo', $docuNos
            )->keyBy('DocuNo');

            $poIds = $headers->pluck('POID')->filter()->unique()->values();
            $items = $poIds->isEmpty() ? collect() : $this->chunkedWhereInGet(
                fn () => DB::connection($this->account03Connection)->table('PODT')->where('CancelFlag', '!=', 'Y')->select('POID', 'GoodName', 'GoodQty2'),
                'POID', $poIds
            )->groupBy('POID');
        } catch (\Throwable $e) {
            // เชื่อมต่อฐานข้อมูล account03 ไม่ได้ (เช่น SQL Server ฝั่งนั้นล่ม/เน็ตหลุด/timeout)
            // ไม่ควรทำให้ทั้งหน้า deliverytrack พังไปด้วย — ปล่อยให้แสดงงาน "รับของเอง" ต่อไปได้
            // เพียงแค่ไม่มีที่อยู่ผู้ขาย/รายการสินค้าแนบมา
            \Illuminate\Support\Facades\Log::warning('เชื่อมต่อฐานข้อมูล account03 ไม่สำเร็จ ใน attachVendorAddressAndItems: ' . $e->getMessage());
            $headers = collect();
            $items = collect();
        }

        foreach ($poJobs as $po) {
            $docuNo = 'PO' . $po->PONum;
            $header = $headers->get($docuNo);
            $po->vendor_address = $header ? collect([$header->VendorAddr1, $header->VendorAddr2, $header->District, $header->Amphur, $header->Province, $header->PostCode])->filter(fn ($v) => filled($v))->implode(' ') : null;
            $po->items = $header ? ($items->get($header->POID) ?? collect())->map(fn ($i) => ['name' => $i->GoodName, 'qty' => rtrim(rtrim($i->GoodQty2, '0'), '.')])->values() : collect();
        }
        return $poJobs;
    }

    private function chunkedWhereInGet(\Closure $queryFactory, string $column, $values, int $chunkSize = 1000)
    {
        $values = $values instanceof \Illuminate\Support\Collection ? $values->all() : $values;
        $results = collect();
        foreach (array_chunk(array_values($values), $chunkSize) as $chunk) {
            if (empty($chunk)) continue;
            $results = $results->merge($queryFactory()->whereIn($column, $chunk)->get());
        }
        return $results;
    }

    private function resolvePOStatus($poList, $receiveEntry, bool $isCancelled): array
    {
        if ($isCancelled) return ['color' => 'red', 'label' => 'ยกเลิก'];
        $newStatus = $receiveEntry->status ?? null;
        if ($newStatus !== null && $newStatus !== 'รับเข้าผิด') {
            $map = ['ครบ' => ['color' => 'green', 'label' => 'ครบ'], 'บางส่วน' => ['color' => 'yellow', 'label' => 'รับบางส่วน'], 'ยกเลิก' => ['color' => 'red', 'label' => 'ยกเลิก']];
            return $map[$newStatus] ?? ['color' => 'inherit', 'label' => $newStatus];
        }
        $old = strtoupper(trim($poList->POstatus ?? ''));
        $map = ['ENTRY' => ['color' => 'inherit', 'label' => 'รอเข้า'], 'COMPLETED' => ['color' => 'green', 'label' => 'ครบ'], 'PARTIAL' => ['color' => 'orange', 'label' => 'เลยกำหนด'], 'CANCELLED' => ['color' => 'red', 'label' => 'ยกเลิก']];
        return $map[$old] ?? ['color' => 'inherit', 'label' => 'ไม่ทราบสถานะ'];
    }

    private function isPoPending(array $status): bool { return !in_array($status['color'], ['green', 'red'], true); }

    /**
     * จัดกลุ่มบิลตาม customer_id
     * ถ้าบิลใดไม่มี customer_id (รหัสลูกค้า) จะไม่ถูกโยนไปรวมกันเป็นกลุ่มเดียวกันหมดอีกต่อไป
     * แต่จะใช้ "ชื่อลูกค้า" เป็น key แทน ทำให้ลูกค้าคนละคนที่ไม่มีรหัส ยังคงแยกกลุ่มกันอยู่
     */
    private function groupBillsByCustomer($bills): array
    {
        $groups = [];
        foreach ($bills as $bill) {
            $custId = $bill->customer_id;
            $groupKey = filled($custId) ? $custId : ('name:' . ($bill->customer_name ?: 'ไม่ระบุชื่อลูกค้า'));

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'customer_id'   => $custId,
                    'customer_name' => $bill->customer_name,
                    'rows'          => [],
                ];
            }
            $groups[$groupKey]['rows'][] = ['bill' => $bill];
        }
        return $groups;
    }

    /**
     * จัดกลุ่มบิลชั่วคราวตาม id_com
     * ถ้าไม่มี id_com จะใช้ "ชื่อบริษัท" (com_name) เป็น key แทน ไม่ให้ไปรวมกับลูกค้ารายอื่นที่ไม่มีรหัสเหมือนกัน
     */
    private function groupDocsByCustomer($docbills): array
    {
        $groups = [];
        foreach ($docbills as $doc) {
            $custId = $doc->id_com;
            $groupKey = filled($custId) ? $custId : ('name:' . ($doc->com_name ?: 'ไม่ระบุชื่อลูกค้า'));

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'customer_id'   => $custId,
                    'customer_name' => $doc->com_name,
                    'rows'          => [],
                ];
            }
            $groups[$groupKey]['rows'][] = ['doc' => $doc];
        }
        return $groups;
    }

    /**
     * จัดกลุ่ม PO ตาม VendorID
     * เดิมถ้าไม่มี VendorID จะ fallback เป็น '-' คงที่ ทำให้ผู้ขายทุกรายที่ไม่มีรหัสถูกรวมเป็นกลุ่มเดียวกันหมด (บั๊ก)
     * ตอนนี้ใช้ "ชื่อผู้ขาย" (VendorName) เป็น key แทนเมื่อไม่มีรหัส เพื่อแยกแต่ละรายออกจากกัน
     */
    private function groupPoByVendor($poJobs): array
    {
        $groups = [];
        foreach ($poJobs as $po) {
            $vendorId = $po->VendorID;
            $groupKey = filled($vendorId) ? $vendorId : ('name:' . ($po->VendorName ?: 'ไม่ระบุชื่อผู้ขาย'));

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'customer_id'    => $vendorId,
                    'customer_name'  => $po->VendorName,
                    'vendor_address' => $po->vendor_address ?? null,
                    'rows'           => [],
                ];
            }
            $groups[$groupKey]['rows'][] = ['po' => $po];
        }
        return $groups;
    }

    public function store(Request $request)
    {
        if ($resp = $this->checkAccess()) return $resp;
        $validated = $request->validate([
            'jobs' => 'required|array|min:1', 'jobs.*' => 'required|string',
            'driver_name' => 'nullable|string|max:255', 'transport_name' => 'required|string|max:255', 'delivery_date' => 'required|date',
        ]);

        if ($validated['transport_name'] === 'เซลล์ไปส่งเอง' && blank($validated['driver_name'] ?? null)) {
            return redirect()->back()->with('error', 'เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเองด้วย');
        }
        if ($validated['transport_name'] !== 'เซลล์ไปส่งเอง' && filled($validated['driver_name'] ?? null) && !in_array($validated['driver_name'], $this->responsiblePersons, true)) {
            return redirect()->back()->with('error', 'กรุณาเลือกชื่อผู้รับผิดชอบจากรายการที่มีให้เท่านั้น');
        }

        $assignedBy = $this->loggedInName();
        DB::beginTransaction();
        try {
            foreach ($validated['jobs'] as $jobKey) {
                if (!str_contains($jobKey, ':')) continue;
                [$type, $id] = explode(':', $jobKey, 2);
                
                if ($type === 'bill') $job = Bill::where('so_detail_id', $id)->first();
                elseif ($type === 'doc') $job = Docbills::where('doc_id', $id)->first();
                elseif ($type === 'po') $job = DB::connection($this->erpConnection)->table('polist')->where('PONum', $id)->first();
                else continue;

                if (!$job || transaction_delivery::where('bill_id', $id)->exists()) continue;

                transaction_delivery::create([
                    'bill_id' => $id, 'name_pick' => $assignedBy, 'time_pick' => now(), 'delivery_date' => $validated['delivery_date'],
                    'transport_name' => $validated['transport_name'], 'driver_name' => $validated['driver_name'] ?: null,
                    'assigned_by' => $assignedBy, 'check_name' => null, 'check_time' => null, 'status' => '0', 'note' => null,
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'บันทึกข้อมูลไม่สำเร็จ: ' . $e->getMessage());
        }
        return redirect()->route('deliverytrack')->with('success', 'บันทึกข้อมูลการจัดส่งเรียบร้อยแล้ว');
    }

    public function summary(Request $request)
    {
        if ($resp = $this->checkAccess()) return $resp;
        $date   = $request->input('date');
        $billId = trim((string) $request->input('bill_id', ''));

        $query = transaction_delivery::query()->orderBy('id');

        if ($billId !== '') {
            // ค้นหาด้วยเลขบิล/SO/PO — ข้ามตัวกรองวันที่ เพื่อให้เจอบิลไม่ว่าจะอยู่วันไหน
            // transaction_delivery.bill_id เก็บ so_detail_id (บิล), doc_id (บิลชั่วคราว) หรือ PONum (รับเอง)
            // งานส่งของจริงมองเห็นเป็น "เลขบิล (billid)/SO" จึงต้องแปลงกลับเป็น so_detail_id ก่อน
            $billMatchIds = Bill::where('billid', 'LIKE', "%{$billId}%")
                ->orWhere('so_id', 'LIKE', "%{$billId}%")
                ->pluck('so_detail_id')->all();
            $query->where(function ($q) use ($billId, $billMatchIds) {
                $q->where('bill_id', 'LIKE', "%{$billId}%");   // ครอบคลุม PONum (รับเอง) และ doc_id (บิลชั่วคราว) ที่เก็บตรง ๆ
                if (!empty($billMatchIds)) $q->orWhereIn('bill_id', $billMatchIds);
            });
        } elseif (filled($date)) {
            // ทุกงาน (ส่งของ/บิลชั่วคราว/รับเอง) ใช้ delivery_date เป็นหลัก
            $query->whereDate('delivery_date', $date);
        }

        $boxesByDate = $this->summaryBoxesByDate($query->get());

        // ตอนกรองวันที่ (ไม่ได้ค้นด้วยเลขบิล) เก็บเฉพาะกลุ่มของวันที่ที่เลือก (เพราะ query ด้านบน over-fetch จาก OR)
        if ($billId === '' && filled($date)) {
            $boxesByDate = array_intersect_key($boxesByDate, [$date => true]);
        }

        ksort($boxesByDate);
        return view('driver.delivery-summary', [
            'date'          => $date,
            'billId'        => $billId,
            'boxesByDate'   => $boxesByDate,
            'loggedInName'  => $this->loggedInName(),
            'canCancelJobs' => $this->canCancelAssignment(Auth::guard('web')->user()),
        ]);
    }

    /**
     * จัดกลุ่ม transaction_delivery เป็น boxes ต่อ "วันที่ที่ถูกต้องตามประเภทงาน"
     *   ทุกงาน (ส่งของ/บิลชั่วคราว/รับเอง) → ใช้ delivery_date
     */
    private function summaryBoxesByDate($deliveries): array
    {
        if ($deliveries->isEmpty()) return [];

        $grouped = $deliveries->groupBy(function ($d) {
            return $d->delivery_date ? Carbon::parse($d->delivery_date)->format('Y-m-d') : 'ไม่ระบุวันที่';
        });

        $boxesByDate = [];
        foreach ($grouped as $dateKey => $group) {
            $boxes = $this->buildDispatchBoxes($group);
            if (!empty($boxes)) $boxesByDate[$dateKey] = $boxes;
        }
        return $boxesByDate;
    }

    // ==========================================
    // แก้ไขส่วนนี้: ใช้ Bulk Query แทนการยิง Query ในลูป (แก้ปัญหา Timeout)
    // ==========================================
    private function buildDispatchBoxes($deliveries): array
    {
        // 1. รวบรวม ID ทั้งหมดที่ต้องการค้นหา
        $deliveryIds = $deliveries->pluck('bill_id')->unique()->toArray();

        if (empty($deliveryIds)) {
            return [];
        }

        // 2. ดึงข้อมูลแบบ Bulk (ยิง Query แค่ 3 ครั้ง ไม่ว่าข้อมูลจะมีกี่พันรายการ)
        $bills = Bill::whereIn('so_detail_id', $deliveryIds)->get()->keyBy('so_detail_id');
        
        $unmatchedForDoc = collect($deliveryIds)->diff($bills->keys())->toArray();
        $docs = Docbills::whereIn('doc_id', $unmatchedForDoc)->get()->keyBy('doc_id');
        
        $unmatchedForPo = collect($unmatchedForDoc)->diff($docs->keys())->toArray();
        $pos = [];
        $poReceiveByPo = collect();
        $poCancelledByPo = collect();
        if (!empty($unmatchedForPo)) {
            $pos = DB::connection($this->erpConnection)->table('polist')
                ->whereIn('PONum', $unmatchedForPo)
                ->get()
                ->keyBy('PONum');

            // สถานะ PO (ครบ/ยกเลิก) — ไว้ "ตัด PO ที่ครบแล้ว" ออกจากเอกสารงานไปรับเอง
            $poIdsWithPrefix = collect($unmatchedForPo)->map(fn ($p) => 'PO' . $p)->unique()->values();
            $poReceiveByPo = $this->chunkedWhereInGet(fn () => PoReceive::query(), 'po_id', $poIdsWithPrefix)
                ->keyBy(fn ($r) => preg_replace('/^PO/', '', $r->po_id));
            $poCancelledByPo = $this->chunkedWhereInGet(fn () => PooutsideCancelled::query(), 'po_id', $poIdsWithPrefix)
                ->keyBy(fn ($r) => preg_replace('/^PO/', '', $r->po_id));
        }

        $boxes = [];

        // 3. วนลูปและดึงข้อมูลจาก Collection ที่เตรียมไว้ (ไม่มีการยิง Query เพิ่ม)
        foreach ($deliveries as $delivery) {
            $id = $delivery->bill_id;

            if ($bills->has($id)) {
                $bill = $bills[$id];
                $customerCode = $bill->customer_id ?: $bill->customer_name;
                $customerName = $bill->customer_name;
                $billNo       = $bill->billid;
                $address      = $bill->customer_address;
                $lalong       = $bill->customer_la_long;
                $notes        = $bill->notes;
                $itemType     = 'bill';
            } elseif ($docs->has($id)) {
                $doc = $docs[$id];
                $customerCode = $doc->id_com ?: $doc->com_name;
                $customerName = $doc->com_name;
                $billNo       = $doc->doc_id;
                $address      = $doc->com_address;
                $lalong       = $doc->com_la_long;
                $notes        = $doc->notes;
                $itemType     = 'doc';
            } elseif (isset($pos[$id])) {
                $po = $pos[$id];
                // งานไปรับเอง: แสดงทุก PO แต่ mark ว่า "ครบแล้ว" (รับเข้าครบ/COMPLETED) เพื่อให้เลือกไม่ได้
                $status       = $this->resolvePOStatus($po, $poReceiveByPo->get($id), $poCancelledByPo->has($id));
                $poComplete   = ($status['color'] ?? '') === 'green';
                $customerCode = $po->VendorID;
                $customerName = $po->VendorName;
                $billNo       = $po->PONum;
                $address      = null;
                $lalong       = null;
                $notes        = 'ไปรับของที่ร้าน (SO ' . $po->SONum . ')';
                $itemType     = 'po';
            } else {
                continue;
            }
            $isComplete = $itemType === 'po' ? ($poComplete ?? false) : false;

            $transport = $delivery->transport_name ?: 'ไม่ระบุวิธีการจัดส่ง';
            $driver    = $delivery->driver_name ?: null;
            $boxKey = $transport . '||' . ($driver ?? '');

            if (!isset($boxes[$boxKey])) {
                $boxes[$boxKey] = [
                    'transport_name' => $transport,
                    'driver_name'    => $driver,
                    'assigned_by'    => $delivery->assigned_by ?: null,
                    'time_pick'      => $delivery->time_pick ?: null,
                    'delivery_date'  => $delivery->delivery_date,
                    'id_transport'   => $delivery->id_transport ?: null,
                    'total_items'    => 0,
                    'customers'      => [],
                ];
            } else {
                if (empty($boxes[$boxKey]['assigned_by']) && !empty($delivery->assigned_by)) {
                    $boxes[$boxKey]['assigned_by'] = $delivery->assigned_by;
                }
                if (empty($boxes[$boxKey]['id_transport']) && !empty($delivery->id_transport)) {
                    $boxes[$boxKey]['id_transport'] = $delivery->id_transport;
                }
            }

            if (!isset($boxes[$boxKey]['customers'][$customerCode])) {
                $boxes[$boxKey]['customers'][$customerCode] = [
                    'customer_code' => $customerCode,
                    'customer_name' => $customerName,
                    'address'       => $address,
                    'lalong'        => $lalong,
                    'items'         => [],
                ];
            }

            // ประเภทขนส่งของ item: บิลจริง = ตาม tblbill.transport_type (null→company),
            // บิลชั่วคราว(doc) = company, งานไปรับเอง(po) = null (แยกด้วย type)
            if ($itemType === 'bill') {
                $itemTransport = ($bill->transport_type ?: 'company');
            } elseif ($itemType === 'doc') {
                $itemTransport = 'company';
            } else {
                $itemTransport = null;
            }

            $boxes[$boxKey]['customers'][$customerCode]['items'][] = [
                'id'             => $id,
                'bill_no'        => $billNo,
                'notes'          => $notes,
                'type'           => $itemType,     // bill | doc | po (po = งานไปรับเอง)
                'is_complete'    => $isComplete,   // true = PO รับเข้าครบแล้ว → เลือกไม่ได้/ไม่พิมพ์
                'transport_type' => $itemTransport,
                'id_transport'   => $delivery->id_transport ?: null,   // เลขขนส่ง แยกรายบิล (งานขนส่งเอกชน)
                'name_pick'      => $delivery->name_pick ?: null,      // ผู้จ่ายงาน (รายบิล)
                'time_pick'      => $delivery->time_pick ?: null,      // เวลาจ่ายงาน (รายบิล)
            ];

            $boxes[$boxKey]['total_items']++;
        }

        // 4. จัดเรียงข้อมูล (เหมือนเดิม)
        foreach ($boxes as &$box) {
            ksort($box['customers']);
            $stopNo = 1;
            foreach ($box['customers'] as &$cust) {
                $cust['stop_no'] = $stopNo;
                $itemNo = 1;
                foreach ($cust['items'] as &$item) {
                    $item['seq'] = $stopNo . '.' . $itemNo;
                    $itemNo++;
                }
                unset($item);
                $stopNo++;
            }
            unset($cust);
        }
        unset($box);

        return $boxes;
    }

    /**
     * ยกเลิกการจ่ายงาน (คืนงานกลับไปหน้าจ่ายงาน) — ลบแถวใน transaction_transport
     * เมื่อไม่มีแถวใน transaction_transport แล้ว งานจะกลับไปโผล่ในหน้า deliverytrack ให้จ่ายใหม่ได้
     */
    public function cancelAssignment(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user) return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        // ยกเลิกงานได้เฉพาะผู้ใช้ชื่อ "FILM" หรือ role admin เท่านั้น
        if (!$this->canCancelAssignment($user)) {
            return response()->json(['ok' => false, 'message' => 'เฉพาะผู้ใช้ FILM หรือ admin เท่านั้นที่ยกเลิกงานได้'], 403);
        }

        $validated = $request->validate([
            'bill_ids'   => 'required|array|min:1',
            'bill_ids.*' => 'required|string',
        ]);
        $billIds = array_values(array_unique($validated['bill_ids']));

        // soft-cancel: ตั้งสถานะ "ยกเลิก" ไว้ (ไม่ลบทิ้ง) -> ข้อมูลไม่หาย, ตรวจย้อนหลังได้,
        // และ global scope จะซ่อนงานที่ยกเลิกออกจากทุกหน้า (สรุปงานคนขับ/so.show/dashboarddoc/oil)
        // ทำให้หน้าจ่ายงานถือว่ายังไม่จ่าย -> คืนงานกลับไปจ่ายใหม่ได้
        $updated = transaction_delivery::whereIn('bill_id', $billIds)
            ->update([
                'cancelled_at' => Carbon::now()->toDateTimeString(),
                'cancelled_by' => $user->name,
            ]);

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบงานที่จะยกเลิก (อาจถูกยกเลิกไปก่อนแล้ว)'], 404);
        }

        \Illuminate\Support\Facades\Log::info('deliverytrack.cancelAssignment', [
            'by' => $user->name, 'bill_ids' => $billIds, 'cancelled' => $updated,
        ]);

        return response()->json(['ok' => true, 'message' => 'ยกเลิกงาน ' . $updated . ' รายการ คืนกลับไปหน้าจ่ายงานแล้ว']);
    }

    /**
     * บันทึกเลขขนส่ง (id_transport) สำหรับงานขนส่งเอกชน — อัปเดตทุกแถวในกล่องงานเดียวกัน
     */
    public function saveTransportId(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user) return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        if (!$this->hasDeliveryAccess($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการ'], 403);
        }

        $validated = $request->validate([
            'bill_ids'     => 'required|array|min:1',
            'bill_ids.*'   => 'required|string',
            'id_transport' => 'nullable|string|max:100',
        ]);
        $billIds     = array_values(array_unique($validated['bill_ids']));
        $idTransport = trim((string) ($validated['id_transport'] ?? ''));

        $updated = transaction_delivery::whereIn('bill_id', $billIds)
            ->update(['id_transport' => $idTransport !== '' ? $idTransport : null]);

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบงานที่จะบันทึกเลขขนส่ง'], 404);
        }
        return response()->json([
            'ok'           => true,
            'message'      => $idTransport !== '' ? 'บันทึกเลขขนส่งแล้ว' : 'ล้างเลขขนส่งแล้ว',
            'id_transport' => $idTransport,
        ]);
    }

    /**
     * เปลี่ยนประเภทขนส่งของบิล (company <-> private) — อัปเดตทุกแถว tblbill ที่ billid ตรงกัน
     */
    public function setTransportType(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user) return response()->json(['ok' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        if (!$this->hasDeliveryAccess($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการ'], 403);
        }

        $validated = $request->validate([
            'billid'         => 'required|string',
            'transport_type' => 'required|in:company,private',
        ]);

        // เก็บ 'private' ตรง ๆ, ส่วน company เก็บเป็น 'company' (null ก็ถือเป็น company อยู่แล้ว)
        $updated = DB::table('tblbill')
            ->where('billid', $validated['billid'])
            ->update(['transport_type' => $validated['transport_type']]);

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบบิลนี้ (อาจถูกเปลี่ยนไปแล้ว)'], 404);
        }

        $label = $validated['transport_type'] === 'private' ? 'ขนส่งเอกชน' : 'ขนส่งโดยบริษัท';
        return response()->json([
            'ok'      => true,
            'message' => 'ย้ายบิล ' . $validated['billid'] . ' ไปเป็น ' . $label . ' แล้ว (' . $updated . ' รายการ)',
        ]);
    }

    /**
     * ปริ้นใบงาน "รับของ (ไปรับเอง)" เฉพาะ PO ที่ติ๊กเลือก จากหน้าสรุป
     */
    public function printSelectedPickup(Request $request)
    {
        if ($resp = $this->checkAccess()) return $resp;
        $ids  = array_values(array_filter((array) $request->input('ids', [])));
        $date = $request->input('date');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'ไม่ได้เลือกงานรับของ');
        }

        $poRows = DB::connection($this->erpConnection)->table('polist')
            ->whereIn('PONum', $ids)
            ->get()
            ->keyBy('PONum');

        // เรียงตามลำดับที่เลือก + เอาเฉพาะที่เป็น PO จริง
        $jobs = collect($ids)->map(fn ($id) => $poRows->get($id))->filter()->values();
        if ($jobs->isEmpty()) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูล PO ที่เลือก');
        }

        $jobs = $this->attachVendorAddressAndItems($jobs);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('driver.pickup-print', [
                'jobs'      => $jobs,
                'date'      => $date,
                'printedBy' => $this->loggedInName(),
                'printedAt' => Carbon::now(),
            ])->setPaper('a4', 'portrait');

        return $pdf->stream('ใบงานรับของ_' . ($date ?: 'all') . '.pdf');
    }

    public function printGroup(Request $request)
    {
        if ($resp = $this->checkAccess()) return $resp;
        $date = $request->input('date');
        $transport = $request->input('transport');
        $driver = $request->input('driver');

        $query = transaction_delivery::where('transport_name', $transport);
        if (filled($date)) $query->whereDate('delivery_date', $date);
        if (filled($driver)) $query->where('driver_name', $driver);
        else $query->where(fn ($q) => $q->whereNull('driver_name')->orWhere('driver_name', ''));

        $deliveries = $query->get();
        $boxes = $this->buildDispatchBoxes($deliveries);
        $box = reset($boxes) ?: [
            'transport_name' => $transport,
            'driver_name'    => $driver ?: null,
            'assigned_by'    => null,
            'time_pick'      => null,
            'customers'      => [],
        ];

        $printedBy = $this->loggedInName();
        $printedAt = Carbon::now();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('driver.delivery', [
                'printMode' => true,
                'date'      => $date,
                'box'       => $box,
                'printedBy' => $printedBy,
                'printedAt' => $printedAt,
            ])->setPaper('a4', 'portrait');

        $pdf->render();
        $canvas = $pdf->getDomPDF()->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $font  = $fontMetrics->get_font('helvetica');
            $text  = "Page {$pageNumber}";
            $size  = 10;
            $color = [0.07, 0.09, 0.15];
            $x = $canvas->get_width() - 50;
            $y = 32;
            $canvas->text($x, $y, $text, $font, $size, $color);
        });

        $fileName = 'ใบงานขนส่ง_' . $transport . '_' . ($date ?: 'all') . '.pdf';
        return $pdf->stream($fileName);
    }

    /**
     * รวมทุกกล่อง (รถ/คนขับ) ของวันเดียวกันให้เป็น PDF ไฟล์เดียว
     * ใช้ปุ่ม "ปริ้นทั้งหมด" ในหน้าสรุปงานคนขับ (deliverytrack.summary)
     */
    public function printAllGroups(Request $request)
    {
        if ($resp = $this->checkAccess()) return $resp;

        // ค่าจาก query string: อาจเป็นวันที่จริง (Y-m-d), 'ไม่ระบุวันที่', หรือว่าง (ไม่ได้ระบุเลย)
        $rawDate = $request->input('date');
        $isUnspecifiedGroup = $rawDate === 'ไม่ระบุวันที่';

        $query = transaction_delivery::query();
        if ($isUnspecifiedGroup) {
            $query->whereNull('delivery_date');
        } elseif (filled($rawDate)) {
            $query->whereDate('delivery_date', $rawDate);
        }

        $deliveries = $query->get();
        $boxes = $this->buildDispatchBoxes($deliveries); // ได้ทุกกล่อง (รถ/คนขับ) ของวันนี้ ไม่กรอง transport/driver

        $printedBy = $this->loggedInName();
        $printedAt = Carbon::now();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('driver.delivery', [
                'printMode' => true,
                'date'      => $isUnspecifiedGroup ? null : $rawDate,
                'boxes'     => $boxes,
                'printedBy' => $printedBy,
                'printedAt' => $printedAt,
            ])->setPaper('a4', 'portrait');

        $pdf->render();
        $canvas = $pdf->getDomPDF()->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $font  = $fontMetrics->get_font('helvetica');
            $text  = "Page {$pageNumber}";
            $size  = 10;
            $color = [0.07, 0.09, 0.15];
            $x = $canvas->get_width() - 50;
            $y = 32;
            $canvas->text($x, $y, $text, $font, $size, $color);
        });

        $fileName = 'ใบงานขนส่งทั้งหมด_' . ($isUnspecifiedGroup ? 'ไม่ระบุวันที่' : ($rawDate ?: 'all')) . '.pdf';
        return $pdf->stream($fileName);
    }

    public function saveNewBill(Request $request, $id)
    {
        // ปล่อยว่างหรือ implement ตามต้องการ
    }
}