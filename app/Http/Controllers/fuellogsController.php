<?php

namespace App\Http\Controllers;

use App\Models\fuel_Logs;
use Illuminate\Http\Request;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    // =============================================
    // INDEX
    // =============================================
    public function oil(Request $request)
    {
        $view         = $request->input('view', 'month');
        $filterDriver = $request->input('driver_name', 'all');
        $filterDay    = $request->input('date', '');
        $filterMonth  = $request->input('month', date('Y-m'));

        $baseQuery = fuel_Logs::query();

        if ($view === 'day' && $filterDay) {
            $baseQuery->whereDate('work_date', $filterDay);
        } elseif ($view === 'month' && $filterMonth) {
            $date = Carbon::parse($filterMonth);
            $baseQuery->whereYear('work_date', $date->year)
                      ->whereMonth('work_date', $date->month);
        } elseif ($view === 'year' && $filterMonth) {
            $baseQuery->whereYear('work_date', substr($filterMonth, 0, 4));
        }

        $drivers = fuel_Logs::distinct()->orderBy('driver_name')->pluck('driver_name');
        $plates  = fuel_Logs::distinct()->orderBy('vehicle_id')->pluck('vehicle_id');

        $query = (clone $baseQuery)->orderBy('work_date', 'desc')->orderBy('id', 'desc');
        if ($filterDriver !== 'all') {
            $query->where('driver_name', $filterDriver);
        }

        $logs = $query->get()->map(fn($l) => $this->appendComputed($l));

        $metrics = null;
        if ($filterDriver !== 'all') {
            $metrics = [
                'total_liters'     => round($logs->sum('liters'), 1),
                'total_price'      => round($logs->sum('total_price')),
                'total_work_hours' => round($logs->sum('work_hours'), 1),
                'avg_km_per_liter' => round($logs->where('km_per_liter', '>', 0)->avg('km_per_liter') ?? 0, 1),
            ];
        }

        $allLogs      = fuel_Logs::orderBy('work_date')->get()->map(fn($l) => $this->appendComputed($l));
        $chartDrivers = $logs->pluck('driver_name')->unique()->sort()->values();

        $costByDriver = $chartDrivers->map(fn($d) => [
            'driver'      => $d,
            'total_price' => round($logs->where('driver_name', $d)->sum('total_price')),
        ])->filter(fn($d) => $d['total_price'] > 0)->values();

        $kmlByDriver = $chartDrivers->map(function ($d) use ($logs) {
            $dl = $logs->where('driver_name', $d)->where('km_per_liter', '>', 0);
            return $dl->count() > 0
                ? ['driver' => $d, 'km_per_liter' => round($dl->avg('km_per_liter'), 2)]
                : null;
        })->filter()->values();

        $trendSrc = $filterDriver !== 'all'
            ? $allLogs->where('driver_name', $filterDriver)
            : $allLogs;

        $trend = $trendSrc
            ->groupBy(fn($l) => substr($l['work_date'], 0, 7))
            ->map(fn($g, $m) => ['month' => $m, 'total_price' => round($g->sum('total_price'))])
            ->sortKeys()->values();

        $reportAll      = fuel_Logs::all()->map(fn($l) => $this->appendComputed($l));
        $reportByDriver = $reportAll->groupBy('driver_name')->map(function ($dl, $d) {
            return [
                'driver_name'      => $d,
                'total_price'      => round($dl->sum('total_price')),
                'total_liters'     => round($dl->sum('liters'), 1),
                'total_work_hours' => round($dl->sum('work_hours'), 1),
            ];
        })->sortKeys()->values();

        $editLog = null;
        if ($request->filled('edit_id')) {
            $found = fuel_Logs::find($request->edit_id);
            if ($found) {
                $editLog = $this->appendComputed($found);
            }
        }

        return view('driver.oil', compact(
            'logs', 'drivers', 'plates',
            'metrics', 'costByDriver', 'kmlByDriver', 'trend',
            'reportByDriver', 'reportAll',
            'view', 'filterDriver', 'filterDay', 'filterMonth',
            'editLog'
        ));
    }

    // =============================================
    // STORE
    // =============================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_date'       => 'required|date',
            'driver_name'     => 'required|string|max:100',
            'vehicle_id'      => 'required|string|max:50',
            'liters'          => 'nullable|numeric|min:0',
            'price_per_liter' => 'nullable|numeric|min:0',
            'total_price'     => 'nullable|numeric|min:0',
            'total_distance'  => 'nullable|numeric|min:0',
            'start_time'      => 'nullable|string',
            'end_time'        => 'nullable|string',
            'note'            => 'nullable|string|max:500',
        ]);

        $this->prepareData($validated);

        fuel_Logs::create($validated);

        return redirect()->route('oil')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    // =============================================
    // UPDATE
    // =============================================
    public function update(Request $request, $id)
    {
        $log = fuel_Logs::findOrFail($id);

        $validated = $request->validate([
            'work_date'       => 'required|date',
            'driver_name'     => 'required|string|max:100',
            'vehicle_id'      => 'required|string|max:50',
            'liters'          => 'nullable|numeric|min:0',
            'price_per_liter' => 'nullable|numeric|min:0',
            'total_price'     => 'nullable|numeric|min:0',
            'total_distance'  => 'nullable|numeric|min:0',
            'start_time'      => 'nullable|string',
            'end_time'        => 'nullable|string',
            'note'            => 'nullable|string|max:500',
        ]);

        $this->prepareData($validated);

        $log->update($validated);

        return redirect()->route('oil')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    // =============================================
    // DESTROY
    // =============================================
    public function destroy($id)
    {
        fuel_Logs::findOrFail($id)->delete();
        return redirect()->route('oil')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    // =============================================
    // PREV MILEAGE (AJAX)
    // =============================================
    public function prevMileage(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $workDate  = $request->input('work_date');
        $excludeId = $request->input('exclude_id');

        $query = fuel_Logs::where('vehicle_id', $vehicleId)
            ->where('work_date', '<', $workDate)
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $prev = $query->first();

        return response()->json([
            'data' => $prev ? $this->appendComputed($prev) : null,
        ]);
    }

    // =============================================
    // PREPARE DATA — จัดการ format ก่อน save
    // =============================================
    private function prepareData(array &$data): void
    {
        // work_date → Y-m-d string เสมอ
        if (!empty($data['work_date'])) {
            $data['work_date'] = Carbon::parse($data['work_date'])->format('Y-m-d');
        }

        // start_time / end_time
        // รองรับ column type TIME ('HH:MM:SS') และ DATETIME ('YYYY-MM-DD HH:MM:SS')
        // ตรวจ type ของ column จาก DB schema อัตโนมัติ
        foreach (['start_time', 'end_time'] as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
                continue;
            }
            $raw = trim($data[$field]);
            // ถ้าเป็น HH:MM หรือ HH:MM:SS ให้ต่อวันที่เข้าไปเลย
            // MySQL จะยอมรับทั้ง TIME และ DATETIME
            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $raw)) {
                $timeStr = strlen($raw) === 5 ? $raw . ':00' : $raw;
                // ส่งเป็น DATETIME เพื่อรองรับทั้ง 2 column type
                $data[$field] = $data['work_date'] . ' ' . $timeStr;
            }
        }

        // คำนวณ liters ถ้าว่าง
        if (empty($data['liters']) && !empty($data['total_price']) && !empty($data['price_per_liter'])) {
            $data['liters'] = round((float)$data['total_price'] / (float)$data['price_per_liter'], 2);
        }

        // null ค่าว่างทุก field ที่ optional
        $optionals = ['liters', 'price_per_liter', 'total_price', 'total_distance', 'note'];
        foreach ($optionals as $f) {
            if (isset($data[$f]) && $data[$f] === '') {
                $data[$f] = null;
            }
        }
    }

    // =============================================
    // APPEND COMPUTED
    // =============================================
    private function appendComputed(fuel_Logs $log): array
    {
        $dist = (float)($log->total_distance ?? 0);
        $lit  = (float)($log->liters ?? 0);
        $kml  = ($lit > 0 && $dist > 0) ? round($dist / $lit, 2) : 0;

        // work_date — รองรับทั้ง string และ Carbon
        $workDate = null;
        if ($log->work_date) {
            try {
                $workDate = Carbon::parse($log->work_date)->format('Y-m-d');
            } catch (\Exception $e) {
                $workDate = (string)$log->work_date;
            }
        }

        // start_time / end_time — ดึงเฉพาะ HH:MM ไปแสดงผล
        $startTime = null;
        $endTime   = null;
        $hours     = 0;
        if ($log->start_time) {
            try {
                $startTime = Carbon::parse($log->start_time)->format('H:i');
            } catch (\Exception $e) {}
        }
        if ($log->end_time) {
            try {
                $endTime = Carbon::parse($log->end_time)->format('H:i');
            } catch (\Exception $e) {}
        }
        if ($startTime && $endTime) {
            try {
                $s = Carbon::parse($log->start_time);
                $e = Carbon::parse($log->end_time);
                $diff = $s->diffInMinutes($e);
                if ($diff > 0) $hours = round($diff / 60, 2);
            } catch (\Exception $ex) {}
        }

        // created_at
        $createdAt = null;
        if ($log->created_at) {
            try {
                $createdAt = Carbon::parse($log->created_at)->format('d/m/Y H:i');
            } catch (\Exception $e) {}
        }

        return [
            'id'              => $log->id,
            'driver_name'     => $log->driver_name,
            'vehicle_id'      => $log->vehicle_id,
            'work_date'       => $workDate,
            'start_time'      => $startTime,
            'end_time'        => $endTime,
            'created_at'      => $createdAt,
            'total_distance'  => $dist > 0 ? $dist : null,
            'liters'          => $lit  > 0 ? $lit  : null,
            'total_price'     => (float)($log->total_price ?? 0) > 0 ? (float)$log->total_price : null,
            'price_per_liter' => (float)($log->price_per_liter ?? 0) > 0 ? (float)$log->price_per_liter : null,
            'note'            => $log->note,
            'km_per_liter'    => $kml,
            'work_hours'      => $hours,
        ];
    }
}