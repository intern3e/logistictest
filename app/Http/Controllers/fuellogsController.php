<?php

namespace App\Http\Controllers;

use App\Models\ng_shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    private function urlParams(Request $request): array
    {
        $params = [];
        if ($request->filled('create_by')) {
            $params['create_by'] = $request->input('create_by');
        }
        return $params;
    }

    public function applyFilter(Request $request)
    {
        session([
            'oil_filter' => [
                'view'        => $request->input('view', 'day'),
                'date_from'   => $request->input('date_from'),
                'date_to'     => $request->input('date_to'),
                'month'       => $request->input('month'),
                'year'        => $request->input('year'),
                'driver_name' => $request->input('driver_name', 'all'),
                'vehicle_id'  => $request->input('vehicle_id', 'all'),
            ]
        ]);

        if ($request->input('redirect_to') === 'report') {
            return redirect()->route('oil.report', $this->urlParams($request));
        }
        if ($request->input('redirect_to') === 'admin') {
            return redirect()->route('oil.admin', $this->urlParams($request));
        }

        return redirect()->route('oil', $this->urlParams($request));
    }

    private function getFilter(Request $request): array
    {
        $filter = session('oil_filter', []);

        return [
            'view'        => $filter['view']        ?? $request->input('view', 'day'),
            'date_from'   => $filter['date_from']   ?? $request->input('date_from', date('Y-m-d')),
            'date_to'     => $filter['date_to']     ?? $request->input('date_to', date('Y-m-d')),
            'month'       => $filter['month']       ?? $request->input('month', date('Y-m')),
            'year'        => $filter['year']        ?? $request->input('year', date('Y')),
            'driver_name' => $filter['driver_name'] ?? $request->input('driver_name', 'all'),
            'vehicle_id'  => $filter['vehicle_id']  ?? $request->input('vehicle_id', 'all'),
        ];
    }

    private function buildLogs(Request $request): \Illuminate\Support\Collection
    {
        $f = $this->getFilter($request);

        $view         = $f['view'];
        $filterMonth  = $f['month'];
        $filterYear   = $f['year'];
        $filterDriver = $f['driver_name'];
        $filterPlate  = $f['vehicle_id'];

        $dateFrom = $f['date_from'];
        $dateTo   = $f['date_to'];

        if ($dateFrom && $dateTo && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $query = DB::table('fuel_logs')
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc');

        if ($view === 'day') {
            $query->whereDate('work_date', '>=', $dateFrom)
                  ->whereDate('work_date', '<=', $dateTo);
        } elseif ($view === 'month') {
            [$y, $m] = explode('-', $filterMonth . '-01');
            $query->whereYear('work_date', $y)->whereMonth('work_date', $m);
        } elseif ($view === 'year') {
            $query->whereYear('work_date', $filterYear);
        }

        if ($filterPlate !== 'all') {
            $query->where('vehicle_id', $filterPlate);
        }

        $norm = function ($s) {
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', (string) $s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
        };
        $driverTarget = ($filterDriver !== 'all') ? $norm($filterDriver) : null;

        return $query->get()
            ->filter(function ($row) use ($norm, $driverTarget) {
                if ($driverTarget === null) return true;
                return $norm(((array) $row)['driver_name'] ?? '') === $driverTarget;
            })
            ->map(function ($row) {
                $row = (array) $row;

                $startTime = null;
                $endTime   = null;
                $workHours = 0;

                if (!empty($row['start_time'])) {
                    $startTime = Carbon::parse($row['start_time'])->format('H:i');
                }
                if (!empty($row['end_time'])) {
                    $endTime = Carbon::parse($row['end_time'])->format('H:i');
                }
                if (!empty($row['start_time']) && !empty($row['end_time'])) {
                    $diff = Carbon::parse($row['start_time'])
                                  ->diffInMinutes(Carbon::parse($row['end_time']), false);
                    if ($diff > 0) $workHours = round($diff / 60, 2);
                }

                $liters   = (float) ($row['liters']         ?? 0);
                $distance = (float) ($row['total_distance'] ?? 0);
                $kml      = ($liters > 0 && $distance > 0) ? round($distance / $liters, 2) : 0;

                return [
                    'id'              => (int) $row['id'],
                    'driver_name'     => $row['driver_name']     ?? '',
                    'vehicle_id'      => $row['vehicle_id']      ?? '',
                    'work_date'       => $row['work_date']       ?? '',
                    'start_time'      => $startTime,
                    'end_time'        => $endTime,
                    'work_hours'      => $workHours,
                    'total_distance'  => $distance,
                    'liters'          => $liters ?: null,
                    'total_price'     => (float) ($row['total_price']     ?? 0),
                    'price_per_liter' => (float) ($row['price_per_liter'] ?? 0),
                    'km_per_liter'    => $kml,
                    'ok_count'        => (int) ($row['ok'] ?? 0),
                    'ng_count'        => (int) ($row['ng'] ?? 0),
                    'note'            => $row['note']            ?? '',
                    'created_at'      => $row['created_at']      ?? null,
                    'ot_cost'         => (float) ($row['ot_cost']       ?? 0),
                    'handling_cost'   => (float) ($row['handling_cost'] ?? 0),
                    'delivery_cost'   => (float) ($row['delivery_cost'] ?? 0),
                ];
            })->values();
    }

    private function parseTimes(?string $workDate, ?string $startStr, ?string $endStr): array
    {
        $startDt = null;
        $endDt   = null;

        $parse = function (?string $str) use ($workDate) {
            if (!$str) return null;
            $str = trim($str);
            if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/', $str)) {
                try { return Carbon::parse($str); } catch (\Exception $e) { return null; }
            }
            if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $str) && $workDate) {
                try { return Carbon::parse("{$workDate} {$str}"); } catch (\Exception $e) { return null; }
            }
            return null;
        };

        $startDt = $parse($startStr);
        $endDt   = $parse($endStr);
        if ($startDt && $endDt && $endDt->lt($startDt)) $endDt->addDay();

        return [
            $startDt ? $startDt->format('Y-m-d H:i:s') : null,
            $endDt   ? $endDt->format('Y-m-d H:i:s')   : null,
        ];
    }

    private function calcLitersPpl($totalPrice, $pplInput, $litersInput): array
    {
        $tp  = (float) $totalPrice;
        $ppl = (float) $pplInput;
        $ltr = (float) $litersInput;

        if ($ppl > 0 && $tp > 0) return [round($tp / $ppl, 2), round($ppl, 2)];
        if ($ltr > 0 && $tp > 0) return [round($ltr, 2), round($tp / $ltr, 2)];

        return [$ltr > 0 ? round($ltr, 2) : null, $ppl > 0 ? round($ppl, 2) : null];
    }

    private function buildViewData(Request $request): array
    {
        $f = $this->getFilter($request);

        $view         = $f['view'];
        $filterDay    = $f['date_from'];
        $filterMonth  = $f['month'];
        $filterYear   = $f['year'];
        $filterDriver = $f['driver_name'];
        $filterPlate  = $f['vehicle_id'];
        $dateFrom     = $f['date_from'];
        $dateTo       = $f['date_to'];

        $logs = $this->buildLogs($request);

        $allLogs = DB::table('fuel_logs')
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($row) {
                $row = (array) $row;
                $liters   = (float) ($row['liters']         ?? 0);
                $distance = (float) ($row['total_distance'] ?? 0);
                $kml      = ($liters > 0 && $distance > 0) ? round($distance / $liters, 2) : 0;
                $workHours = 0;
                if (!empty($row['start_time']) && !empty($row['end_time'])) {
                    $diff = Carbon::parse($row['start_time'])
                                  ->diffInMinutes(Carbon::parse($row['end_time']), false);
                    if ($diff > 0) $workHours = round($diff / 60, 2);
                }
                return [
                    'id'             => (int) ($row['id'] ?? 0),
                    'driver_name'    => $row['driver_name']    ?? '',
                    'vehicle_id'     => $row['vehicle_id']     ?? '',
                    'work_date'      => $row['work_date']      ?? '',
                    'total_price'    => (float) ($row['total_price']    ?? 0),
                    'liters'         => $liters,
                    'total_distance' => $distance,
                    'km_per_liter'   => $kml,
                    'work_hours'     => $workHours,
                    'start_time'     => $row['start_time'] ?? null,
                    'end_time'       => $row['end_time']   ?? null,
                    'ok'             => (int) ($row['ok'] ?? 0),
                    'ng'             => (int) ($row['ng'] ?? 0),
                    'ot_cost'        => (float) ($row['ot_cost']       ?? 0),
                    'handling_cost'  => (float) ($row['handling_cost'] ?? 0),
                    'delivery_cost'  => (float) ($row['delivery_cost'] ?? 0),
                ];
            });

        $drivers = DB::table('fuel_logs')->distinct()->orderBy('driver_name')
                     ->pluck('driver_name')->filter()->values()->toArray();

        $plates = DB::table('fuel_logs')->distinct()->orderBy('vehicle_id')
                    ->pluck('vehicle_id')->filter()->values()->toArray();

        $metrics = null;
        if ($logs->count() > 0) {
            $kmlValues = $logs->filter(fn($r) => ($r['km_per_liter'] ?? 0) > 0)
                              ->pluck('km_per_liter');
            $metrics = [
                'total_liters'     => round($logs->sum('liters'), 2),
                'total_price'      => $logs->sum('total_price'),
                'avg_km_per_liter' => $kmlValues->count() ? round($kmlValues->avg(), 2) : 0,
                'total_work_hours' => round($logs->sum('work_hours'), 2),
            ];
        }

        $costByDriver = $logs->groupBy('driver_name')
            ->map(fn($g, $d) => ['driver' => $d, 'total_price' => round($g->sum('total_price'), 2)])
            ->sortByDesc('total_price')->values()->toArray();

        $kmlByDriver = $logs->groupBy('driver_name')
            ->map(function ($g, $d) {
                $kv = $g->filter(fn($r) => $r['km_per_liter'] > 0)->pluck('km_per_liter');
                return ['driver' => $d, 'km_per_liter' => $kv->count() ? round($kv->avg(), 2) : 0];
            })
            ->filter(fn($d) => $d['km_per_liter'] > 0)->values()->toArray();

        $deliveryStats = null;
        $editLog       = null;

        return compact(
            'logs', 'allLogs',
            'view', 'filterDay', 'filterMonth', 'filterYear', 'filterDriver', 'filterPlate',
            'dateFrom', 'dateTo',
            'drivers', 'plates', 'metrics', 'costByDriver', 'kmlByDriver',
            'deliveryStats', 'editLog'
        );
    }

    public function oil(Request $request)
    {
        return view('driver.oil', $this->buildViewData($request));
    }

    public function report(Request $request)
    {
        return view('driver.report', $this->buildViewData($request));
    }

    public function admin(Request $request)
    {
        return view('driver.admin', $this->buildViewData($request));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_date'       => 'required|date',
            'driver_name'     => 'required|string|max:100',
            'vehicle_id'      => 'required|string|max:50',
            'total_price'     => 'required|numeric|min:0',
            'total_distance'  => 'nullable|numeric|min:0',
            'liters'          => 'nullable|numeric|min:0',
            'price_per_liter' => 'nullable|numeric|min:0',
            'ot_cost'         => 'nullable|numeric|min:0',
            'handling_cost'   => 'nullable|numeric|min:0',
            'delivery_cost'   => 'nullable|numeric|min:0',
        ]);

        [$startDt, $endDt] = $this->parseTimes(
            $request->work_date, $request->start_time, $request->end_time
        );
        [$liters, $ppl] = $this->calcLitersPpl(
            $request->total_price, $request->price_per_liter, $request->liters
        );

        if (trim($request->vehicle_id) === '-') {
            $exists = DB::table('fuel_logs')
                ->where('driver_name', trim($request->driver_name))
                ->where('work_date', $request->work_date)
                ->where('vehicle_id', '-')
                ->exists();
            if ($exists) {
                return redirect()->route('oil', $this->urlParams($request))
                                 ->with('success', 'มีข้อมูลอยู่แล้ว');
            }
        }

        DB::table('fuel_logs')->insert([
            'driver_name'     => trim($request->driver_name),
            'vehicle_id'      => trim($request->vehicle_id),
            'work_date'       => $request->work_date,
            'start_time'      => $startDt,
            'end_time'        => $endDt,
            'total_distance'  => (float) ($request->total_distance ?? 0),
            'liters'          => $liters ?? 0,            
            'total_price'     => (float) $request->total_price,
            'price_per_liter' => $ppl ?? 0,           
            'ot_cost'         => (float) ($request->ot_cost ?? 0),
            'handling_cost'   => (float) ($request->handling_cost ?? 0),
            'delivery_cost'   => (float) ($request->delivery_cost ?? 0),
            'ok'              => (int) ($request->ok ?? 0),
            'ng'              => (int) ($request->ng ?? 0),
            'note'            => $request->note ? trim($request->note) : null,
            'created_at'      => now(),
        ]);

        return redirect()->route('oil', $this->urlParams($request))
                         ->with('success', 'บันทึกข้อมูลน้ำมันสำเร็จ ✅');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'work_date'       => 'required|date',
            'driver_name'     => 'required|string|max:100',
            'vehicle_id'      => 'required|string|max:50',
            'total_price'     => 'required|numeric|min:0',
            'total_distance'  => 'nullable|numeric|min:0',
            'liters'          => 'nullable|numeric|min:0',
            'price_per_liter' => 'nullable|numeric|min:0',
            'ot_cost'         => 'nullable|numeric|min:0',
            'handling_cost'   => 'nullable|numeric|min:0',
            'delivery_cost'   => 'nullable|numeric|min:0',
        ]);

        abort_unless(DB::table('fuel_logs')->where('id', $id)->exists(), 404);

        [$startDt, $endDt] = $this->parseTimes(
            $request->work_date, $request->start_time, $request->end_time
        );
        [$liters, $ppl] = $this->calcLitersPpl(
            $request->total_price, $request->price_per_liter, $request->liters
        );

        DB::table('fuel_logs')->where('id', $id)->update([
            'driver_name'     => trim($request->driver_name),
            'vehicle_id'      => trim($request->vehicle_id),
            'work_date'       => $request->work_date,
            'start_time'      => $startDt,
            'end_time'        => $endDt,
            'total_distance'  => (float) ($request->total_distance ?? 0),
            'liters'          => $liters ?? 0,
            'total_price'     => (float) $request->total_price,
            'price_per_liter' => $ppl ?? 0,
            'ot_cost'         => (float) ($request->ot_cost ?? 0),
            'handling_cost'   => (float) ($request->handling_cost ?? 0),
            'delivery_cost'   => (float) ($request->delivery_cost ?? 0),
            'ok'              => (int) ($request->ok ?? 0),
            'ng'              => (int) ($request->ng ?? 0),
            'note'            => $request->note ? trim($request->note) : null,
        ]);

        return redirect()->route('oil', $this->urlParams($request))
                         ->with('success', 'อัปเดตข้อมูลสำเร็จ ✅');
    }

    public function destroy(Request $request, $id)
    {
        $deleted = DB::table('fuel_logs')->where('id', $id)->delete();
        if (!$deleted) {
            return redirect()->route('oil', $this->urlParams($request))
                             ->with('error', 'ไม่พบรายการที่ต้องการลบ');
        }
        return redirect()->route('oil', $this->urlParams($request))
                         ->with('success', 'ลบข้อมูลเรียบร้อย');
    }

    public function cleanupGarbage(Request $request)
    {
        $banned = ['ลูกค้า','เซ็นบิล','เซ็น','บิล','สาขา','จำกัด','บริษัท','หจก','ร้าน','คุณ','ไป','ที่','กับ'];
        $rows = DB::table('fuel_logs')->where('vehicle_id', '-')->get();
        $deleteIds = [];
        foreach ($rows as $row) {
            $name = trim($row->driver_name ?? '');
            $isGarbage = false;
            if (mb_strlen($name) > 20) $isGarbage = true;
            foreach ($banned as $w) { if (mb_strpos($name, $w) !== false) { $isGarbage = true; break; } }
            if (preg_match_all('/\d/', $name) >= 4) $isGarbage = true;
            if ($isGarbage) $deleteIds[] = $row->id;
        }
        $count = 0;
        if (!empty($deleteIds)) {
            $count = DB::table('fuel_logs')->whereIn('id', $deleteIds)->delete();
        }
        return redirect()->route('oil', $this->urlParams($request))
                         ->with('success', "ลบข้อมูลขยะ {$count} รายการ");
    }

    public function prevMileage(Request $request)
    {
        $vehicleId = $request->get('vehicle_id');
        $workDate  = $request->get('work_date');
        $excludeId = $request->get('exclude_id');

        if (!$vehicleId || !$workDate) {
            return response()->json(['success' => false, 'data' => null]);
        }

        $query = DB::table('fuel_logs')
            ->where('vehicle_id', $vehicleId)
            ->where('work_date', '<', $workDate)
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc');

        if ($excludeId) $query->where('id', '!=', (int) $excludeId);

        $prev = $query->first();

        return response()->json([
            'success' => (bool) $prev,
            'data'    => $prev ? [
                'work_date'      => $prev->work_date,
                'total_distance' => $prev->total_distance,
            ] : null,
        ]);
    }

    public function ngList(Request $request)
    {
        $q = ng_shipment::query()->latest('ng_date')->latest('id');

        if ($request->filled('driver_name')) {
            $q->where('driver_name', $request->driver_name);
        }

        $status = $request->get('status', 'ng');
        if ($status !== 'all') {
            $q->where('status', $status);
        }

        if ($request->filled('from')) $q->whereDate('ng_date', '>=', $request->from);
        if ($request->filled('to'))   $q->whereDate('ng_date', '<=', $request->to);

        return response()->json($q->paginate(50));
    }

    public function syncNg(Request $request)
    {
        $request->validate([
            'date'                   => 'required|date_format:Y-m-d',
            'jobs'                   => 'required|array|min:1',
            'jobs.*.bill_no'         => 'required|max:50',
            'jobs.*.driver_name'     => 'required|string|max:100',
            'jobs.*.bill_in_by'      => 'nullable|string|max:100',
            'jobs.*.customer_name'   => 'nullable|string|max:200',
            'jobs.*.status'          => 'required|string',
            'jobs.*.so_id'           => 'nullable|string',
            'jobs.*.note'            => 'nullable|string|max:500',
        ]);

        $date      = $request->date;
        $jobs      = $request->jobs;
        $okBillNos = [];
        $ngJobs    = [];

        foreach ($jobs as $job) {
            $status = trim($job['status'] ?? '');
            $isOk   = (str_contains($status, 'สำเร็จ') && !str_contains($status, 'ไม่'))
                   || in_array(strtolower($status), ['ok', 'success', '1']);

            if ($isOk) {
                $okBillNos[] = (string) $job['bill_no'];
            } else {
                $ngJobs[] = $job;
            }
        }

        $result = ng_shipment::syncDay($date, $ngJobs, $okBillNos);

        return response()->json([
            'success'  => true,
            'date'     => $date,
            'total'    => count($jobs),
            'ok'       => count($okBillNos),
            'ng'       => count($ngJobs),
            'inserted' => $result['inserted'],
            'resolved' => $result['resolved'],
        ]);
    }

    public function savedDrivers(Request $request)
    {
        $date = $request->get('date');
        if (!$date) {
            return response()->json([]);
        }

        $drivers = DB::table('fuel_logs')
            ->where('work_date', $date)
            ->distinct()
            ->pluck('driver_name')
            ->filter()
            ->values();

        return response()->json($drivers);
    }

    private array $motorcycleVehicleIds = [];

    public function Deliveryfee(Request $request)
    {
        $mode = $request->input('mode', 'month');
        $mode = in_array($mode, ['month', 'week']) ? $mode : 'month';

        $selMonth = (int) $request->input('month', now()->month);
        $selYear  = (int) $request->input('year', now()->year);

        $weekStartInput = $request->input('week_start', now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'));
        try {
            $weekStart = Carbon::parse($weekStartInput)->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $weekStart = now()->startOfWeek(Carbon::MONDAY);
        }
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        if ($mode === 'week') {
            $rangeStart = $weekStart->copy();
            $rangeEnd   = $weekEnd->copy();
        } else {
            $rangeStart = Carbon::create($selYear, $selMonth, 1)->startOfMonth();
            $rangeEnd   = $rangeStart->copy()->endOfMonth();
        }

        $days = [];
        for ($d = $rangeStart->copy(); $d->lte($rangeEnd); $d->addDay()) {
            $days[] = $d->format('Y-m-d');
        }

        $roster = ['บังเดช','กอลฟ์','เก่ง','เอ้','แซม','เอ','แฟงค์','yuth','แมน','กบ','joey','บอยBTS'];

        $norm = function ($s) {
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string) $s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
        };

        $allDriverNames = DB::table('fuel_logs')
            ->distinct()
            ->pluck('driver_name')
            ->filter()
            ->values()
            ->all();

        $activeRoster = [];
        foreach ($roster as $label) {
            $rn = $norm($label);
            foreach ($allDriverNames as $dbName) {
                if ($norm($dbName) === $rn) {
                    $activeRoster[] = ['label' => $label, 'db_name' => $dbName];
                    break;
                }
            }
        }
        if (empty($activeRoster)) {
            sort($allDriverNames);
            foreach ($allDriverNames as $dbName) {
                $activeRoster[] = ['label' => $dbName, 'db_name' => $dbName];
            }
        }

        $rangeFrom = $rangeStart->format('Y-m-d');
        $rangeTo   = $rangeEnd->format('Y-m-d');

        $rawRows = DB::table('fuel_logs')
            ->whereDate('work_date', '>=', $rangeFrom)
            ->whereDate('work_date', '<=', $rangeTo)
            ->when(!empty($this->motorcycleVehicleIds), function ($q) {
                $q->whereNotIn('vehicle_id', $this->motorcycleVehicleIds);
            })
            ->get();

        $matrix = [];
        foreach ($rawRows as $row) {
            $row = (array) $row;
            $dn  = $row['driver_name'] ?? '';
            $wd  = $row['work_date']   ?? '';
            if ($dn === '' || $wd === '') continue;
            if (!isset($matrix[$dn][$wd])) {
                $matrix[$dn][$wd] = ['delivery' => 0.0, 'ot' => 0.0, 'handling' => 0.0];
            }
            $matrix[$dn][$wd]['delivery'] += (float) ($row['delivery_cost'] ?? 0);
            $matrix[$dn][$wd]['ot']       += (float) ($row['ot_cost']       ?? 0);
            $matrix[$dn][$wd]['handling'] += (float) ($row['handling_cost'] ?? 0);
        }

        $latestPlates = DB::table('fuel_logs')
            ->select('driver_name', 'vehicle_id')
            ->whereIn('driver_name', array_column($activeRoster, 'db_name'))
            ->when(!empty($this->motorcycleVehicleIds), function ($q) {
                $q->whereNotIn('vehicle_id', $this->motorcycleVehicleIds);
            })
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('driver_name')
            ->map(fn ($g) => $g->first()->vehicle_id ?? '-');

        $driverGrid = [];
        foreach ($activeRoster as $d) {
            $dbName = $d['db_name'];
            $dayVals = [];
            $totDelivery = 0.0; $totOt = 0.0; $totHandling = 0.0;
            foreach ($days as $day) {
                // has = true ก็ต่อเมื่อมี row จริงในตาราง fuel_logs สำหรับคนขับ+วันนี้
                // (ไม่ว่าค่าจะเป็น 0 หรือไม่ก็ตาม) ใช้แยกกรณี "ยังไม่มีข้อมูลเลย" ออกจาก "มีข้อมูลแต่เป็น 0"
                $has = isset($matrix[$dbName][$day]);
                $v = $matrix[$dbName][$day] ?? ['delivery' => 0.0, 'ot' => 0.0, 'handling' => 0.0];
                $v['has'] = $has;
                $dayVals[$day] = $v;
                $totDelivery += $v['delivery'];
                $totOt       += $v['ot'];
                $totHandling += $v['handling'];
            }

            if ($totDelivery == 0 && $totOt == 0 && $totHandling == 0 && ($latestPlates[$dbName] ?? '-') === '-') {
                continue;
            }

            $driverGrid[] = [
                'label'        => $d['label'],
                'db_name'      => $dbName,  // ⭐ เพิ่มบรรทัดนี้สำหรับ Inline Edit
                'plate'        => $latestPlates[$dbName] ?? '-',
                'days'         => $dayVals,
                'totDelivery'  => $totDelivery,
                'totOt'        => $totOt,
                'totHandling'  => $totHandling,
                'totAll'       => $totDelivery + $totOt + $totHandling,
            ];
        }

        $dayTotals = [];
        foreach ($days as $day) {
            $sd = 0.0; $so = 0.0; $sh = 0.0;
            foreach ($driverGrid as $dg) {
                $sd += $dg['days'][$day]['delivery'];
                $so += $dg['days'][$day]['ot'];
                $sh += $dg['days'][$day]['handling'];
            }
            $dayTotals[$day] = ['delivery' => $sd, 'ot' => $so, 'handling' => $sh];
        }
        $grandDelivery = array_sum(array_column($dayTotals, 'delivery'));
        $grandOt       = array_sum(array_column($dayTotals, 'ot'));
        $grandHandling = array_sum(array_column($dayTotals, 'handling'));
        $grandTotal    = $grandDelivery + $grandOt + $grandHandling;

        return view('driver.deliveryfee', [
            'mode'          => $mode,
            'days'          => $days,
            'driverGrid'    => $driverGrid,
            'dayTotals'     => $dayTotals,
            'grandDelivery' => $grandDelivery,
            'grandOt'       => $grandOt,
            'grandHandling' => $grandHandling,
            'grandTotal'    => $grandTotal,
            'selMonth'      => $selMonth,
            'selYear'       => $selYear,
            'weekStart'     => $weekStart->format('Y-m-d'),
            'weekEnd'       => $weekEnd->format('Y-m-d'),
        ]);
    }

    // ⭐ เพิ่ม method นี้สำหรับ Inline Edit
    public function updateCell(Request $request)
    {
        // เดิม 'value' => 'required|numeric|min:0|max:9999999' บล็อกไม่ให้บันทึกค่าติดลบ
        // แก้เป็น min:-9999999 เพื่ออนุญาตให้บันทึกค่าติดลบได้ (เช่น รายการหักเงิน/ปรับยอด)
        $request->validate([
            'driver_name' => 'required|string|max:100',
            'work_date'   => 'required|date',
            'field'       => 'required|in:delivery,ot,handling',
            'value'       => 'required|numeric|min:-9999999|max:9999999',
        ]);

        $driverName = $request->driver_name;
        $workDate   = $request->work_date;
        $field      = $request->field;
        $value      = (float) $request->value;

        $fieldMap = [
            'delivery' => 'delivery_cost',
            'ot'       => 'ot_cost',
            'handling' => 'handling_cost',
        ];
        $dbField = $fieldMap[$field];

        $logs = DB::table('fuel_logs')
            ->where('driver_name', $driverName)
            ->whereDate('work_date', $workDate)
            ->orderByDesc('id')
            ->get();

        if ($logs->isNotEmpty()) {
            $lastId = $logs->first()->id;
            DB::table('fuel_logs')->where('id', $lastId)->update([$dbField => $value]);

            $otherIds = $logs->where('id', '!=', $lastId)->pluck('id');
            if ($otherIds->isNotEmpty()) {
                DB::table('fuel_logs')->whereIn('id', $otherIds)->update([$dbField => 0]);
            }
        } else {
            $lastVehicle = DB::table('fuel_logs')
                ->where('driver_name', $driverName)
                ->orderByDesc('work_date')
                ->orderByDesc('id')
                ->value('vehicle_id') ?? '-';

            DB::table('fuel_logs')->insert([
                'driver_name'    => $driverName,
                'vehicle_id'     => $lastVehicle,
                'work_date'      => $workDate,
                'total_price'    => 0,
                'total_distance' => 0,
                'liters'         => 0,
                'price_per_liter'=> 0,
                'delivery_cost'  => $field === 'delivery' ? $value : 0,
                'ot_cost'        => $field === 'ot' ? $value : 0,
                'handling_cost'  => $field === 'handling' ? $value : 0,
                'ok'             => 0,
                'ng'             => 0,
                'created_at'     => now(),
            ]);
        }

        $sums = DB::table('fuel_logs')
            ->where('driver_name', $driverName)
            ->whereDate('work_date', $workDate)
            ->selectRaw('
                COALESCE(SUM(delivery_cost),0) as delivery,
                COALESCE(SUM(ot_cost),0) as ot,
                COALESCE(SUM(handling_cost),0) as handling
            ')
            ->first();

        return response()->json([
            'success'  => true,
            'delivery' => (float) $sums->delivery,
            'ot'       => (float) $sums->ot,
            'handling' => (float) $sums->handling,
            'total'    => (float) ($sums->delivery + $sums->ot + $sums->handling),
        ]);
    }

    public function service(Request $request)
    {
        return view('driver.service');
    }
}