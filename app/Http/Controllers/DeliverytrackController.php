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

    private function checkAccess()
    {
        if (!Auth::guard('web')->check()) return redirect()->guest(route('login'));
        $user = Auth::guard('web')->user();
        if (!in_array($user->role, ['admin', 'store'], true)) abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
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

        return view('driver.delivery', [
            'billGroups'         => $this->groupBillsByCustomer($bills),
            'docGroups'          => $this->groupDocsByCustomer($docbills),
            'poGroups'           => $this->groupPoByVendor($poJobs),
            'responsiblePersons' => $this->responsiblePersons,
            'deliveryMethods'    => $this->deliveryMethods,
            'loggedInName'       => $this->loggedInName(),
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
        $date = $request->input('date');
        $query = transaction_delivery::query()->orderBy('delivery_date');
        if (filled($date)) $query->whereDate('delivery_date', $date);
        
        $boxesByDate = [];
        foreach ($query->get()->groupBy(function ($d) {
            return $d->delivery_date ? Carbon::parse($d->delivery_date)->format('Y-m-d') : 'ไม่ระบุวันที่';
        }) as $dateKey => $group) {
            $boxesByDate[$dateKey] = $this->buildDispatchBoxes($group);
        }
        ksort($boxesByDate);
        return view('driver.delivery-summary', ['date' => $date, 'boxesByDate' => $boxesByDate, 'loggedInName' => $this->loggedInName()]);
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
        if (!empty($unmatchedForPo)) {
            $pos = DB::connection($this->erpConnection)->table('polist')
                ->whereIn('PONum', $unmatchedForPo)
                ->get()
                ->keyBy('PONum');
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
            } elseif ($docs->has($id)) {
                $doc = $docs[$id];
                $customerCode = $doc->id_com ?: $doc->com_name;
                $customerName = $doc->com_name;
                $billNo       = $doc->doc_id;
                $address      = $doc->com_address;
                $lalong       = $doc->com_la_long;
                $notes        = $doc->notes;
            } elseif (isset($pos[$id])) {
                $po = $pos[$id];
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
                    'time_pick'      => $delivery->time_pick ?: null,
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