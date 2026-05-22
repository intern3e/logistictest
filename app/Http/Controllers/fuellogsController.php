<?php

namespace App\Http\Controllers;

use App\Models\ng_shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    /* ══════════════════════════════════════════════════════════════════
       Helper: คืน array params สำหรับ redirect — รวม create_by ถ้ามี
       ใช้เป็น: return redirect()->route('oil', $this->urlParams($request))
    ══════════════════════════════════════════════════════════════════ */
    private function urlParams(Request $request): array
    {
        $params = [];
        if ($request->filled('create_by')) {
            $params['create_by'] = $request->input('create_by');
        }
        return $params;
    }

    /* ══════════════════════════════════════════════════════════════════
       รับ filter จาก form POST → เก็บใน session → redirect /oil
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
                'vehicle_id'  => $request->input('vehicle_id', 'all'),
            ]
        ]);

        return redirect()->route('oil', $this->urlParams($request));
    }

    /* ══════════════════════════════════════════════════════════════════
       ดึง filter จาก session → ถ้าไม่มี fallback เป็น request
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

        // helper normalize — ตัด zero-width + whitespace ส่วนเกิน + lowercase
        $norm = function ($s) {
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', (string) $s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
        };
        $driverTarget = ($filterDriver !== 'all') ? $norm($filterDriver) : null;

        return $query->get()
            ->filter(function ($row) use ($norm, $driverTarget) {
                // กรองคนขับแบบ normalize (กันชื่อมี zero-width/ช่องว่างซ่อน)
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
        })->values();
    }

    private function parseTimes(?string $workDate, ?string $startStr, ?string $endStr): array
    {
        $startDt = null;
        $endDt   = null;

        // helper: รับได้ทั้ง "HH:MM", "HH:MM:SS", และ datetime เต็ม "Y-m-d H:i(:s)"
        $parse = function (?string $str) use ($workDate) {
            if (!$str) return null;
            $str = trim($str);
            // datetime เต็ม (มีทั้งวันที่+เวลา) → parse ตรงๆ
            if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/', $str)) {
                try { return Carbon::parse($str); } catch (\Exception $e) { return null; }
            }
            // เวลาอย่างเดียว HH:MM หรือ HH:MM:SS → ต่อกับ workDate
            if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $str) && $workDate) {
                try { return Carbon::parse("{$workDate} {$str}"); } catch (\Exception $e) { return null; }
            }
            return null;
        };

        $startDt = $parse($startStr);
        $endDt   = $parse($endStr);
        // ถ้าจบก่อนเริ่ม (เวลาอย่างเดียว ข้ามคืน) → +1 วัน
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

    public function oil(Request $request)
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
            'view', 'filterDay', 'filterMonth', 'filterYear', 'filterDriver', 'filterPlate',
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
            'total_price'     => 'required|numeric|min:0',
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

        // กันลงซ้ำสำหรับรายการ auto-store (คนนอก whitelist, vehicle_id = '-')
        // ถ้ามี driver+date เดียวกันอยู่แล้ว → ไม่ลงซ้ำ
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
            'liters'          => $liters,
            'total_price'     => (float) $request->total_price,
            'price_per_liter' => $ppl,
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

    /* ลบ record ขยะ — ที่ vehicle_id = '-' และชื่อไม่ใช่คนขับ
       (ชื่อยาวเกิน 20 ตัว หรือมีคำต้องห้าม) */
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

    public function service(Request $request)
    {
        return view('driver.service');
    }
}