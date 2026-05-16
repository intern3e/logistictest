<?php

namespace App\Http\Controllers;

use App\Models\ng_shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    /* ══════════════════════════════════════════════════════════════════
       ✅ NEW: รับ filter จาก form POST → เก็บใน session → redirect /oil
       เพื่อให้ URL สะอาดเสมอ (ไม่มี query string)
    ══════════════════════════════════════════════════════════════════ */
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
            ]
        ]);

        return redirect()->route('oil');
    }

    /* ══════════════════════════════════════════════════════════════════
       ✅ NEW: ดึง filter จาก session → ถ้าไม่มี fallback เป็น request
    ══════════════════════════════════════════════════════════════════ */
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
        ];
    }

    private function buildLogs(Request $request): \Illuminate\Support\Collection
    {
        // ── ✅ ใช้ filter จาก session (ผ่าน getFilter) แทน request โดยตรง ──
        $f = $this->getFilter($request);

        $view         = $f['view'];
        $filterMonth  = $f['month'];
        $filterYear   = $f['year'];
        $filterDriver = $f['driver_name'];

        // ── รับช่วงวันที่ (date_from – date_to) ──
        $dateFrom = $f['date_from'];
        $dateTo   = $f['date_to'];

        // ถ้ากลับหัว (from > to) ให้ swap
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

        if ($filterDriver !== 'all') {
            $query->where('driver_name', $filterDriver);
        }

        return $query->get()->map(function ($row) {
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
                'work_date'       => $row['work_date']        ?? '',
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
                'note'            => $row['note']       ?? '',
                'created_at'      => $row['created_at'] ?? null,
            ];
        });
    }

    private function parseTimes(?string $workDate, ?string $startStr, ?string $endStr): array
    {
        if (!$workDate) return [null, null];

        $startDt = null;
        $endDt   = null;

        if ($startStr && preg_match('/^\d{2}:\d{2}$/', $startStr)) {
            $startDt = Carbon::createFromFormat('Y-m-d H:i', "{$workDate} {$startStr}");
        }
        if ($endStr && preg_match('/^\d{2}:\d{2}$/', $endStr)) {
            $endDt = Carbon::createFromFormat('Y-m-d H:i', "{$workDate} {$endStr}");
            if ($startDt && $endDt->lt($startDt)) $endDt->addDay();
        }

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

    public function oil(Request $request)
    {
        // ── ✅ ดึง filter จาก session (fallback เป็น request) ──
        $f = $this->getFilter($request);

        $view         = $f['view'];
        $filterDay    = $f['date_from'];
        $filterMonth  = $f['month'];
        $filterYear   = $f['year'];
        $filterDriver = $f['driver_name'];
        $dateFrom     = $f['date_from'];
        $dateTo       = $f['date_to'];

        $logs = $this->buildLogs($request);

        // ── ดึงข้อมูลทั้งหมด (ไม่ filter) สำหรับหน้า Report ──
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
                    'driver_name'    => $row['driver_name']    ?? '',
                    'work_date'      => $row['work_date']      ?? '',
                    'total_price'    => (float) ($row['total_price']    ?? 0),
                    'liters'         => $liters,
                    'total_distance' => $distance,
                    'km_per_liter'   => $kml,
                    'work_hours'     => $workHours,
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

        return view('driver.oil', compact(
            'logs', 'allLogs',
            'view', 'filterDay', 'filterMonth', 'filterYear', 'filterDriver',
            'dateFrom', 'dateTo',
            'drivers', 'plates', 'metrics', 'costByDriver', 'kmlByDriver',
            'deliveryStats', 'editLog'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'work_date'       => 'required|date',
            'driver_name'     => 'required|string|max:100',
            'vehicle_id'      => 'required|string|max:50',
            'total_price'     => 'required|numeric|min:0.00',
            'total_distance'  => 'nullable|numeric|min:0',
            'liters'          => 'nullable|numeric|min:0',
            'price_per_liter' => 'nullable|numeric|min:0',
        ]);

        [$startDt, $endDt] = $this->parseTimes(
            $request->work_date, $request->start_time, $request->end_time
        );
        [$liters, $ppl] = $this->calcLitersPpl(
            $request->total_price, $request->price_per_liter, $request->liters
        );

        DB::table('fuel_logs')->insert([
            'driver_name'     => trim($request->driver_name),
            'vehicle_id'      => trim($request->vehicle_id),
            'work_date'       => $request->work_date,
            'start_time'      => $startDt,
            'end_time'        => $endDt,
            'total_distance'  => (float) ($request->total_distance ?? 0),
            'liters'          => $liters,
            'total_price'     => (float) $request->total_price,
            'price_per_liter' => $ppl,
            'ok'              => (int) ($request->ok ?? 0),
            'ng'              => (int) ($request->ng ?? 0),
            'note'            => $request->note ? trim($request->note) : null,
            'created_at'      => now(),
        ]);

        return redirect()->route('oil')->with('success', 'บันทึกข้อมูลน้ำมันสำเร็จ ✅');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'work_date'       => 'required|date',
            'driver_name'     => 'required|string|max:100',
            'vehicle_id'      => 'required|string|max:50',
            'total_price'     => 'required|numeric|min:0.01',
            'total_distance'  => 'nullable|numeric|min:0',
            'liters'          => 'nullable|numeric|min:0',
            'price_per_liter' => 'nullable|numeric|min:0',
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
            'liters'          => $liters,
            'total_price'     => (float) $request->total_price,
            'price_per_liter' => $ppl,
            'ok'              => (int) ($request->ok ?? 0),
            'ng'              => (int) ($request->ng ?? 0),
            'note'            => $request->note ? trim($request->note) : null,
        ]);

        return redirect()->route('oil')->with('success', 'อัปเดตข้อมูลสำเร็จ ✅');
    }

    public function destroy($id)
    {
        $deleted = DB::table('fuel_logs')->where('id', $id)->delete();
        if (!$deleted) {
            return redirect()->route('oil')->with('error', 'ไม่พบรายการที่ต้องการลบ');
        }
        return redirect()->route('oil')->with('success', 'ลบข้อมูลเรียบร้อย');
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
public function service(Request $request)
{
    return view('driver.service');
}
}