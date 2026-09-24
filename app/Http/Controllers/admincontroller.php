<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tblsos;
use App\Models\tblcustomer;
use App\Models\bill_detail;
use App\Models\so_item_id;
use App\Models\Bill;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $this->requireLogin($request);
        $date = $request->get('date');
        $search = $request->get('search'); // คำค้นหา ใช้ค้นทุกแถวในระบบ ไม่ใช่แค่หน้าที่แสดงอยู่
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // แสดงเฉพาะข้อมูลตั้งแต่วันที่นี้เป็นต้นไป
        $startDate = '2026-09-19';

        // เงื่อนไขช่วงวันที่: นับทั้งวันที่ส่งของ (date_of_dali) หรือ วันที่งานเข้า (time) อย่างใดอย่างหนึ่ง
        $sinceStart = function ($q) use ($startDate) {
            $q->whereDate('date_of_dali', '>=', $startDate)
              ->orWhereDate('time', '>=', $startDate);
        };

        $query = Bill::query()->where($sinceStart);

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่วันที่ส่งของ หรือ วันที่งานเข้า ตรงกับที่เลือก
        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('date_of_dali', $date)
                  ->orWhereDate('time', $date);
            });
        }

        // ถ้าผู้ใช้พิมพ์คำค้นหา ให้ค้นจากรหัสลูกค้า, รหัส SO และเลขบิล (billid) ทั่วทั้งฐานข้อมูล
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_id', 'like', "%{$search}%")
                  ->orWhere('so_id', 'like', "%{$search}%")
                  ->orWhere('billid', 'like', "%{$search}%");
            });
        }

        // ===== สรุปความคืบหน้าแต่ละขั้น =====
        // นับตามเงื่อนไขที่กรองอยู่ (วันที่/คำค้นหา) ถ้าไม่กรอง = ทั้งระบบ, ไม่นับรายการที่ยกเลิก (statuspdf = 6)
        $billTable = (new Bill)->getTable();
        $statsBase = clone $query;
        $active = function () use ($statsBase) {
            return (clone $statsBase)->where(function ($q) {
                $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
            });
        };

        $activeCount    = $active()->count();
        $cancelledCount = (clone $statsBase)->where('statuspdf', 6)->count();

        $billDone  = $active()->where('statuspdf', 1)->count();
        // ===== "จัดสินค้า" ดูจาก tblbill.emp_picker และ tblbill.picker_time =====
        //   มีค่า (ไม่ใช่ NULL) อย่างใดอย่างหนึ่ง = จัดสินค้าแล้ว, เป็น NULL ทั้งคู่ = รอดำเนินการ
        $pickDoneCond = function ($q) {
            $q->whereNotNull('emp_picker')->orWhereNotNull('picker_time');
        };
        $pickPendingCond = function ($q) {
            $q->whereNull('emp_picker')->whereNull('picker_time');
        };

        // เงื่อนไข "จัดเส้นทางแล้ว" = status = 1 (กด "ดาวน์โหลด เส้นทาง")
        //   หรือ มีแถวของบิลนี้ใน transaction_transport แล้ว (ถูกจัดลงรถ/เส้นทางแล้ว)
        //   2 ตารางใช้ collation ต่างกัน ต้องบังคับให้ตรงกันก่อนเทียบ
        $inTransport = function ($q) use ($billTable) {
            $q->select(DB::raw(1))
              ->from('transaction_transport')
              ->whereRaw('transaction_transport.bill_id COLLATE utf8mb4_unicode_ci = ' . $billTable . '.so_detail_id COLLATE utf8mb4_unicode_ci');
        };
        $routeDoneCond = function ($q) use ($inTransport) {
            $q->where('status', 1)->orWhereExists($inTransport);
        };
        $routePendingCond = function ($q) use ($inTransport) {
            $q->where(fn ($q2) => $q2->whereNull('status')->orWhere('status', '!=', 1))
              ->whereNotExists($inTransport);
        };

        $pickDone  = $active()->where($pickDoneCond)->count();
        $routeDone = $active()->where($routeDoneCond)->count();
        $deliDone  = $active()->where('statusdeli', 'จัดส่งสำเร็จ')->count();

        $stageStats = [
            ['label' => 'เปิดบิลส่งของ', 'icon' => 'fa-file-invoice', 'done' => $billDone,  'pending' => $activeCount - $billDone],
            ['label' => 'จัดสินค้า',     'icon' => 'fa-box-open',     'done' => $pickDone,  'pending' => $activeCount - $pickDone],
            ['label' => 'จัดเส้นทาง',    'icon' => 'fa-route',        'done' => $routeDone, 'pending' => $activeCount - $routeDone],
            ['label' => 'ส่งสินค้า',      'icon' => 'fa-truck',        'done' => $deliDone,  'pending' => $activeCount - $deliDone],
        ];
        foreach ($stageStats as &$s) {
            $s['percent'] = $activeCount > 0 ? round($s['done'] * 100 / $activeCount) : 0;
        }
        unset($s);

        // ===== เวลาเฉลี่ยแต่ละช่วง (จากเปิดบิล) เพื่อดูว่าจุดไหนช้า =====
        $timeRows = $active()->get(['so_detail_id', 'time', 'picker_time']);
        $timeLogs = collect();
        foreach (array_chunk($timeRows->pluck('so_detail_id')->filter()->map(fn ($v) => (string) $v)->unique()->values()->all(), 1000) as $chunk) {
            $timeLogs = $timeLogs->merge(
                DB::table('transaction_transport')->whereIn('bill_id', $chunk)->orderBy('id')->get(['bill_id', 'time_pick', 'check_time'])
            );
        }
        $timeLogs = $timeLogs->keyBy(fn ($r) => (string) $r->bill_id);

        $durSum = ['pick' => [0, 0], 'route' => [0, 0], 'deli' => [0, 0], 'total' => [0, 0]];
        foreach ($timeRows as $r) {
            $lg = $timeLogs->get((string) $r->so_detail_id);
            $d  = self::stageDurations($r->time, $r->picker_time, $lg->time_pick ?? null, $lg->check_time ?? null);
            foreach ($d as $k => $m) {
                if ($m !== null) { $durSum[$k][0] += $m; $durSum[$k][1]++; }
            }
        }
        $avgMin = [];
        foreach ($durSum as $k => [$sum, $n]) {
            $avgMin[$k] = $n > 0 ? (int) round($sum / $n) : null;
        }
        $slowKey = collect(['pick', 'route', 'deli'])->filter(fn ($k) => $avgMin[$k] !== null)
            ->sortByDesc(fn ($k) => $avgMin[$k])->first();

        $stageKeys = [null, 'pick', 'route', 'deli'];   // การ์ดที่ 1 (เปิดบิล) เป็นจุดเริ่ม ไม่มีเวลา
        foreach ($stageStats as $i => &$s) {
            $k = $stageKeys[$i];
            $s['avg']  = $k ? self::fmtDur($avgMin[$k]) : null;
            $s['slow'] = $k !== null && $k === $slowKey;
        }
        unset($s);
        $avgTotal = self::fmtDur($avgMin['total']);

        // ===== ฟิลเตอร์ตามสถานะแต่ละขั้น (ใช้กับตารางเท่านั้น การ์ดสรุปด้านบนยังนับตามวันที่/คำค้นหา) =====
        $notCancelled = function ($q) {
            $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
        };

        $billStatus  = $request->get('bill_status');
        $pickStatus  = $request->get('pick_status');
        $routeStatus = $request->get('route_status');
        $deliStatus  = $request->get('deli_status');

        // เปิดบิลส่งของ
        if ($billStatus === 'done') {
            $query->where('statuspdf', 1);
        } elseif ($billStatus === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('statuspdf')->orWhereNotIn('statuspdf', [1, 6]);
            });
        } elseif ($billStatus === 'cancel') {
            $query->where('statuspdf', 6);
        }

        // จัดสินค้า
        if ($pickStatus === 'done') {
            $query->where($notCancelled)->where($pickDoneCond);
        } elseif ($pickStatus === 'pending') {
            $query->where($notCancelled)->where($pickPendingCond);
        }

        // จัดเส้นทาง
        if ($routeStatus === 'done') {
            $query->where($notCancelled)->where($routeDoneCond);
        } elseif ($routeStatus === 'pending') {
            $query->where($notCancelled)->where($routePendingCond);
        }

        // ส่งสินค้า
        //   ผลส่งมี 4 แบบ: จัดส่งสำเร็จ / ค้างบิล / ส่งใหม่ (จ่ายงานใหม่) / สินค้าผิด
        $deliMap = ['success' => 'จัดส่งสำเร็จ', 'hold' => 'ค้างบิล', 'wrong' => 'สินค้าผิด'];
        if (isset($deliMap[$deliStatus])) {
            $query->where($notCancelled)->where('statusdeli', $deliMap[$deliStatus]);
        } elseif ($deliStatus === 'resend') {
            $query->where($notCancelled)->where('statusdeli', 'like', 'ส่งใหม่%');
        } elseif ($deliStatus === 'pending') {
            // "รอดำเนินการ" = ยังไม่มีผลส่งจริง (null, '', '0' หรือค่าอื่นที่ไม่ใช่ 4 สถานะผลส่ง)
            $query->where($notCancelled)->where(function ($q) use ($deliMap) {
                $q->whereNull('statusdeli')
                  ->orWhere(function ($q2) use ($deliMap) {
                      $q2->whereNotIn('statusdeli', array_values($deliMap))
                         ->where('statusdeli', 'not like', 'ส่งใหม่%');
                  });
            });
        }

        $bill = $query->orderBy('so_id', 'desc') // เรียงตาม so_id
                      ->paginate(200);           // แบ่งหน้า 200 รายการ

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($bill->isEmpty()) {
            $message = 'ไม่พบข้อมูลที่ตรงกับเงื่อนไขที่เลือก';
        }

        // คงค่า Query String (เช่น วันที่เลือก, คำค้นหา) ไว้ในลิงก์เปลี่ยนหน้า
        $bill->appends($request->all());

        // ดึงข้อมูลจาก transaction_transport (bill_id = so_detail_id ของบิล)
        $soDetailIds = $bill->getCollection()->pluck('so_detail_id')->filter()->unique()->values()->toArray();

        $pickLogs = DB::table('transaction_transport')
            ->whereIn('bill_id', $soDetailIds)
            ->get()
            ->keyBy('bill_id');

        $bill->getCollection()->transform(function ($item) use ($pickLogs) {
            $log = $pickLogs->get($item->so_detail_id);

            // "จัดสินค้า": tblbill.emp_picker / picker_time (NULL ทั้งคู่ = รอดำเนินการ)
            $picker = trim((string) ($item->emp_picker ?? ''));
            $pTime  = $item->picker_time ?? null;
            $item->pick_done = (!is_null($item->emp_picker ?? null) || !is_null($pTime));
            $item->pick_name = $picker !== '' ? $picker : null;
            $item->pick_time = null;
            if (!empty($pTime)) {
                try {
                    $item->pick_time = Carbon::parse($pTime)->format('Y-m-d H:i');
                } catch (\Throwable $e) {
                    $item->pick_time = (string) $pTime;
                }
            }

            // "จัดเส้นทาง": status = 1 หรือ มีแถวใน transaction_transport แล้ว (เวลาจาก time_pick)
            $item->route_done = ((string) ($item->status ?? '') === '1') || !is_null($log);
            $item->route_time = null;
            $item->route_name = $log->name_pick ?? null; // ชื่อผู้จัด (transaction_transport.name_pick)
            if (!empty($log->time_pick ?? null)) {
                try {
                    $item->route_time = Carbon::parse($log->time_pick)->format('Y-m-d H:i');
                } catch (\Throwable $e) {
                    $item->route_time = (string) $log->time_pick;
                }
            }

            // "ส่งสินค้า": เวลายืนยันผลส่ง + ชื่อคนขับ (transaction_transport.driver_name)
            $item->deli_name = $log->driver_name ?? null;
            $item->deli_time = $log->check_time ?? null;

            // ===== ระยะเวลาแต่ละช่วง (นับจากเปิดบิล) =====
            $d = self::stageDurations($item->time, $pTime, $log->time_pick ?? null, $log->check_time ?? null);
            $item->dur = [
                'pick'  => self::fmtDur($d['pick']),
                'route' => self::fmtDur($d['route']),
                'deli'  => self::fmtDur($d['deli']),
                'total' => self::fmtDur($d['total']),
            ];
            // ช่วงที่ใช้เวลานานสุดของบิลนี้
            $maxK = collect(['pick', 'route', 'deli'])->filter(fn ($k) => $d[$k] !== null)->sortByDesc(fn ($k) => $d[$k])->first();
            $item->dur_slow = ($maxK && $d[$maxK] > 0) ? $maxK : null;

            // ขั้นที่ยังค้างอยู่: รอมาแล้วนานเท่าไร (นับจากขั้นก่อนหน้าที่เสร็จ ถึงตอนนี้)
            $item->wait = null;
            $isCancelled = (string) ($item->statuspdf ?? '') === '6';
            if (!$isCancelled && ($item->statusdeli ?? '') !== 'จัดส่งสำเร็จ') {
                $prev = self::toTime($log->time_pick ?? null) ?? self::toTime($pTime) ?? self::toTime($item->time);
                if ($prev) {
                    $mins = (int) floor((Carbon::now('Asia/Bangkok')->getTimestamp() - $prev->getTimestamp()) / 60);
                    $item->wait = $mins >= 0 ? self::fmtDur($mins) : null;
                }
            }
            return $item;
        });

        // คำนวณจำนวนทั้งหมดในระบบ (ตั้งแต่วันเริ่ม นับทั้งวันที่ส่งของ หรือ วันที่งานเข้า)
        $totalCount = Bill::where($sinceStart)->count();

        // คำนวณจำนวนของวันที่เลือกในตัวกรอง (ถ้าไม่เลือก = วันนี้ เวลาไทย)
        //   นับทั้งงานที่เข้าวันนั้น และงานที่ส่งวันนั้น
        $today = $date
            ? Carbon::parse($date)->toDateString()
            : Carbon::today('Asia/Bangkok')->toDateString();
        $countDate = $today;
        $todayCount = Bill::where(function ($q) use ($today) {
            $q->whereDate('date_of_dali', $today)
              ->orWhereDate('time', $today);
        })->count();

        // ===== รายการ SO + PO สำหรับรวม "จำนวนเงิน" (ไม่นับยกเลิก) =====
        //   ราคา (NetAmnt) ดึงจาก API ฝั่งหน้าเว็บ ตรงนี้ส่งแค่รายการเลข SO/PO ไปให้
        $notCancelledBill = function ($q) {
            $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
        };
        $moneyPairs = function ($q) {
            return $q->whereNotNull('so_id')->where('so_id', '!=', '')
                ->get(['so_id', 'billid'])
                ->map(fn ($r) => ['so' => (string) $r->so_id, 'po' => (string) $r->billid])
                ->unique(fn ($r) => $r['so'] . '|' . $r['po'])
                ->values();
        };
        $moneyAll = $moneyPairs(Bill::where($sinceStart)->where($notCancelledBill));
        $moneyDay = $moneyPairs(Bill::where($notCancelledBill)->where(function ($q) use ($today) {
            $q->whereDate('date_of_dali', $today)->orWhereDate('time', $today);
        }));

        // ===== หน้า "สรุป": สรุปตามคนขับ (กรอง รายวัน / รายเดือน / รายปี + คนขับ) =====
        //   อิงวันที่ส่งจริง (check_time) / วันที่ส่งของ, ไม่นับบิลยกเลิก, นับเฉพาะบิลที่มีคนขับใน transaction_transport
        $sumPeriod = in_array($request->get('sum_period'), ['day', 'month', 'year'], true) ? $request->get('sum_period') : 'day';
        $sumDateIn = trim((string) $request->get('sum_date', ''));
        $sumDriver = trim((string) $request->get('sum_driver', ''));
        $nowTh     = Carbon::now('Asia/Bangkok');

        try {
            if ($sumPeriod === 'year') {
                $base    = $sumDateIn !== '' ? Carbon::createFromDate((int) substr($sumDateIn, 0, 4), 1, 1) : $nowTh->copy();
                $sumFrom = $base->copy()->startOfYear();
                $sumTo   = $base->copy()->endOfYear();
                $sumDate = $sumFrom->format('Y');
                $sumLabel = 'ปี ' . $sumFrom->format('Y');
            } elseif ($sumPeriod === 'month') {
                $base    = $sumDateIn !== '' ? Carbon::parse(substr($sumDateIn, 0, 7) . '-01') : $nowTh->copy();
                $sumFrom = $base->copy()->startOfMonth();
                $sumTo   = $base->copy()->endOfMonth();
                $sumDate = $sumFrom->format('Y-m');
                $sumLabel = 'เดือน ' . $sumFrom->format('m/Y');
            } else {
                $base    = $sumDateIn !== '' ? Carbon::parse(substr($sumDateIn, 0, 10)) : $nowTh->copy();
                $sumFrom = $base->copy()->startOfDay();
                $sumTo   = $base->copy()->endOfDay();
                $sumDate = $sumFrom->format('Y-m-d');
                $sumLabel = 'วันที่ ' . $sumFrom->format('d/m/Y');
            }
        } catch (\Throwable $e) {
            $sumFrom = $nowTh->copy()->startOfDay();
            $sumTo   = $nowTh->copy()->endOfDay();
            $sumDate = $sumFrom->format('Y-m-d');
            $sumLabel = 'วันที่ ' . $sumFrom->format('d/m/Y');
            $sumPeriod = 'day';
        }

        // รายชื่อคนขับทั้งหมด (ไว้ใส่ dropdown)
        $driverOptions = DB::table('transaction_transport')
            ->whereNotNull('driver_name')->where('driver_name', '!=', '')
            ->distinct()->orderBy('driver_name')->pluck('driver_name');

        // หางานส่งในช่วงที่เลือก จาก 2 ทาง แล้วรวมกัน:
        //   1) transaction_transport: วันที่ยืนยันส่ง (check_time) หรือถ้ายังไม่ยืนยัน ใช้ delivery_date / time_pick
        //   2) tblbill: วันที่ส่งของ (date_of_dali) อยู่ในช่วง
        // เหมือนหน้ารายการ: นับเฉพาะตั้งแต่วันเริ่ม ($startDate = 19/09/2026) เป็นต้นไป
        $fromD = max($sumFrom->toDateString(), $startDate);
        $toD   = $sumTo->toDateString();

        try {
            $logsByDate = DB::table('transaction_transport')
                ->whereNotNull('driver_name')->where('driver_name', '!=', '')
                ->whereNull('cancelled_at')
                ->where(function ($q) use ($fromD, $toD) {
                    $q->where(function ($q2) use ($fromD, $toD) {
                        $q2->whereNotNull('check_time')
                           ->whereDate('check_time', '>=', $fromD)->whereDate('check_time', '<=', $toD);
                    })->orWhere(function ($q2) use ($fromD, $toD) {
                        $q2->whereNull('check_time')->where(function ($q3) use ($fromD, $toD) {
                            $q3->where(fn ($q4) => $q4->whereDate('delivery_date', '>=', $fromD)->whereDate('delivery_date', '<=', $toD))
                               ->orWhere(fn ($q4) => $q4->whereDate('time_pick', '>=', $fromD)->whereDate('time_pick', '<=', $toD));
                        });
                    });
                })
                ->orderBy('id')->get();
        } catch (\Throwable $e) {
            // เผื่อบางคอลัมน์ (delivery_date / cancelled_at) ไม่มี: ใช้แค่ check_time / time_pick
            \Log::warning('driver summary by date failed: ' . $e->getMessage());
            $logsByDate = DB::table('transaction_transport')
                ->whereNotNull('driver_name')->where('driver_name', '!=', '')
                ->where(function ($q) use ($fromD, $toD) {
                    $q->where(fn ($q2) => $q2->whereDate('check_time', '>=', $fromD)->whereDate('check_time', '<=', $toD))
                      ->orWhere(fn ($q2) => $q2->whereNull('check_time')->whereDate('time_pick', '>=', $fromD)->whereDate('time_pick', '<=', $toD));
                })
                ->orderBy('id')->get();
        }

        $idsByBillDate = Bill::whereDate('date_of_dali', '>=', $fromD)
            ->whereDate('date_of_dali', '<=', $toD)
            ->pluck('so_detail_id')->filter()->map(fn ($v) => (string) $v)->all();

        $logsByBill = collect();
        foreach (array_chunk(array_values(array_unique($idsByBillDate)), 1000) as $chunk) {
            $q = DB::table('transaction_transport')->whereIn('bill_id', $chunk)
                ->whereNotNull('driver_name')->where('driver_name', '!=', '')
                ->orderBy('id');
            try {
                $rows = (clone $q)->whereNull('cancelled_at')->get();
            } catch (\Throwable $e) {
                $rows = $q->get();   // ไม่มีคอลัมน์ cancelled_at
            }
            $logsByBill = $logsByBill->merge($rows);
        }

        // ใช้แถวล่าสุดของแต่ละบิล
        $sumLogs = $logsByDate->merge($logsByBill)->sortBy('id')->keyBy(fn ($r) => (string) $r->bill_id);

        $sumBills = collect();
        foreach (array_chunk($sumLogs->keys()->all(), 1000) as $chunk) {
            $sumBills = $sumBills->merge(
                Bill::whereIn('so_detail_id', $chunk)
                    ->where(function ($q) {
                        $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
                    })
                    ->get(['so_detail_id', 'so_id', 'billid', 'customer_id', 'date_of_dali', 'statusdeli'])
            );
        }
        $sumBills = $sumBills->sortByDesc(function ($b) use ($sumLogs) {
            $log = $sumLogs->get((string) $b->so_detail_id);
            return (string) ($log->check_time ?? $b->date_of_dali);
        })->values();

        $driverSummary = [];
        foreach ($sumBills as $b) {
            $log = $sumLogs->get((string) $b->so_detail_id);
            if (!$log) continue;
            $driver = trim((string) $log->driver_name);
            if ($sumDriver !== '' && $driver !== $sumDriver) continue;

            $st = in_array($b->statusdeli, ['จัดส่งสำเร็จ', 'ค้างบิล', 'สินค้าผิด'], true)
                ? $b->statusdeli
                : (str_starts_with((string) $b->statusdeli, 'ส่งใหม่') ? 'ส่งใหม่' : 'รอผลส่ง');
            $dr = $driverSummary[$driver] ?? [
                'name' => $driver, 'jobs' => 0, 'success' => 0, 'hold' => 0, 'resend' => 0, 'wrong' => 0, 'pending' => 0,
                'pairs' => [], 'pairs_ok' => [], 'bills' => [], 'days' => [],
            ];
            $dr['jobs']++;
            $dr[['จัดส่งสำเร็จ' => 'success', 'ค้างบิล' => 'hold', 'ส่งใหม่' => 'resend', 'สินค้าผิด' => 'wrong', 'รอผลส่ง' => 'pending'][$st]]++;

            if (!empty($b->so_id)) {
                $pair = ['so' => (string) $b->so_id, 'po' => (string) $b->billid];
                $dr['pairs'][] = $pair;
                if ($st === 'จัดส่งสำเร็จ') $dr['pairs_ok'][] = $pair;
            }
            // วันที่ทำงาน (ไว้คิดค่าเฉลี่ยต่อวัน)
            $workDay = substr((string) (!empty($log->check_time) ? $log->check_time : $b->date_of_dali), 0, 10);
            if ($workDay !== '') $dr['days'][$workDay] = true;

            $dr['bills'][] = [
                'so'       => $b->so_id,
                'po'       => $b->billid,
                'customer' => $b->customer_id,
                'date'     => substr((string) (!empty($log->check_time) ? $log->check_time : $b->date_of_dali), 0, 10),
                'status'   => $st,
                'time'     => !empty($log->check_time) ? substr((string) $log->check_time, 0, 16) : null,
            ];
            $driverSummary[$driver] = $dr;
        }
        // ค่าเฉลี่ยงานต่อวัน (เฉพาะวันที่มีงาน) + % ส่งสำเร็จ ของแต่ละคนขับ
        foreach ($driverSummary as &$dr) {
            $nDays          = max(1, count($dr['days']));
            $dr['work_days'] = count($dr['days']);
            $dr['per_day']  = round($dr['jobs'] / $nDays, 1);
            $dr['rate']     = $dr['jobs'] > 0 ? (int) round($dr['success'] * 100 / $dr['jobs']) : 0;
        }
        unset($dr);
        usort($driverSummary, fn ($a, $b) => $b['jobs'] <=> $a['jobs']);   // งานเยอะสุดขึ้นก่อน

        $sumTotals = [
            'jobs'    => array_sum(array_column($driverSummary, 'jobs')),
            'success' => array_sum(array_column($driverSummary, 'success')),
            'pairs'   => array_merge([], ...array_column($driverSummary, 'pairs')),
            'pairs_ok'=> array_merge([], ...array_column($driverSummary, 'pairs_ok')),
        ];

        return view('admin.dashboardadmin', compact('bill', 'message', 'totalCount', 'todayCount', 'stageStats', 'activeCount', 'cancelledCount', 'startDate', 'countDate', 'moneyAll', 'moneyDay', 'avgTotal', 'driverSummary', 'driverOptions', 'sumPeriod', 'sumDate', 'sumDriver', 'sumLabel', 'sumTotals'));
    }

    /** แปลงนาที -> ข้อความอ่านง่าย เช่น "1 วัน 2 ชม.", "3 ชม. 15 นาที", "20 นาที" */
    private static function fmtDur(?int $min): ?string
    {
        if ($min === null) return null;
        if ($min < 1) return 'ไม่ถึง 1 นาที';
        $d = intdiv($min, 1440);
        $h = intdiv($min % 1440, 60);
        $m = $min % 60;
        if ($d > 0) return $d . ' วัน' . ($h ? ' ' . $h . ' ชม.' : '');
        if ($h > 0) return $h . ' ชม.' . ($m ? ' ' . $m . ' นาที' : '');
        return $m . ' นาที';
    }

    /** แปลงค่าเวลาเป็น Carbon (อ่านไม่ได้/ว่าง = null) */
    private static function toTime($v): ?Carbon
    {
        if (empty($v) || str_starts_with((string) $v, '0000-00-00')) return null;
        try {
            return Carbon::parse($v, 'Asia/Bangkok');   // เวลาใน DB เป็นเวลาไทย
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * ระยะเวลาแต่ละช่วง (นาที): เปิดบิล -> จัดสินค้า -> จัดเส้นทาง -> ส่งสินค้า
     * คืน ['pick' => ?, 'route' => ?, 'deli' => ?, 'total' => ?]  (null = ยังไม่ถึง/ข้อมูลไม่พอ)
     */
    private static function stageDurations($billTime, $pickTime, $routeTime, $deliTime): array
    {
        $t = [self::toTime($billTime), self::toTime($pickTime), self::toTime($routeTime), self::toTime($deliTime)];
        $diff = function ($a, $b) {
            if (!$a || !$b) return null;
            $m = (int) floor(($b->getTimestamp() - $a->getTimestamp()) / 60);
            return $m >= 0 ? $m : null;   // เวลาย้อนกลับ = ข้อมูลผิด ไม่นับ
        };
        return [
            'pick'  => $diff($t[0], $t[1]),
            'route' => $diff($t[1] ?? $t[0], $t[2]),   // ถ้าไม่มีเวลาจัดสินค้า นับจากเปิดบิล
            'deli'  => $diff($t[2], $t[3]),
            'total' => $diff($t[0], $t[3]),
        ];
    }

    public function dashboardpdf(Request $request)
    {
        // หน้านี้ไม่ต้อง login
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.dashboardadminpdf', compact('bill', 'message'));
    }

    public function adminroute(Request $request)
    {
        $this->requireLogin($request);
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.adminroute', compact('bill', 'message'));
    }

    public function history(Request $request)
    {
        // หน้านี้ไม่ต้อง login
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('time', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.history', compact('bill', 'message'));
    }

public function updateStatus(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['status' => 1]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateStatuspdf(Request $request)
{
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update([
                'statuspdf'  => 1,
                'print_time' => \Carbon\Carbon::now('Asia/Bangkok'),
            ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateStatuspdfback(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update([
                'statuspdf' => 1,
                'status' => 0
            ]);


        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
// app/Http/Controllers/BillController.php
public function updateBillIssue(Request $request)
{
    $request->validate([
        'so_detail_id' => 'required',
        'bill_issue_no' => 'required|string|max:255',
    ]);

    $bill = Bill::where('so_detail_id', $request->so_detail_id)->first();

    if (!$bill) {
        return response()->json(['message' => 'ไม่พบข้อมูล so_detail_id'], 404);
    }

    $bill->bill_issue_no = $request->bill_issue_no;
    $bill->save();

    return response()->json(['message' => 'อัปเดตสำเร็จ']);
}

public function updateStatuspdf2(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
                ->update([
                        'statuspdf' => 2,
                        'status' => 1
                    ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateDeliveryDate(Request $request)
{
    try {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validator = Validator::make($request->all(), [
            'so_detail_id' => 'required',
            'new_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
            ], 422);
        }

        // ตรวจสอบก่อนว่ามีข้อมูลในฐานข้อมูลหรือไม่
        $existing = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->first();

        if (!$existing) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลที่ต้องการอัปเดต'
            ], 404);
        }

        // อัปเดตข้อมูล
        $updated = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->update([
                'date_of_dali' => $request->new_date,
            ]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตวันที่ส่งของเรียบร้อยแล้ว'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตข้อมูลได้'
            ], 500);
        }
    } catch (\Exception $e) {
        // บันทึกข้อผิดพลาดลง log
        \Log::error('Error updating delivery date: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], 500);
    }
}
public function upload(Request $request)
{
    $request->validate([
        'pdffile' => 'required|mimes:pdf|max:10240'
    ]);

    $file = $request->file('pdffile');
    $originalName = $file->getClientOriginalName();
    $path1 = storage_path('app/public/doc_document');
    $path2 = storage_path('app/public/bill_document');
    if (!file_exists($path1)) mkdir($path1, 0777, true);
    if (!file_exists($path2)) mkdir($path2, 0777, true);
    $file->move($path1, $originalName);
    copy($path1 . '/' . $originalName, $path2 . '/' . $originalName);
    return back()->with('success', 'อัปโหลดไฟล์ ' . $originalName . ' เรียบร้อยแล้ว!');
}

public function uploadBillIssue(Request $request)
{
    $request->validate([
        'bill_issue_no' => 'required|string',
        'pdffilebillissue' => 'required|mimes:pdf|max:10240'
    ]);

    $billIssueNo = $request->bill_issue_no;
    $file = $request->file('pdffilebillissue');

    $filename = $billIssueNo . '.pdf';

    // จัดเก็บไฟล์ใน storage/app/public/billissue_document
    $file->storeAs('public/billissue_document', $filename);

    return back()->with('message', '✅ อัปโหลดไฟล์สำเร็จ');
}
public function updateStatuspdfcan(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['statuspdf' => '6']);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
}