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
    /**
     * connection ของฐานข้อมูล ERP หลัก (ตาราง polist, SOHD, POHD ฯลฯ)
     * ยืนยันแล้วว่าคือ database "3e" ตาม config/database.php ที่มีอยู่แล้ว
     */
    protected string $erpConnection = 'mysql_3e';

    /**
     * connection SQL Server (POHD/PODT/EMVendor/EMTransp) — read-only บังคับ
     * (driver = sqlsrv_readonly ใน config/database.php)
     */
    protected string $account03Connection = 'mssql_account03';

    /**
     * วิธีจัดส่ง/รับของ ที่ถือว่าเป็น "รับเอง" (ต้องออกเส้นทางให้คนขับไปรับกลับมา)
     * ยืนยันค่าจริงแล้วผ่าน tinker (2026-08) จาก:
     *   \App\PoList::whereNotNull('DeliveryMethod')->select('DeliveryMethod')->distinct()->get();
     * ⚠️ สะกด "มอเตอร์ไซด์" (ด) ไม่ใช่ "มอเตอร์ไซค์" (ค) ตามที่เก็บจริงในระบบ
     */
    protected array $selfPickupMethods = [
        'รับเองรถใหญ่',
        'รับเองมอเตอร์ไซด์',
    ];

    /**
     * เริ่มดึงข้อมูล PO รับเองตั้งแต่วันที่นี้เป็นต้นไป (กรองจาก DeliveryDate)
     * ตัดข้อมูลเก่าก่อนหน้านี้ทิ้งไปเลย ไม่ต้องดึงมาประมวลผล ลดโหลด query ลงได้มาก
     */
    protected string $selfPickupStartDate = '2026-01-01';

    protected array $deliveryMethods = [
        'มอเตอร์ไซต์กบ',
        'มอเตอร์ไซด์ในเมือง',
        'มอเตอร์ไซค์ - พระราม 2',
        'เซลล์ไปส่งเอง',
        '3ฒย 478',
        '3ฉมง 3059',
        '2ฒธ 1621',
        '2ฒธ 1620',
        '3ฒก 6071',
        '2ฒฏ 3017',
        '4ฒฎ 5861',
        '2ฒศ 6762',
        '2ฉธ 1619',
        '6 ล้อ',
        'laramove',
        'สุราษฎร์ทัวร์ เอ็กเพรส',
        'แท็กซี่คอนซูม 02-6230110',
        'AT SPEED 02-233-6062',
        'PM 081-564-5920',
        'ป้าติ๊ก P.P 083-082-1026',
        'ข้ามสมุทรขนส่ง 02-887-0368',
        'ระยองพัฒนา 02-2229296',
        'นิวอุดร ขนส่ง 085-4830094',
        '999ขนส่ง 087-053 5488',
        'นิ่มซี่เส็ง 02-282-7936',
        'สยามเฟริส 02-954-3601',
        'เจ๊ แต๋ว 02-623-3919',
        'นครพนมขนส่ง 02-448-2065',
        'สกุลทองขนส่ง 02-2225595',
        'NTC 02-611-9582',
        'PM PL 02-214-0629',
        'ศรีราชาทัวร์ 02-391-5188',
        'พัฒนาเอ็กสเพรส 02-223-3831',
        'ชวาลกิต ขนส่ง 02-8894747-9',
        'ลูกค้ารับเอง',
        'เกียรติสกุลทรานสปอร์ต สาย 2 064-7894452,064-7893653',
        'ประจวบทองขัยขนส่ง สาย 2 02-4481976-7, 086-3679602',
        'สี่สหายขนส่ง (1988) 02-4516712-6',
        'ไอที ทรานสปอร์ต 089-6491111',
        'เอ็มเอส เอ็กซ์เพรส 086-1217672, 089-0990782',
        'เอส.ดี. เอ็กซ์เพรส 02-2144341, 2165846',
        'KERRY',
        'Grab',
        'ขนส่ง SD EXPRESS',
        'TB พาร์ท',
        'ขนส่ง โกโลด',
        'ขนส่ง BS',
        'ขนส่ง มะม่วง',
        'ขนส่ง PJ',
        'ขนส่ง คู่บุญ',
        'ธนมัยสาย2',
    ];

    protected array $responsiblePersons = [
        'บอย',
        'แซม',
        'กบ',
        'joey',
        'yuth',
        'แฟงค์',
        'เก่ง',
        'แมน',
        'เอ',
        'กอลฟ์',
        'บังเดช',
        'เอ้',
    ];

    /**
     * เช็ค login + สิทธิ์การเข้าใช้งาน (เฉพาะ role admin, store เท่านั้น)
     */
    private function checkAccess()
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::guard('web')->user();

        if (!in_array($user->role, ['admin', 'store'], true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        return null;
    }

    private function loggedInName()
    {
        $user = Auth::guard('web')->user();
        return $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? '-');
    }

    /**
     * หน้าจ่ายงาน — แสดง "งานทั้งหมดที่ยังไม่ได้จ่ายให้คนขับ"
     * แบ่งเป็น 2 กลุ่มใหญ่ในหน้าเว็บ (view เป็นคนจัด layout):
     *   1) รับของเอง  -> งาน PO รับเอง (คนขับต้องออกไปรับของกลับมา)
     *   2) ส่งของ     -> งานบิลขาย + งานบิลชั่วคราว (คนขับเอาของไปส่งลูกค้า)
     *
     * ไม่มีการกรองตามวันที่แล้ว — แสดงงานค้างทั้งหมดทุกวัน
     */
    public function index(Request $request)
    {
        if ($resp = $this->checkAccess()) {
            return $resp;
        }

        // ---- บิลขาย ----
        $bills = Bill::whereNotNull('emp_picker')
            ->where('emp_picker', '!=', '')
            ->orderBy('time')
            ->get();

        // ---- บิลชั่วคราว ----
        $docbills = Docbills::where('status', '0')
            ->orderBy('time')
            ->get();

        // ---- PO รับเอง ----
        $poJobs = $this->getPendingSelfPickupPOs();

        // เช็คว่างานไหนถูกจ่ายให้คนขับไปแล้ว (ไม่สนวันที่ — เอาทุก record, รวมทั้ง 3 ประเภท)
        $relevantIds = $bills->pluck('so_detail_id')
            ->merge($docbills->pluck('doc_id'))
            ->merge($poJobs->pluck('PONum'));

        // ⚠️ orderBy('id') สำคัญ — ป้องกัน keyBy() สุ่มเลือกแถวเก่า/ใหม่ไม่แน่นอน
        // เมื่อมีหลายแถว bill_id เดียวกัน (เช่น แถวเดิม + แถวที่เกิดจาก "ส่งใหม่พรุ่งนี้")
        // ให้ยึดแถว id ล่าสุดเสมอเป็นตัวตัดสินว่างานนี้ "ถูกจ่ายอยู่ปัจจุบัน" หรือไม่
        $dispatched = $this->chunkedWhereInGet(
            fn () => transaction_delivery::query()->orderBy('id'),
            'bill_id',
            $relevantIds
        )->keyBy('bill_id');

        // เอาเฉพาะงานที่ "ยังไม่ได้จ่าย" เท่านั้น
        $bills    = $bills->reject(fn ($b) => $dispatched->has($b->so_detail_id))->values();
        $docbills = $docbills->reject(fn ($d) => $dispatched->has($d->doc_id))->values();
        $poJobs   = $poJobs->reject(fn ($p) => $dispatched->has($p->PONum))->values();

        $billGroups = $this->groupBillsByCustomer($bills);
        $docGroups  = $this->groupDocsByCustomer($docbills);
        $poGroups   = $this->groupPoByVendor($poJobs);

        return view('driver.delivery', [
            'billGroups'         => $billGroups,
            'docGroups'          => $docGroups,
            'poGroups'           => $poGroups,
            'responsiblePersons' => $this->responsiblePersons,
            'deliveryMethods'    => $this->deliveryMethods,
            'loggedInName'       => $this->loggedInName(),
        ]);
    }

    /**
     * ดึงรายการ PO ที่วิธีรับของเป็น "รับเอง" และยังไม่เสร็จงาน
     * (ไม่ใช่ ครบ(green) และไม่ใช่ ยกเลิก(red) — ดู resolvePOStatus() ด้านล่าง)
     *
     * ดึงเป็นก้อนเดียว (whereIn) ทั้งหมด ไม่ loop query ทีละ PO
     */
    private function getPendingSelfPickupPOs()
    {
        $poLists = DB::connection($this->erpConnection)->table('polist')
            ->whereIn('DeliveryMethod', $this->selfPickupMethods)
            ->where('DeliveryDate', '>=', $this->selfPickupStartDate)
            // ตัด PO ที่ระบบเก่าบันทึกว่า "COMPLETED" แล้วออกตั้งแต่ต้นทาง
            // (COALESCE+TRIM+UPPER กันเคส NULL/เว้นวรรค/ตัวพิมพ์เล็ก-ใหญ่ ให้ไม่หลุดจากการกรองไปโดยไม่ตั้งใจ)
            ->whereRaw("UPPER(TRIM(COALESCE(POstatus, ''))) != 'COMPLETED'")
            ->orderBy('SONum')
            ->orderBy('PONum')
            ->get();

        if ($poLists->isEmpty()) {
            return collect();
        }

        $poIdsWithPrefix = $poLists->pluck('PONum')->map(fn ($p) => 'PO' . $p)->unique()->values();

        // ใช้ whereIn('po_id', ...) อย่างเดียวก็พอ (ไม่ต้อง whereIn('so_id', ...) ซ้ำ)
        // เพราะ key ที่ผูกกับแต่ละแถวใช้ so_id ที่ได้จากผลลัพธ์เองอยู่แล้ว
        // และช่วยลดจำนวน placeholder ในคำสั่ง SQL ลงครึ่งหนึ่งด้วย
        $poReceives = $this->chunkedWhereInGet(
            fn () => PoReceive::query(),
            'po_id',
            $poIdsWithPrefix
        )->keyBy(fn ($r) => $r->so_id . '|' . preg_replace('/^PO/', '', $r->po_id));

        $cancelled = $this->chunkedWhereInGet(
            fn () => PooutsideCancelled::query(),
            'po_id',
            $poIdsWithPrefix
        )->keyBy(fn ($r) => $r->so_id . '|' . preg_replace('/^PO/', '', $r->po_id));

        $result = collect();

        foreach ($poLists as $po) {
            $key = $po->SONum . '|' . $po->PONum;

            $status = $this->resolvePOStatus(
                $po,
                $poReceives->get($key),
                $cancelled->has($key)
            );

            if (!$this->isPoPending($status)) {
                continue; // ครบ หรือ ยกเลิกแล้ว ไม่ต้องเอาเข้ามาเป็นงาน
            }

            // stdClass จาก query builder — เพิ่ม property ใหม่ได้ตรงๆ เหมือน Eloquent
            $po->status_color = $status['color'];
            $po->status_label = $status['label'];
            $result->push($po);
        }

        // ⚠️ ต้อง return ผ่านตัวนี้เสมอ — จุดนี้เองที่ทำให้ vendor_address / items
        // ถูกแนบเข้าไปในแต่ละ $po ก่อนส่งกลับ ถ้า return $result; เฉยๆ จะไม่มี
        // ที่อยู่/รายการสินค้าเลย เพราะ attachVendorAddressAndItems() ไม่เคยถูกเรียก
        return $this->attachVendorAddressAndItems($result);
    }

    /**
     * bulk-fetch "ที่อยู่ vendor" + "รายการสินค้า/จำนวน" ของแต่ละ PO
     * จาก mssql_account03 (POHD + EMVendor + PODT) แล้ว attach เข้า object PO เดิม
     *
     * ⚠️ ยืนยันจาก tinker (2026-08):
     *   - EMVendor ไม่มีคอลัมน์ "VendorAddress" เดียว — ไม่มีคอลัมน์นี้อยู่จริงในตาราง
     *     ต้องต่อเองจาก VendorAddr1, VendorAddr2, District, Amphur, Province, PostCode
     *   - PODT ไม่มีคอลัมน์ DocuNo — เชื่อมกับ POHD ผ่าน POID เท่านั้น
     *     (POHD.POID = PODT.POID)
     *   - PODT มี CancelFlag ('N' = ปกติ) — กรองไม่เอาแถวที่ถูกยกเลิก
     */
    private function attachVendorAddressAndItems($poJobs)
    {
        if ($poJobs->isEmpty()) {
            return $poJobs;
        }

        $docuNos = $poJobs->pluck('PONum')->map(fn ($p) => 'PO' . $p)->unique()->values();

        // ---- หัว PO + ที่อยู่ vendor (POHD -> EMVendor) ----
        // ดึง POID มาด้วย เพราะต้องใช้เป็น key เชื่อมไป PODT ต่อ
        $headers = $this->chunkedWhereInGet(
            fn () => DB::connection($this->account03Connection)->table('POHD')
                ->leftJoin('EMVendor', 'POHD.VendorID', '=', 'EMVendor.VendorID')
                ->select(
                    'POHD.POID',
                    'POHD.DocuNo',
                    'EMVendor.VendorAddr1',
                    'EMVendor.VendorAddr2',
                    'EMVendor.District',
                    'EMVendor.Amphur',
                    'EMVendor.Province',
                    'EMVendor.PostCode'
                ),
            'POHD.DocuNo',
            $docuNos
        )->keyBy('DocuNo');

        // ---- รายการสินค้า/จำนวน (PODT) — เชื่อมด้วย POID ไม่ใช่ DocuNo ----
        $poIds = $headers->pluck('POID')->filter()->unique()->values();

        $items = $poIds->isEmpty()
            ? collect()
            : $this->chunkedWhereInGet(
                fn () => DB::connection($this->account03Connection)->table('PODT')
                    ->where('CancelFlag', '!=', 'Y') // กันแถวที่ถูกยกเลิก
                    ->select('POID', 'GoodName', 'GoodQty2'),
                'POID',
                $poIds
            )->groupBy('POID');

        foreach ($poJobs as $po) {
            $docuNo = 'PO' . $po->PONum;
            $header = $headers->get($docuNo);

            // ต่อที่อยู่จากหลายคอลัมน์ ข้ามส่วนที่ว่าง
            $po->vendor_address = $header
                ? collect([
                    $header->VendorAddr1,
                    $header->VendorAddr2,
                    $header->District,
                    $header->Amphur,
                    $header->Province,
                    $header->PostCode,
                ])->filter(fn ($v) => filled($v))->implode(' ')
                : null;

            $po->items = $header
                ? ($items->get($header->POID) ?? collect())
                    ->map(fn ($i) => [
                        'name' => $i->GoodName,
                        'qty'  => rtrim(rtrim($i->GoodQty2, '0'), '.'), // ตัด .0000 ท้ายทิ้ง
                    ])
                    ->values()
                : collect();
        }

        return $poJobs;
    }

    /**
     * ดึงข้อมูลด้วย whereIn($column, $values) แบบแบ่งเป็นก้อนๆ (chunk ทีละ 1000 ตัว)
     * แทนการยัด array ยาวๆ ลงไปใน whereIn เดียว — ป้องกัน MySQL/SQL Server error
     * "too many placeholders" ตอนข้อมูลมีเยอะมาก
     *
     * $queryFactory ต้องเป็น closure ที่คืน query builder (หรือ Eloquent query()) ใหม่
     * ทุกครั้งที่ถูกเรียก ห้าม share instance เดิมข้ามรอบ
     */
    private function chunkedWhereInGet(\Closure $queryFactory, string $column, $values, int $chunkSize = 1000)
    {
        $values  = $values instanceof \Illuminate\Support\Collection ? $values->all() : $values;
        $results = collect();

        foreach (array_chunk(array_values($values), $chunkSize) as $chunk) {
            if (empty($chunk)) {
                continue;
            }
            $results = $results->merge($queryFactory()->whereIn($column, $chunk)->get());
        }

        return $results;
    }

    /**
     * ตัดสินสถานะ PO 1 ใบ — เอาไว้แค่บอกว่า "คนขับเอาของมาแล้วหรือยัง"
     * ไม่สนใจรายละเอียดหลังบ้านของคลัง (เช่น รับแล้วแต่ยังไม่ระบุชั้นวาง)
     * เพราะไม่เกี่ยวกับหน้าที่ของหน้านี้ — จ่ายงานให้คนขับเท่านั้น
     *
     * @return array{color:string,label:string}
     */
    private function resolvePOStatus($poList, $receiveEntry, bool $isCancelled): array
    {
        if ($isCancelled) {
            return ['color' => 'red', 'label' => 'ยกเลิก'];
        }

        $newStatus = $receiveEntry->status ?? null;

        // "รับเข้าผิด" ถือว่าไม่มีข้อมูลจากระบบใหม่ -> fallback ไปสถานะเก่า
        if ($newStatus !== null && $newStatus !== 'รับเข้าผิด') {
            $map = [
                'ครบ'     => ['color' => 'green',  'label' => 'ครบ'],
                'บางส่วน' => ['color' => 'yellow', 'label' => 'รับบางส่วน'],
                'ยกเลิก'  => ['color' => 'red',    'label' => 'ยกเลิก'],
            ];

            return $map[$newStatus] ?? ['color' => 'inherit', 'label' => $newStatus];
        }

        // fallback: สถานะเก่าจาก polist.POstatus
        $old = strtoupper(trim($poList->POstatus ?? ''));
        $map = [
            'ENTRY'     => ['color' => 'inherit', 'label' => 'รอเข้า'],
            'COMPLETED' => ['color' => 'green',   'label' => 'ครบ'],
            'PARTIAL'   => ['color' => 'orange',  'label' => 'เลยกำหนด'],
            'CANCELLED' => ['color' => 'red',     'label' => 'ยกเลิก'],
        ];

        return $map[$old] ?? ['color' => 'inherit', 'label' => 'ไม่ทราบสถานะ'];
    }

    /** true = ยังไม่เสร็จงาน (ไม่ใช่ ครบ(green) และไม่ใช่ ยกเลิก(red)) */
    private function isPoPending(array $status): bool
    {
        return !in_array($status['color'], ['green', 'red'], true);
    }

    private function groupBillsByCustomer($bills): array
    {
        $groups = [];

        foreach ($bills as $bill) {
            $custId = $bill->customer_id;

            if (!isset($groups[$custId])) {
                $groups[$custId] = [
                    'customer_id'   => $custId,
                    'customer_name' => $bill->customer_name,
                    'rows'          => [],
                ];
            }

            $groups[$custId]['rows'][] = ['bill' => $bill];
        }

        return $groups;
    }

    private function groupDocsByCustomer($docbills): array
    {
        $groups = [];

        foreach ($docbills as $doc) {
            $custId = $doc->id_com;

            if (!isset($groups[$custId])) {
                $groups[$custId] = [
                    'customer_id'   => $custId,
                    'customer_name' => $doc->com_name,
                    'rows'          => [],
                ];
            }

            $groups[$custId]['rows'][] = ['doc' => $doc];
        }

        return $groups;
    }

    /**
     * จัดกลุ่ม PO ตาม "ร้าน" (vendor) ที่ต้องไปรับของ — ใช้ VendorID/VendorName
     * ที่ denormalize ไว้อยู่แล้วบน polist ไม่ต้อง query เพิ่ม
     * ที่อยู่ vendor (vendor_address) มาจาก attachVendorAddressAndItems() ที่ผูกไว้
     * กับ $po ก่อนหน้านี้แล้ว — เก็บที่ระดับกลุ่มครั้งเดียว (vendor เดียวกันที่อยู่เดียวกัน)
     */
    private function groupPoByVendor($poJobs): array
    {
        $groups = [];

        foreach ($poJobs as $po) {
            $vendorId = $po->VendorID ?: '-';

            if (!isset($groups[$vendorId])) {
                $groups[$vendorId] = [
                    'customer_id'    => $po->VendorID,
                    'customer_name'  => $po->VendorName,
                    'vendor_address' => $po->vendor_address ?? null,
                    'rows'           => [],
                ];
            }

            $groups[$vendorId]['rows'][] = ['po' => $po];
        }

        return $groups;
    }

    /**
     * บันทึกการจ่ายงานให้คนขับ — ต้องระบุ "วันที่จัดส่ง" (delivery_date) ด้วยเสมอ
     * เพื่อให้หน้าสรุปงานคนขับ (summary) เอาไปแยกวันได้
     */
    public function store(Request $request)
    {
        if ($resp = $this->checkAccess()) {
            return $resp;
        }

        $validated = $request->validate([
            'jobs'           => 'required|array|min:1',
            'jobs.*'         => 'required|string',
            'driver_name'    => 'nullable|string|max:255',
            'transport_name' => 'required|string|max:255',
            'delivery_date'  => 'required|date',
        ]);

        if ($validated['transport_name'] === 'เซลล์ไปส่งเอง' && blank($validated['driver_name'] ?? null)) {
            return redirect()->back()->with('error', 'เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเองด้วย');
        }

        if ($validated['transport_name'] !== 'เซลล์ไปส่งเอง'
            && filled($validated['driver_name'] ?? null)
            && !in_array($validated['driver_name'], $this->responsiblePersons, true)
        ) {
            return redirect()->back()->with('error', 'กรุณาเลือกชื่อผู้รับผิดชอบจากรายการที่มีให้เท่านั้น');
        }

        $assignedBy = $this->loggedInName();

        DB::beginTransaction();
        try {
            foreach ($validated['jobs'] as $jobKey) {
                if (!str_contains($jobKey, ':')) {
                    continue;
                }

                [$type, $id] = explode(':', $jobKey, 2);

                if ($type === 'bill') {
                    $job = Bill::where('so_detail_id', $id)->first();
                    if (!$job) {
                        continue;
                    }
                } elseif ($type === 'doc') {
                    $job = Docbills::where('doc_id', $id)->first();
                    if (!$job) {
                        continue;
                    }
                } elseif ($type === 'po') {
                    // job สำหรับ "ไปรับของ" — เช็คว่า PO นี้มีจริงในระบบ
                    $job = DB::connection($this->erpConnection)->table('polist')
                        ->where('PONum', $id)->first();
                    if (!$job) {
                        continue;
                    }
                } else {
                    continue;
                }

                $alreadyDispatched = transaction_delivery::where('bill_id', $id)->exists();
                if ($alreadyDispatched) {
                    continue;
                }

                transaction_delivery::create([
                    'bill_id'        => $id,
                    'name_pick'      => $assignedBy,
                    'time_pick'      => now(),
                    'delivery_date'  => $validated['delivery_date'],
                    'transport_name' => $validated['transport_name'],
                    'driver_name'    => $validated['driver_name'] ?: null,
                    'assigned_by'    => $assignedBy,
                    'check_name'     => null,
                    'check_time'     => null,
                    'status'         => '0',
                    'note'           => null,
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'บันทึกข้อมูลไม่สำเร็จ: ' . $e->getMessage());
        }

        return redirect()
            ->route('deliverytrack')
            ->with('success', 'บันทึกข้อมูลการจัดส่งเรียบร้อยแล้ว');
    }

    /**
     * หน้าสรุปงานคนขับ / งานที่จัดส่งแล้ว — แยกเป็นอีกหน้าต่างหาก
     * รับ ?date=YYYY-MM-DD (ไม่บังคับ) เพื่อกรองตาม "วันที่จัดส่ง" (delivery_date)
     */
    public function summary(Request $request)
    {
        if ($resp = $this->checkAccess()) {
            return $resp;
        }

        $date = $request->input('date');

        $query = transaction_delivery::query()->orderBy('delivery_date');
        if (filled($date)) {
            $query->whereDate('delivery_date', $date);
        }
        $deliveries = $query->get();

        $boxesByDate = [];
        foreach ($deliveries->groupBy(function ($d) {
            return $d->delivery_date ? Carbon::parse($d->delivery_date)->format('Y-m-d') : 'ไม่ระบุวันที่';
        }) as $dateKey => $group) {
            $boxesByDate[$dateKey] = $this->buildDispatchBoxes($group);
        }
        ksort($boxesByDate);

        return view('driver.delivery-summary', [
            'date'         => $date,
            'boxesByDate'  => $boxesByDate,
            'loggedInName' => $this->loggedInName(),
        ]);
    }

    private function buildDispatchBoxes($deliveries): array
    {
        $boxes = [];

        foreach ($deliveries as $delivery) {
            $id = $delivery->bill_id;

            $bill = Bill::where('so_detail_id', $id)->first();
            $doc  = $bill ? null : Docbills::where('doc_id', $id)->first();
            $po   = ($bill || $doc) ? null : DB::connection($this->erpConnection)->table('polist')
                ->where('PONum', $id)->first();

            if ($bill) {
                $customerCode = $bill->customer_id;
                $customerName = $bill->customer_name;
                $billNo       = $bill->billid;
                $address      = $bill->customer_address;
                $lalong       = $bill->customer_la_long;
                $notes        = $bill->notes;
            } elseif ($doc) {
                $customerCode = $doc->id_com;
                $customerName = $doc->com_name;
                $billNo       = $doc->doc_id;
                $address      = $doc->com_address;
                $lalong       = $doc->com_la_long;
                $notes        = $doc->notes;
            } elseif ($po) {
                // งาน "ไปรับของ" — ไม่มีที่อยู่ลูกค้า ใช้ชื่อร้าน(vendor)แทน
                $customerCode = $po->VendorID;
                $customerName = $po->VendorName;
                $billNo       = $po->PONum;
                $address      = null;
                $lalong       = null;
                $notes        = 'ไปรับของที่ร้าน (SO ' . $po->SONum . ')';
            } else {
                continue;
            }

            $transport = $delivery->transport_name ?: 'ไม่ระบุวิธีการจัดส่ง';
            $driver    = $delivery->driver_name ?: null;

            $boxKey = $transport . '||' . ($driver ?? '');

            if (!isset($boxes[$boxKey])) {
                $boxes[$boxKey] = [
                    'transport_name' => $transport,
                    'driver_name'    => $driver,
                    'assigned_by'    => $delivery->assigned_by ?: null,
                    'delivery_date'  => $delivery->delivery_date,
                    'total_items'    => 0,
                    'customers'      => [],
                ];
            } elseif (empty($boxes[$boxKey]['assigned_by']) && !empty($delivery->assigned_by)) {
                $boxes[$boxKey]['assigned_by'] = $delivery->assigned_by;
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

            $boxes[$boxKey]['customers'][$customerCode]['items'][] = [
                'id'      => $id,
                'bill_no' => $billNo,
                'notes'   => $notes,
            ];

            $boxes[$boxKey]['total_items']++;
        }

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

    public function printGroup(Request $request)
    {
        if ($resp = $this->checkAccess()) {
            return $resp;
        }

        $date      = $request->input('date');
        $transport = $request->input('transport');
        $driver    = $request->input('driver');

        $query = transaction_delivery::where('transport_name', $transport);

        if (filled($date)) {
            $query->whereDate('delivery_date', $date);
        }

        if (filled($driver)) {
            $query->where('driver_name', $driver);
        } else {
            $query->where(function ($q) {
                $q->whereNull('driver_name')->orWhere('driver_name', '');
            });
        }

        $deliveries = $query->get();
        $boxes = $this->buildDispatchBoxes($deliveries);
        $box = reset($boxes) ?: [
            'transport_name' => $transport,
            'driver_name'    => $driver ?: null,
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

        $fileName = 'ใบงานขนส่ง_' . $transport . '_' . ($date ?: 'all') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function saveNewBill(Request $request, $id)
    {
        //
    }
}