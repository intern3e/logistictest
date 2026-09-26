<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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
        $search = $request->get('search');
        $message = null;

        $startDate = '2026-09-19';

        $sinceStart = function ($q) use ($startDate) {
            $q->whereDate('date_of_dali', '>=', $startDate)
              ->orWhereDate('time', '>=', $startDate);
        };

        $query = Bill::query()->where($sinceStart);

        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('date_of_dali', $date)
                  ->orWhereDate('time', $date);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_id', 'like', "%{$search}%")
                  ->orWhere('so_id', 'like', "%{$search}%")
                  ->orWhere('billid', 'like', "%{$search}%");
            });
        }

        $billTable = (new Bill)->getTable();
        $statsBase = clone $query;
        $active = function () use ($statsBase) {
            return (clone $statsBase)->where(function ($q) {
                $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
            });
        };

        $activeCount    = $active()->count();
        $cancelledCount = (clone $statsBase)->where('statuspdf', 6)->count();

        $billDone  = $active()->whereIn('statuspdf', [1, 2])->count();

        $pickDoneCond = function ($q) {
            $q->whereNotNull('emp_picker')->orWhereNotNull('picker_time');
        };
        $pickPendingCond = function ($q) {
            $q->whereNull('emp_picker')->whereNull('picker_time');
        };

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

        $stageKeys = [null, 'pick', 'route', 'deli'];
        foreach ($stageStats as $i => &$s) {
            $k = $stageKeys[$i];
            $s['avg']  = $k ? self::fmtDur($avgMin[$k]) : null;
            $s['slow'] = $k !== null && $k === $slowKey;
        }
        unset($s);
        $avgTotal = self::fmtDur($avgMin['total']);

        $notCancelled = function ($q) {
            $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
        };

        $billStatus  = $request->get('bill_status');
        $pickStatus  = $request->get('pick_status');
        $routeStatus = $request->get('route_status');
        $deliStatus  = $request->get('deli_status');

        if ($billStatus === 'done') {
            $query->whereIn('statuspdf', [1, 2]);
        } elseif ($billStatus === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('statuspdf')->orWhereNotIn('statuspdf', [1, 2, 6]);
            });
        } elseif ($billStatus === 'cancel') {
            $query->where('statuspdf', 6);
        }

        if ($pickStatus === 'done') {
            $query->where($notCancelled)->where($pickDoneCond);
        } elseif ($pickStatus === 'pending') {
            $query->where($notCancelled)->where($pickPendingCond);
        }

        if ($routeStatus === 'done') {
            $query->where($notCancelled)->where($routeDoneCond);
        } elseif ($routeStatus === 'pending') {
            $query->where($notCancelled)->where($routePendingCond);
        }

        $deliMap = ['success' => 'จัดส่งสำเร็จ', 'hold' => 'ค้างบิล', 'wrong' => 'สินค้าผิด'];
        if (isset($deliMap[$deliStatus])) {
            $query->where($notCancelled)->where('statusdeli', $deliMap[$deliStatus]);
        } elseif ($deliStatus === 'resend') {
            $query->where($notCancelled)->where('statusdeli', 'like', 'ส่งใหม่%');
        } elseif ($deliStatus === 'pending') {
            $query->where($notCancelled)->where(function ($q) use ($deliMap) {
                $q->whereNull('statusdeli')
                  ->orWhere(function ($q2) use ($deliMap) {
                      $q2->whereNotIn('statusdeli', array_values($deliMap))
                         ->where('statusdeli', 'not like', 'ส่งใหม่%');
                  });
            });
        }

        $bill = $query->orderBy('so_id', 'desc')
                      ->paginate(200);

        if ($bill->isEmpty()) {
            $message = 'ไม่พบข้อมูลที่ตรงกับเงื่อนไขที่เลือก';
        }

        $bill->appends($request->all());

        $soDetailIds = $bill->getCollection()->pluck('so_detail_id')->filter()->unique()->values()->toArray();

        $pickLogs = DB::table('transaction_transport')
            ->whereIn('bill_id', $soDetailIds)
            ->get()
            ->keyBy('bill_id');

        $bill->getCollection()->transform(function ($item) use ($pickLogs) {
            $log = $pickLogs->get($item->so_detail_id);

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

            $item->route_done = ((string) ($item->status ?? '') === '1') || !is_null($log);
            $item->route_time = null;
            $item->route_name = $log->name_pick ?? null;
            if (!empty($log->time_pick ?? null)) {
                try {
                    $item->route_time = Carbon::parse($log->time_pick)->format('Y-m-d H:i');
                } catch (\Throwable $e) {
                    $item->route_time = (string) $log->time_pick;
                }
            }

            $item->deli_name = $log->driver_name ?? null;
            $item->deli_time = $log->check_time ?? null;
            $item->deli_receiver = $log->check_name ?? null;

            $sendDate = self::toTime($log->delivery_date ?? null) ?? self::toTime($item->date_of_dali ?? null);
            $item->route_send_date = $sendDate ? $sendDate->format('d/m/Y') : null;

            $d = self::stageDurations($item->time, $pTime, $log->time_pick ?? null, $log->check_time ?? null);
            $item->dur = [
                'pick'  => self::fmtDur($d['pick']),
                'route' => self::fmtDur($d['route']),
                'deli'  => self::fmtDur($d['deli']),
                'total' => self::fmtDur($d['total']),
            ];
            $maxK = collect(['pick', 'route', 'deli'])->filter(fn ($k) => $d[$k] !== null)->sortByDesc(fn ($k) => $d[$k])->first();
            $item->dur_slow = ($maxK && $d[$maxK] > 0) ? $maxK : null;

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

        $totalCount = Bill::where($sinceStart)->count();

        $today = $date
            ? Carbon::parse($date)->toDateString()
            : Carbon::today('Asia/Bangkok')->toDateString();
        $countDate = $today;
        $todayCount = Bill::where(function ($q) use ($today) {
            $q->whereDate('date_of_dali', $today)
              ->orWhereDate('time', $today);
        })->count();

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

        $driverOptions = DB::table('transaction_transport')
            ->whereNotNull('driver_name')->where('driver_name', '!=', '')
            ->distinct()->orderBy('driver_name')->pluck('driver_name');

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
                $rows = $q->get();
            }
            $logsByBill = $logsByBill->merge($rows);
        }

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
        foreach ($driverSummary as &$dr) {
            $nDays          = max(1, count($dr['days']));
            $dr['work_days'] = count($dr['days']);
            $dr['per_day']  = round($dr['jobs'] / $nDays, 1);
            $dr['rate']     = $dr['jobs'] > 0 ? (int) round($dr['success'] * 100 / $dr['jobs']) : 0;
        }
        unset($dr);
        usort($driverSummary, fn ($a, $b) => $b['jobs'] <=> $a['jobs']);

        $sumTotals = [
            'jobs'    => array_sum(array_column($driverSummary, 'jobs')),
            'success' => array_sum(array_column($driverSummary, 'success')),
            'pairs'   => array_merge([], ...array_column($driverSummary, 'pairs')),
            'pairs_ok'=> array_merge([], ...array_column($driverSummary, 'pairs_ok')),
        ];

        $sumTab  = $request->get('sum_tab') === 'sale' ? 'sale' : 'driver';
        $sumSale = trim((string) $request->get('sum_sale', ''));

        $saleOptions = Bill::where($sinceStart)
            ->whereNotNull('sale_name')->where('sale_name', '!=', '')
            ->distinct()->orderBy('sale_name')->pluck('sale_name');

        $saleBills = Bill::whereDate('time', '>=', $fromD)->whereDate('time', '<=', $toD)
            ->when($sumSale !== '', fn ($q) => $q->where('sale_name', $sumSale))
            ->get(['so_detail_id', 'so_id', 'billid', 'customer_id', 'sale_name', 'time', 'statuspdf', 'statusdeli']);

        $saleSummary = [];
        foreach ($saleBills as $b) {
            $name = trim((string) $b->sale_name) !== '' ? trim((string) $b->sale_name) : '(ไม่ระบุ Sale)';
            $sa = $saleSummary[$name] ?? [
                'name' => $name, 'jobs' => 0, 'cancel' => 0, 'success' => 0, 'hold' => 0, 'resend' => 0, 'wrong' => 0, 'pending' => 0,
                'pairs' => [], 'pairs_ok' => [], 'bills' => [],
            ];
            if ((string) $b->statuspdf === '6') {
                $sa['cancel']++;
                $saleSummary[$name] = $sa;
                continue;
            }
            $st = in_array($b->statusdeli, ['จัดส่งสำเร็จ', 'ค้างบิล', 'สินค้าผิด'], true)
                ? $b->statusdeli
                : (str_starts_with((string) $b->statusdeli, 'ส่งใหม่') ? 'ส่งใหม่' : 'รอผลส่ง');
            $sa['jobs']++;
            $sa[['จัดส่งสำเร็จ' => 'success', 'ค้างบิล' => 'hold', 'ส่งใหม่' => 'resend', 'สินค้าผิด' => 'wrong', 'รอผลส่ง' => 'pending'][$st]]++;
            if (!empty($b->so_id)) {
                $pair = ['so' => (string) $b->so_id, 'po' => (string) $b->billid];
                $sa['pairs'][] = $pair;
                if ($st === 'จัดส่งสำเร็จ') $sa['pairs_ok'][] = $pair;
            }
            $sa['bills'][] = [
                'so'       => $b->so_id,
                'po'       => $b->billid,
                'customer' => $b->customer_id,
                'date'     => substr((string) $b->time, 0, 16),
                'status'   => $st,
            ];
            $saleSummary[$name] = $sa;
        }
        foreach ($saleSummary as &$sa) {
            $sa['rate'] = $sa['jobs'] > 0 ? (int) round($sa['success'] * 100 / $sa['jobs']) : 0;
        }
        unset($sa);
        usort($saleSummary, fn ($a, $b) => $b['jobs'] <=> $a['jobs']);

        $saleTotals = [
            'jobs'     => array_sum(array_column($saleSummary, 'jobs')),
            'success'  => array_sum(array_column($saleSummary, 'success')),
            'pairs'    => array_merge([], ...array_column($saleSummary, 'pairs')),
            'pairs_ok' => array_merge([], ...array_column($saleSummary, 'pairs_ok')),
        ];

        $needSo = collect($moneyAll)->pluck('so')
            ->merge(collect($moneyDay)->pluck('so'))
            ->merge($bill->getCollection()->pluck('so_id'))
            ->merge(collect($sumTotals['pairs'] ?? [])->pluck('so'))
            ->merge(collect($saleTotals['pairs'] ?? [])->pluck('so'))
            ->filter()->map(fn ($v) => (string) $v)->unique()->values()->all();
        $prices = $this->fetchSoPrices($needSo);

        $bill->getCollection()->transform(function ($item) use ($prices) {
            $item->price = !empty($item->so_id) ? self::priceFor($prices, $item->so_id, $item->billid) : null;
            return $item;
        });

        $moneyAllSum = self::sumPrices($prices, $moneyAll->all());
        $moneyDaySum = self::sumPrices($prices, $moneyDay->all());

        foreach ($driverSummary as &$dr) {
            $dr['money']    = self::sumPrices($prices, $dr['pairs']);
            $dr['money_ok'] = self::sumPrices($prices, $dr['pairs_ok']);
        }
        unset($dr);
        $sumTotals['money']    = self::sumPrices($prices, $sumTotals['pairs'] ?? []);
        $sumTotals['money_ok'] = self::sumPrices($prices, $sumTotals['pairs_ok'] ?? []);

        foreach ($saleSummary as &$sa) {
            $sa['money']    = self::sumPrices($prices, $sa['pairs']);
            $sa['money_ok'] = self::sumPrices($prices, $sa['pairs_ok']);
        }
        unset($sa);
        $saleTotals['money']    = self::sumPrices($prices, $saleTotals['pairs']);
        $saleTotals['money_ok'] = self::sumPrices($prices, $saleTotals['pairs_ok']);

        // ===== PO รับของ =====
        [$poList, $poCounts, $poError] = $this->buildPoReceive($request);
        $poStatus = $request->input('po_status', '');
        $poSearch = trim((string) $request->input('po_search', ''));
        $poDate   = $request->input('po_date', '');

        return view('admin.dashboardadmin', compact('bill', 'message', 'totalCount', 'todayCount', 'stageStats', 'activeCount', 'cancelledCount', 'startDate', 'countDate', 'moneyAllSum', 'moneyDaySum', 'avgTotal', 'driverSummary', 'driverOptions', 'sumPeriod', 'sumDate', 'sumDriver', 'sumLabel', 'sumTotals', 'sumTab', 'sumSale', 'saleOptions', 'saleSummary', 'saleTotals', 'poList', 'poCounts', 'poError', 'poStatus', 'poSearch', 'poDate'));
    }

    // ===================== PO รับของ (ไปรับของเอง) =====================
    const PO_ERP_CONN      = 'mysql_3e';
    const PO_ACCOUNT_CONN  = 'mssql_account03';
    const PO_PICKUP_METHOD = ['รับเองรถใหญ่', 'รับเองมอเตอร์ไซด์'];
    
    // ✅ เพิ่มค่าคงที่วันที่เริ่มต้นดึงข้อมูล PO (เหมือนตารางหลัก)
    const PO_START_DATE    = '2026-09-19';

    private function resolvePoReceiveStatus($po, $receive, bool $isCancelled): array
    {
        if ($isCancelled) return ['key' => 'cancel', 'label' => 'ยกเลิก', 'badge' => 'danger'];
        $new = $receive->status ?? null;
        if ($new !== null && $new !== 'รับเข้าผิด') {
            $map = [
                'ครบ'     => ['key' => 'done',    'label' => 'รับครบแล้ว',   'badge' => 'success'],
                'บางส่วน' => ['key' => 'partial', 'label' => 'รับบางส่วน', 'badge' => 'hold'],
                'ยกเลิก'  => ['key' => 'cancel',  'label' => 'ยกเลิก',     'badge' => 'danger'],
            ];
            return $map[$new] ?? ['key' => 'wait', 'label' => $new, 'badge' => 'pending'];
        }
        $old = strtoupper(trim((string) ($po->POstatus ?? '')));
        $map = [
            'ENTRY'     => ['key' => 'wait',   'label' => 'รอรับของ',   'badge' => 'pending'],
            'COMPLETED' => ['key' => 'done',   'label' => 'รับครบแล้ว',  'badge' => 'success'],
            'PARTIAL'   => ['key' => 'wait',   'label' => 'เลยกำหนด',  'badge' => 'hold'],
            'CANCELLED' => ['key' => 'cancel', 'label' => 'ยกเลิก',    'badge' => 'danger'],
        ];
        return $map[$old] ?? ['key' => 'wait', 'label' => 'รอรับของ', 'badge' => 'pending'];
    }

    private function buildPoReceive(Request $request): array
    {
        set_time_limit(120);
        ini_set('memory_limit', '256M');

        $status = (string) $request->input('po_status', '');
        $search = trim((string) $request->input('po_search', ''));
        $date   = (string) $request->input('po_date', '');
        $counts = ['all' => 0, 'wait' => 0, 'partial' => 0, 'done' => 0, 'cancel' => 0, 'unassigned' => 0];

        try {
            $q = DB::connection(self::PO_ERP_CONN)->table('polist')
                ->whereIn('DeliveryMethod', self::PO_PICKUP_METHOD);

            // ✅ แก้ไข: เปลี่ยนจาก now()->subYear() เป็น PO_START_DATE (19/09/2026)
            if ($date === '' && $search === '') {
                $q->where('DeliveryDate', '>=', self::PO_START_DATE);
            }

            if ($date !== '') {
                $q->whereDate('DeliveryDate', $date);
            }

            if ($search !== '') {
                $like = '%' . $search . '%';
                $q->where(fn ($w) => $w->where('PONum', 'like', $like)
                                      ->orWhere('SONum', 'like', $like)
                                      ->orWhere('VendorName', 'like', $like)
                                      ->orWhere('VendorID', 'like', $like));
            }

            $pos = $q->orderByDesc('DeliveryDate')->orderByDesc('PONum')->get();

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('dashboard PO รับของ: เชื่อมต่อ polist ไม่ได้ - ' . $e->getMessage());
            return [collect(), $counts, 'เชื่อมต่อฐานข้อมูล PO (ERP) ไม่ได้'];
        }

        if ($pos->isEmpty()) return [collect(), $counts, null];

        // ดึงชื่อ Sale (createdBy) จากตาราง so โดยใช้ SONum
        $soNums = $pos->pluck('SONum')->filter()->unique()->values()->all();
        $saleInfo = collect();
        if (!empty($soNums)) {
            try {
                $saleInfo = DB::connection(self::PO_ERP_CONN)->table('so')
                    ->whereIn('SONum', $soNums)
                    ->pluck('createdBy', 'SONum');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('dashboard PO รับของ: ดึงข้อมูล sale จาก so ไม่ได้ - ' . $e->getMessage());
            }
        }

        $poNums   = $pos->pluck('PONum')->map(fn ($p) => (string) $p)->unique()->values();
        $poPrefix = $poNums->map(fn ($p) => 'PO' . $p)->all();

        $receives = collect(); $cancels = collect();
        foreach (array_chunk($poPrefix, 1000) as $chunk) {
            $receives = $receives->merge(\App\Models\PoReceive::whereIn('po_id', $chunk)->get());
            $cancels  = $cancels->merge(\App\Models\PooutsideCancelled::whereIn('po_id', $chunk)->get());
        }
        $strip = fn ($v) => preg_replace('/^PO/', '', (string) $v);
        $recByKey = $receives->keyBy(fn ($r) => $r->so_id . '|' . $strip($r->po_id));
        $recByPo  = $receives->keyBy(fn ($r) => $strip($r->po_id));
        $canByKey = $cancels->keyBy(fn ($r) => $r->so_id . '|' . $strip($r->po_id));

        $logs = collect();
        foreach ($poNums->chunk(1000) as $chunk) {
            try {
                $rows = DB::table('transaction_transport')->whereIn('bill_id', $chunk->all())->whereNull('cancelled_at')->orderBy('id')->get();
            } catch (\Throwable $e) {
                $rows = DB::table('transaction_transport')->whereIn('bill_id', $chunk->all())->orderBy('id')->get();
            }
            $logs = $logs->merge($rows);
        }
        $logs = $logs->keyBy('bill_id');

        $today = Carbon::now('Asia/Bangkok')->toDateString();
        $rows = collect();
        foreach ($pos as $po) {
            $po->sale_name = $saleInfo->get($po->SONum) ?? null;

            $key = $po->SONum . '|' . $po->PONum;
            $rec = $recByKey->get($key) ?? $recByPo->get((string) $po->PONum);
            $st  = $this->resolvePoReceiveStatus($po, $rec, $canByKey->has($key));
            $log = $logs->get((string) $po->PONum);

            $po->st            = $st;
            $po->pickup_date   = $po->DeliveryDate ? Carbon::parse($po->DeliveryDate)->format('d/m/Y') : null;
            $po->overdue       = in_array($st['key'], ['wait', 'partial'], true) && $po->DeliveryDate && substr((string) $po->DeliveryDate, 0, 10) < $today;

            $po->days_label = null;
            $po->days_class = '';
            if ($po->DeliveryDate) {
                try {
                    $pickDate  = Carbon::parse($po->DeliveryDate)->startOfDay();
                    $todayDate = Carbon::parse($today)->startOfDay();
                    $diff = (int) $todayDate->diffInDays($pickDate, false);
                    if ($diff < 0) {
                        $po->days_label = 'เลยกำหนด ' . abs($diff) . ' วัน';
                        $po->days_class = 'po-late';
                    } elseif ($diff === 0) {
                        $po->days_label = 'วันนี้';
                        $po->days_class = 'po-today';
                    } elseif ($diff <= 3) {
                        $po->days_label = 'อีก ' . $diff . ' วัน';
                        $po->days_class = 'po-soon';
                    } else {
                        $po->days_label = 'อีก ' . $diff . ' วัน';
                        $po->days_class = 'po-future';
                    }
                } catch (\Throwable $e) {
                    $po->days_label = null;
                }
            }

            $po->assigned      = (bool) $log;
            $po->assign_time   = $log && $log->time_pick ? Carbon::parse($log->time_pick)->format('d/m/Y H:i') : null;
            $po->assign_by     = $log->name_pick ?? null;
            $po->picker        = $log ? ($log->driver_name ?: $log->transport_name) : null;
            $po->go_date       = $log && $log->delivery_date ? Carbon::parse($log->delivery_date)->format('d/m/Y') : null;
            $po->receive_time  = $rec && ($rec->updated_at ?? $rec->created_at ?? null) ? Carbon::parse($rec->updated_at ?? $rec->created_at)->format('d/m/Y H:i') : null;
            $po->items         = collect();

            $counts['all']++;
            $counts[$st['key']]++;
            if (!$po->assigned && in_array($st['key'], ['wait', 'partial'], true)) $counts['unassigned']++;

            if ($status === '' || $status === $st['key'] || ($status === 'unassigned' && !$po->assigned && in_array($st['key'], ['wait', 'partial'], true))) {
                $rows->push($po);
            }
        }

        if ($rows->isNotEmpty()) {
            try {
                $docuNos = $rows->pluck('PONum')->map(fn ($p) => 'PO' . $p)->unique()->values()->all();
                $headers = collect();

                foreach (array_chunk($docuNos, 1000) as $chunk) {
                    $headers = $headers->merge(
                        DB::connection(self::PO_ACCOUNT_CONN)
                            ->table('POHD')
                            ->whereIn('DocuNo', $chunk)
                            ->get(['POID', 'DocuNo', 'DocuDate'])
                    );
                }
                $headers = $headers->keyBy('DocuNo');

                $items = collect();
                foreach (array_chunk($headers->pluck('POID')->filter()->unique()->values()->all(), 1000) as $chunk) {
                    $items = $items->merge(
                        DB::connection(self::PO_ACCOUNT_CONN)
                            ->table('PODT')
                            ->whereIn('POID', $chunk)
                            ->where('CancelFlag', '!=', 'Y')
                            ->get(['POID', 'GoodName', 'GoodQty2'])
                    );
                }
                $items = $items->groupBy('POID');

                foreach ($rows as $po) {
                    $h = $headers->get('PO' . $po->PONum);

                    $po->docu_date = null;
                    $po->docu_date_raw = null;
                    if ($h && !empty($h->DocuDate)) {
                        $po->docu_date_raw = $h->DocuDate;
                        try {
                            $dt = Carbon::parse($h->DocuDate);
                            if ($dt->format('H:i:s') === '00:00:00') {
                                $po->docu_date = $dt->format('d/m/Y');
                            } else {
                                $po->docu_date = $dt->format('d/m/Y H:i');
                            }
                        } catch (\Throwable $e) {
                            $po->docu_date = (string) $h->DocuDate;
                        }
                    }

                    $po->transport_log = $logs->get((string) $po->PONum);
                    $po->is_transport_sent = !empty($po->transport_log);
                    $po->transport_sent_complete = $po->transport_log && !empty($po->transport_log->check_time);
                    $po->transport_sent_time = $po->transport_log && !empty($po->transport_log->check_time)
                        ? Carbon::parse($po->transport_log->check_time)->format('d/m/Y H:i')
                        : null;
                    $po->transport_driver = $po->transport_log->driver_name ?? null;
                    $po->transport_assign_time = $po->transport_log && !empty($po->transport_log->time_pick)
                        ? Carbon::parse($po->transport_log->time_pick)->format('d/m/Y H:i')
                        : null;

                    $po->items = $h ? ($items->get($h->POID) ?? collect())->map(fn ($i) => [
                        'name' => $i->GoodName,
                        'qty'  => rtrim(rtrim((string) $i->GoodQty2, '0'), '.'),
                    ])->values() : collect();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('dashboard PO รับของ: เชื่อมต่อ account03 ไม่ได้ - ' . $e->getMessage());
            }
        }

        return [$rows->values(), $counts, null];
    }

    // ===================== ราคา =====================
    const SO_DETAIL_API = 'http://server_update:8000/api/getSODetail';
    const VAT_RATE      = 1.07;

    private static function normPo($v): string
    {
        return strtoupper(preg_replace('/^(SO|PO)/i', '', preg_replace('/\s+/', '', (string) $v)));
    }

    private static function billTotals($data): array
    {
        $out  = [];
        $walk = function ($node) use (&$walk, &$out) {
            if (!is_array($node)) return;
            foreach ($node as $key => $val) {
                if (is_array($val) && isset($val['items']) && is_array($val['items'])) {
                    $sum = 0.0;
                    foreach ($val['items'] as $it) {
                        if (isset($it['GoodAmnt']) && is_numeric($it['GoodAmnt'])) $sum += (float) $it['GoodAmnt'];
                    }
                    $k = self::normPo($key);
                    $out[$k] = ($out[$k] ?? 0) + $sum;
                } elseif (is_array($val)) {
                    $walk($val);
                }
            }
        };
        $walk($data);
        return $out;
    }

    private function fetchSoPrices(array $soList): array
    {
        $result  = [];
        $missing = [];
        foreach (array_unique(array_filter(array_map('strval', $soList))) as $so) {
            $hit = Cache::get('so_bills:' . md5($so));
            if (is_array($hit)) $result[$so] = $hit;
            else $missing[] = $so;
        }

        foreach (array_chunk($missing, 8) as $chunk) {
            try {
                $responses = Http::pool(function ($pool) use ($chunk) {
                    return array_map(
                        fn ($so) => $pool->as($so)->timeout(20)->get(self::SO_DETAIL_API, ['SONum' => $so]),
                        $chunk
                    );
                });
            } catch (\Throwable $e) {
                $responses = [];
            }

            foreach ($chunk as $so) {
                $res = $responses[$so] ?? null;
                $ok  = $res instanceof \Illuminate\Http\Client\Response && $res->successful();
                if (!$ok) {
                    try {
                        $res = Http::timeout(20)->retry(2, 800, throw: false)->get(self::SO_DETAIL_API, ['SONum' => $so]);
                        $ok  = $res->successful();
                    } catch (\Throwable $e) {
                        $ok = false;
                    }
                }
                if ($ok) {
                    $totals = self::billTotals($res->json() ?? []);
                    Cache::put('so_bills:' . md5($so), $totals, 600);
                    $result[$so] = $totals;
                } else {
                    \Log::warning('getSODetail failed for SO ' . $so);
                    $result[$so] = null;
                }
            }
        }
        return $result;
    }

    private static function priceFor(array $prices, $so, $po): ?float
    {
        $bills = $prices[(string) $so] ?? null;
        $k     = self::normPo($po);
        if (!is_array($bills) || $k === '' || !array_key_exists($k, $bills)) return null;
        return round($bills[$k] * self::VAT_RATE, 2);
    }

    private static function sumPrices(array $prices, array $pairs): array
    {
        $seen = [];
        $total = 0.0;
        $missing = 0;
        foreach ($pairs as $r) {
            $key = $r['so'] . '|' . self::normPo($r['po']);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $amt = self::priceFor($prices, $r['so'], $r['po']);
            if ($amt === null) { $missing++; continue; }
            $total += $amt;
        }
        return ['total' => round($total, 2), 'missing' => $missing];
    }

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

    private static function toTime($v): ?Carbon
    {
        if (empty($v) || str_starts_with((string) $v, '0000-00-00')) return null;
        try {
            return Carbon::parse($v, 'Asia/Bangkok');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function stageDurations($billTime, $pickTime, $routeTime, $deliTime): array
    {
        $t = [self::toTime($billTime), self::toTime($pickTime), self::toTime($routeTime), self::toTime($deliTime)];
        $diff = function ($a, $b) {
            if (!$a || !$b) return null;
            $m = (int) floor(($b->getTimestamp() - $a->getTimestamp()) / 60);
            return $m >= 0 ? $m : null;
        };
        return [
            'pick'  => $diff($t[0], $t[1]),
            'route' => $diff($t[1] ?? $t[0], $t[2]),
            'deli'  => $diff($t[2], $t[3]),
            'total' => $diff($t[0], $t[3]),
        ];
    }

    public function dashboardpdf(Request $request)
    {
        $date = $request->get('date');
        $message = null;

        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.dashboardadminpdf', compact('bill', 'message'));
    }

    public function adminroute(Request $request)
    {
        $this->requireLogin($request);
        $date = $request->get('date');
        $message = null;

        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.adminroute', compact('bill', 'message'));
    }

    public function history(Request $request)
    {
        $date = $request->get('date');
        $message = null;

        if ($date) {
            $bill = Bill::whereDate('time', $date)
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.history', compact('bill', 'message'));
    }

    public function updateStatus(Request $request)
    {
        $soDetailIds = $request->input('soDetailIds');
        if (empty($soDetailIds)) {
            return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
        }

        try {
            DB::table('tblbill')
                ->whereIn('so_detail_id', $soDetailIds)
                ->update(['status' => 1]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
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
        $soDetailIds = $request->input('soDetailIds');
        if (empty($soDetailIds)) {
            return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
        }

        try {
            DB::table('tblbill')
                ->whereIn('so_detail_id', $soDetailIds)
                ->update([
                    'statuspdf' => 1,
                    'status' => 0
                ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
        }
    }

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
        $soDetailIds = $request->input('soDetailIds');
        if (empty($soDetailIds)) {
            return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
        }

        try {
            DB::table('tblbill')
                ->whereIn('so_detail_id', $soDetailIds)
                ->update([
                        'statuspdf' => 2,
                        'status' => 1
                    ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateDeliveryDate(Request $request)
    {
        try {
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

            $existing = DB::table('tblbill')
                ->where('so_detail_id', $request->so_detail_id)
                ->first();

            if (!$existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลที่ต้องการอัปเดต'
                ], 404);
            }

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

        $file->storeAs('public/billissue_document', $filename);

        return back()->with('message', '✅ อัปโหลดไฟล์สำเร็จ');
    }

    public function updateStatuspdfcan(Request $request)
    {
        $soDetailIds = $request->input('soDetailIds');
        if (empty($soDetailIds)) {
            return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
        }

        try {
            DB::table('tblbill')
                ->whereIn('so_detail_id', $soDetailIds)
                ->update(['statuspdf' => '6']);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
        }
    }
}