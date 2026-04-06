<?php

namespace App\Http\Controllers;

use App\Models\fuel_Logs;
use Illuminate\Http\Request;

class fuellogsController extends Controller
{
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
            [$y, $m] = explode('-', $filterMonth);
            $baseQuery->whereYear('work_date', $y)->whereMonth('work_date', $m);
        } elseif ($view === 'year' && $filterMonth) {
            $baseQuery->whereYear('work_date', substr($filterMonth, 0, 4));
        }
        $drivers = fuel_Logs::select('driver_name')
            ->distinct()
            ->orderBy('driver_name')
            ->pluck('driver_name');
        $plates = fuel_Logs::select('vehicle_id')->distinct()->orderBy('vehicle_id')->pluck('vehicle_id');
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
                'total_work_hours' => round($logs->sum(fn($l) => $l['work_hours']), 1),
                'avg_km_per_liter' => round($logs->filter(fn($l) => $l['km_per_liter'] > 0)->avg('km_per_liter') ?? 0, 1),
            ];
        }

        $allLogs      = fuel_Logs::orderBy('work_date')->get()->map(fn($l) => $this->appendComputed($l));
        $chartDrivers = $logs->pluck('driver_name')->unique()->sort()->values();

        $costByDriver = $chartDrivers->map(fn($d) => [
            'driver'      => $d,
            'total_price' => round($logs->where('driver_name', $d)->sum('total_price')),
        ])->filter(fn($d) => $d['total_price'] > 0)->values();

        $kmlByDriver = $chartDrivers->map(function ($d) use ($logs) {
            $dl = $logs->where('driver_name', $d)->filter(fn($l) => $l['km_per_liter'] > 0);
            return $dl->count() > 0 ? ['driver' => $d, 'km_per_liter' => round($dl->avg('km_per_liter'), 2)] : null;
        })->filter()->values();

        $trendSrc = $filterDriver !== 'all' ? $allLogs->where('driver_name', $filterDriver) : $allLogs;
        $trend    = $trendSrc->groupBy(fn($l) => substr($l['work_date'], 0, 7))
            ->map(fn($g, $m) => ['month' => $m, 'total_price' => round($g->sum('total_price'))])
            ->sortKeys()->values();

        $reportAll      = fuel_Logs::all()->map(fn($l) => $this->appendComputed($l));
        $reportByDriver = $reportAll->groupBy('driver_name')->map(function ($dl, $d) {
            return [
                'driver_name'      => $d,
                'total_price'      => round($dl->sum('total_price')),
                'total_liters'     => round($dl->sum('liters'), 1),
                'total_work_hours' => round($dl->sum(fn($l) => $l['work_hours']), 1),
            ];
        })->sortKeys()->values();

        $editLog     = null;
        $editLogData = null;
        if ($request->filled('edit_id')) {
            $editLog = fuel_Logs::find($request->edit_id);
            if ($editLog) {
                $editLogData = [
                    'id'              => $editLog->id,
                    'work_date'       => $editLog->work_date->toDateString(),
                    'driver_name'     => $editLog->driver_name,
                    'vehicle_id'      => $editLog->vehicle_id,
                    'start_time'      => $editLog->start_time?->format('H:i'),
                    'end_time'        => $editLog->end_time?->format('H:i'),
                    'start_mileage'   => $editLog->start_mileage,
                    'total_distance'  => $editLog->total_distance,
                    'liters'          => $editLog->liters,
                    'total_price'     => $editLog->total_price,
                    'price_per_liter' => $editLog->price_per_liter,
                    'note'            => $editLog->note,
                ];
            }
        }

        return view('wrongitem.oil', compact(
            'logs', 'drivers', 'plates',
            'metrics', 'costByDriver', 'kmlByDriver', 'trend',
            'reportByDriver', 'reportAll',
            'view', 'filterDriver', 'filterDay', 'filterMonth',
            'editLog', 'editLogData'
        ));
    }
    public function getKmPerLiterAttribute()
    {
        $dist = $this->total_distance > 0 ? $this->total_distance : $this->distance;
        if ($this->liters > 0 && $dist > 0) {
            return round($dist / $this->liters, 2);
        }
        return 0;
    }
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());
        fuel_Logs::create($validated);

        return redirect()->route('oil')->with('success', '✅ บันทึกข้อมูลสำเร็จ');
    }

    public function update(Request $request, int $id)
    {
        $log       = fuel_Logs::findOrFail($id);
        $validated = $request->validate($this->rules(), $this->messages());

        $log->update($validated);

        return redirect()->route('oil')->with('success', '✅ อัพเดตข้อมูลสำเร็จ');
    }
    public function destroy(int $id)
    {
        fuel_Logs::findOrFail($id)->delete();

        return redirect()->route('oil')->with('success', '✅ ลบรายการสำเร็จ');
    }
    public function prevMileage(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|string',
            'work_date'  => 'required|date',
            'exclude_id' => 'nullable|integer',
        ]);

        $query = fuel_Logs::where('vehicle_id', $request->vehicle_id)
            ->where('work_date', '<=', $request->work_date);

        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $prev = $query->orderBy('work_date', 'desc')->orderBy('id', 'desc')->first();

        return response()->json([
            'data' => $prev ? [
                'id'            => $prev->id,
                'work_date'     => $prev->work_date->toDateString(),
                'start_mileage' => $prev->start_mileage,
            ] : null,
        ]);
    }
    private function appendComputed(fuel_Logs $log): array
    {
        return [
            'id'            => $log->id,
            'driver_name'   => $log->driver_name,
            'vehicle_id'    => $log->vehicle_id,
            'work_date'     => $log->work_date->toDateString(),
            'start_time'    => $log->start_time?->format('H:i'),
            'end_time'      => $log->end_time?->format('H:i'),
            'start_mileage' => $log->start_mileage,
            'created_at'     => $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') : null,
            'total_distance' => $log->total_distance,
            'liters'        => $log->liters,
            'total_price'   => $log->total_price,
            'note'          => $log->note,
            'distance'      => $log->distance,
            'km_per_liter'  => $log->km_per_liter,
            'work_hours'    => $log->work_hours,
            'price_per_liter' => $log->price_per_liter,
        ];
    }

    private function rules(): array
    {
        return [
            'driver_name'   => 'required|string|max:100',
            'vehicle_id'    => 'required|string|max:50',
            'work_date'     => 'required|date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'start_mileage' => 'required|numeric|min:0',
            'total_distance' => 'nullable|numeric|min:0.1',
            'liters'        => 'required|numeric|min:0.1',
            'total_price'   => 'required|numeric|min:0',
            'note'          => 'nullable|string|max:500',
            'price_per_liter' => 'nullable|numeric|min:0'
        ];
    }

    private function messages(): array
    {
        return [
            'driver_name.required'   => 'กรุณาเลือกคนขับ',
            'vehicle_id.required'    => 'กรุณาเลือกทะเบียนรถ',
            'work_date.required'     => 'กรุณาระบุวันที่ทำงาน',
            'work_date.date'         => 'รูปแบบวันที่ไม่ถูกต้อง',
            'start_time.required'    => 'กรุณาระบุเวลาเริ่มต้น',
            'start_time.date_format' => 'รูปแบบเวลาต้องเป็น HH:MM',
            'end_time.required'      => 'กรุณาระบุเวลาสิ้นสุด',
            'end_time.date_format'   => 'รูปแบบเวลาต้องเป็น HH:MM',
            'end_time.after'         => 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น',
            'start_mileage.required' => 'กรุณาระบุเลขไมล์เริ่มต้น',
            'start_mileage.numeric'  => 'เลขไมล์ต้องเป็นตัวเลข',
            'liters.required'        => 'กรุณาระบุจำนวนลิตร',
            'liters.numeric'         => 'จำนวนลิตรต้องเป็นตัวเลข',
            'liters.min'             => 'จำนวนลิตรต้องมากกว่า 0',
            'total_price.required'   => 'กรุณาระบุค่าน้ำมันรวม',
            'total_price.numeric'    => 'ค่าน้ำมันต้องเป็นตัวเลข',
        ];
    }
}