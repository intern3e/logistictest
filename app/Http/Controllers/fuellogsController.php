<?php

namespace App\Http\Controllers;

use App\Models\fuel_Logs;
use Illuminate\Http\Request;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    public function oil(Request $request)
    {
        $view         = $request->input('view', 'month');
        $filterDriver = $request->input('driver_name', 'all');
        $filterDay    = $request->input('date', '');
        $filterMonth  = $request->input('month', date('Y-m'));
        
        $baseQuery = fuel_Logs::query();

        // แก้ไขการ Query วันที่ให้แม่นยำขึ้น
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

        // Map ข้อมูลและคำนวณค่าต่างๆ ผ่านฟังก์ชันเดียวเพื่อความเป๊ะ
        $logs = $query->get()->map(fn($l) => $this->appendComputed($l));

        $metrics = null;
        if ($filterDriver !== 'all') {
            $metrics = [
                'total_liters'     => round($logs->sum('liters'), 1),
                'total_price'      => round($logs->sum('total_price')),
                'total_work_hours' => round($logs->sum('work_hours'), 1),
                // ป้องกันการหารด้วยศูนย์ในกรณีไม่มีข้อมูล Km/L
                'avg_km_per_liter' => round($logs->where('km_per_liter', '>', 0)->avg('km_per_liter') ?? 0, 1),
            ];
        }

        $allLogs      = fuel_Logs::orderBy('work_date')->get()->map(fn($l) => $this->appendComputed($l));
        $chartDrivers = $logs->pluck('driver_name')->unique()->sort()->values();

        // กราฟสรุปค่าใช้จ่ายแยกตามคนขับ
        $costByDriver = $chartDrivers->map(fn($d) => [
            'driver'      => $d,
            'total_price' => round($logs->where('driver_name', $d)->sum('total_price')),
        ])->filter(fn($d) => $d['total_price'] > 0)->values();

        // อัตราสิ้นเปลืองเฉลี่ย
        $kmlByDriver = $chartDrivers->map(function ($d) use ($logs) {
            $dl = $logs->where('driver_name', $d)->where('km_per_liter', '>', 0);
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
                'total_work_hours' => round($dl->sum('work_hours'), 1),
            ];
        })->sortKeys()->values();

        // ข้อมูลสำหรับการแก้ไข (Edit Modal)
        $editLogData = null;
        if ($request->filled('edit_id')) {
            $editLog = fuel_Logs::find($request->edit_id);
            if ($editLog) {
                $editLogData = $this->appendComputed($editLog);
            }
        }

        return view('driver.oil', compact(
            'logs', 'drivers', 'plates',
            'metrics', 'costByDriver', 'kmlByDriver', 'trend',
            'reportByDriver', 'reportAll',
            'view', 'filterDriver', 'filterDay', 'filterMonth',
            'editLogData'
        ));
    }

    // ฟังก์ชันคำนวณค่าต่างๆ (ย้ายตรรกะมาไว้ที่นี่ที่เดียว)
    private function appendComputed(fuel_Logs $log): array
    {
        // 1. คำนวณอัตราสิ้นเปลือง
        $dist = $log->total_distance > 0 ? $log->total_distance : 0;
        $kml = ($log->liters > 0 && $dist > 0) ? round($dist / $log->liters, 2) : 0;

        // 2. คำนวณชั่วโมงทำงาน (Start - End Time)
        $hours = 0;
        if ($log->start_time && $log->end_time) {
            $start = Carbon::parse($log->start_time);
            $end = Carbon::parse($log->end_time);
            $hours = round($start->diffInMinutes($end) / 60, 2);
        }

        return [
            'id'              => $log->id,
            'driver_name'     => $log->driver_name,
            'vehicle_id'      => $log->vehicle_id,
            'work_date'       => $log->work_date ? $log->work_date->toDateString() : null,
            'start_time'      => $log->start_time ? Carbon::parse($log->start_time)->format('H:i') : null,
            'end_time'        => $log->end_time ? Carbon::parse($log->end_time)->format('H:i') : null,
            'created_at'      => $log->created_at ? $log->created_at->format('d/m/Y H:i') : null,
            'total_distance'  => $log->total_distance,
            'liters'          => $log->liters,
            'total_price'     => $log->total_price,
            'price_per_liter' => $log->price_per_liter,
            'note'            => $log->note,
            'km_per_liter'    => $kml,
            'work_hours'      => $hours,
        ];
    }

    // ... ส่วนของ store, update, destroy ใช้โครงเดิมได้ แต่แนะนำให้ใช้ $this->rules() ...
}