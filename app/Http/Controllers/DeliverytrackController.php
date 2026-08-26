<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Docbills;
use App\Models\transaction_delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliverytrackController extends Controller
{
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
     * แบบเดียวกับตัวอย่าง MobilePoappController@index
     * คืนค่า RedirectResponse ถ้ายังไม่ login (ให้ caller return ค่านี้ต่อ)
     * ถ้า login แล้วแต่ role ไม่ผ่าน จะ abort(403) หยุดการทำงานทันที
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

    public function index(Request $request)
    {
        if ($resp = $this->checkAccess()) {
            return $resp;
        }

        $user = Auth::guard('web')->user();
        $loggedInName = $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? '-');

        $date    = $request->input('date');
        $hasDate = filled($date);

        $bills         = collect();
        $docbills      = collect();
        $billGroups    = [];
        $docGroups     = [];
        $dispatchBoxes = [];

        if ($hasDate) {
            $bills = Bill::whereNotNull('emp_picker')
                ->where('emp_picker', '!=', '')
                ->whereDate('time', $date)
                ->orderBy('time')
                ->get();

            $docbills = Docbills::where('status', '0')
                ->whereDate('time', $date)
                ->orderBy('time')
                ->get();

            // งานที่ถูกจ่ายไปแล้วของวันนี้: จับคู่ด้วย bill_id ที่อยู่ในรายการของวันนี้
            // (time_pick คือ "เวลาที่คนจ่ายงานกด" ไม่ใช่วันที่ของงาน จึงจับคู่ด้วย bill_id แทน)
            $relevantIds = $bills->pluck('so_detail_id')->merge($docbills->pluck('doc_id'));
            $deliveries  = transaction_delivery::whereIn('bill_id', $relevantIds)->get();
            $dispatched  = $deliveries->keyBy('bill_id');

            $billGroups = $this->groupBillsByCustomer($bills, $dispatched);
            $docGroups  = $this->groupDocsByCustomer($docbills, $dispatched);

            $dispatchBoxes = $this->buildDispatchBoxes($deliveries);
        }

        return view('driver.delivery', [
            'bills'              => $bills,
            'docbills'           => $docbills,
            'billGroups'         => $billGroups,
            'docGroups'          => $docGroups,
            'date'               => $date,
            'hasDate'            => $hasDate,
            'responsiblePersons' => $this->responsiblePersons,
            'deliveryMethods'    => $this->deliveryMethods,
            'dispatchBoxes'      => $dispatchBoxes,
            'loggedInName'       => $loggedInName,
        ]);
    }

    private function groupBillsByCustomer($bills, $dispatched): array
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

            $groups[$custId]['rows'][] = [
                'bill'     => $bill,
                'assigned' => $dispatched->get($bill->so_detail_id),
            ];
        }

        foreach ($groups as &$g) {
            usort($g['rows'], fn ($a, $b) => ($a['assigned'] ? 1 : 0) <=> ($b['assigned'] ? 1 : 0));
        }
        unset($g);

        // ลูกค้าที่จ่ายงานครบทุกบิลแล้ว (ไม่มีบิลค้างเลย) ให้ไปอยู่ท้ายสุดของกริด
        uasort($groups, function ($a, $b) {
            $aDone = collect($a['rows'])->every(fn ($r) => $r['assigned']) ? 1 : 0;
            $bDone = collect($b['rows'])->every(fn ($r) => $r['assigned']) ? 1 : 0;
            return $aDone <=> $bDone;
        });

        return $groups;
    }

    private function groupDocsByCustomer($docbills, $dispatched): array
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

            $groups[$custId]['rows'][] = [
                'doc'      => $doc,
                'assigned' => $dispatched->get($doc->doc_id),
            ];
        }

        foreach ($groups as &$g) {
            usort($g['rows'], fn ($a, $b) => ($a['assigned'] ? 1 : 0) <=> ($b['assigned'] ? 1 : 0));
        }
        unset($g);

        // ลูกค้าที่จ่ายงานครบทุกเอกสารแล้ว ให้ไปอยู่ท้ายสุดของกริด
        uasort($groups, function ($a, $b) {
            $aDone = collect($a['rows'])->every(fn ($r) => $r['assigned']) ? 1 : 0;
            $bDone = collect($b['rows'])->every(fn ($r) => $r['assigned']) ? 1 : 0;
            return $aDone <=> $bDone;
        });

        return $groups;
    }

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
        ]);

        // ถ้าเลือกวิธีการจัดส่งเป็น "เซลล์ไปส่งเอง" บังคับต้องระบุชื่อเซลล์ (เก็บลง driver_name/ผู้รับผิดชอบ)
        if ($validated['transport_name'] === 'เซลล์ไปส่งเอง' && blank($validated['driver_name'] ?? null)) {
            return redirect()->back()->with('error', 'เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเองด้วย');
        }

        // ถ้าไม่ใช่ "เซลล์ไปส่งเอง" ผู้รับผิดชอบต้องอยู่ในรายการ $responsiblePersons เท่านั้น (พิมพ์ชื่ออิสระไม่ได้)
        if ($validated['transport_name'] !== 'เซลล์ไปส่งเอง'
            && filled($validated['driver_name'] ?? null)
            && !in_array($validated['driver_name'], $this->responsiblePersons, true)
        ) {
            return redirect()->back()->with('error', 'กรุณาเลือกชื่อผู้รับผิดชอบจากรายการที่มีให้เท่านั้น');
        }

        $user = Auth::guard('web')->user();
        $assignedBy = $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? null);

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
                } else {
                    continue;
                }

                // กันซ้ำ: ถ้างานนี้ (bill_id นี้) ถูกมอบให้ผู้รับผิดชอบไปแล้วไม่ว่าวันไหนก็ตาม ไม่ต้องสร้างซ้ำ
                $alreadyDispatched = transaction_delivery::where('bill_id', $id)->exists();
                if ($alreadyDispatched) {
                    continue;
                }

                transaction_delivery::create([
                    'bill_id'        => $id,
                    // name_pick / time_pick = ชื่อคนที่กดจ่ายงาน และเวลาที่กด (ไม่ใช่ดึงจาก tblbill/docbills แล้ว)
                    'name_pick'      => $assignedBy,
                    'time_pick'      => now(),
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
            ->route('deliverytrack', ['date' => $request->input('date')])
            ->with('success', 'บันทึกข้อมูลการจัดส่งเรียบร้อยแล้ว');
    }

    private function buildDispatchBoxes($deliveries): array
    {
        $boxes = [];

        foreach ($deliveries as $delivery) {
            $id = $delivery->bill_id;

            $bill = Bill::where('so_detail_id', $id)->first();
            $doc  = $bill ? null : Docbills::where('doc_id', $id)->first();

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
                    // ที่อยู่ + lalong เป็นข้อมูลระดับลูกค้า เขียน/ทำ QR แค่ครั้งเดียวต่อกลุ่ม
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

            // ลำดับงาน = "จุดที่.ลำดับย่อยในจุดนั้น" เช่น 1.1 / 2.1 / 3.1 / 3.2
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

        // หา bill_id ของงานที่อยู่ในวันนั้นก่อน (time_pick ไม่ใช่วันที่ของงานแล้ว จึงกรองผ่าน bill_id แทน)
        $billIds = Bill::whereNotNull('emp_picker')
            ->where('emp_picker', '!=', '')
            ->whereDate('time', $date)
            ->pluck('so_detail_id');

        $docIds = Docbills::where('status', '0')
            ->whereDate('time', $date)
            ->pluck('doc_id');

        $relevantIds = $billIds->merge($docIds);

        $query = transaction_delivery::whereIn('bill_id', $relevantIds)
            ->where('transport_name', $transport);

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

        $user = Auth::guard('web')->user();
        $printedBy = $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? '-');
        $printedAt = Carbon::now();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('driver.delivery', [
                'printMode' => true,
                'date'      => $date,
                'box'       => $box,
                'printedBy' => $printedBy,
                'printedAt' => $printedAt,
            ])->setPaper('a4', 'portrait');

        $fileName = 'ใบงานขนส่ง_' . $transport . '_' . $date . '.pdf';

        return $pdf->stream($fileName);
    }

    public function saveNewBill(Request $request, $id)
    {
        //
    }
}