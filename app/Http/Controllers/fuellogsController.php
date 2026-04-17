<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a filtered, enriched collection of fuel_log rows.
     * Each row is a plain PHP array with pre-computed fields:
     *   start_time  → "H:i"  (Bangkok)
     *   end_time    → "H:i"  (Bangkok)
     *   work_hours  → float (hours, 2 dp)
     *   km_per_liter→ float (2 dp) or 0
     */
    private function buildLogs(Request $request): \Illuminate\Support\Collection
    {
        $view         = $request->get('view',        'month');
        $filterDay    = $request->get('date',        date('Y-m-d'));
        $filterMonth  = $request->get('month',       date('Y-m'));
        $filterYear   = $request->get('year',        date('Y'));
        $filterDriver = $request->get('driver_name', 'all');

        $query = DB::table('fuel_logs')
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc');

        // ── Date filter ──────────────────────────────────────────────────────
        if ($view === 'day') {
            $query->whereDate('work_date', $filterDay);

        } elseif ($view === 'month') {
            [$y, $m] = explode('-', $filterMonth . '-01');
            $query->whereYear('work_date', $y)
                  ->whereMonth('work_date', $m);

        } elseif ($view === 'year') {
            $query->whereYear('work_date', $filterYear);
        }
        // 'all' → no date filter

        // ── Driver filter ────────────────────────────────────────────────────
        if ($filterDriver !== 'all') {
            $query->where('driver_name', $filterDriver);
        }

        return $query->get()->map(function ($row) {
            $row = (array) $row;

            // ── Parse start / end times → "H:i" display strings ─────────────
            $startTime = null;
            $endTime   = null;
            $workHours = 0;

            if (!empty($row['start_time'])) {
                $startCarbon = Carbon::parse($row['start_time']);
                $startTime   = $startCarbon->format('H:i');
            }
            if (!empty($row['end_time'])) {
                $endCarbon = Carbon::parse($row['end_time']);
                $endTime   = $endCarbon->format('H:i');
            }

            // ── Work hours ───────────────────────────────────────────────────
            if (!empty($row['start_time']) && !empty($row['end_time'])) {
                $s    = Carbon::parse($row['start_time']);
                $e    = Carbon::parse($row['end_time']);
                $diff = $s->diffInMinutes($e, false);   // signed
                if ($diff > 0) {
                    $workHours = round($diff / 60, 2);
                }
            }

            // ── km/L ─────────────────────────────────────────────────────────
            $liters   = (float) ($row['liters']         ?? 0);
            $distance = (float) ($row['total_distance'] ?? 0);
            $kml      = ($liters > 0 && $distance > 0)
                        ? round($distance / $liters, 2) : 0;

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
                'total_price'     => (float) ($row['total_price']    ?? 0),
                'price_per_liter' => (float) ($row['price_per_liter'] ?? 0),
                'km_per_liter'    => $kml,
                'note'            => $row['note']       ?? '',
                'created_at'      => $row['created_at'] ?? null,
            ];
        });
    }

    /**
     * Parse "H:i" time strings + work_date into datetime strings for DB.
     * Handles overnight shifts (end < start → add 1 day to end).
     *
     * @return array [startDatetime|null, endDatetime|null]
     */
    private function parseTimes(?string $workDate, ?string $startStr, ?string $endStr): array
    {
        if (!$workDate) {
            return [null, null];
        }

        $startDt = null;
        $endDt   = null;

        if ($startStr && preg_match('/^\d{2}:\d{2}$/', $startStr)) {
            $startDt = Carbon::createFromFormat('Y-m-d H:i', "{$workDate} {$startStr}");
        }

        if ($endStr && preg_match('/^\d{2}:\d{2}$/', $endStr)) {
            $endDt = Carbon::createFromFormat('Y-m-d H:i', "{$workDate} {$endStr}");
            // Overnight shift
            if ($startDt && $endDt->lt($startDt)) {
                $endDt->addDay();
            }
        }

        return [
            $startDt ? $startDt->format('Y-m-d H:i:s') : null,
            $endDt   ? $endDt->format('Y-m-d H:i:s')   : null,
        ];
    }

    /**
     * Compute liters and price_per_liter from total_price + one other value.
     *
     * @return array [liters|null, price_per_liter|null]
     */
    private function calcLitersPpl($totalPrice, $pplInput, $litersInput): array
    {
        $tp  = (float) $totalPrice;
        $ppl = (float) $pplInput;
        $ltr = (float) $litersInput;

        // price_per_liter given → derive liters
        if ($ppl > 0 && $tp > 0) {
            return [round($tp / $ppl, 2), round($ppl, 2)];
        }

        // liters given → derive price_per_liter
        if ($ltr > 0 && $tp > 0) {
            return [round($ltr, 2), round($tp / $ltr, 2)];
        }

        return [$ltr > 0 ? round($ltr, 2) : null, $ppl > 0 ? round($ppl, 2) : null];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /oil  — main listing page
    // ─────────────────────────────────────────────────────────────────────────
    public function oil(Request $request)
    {
        $view         = $request->get('view',        'month');
        $filterDay    = $request->get('date',        date('Y-m-d'));
        $filterMonth  = $request->get('month',       date('Y-m'));
        $filterDriver = $request->get('driver_name', 'all');

        $logs = $this->buildLogs($request);

        // ── Dropdown lists for the filter bar & modals ───────────────────────
        $drivers = DB::table('fuel_logs')
            ->distinct()->orderBy('driver_name')
            ->pluck('driver_name')->filter()->values()->toArray();

        $plates = DB::table('fuel_logs')
            ->distinct()->orderBy('vehicle_id')
            ->pluck('vehicle_id')->filter()->values()->toArray();

        // ── Summary metrics card ─────────────────────────────────────────────
        $metrics = null;
        if ($logs->count() > 0) {
            $kmlValues = $logs->filter(fn($r) => ($r['km_per_liter'] ?? 0) > 0)
                              ->pluck('km_per_liter');

            $metrics = [
                'total_liters'     => round($logs->sum('liters'), 2),
                'total_price'      => $logs->sum('total_price'),
                'avg_km_per_liter' => $kmlValues->count()
                                      ? round($kmlValues->avg(), 2) : 0,
                'total_work_hours' => round($logs->sum('work_hours'), 2),
            ];
        }

        // ── Bar chart: cost per driver ───────────────────────────────────────
        $costByDriver = $logs
            ->groupBy('driver_name')
            ->map(fn($g, $d) => [
                'driver'      => $d,
                'total_price' => round($g->sum('total_price'), 2),
            ])
            ->sortByDesc('total_price')
            ->values()
            ->toArray();

        // ── Bar chart: km/L per driver ───────────────────────────────────────
        $kmlByDriver = $logs
            ->groupBy('driver_name')
            ->map(function ($g, $d) {
                $kv = $g->filter(fn($r) => $r['km_per_liter'] > 0)
                        ->pluck('km_per_liter');
                return [
                    'driver'       => $d,
                    'km_per_liter' => $kv->count() ? round($kv->avg(), 2) : 0,
                ];
            })
            ->filter(fn($d) => $d['km_per_liter'] > 0)
            ->values()
            ->toArray();

        // ── Delivery stats (plug in your own source here) ────────────────────
        $deliveryStats = null;

        // ── Edit log (populated on validation failure for edit flow) ─────────
        $editLog = null;

        return view('driver.oil', compact(
            'logs',
            'view',
            'filterDay',
            'filterMonth',
            'filterDriver',
            'drivers',
            'plates',
            'metrics',
            'costByDriver',
            'kmlByDriver',
            'deliveryStats',
            'editLog'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /oil  — store new record
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'work_date'      => 'required|date',
            'driver_name'    => 'required|string|max:100',
            'vehicle_id'     => 'required|string|max:50',
            'total_price'    => 'required|numeric|min:0.01',
            'total_distance' => 'nullable|numeric|min:0',
            'liters'         => 'nullable|numeric|min:0',
            'price_per_liter'=> 'nullable|numeric|min:0',
        ]);

        [$startDt, $endDt] = $this->parseTimes(
            $request->work_date,
            $request->start_time,
            $request->end_time
        );

        [$liters, $ppl] = $this->calcLitersPpl(
            $request->total_price,
            $request->price_per_liter,
            $request->liters
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
            'note'            => $request->note ? trim($request->note) : null,
            'created_at'      => now(),
        ]);

        return redirect()->route('oil')->with('success', 'บันทึกข้อมูลน้ำมันสำเร็จ ✅');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /oil/update/{id}  — update existing record
    // ─────────────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'work_date'      => 'required|date',
            'driver_name'    => 'required|string|max:100',
            'vehicle_id'     => 'required|string|max:50',
            'total_price'    => 'required|numeric|min:0.01',
            'total_distance' => 'nullable|numeric|min:0',
            'liters'         => 'nullable|numeric|min:0',
            'price_per_liter'=> 'nullable|numeric|min:0',
        ]);

        // Verify the record exists
        abort_unless(
            DB::table('fuel_logs')->where('id', $id)->exists(),
            404,
            'ไม่พบรายการที่ต้องการแก้ไข'
        );

        [$startDt, $endDt] = $this->parseTimes(
            $request->work_date,
            $request->start_time,
            $request->end_time
        );

        [$liters, $ppl] = $this->calcLitersPpl(
            $request->total_price,
            $request->price_per_liter,
            $request->liters
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
            'note'            => $request->note ? trim($request->note) : null,
        ]);

        return redirect()->route('oil')->with('success', 'อัปเดตข้อมูลสำเร็จ ✅');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /oil/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $deleted = DB::table('fuel_logs')->where('id', $id)->delete();

        if (!$deleted) {
            return redirect()->route('oil')->with('error', 'ไม่พบรายการที่ต้องการลบ');
        }

        return redirect()->route('oil')->with('success', 'ลบข้อมูลเรียบร้อย');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /oil/prev-mileage  — AJAX: previous log for a vehicle
    // ─────────────────────────────────────────────────────────────────────────
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
            ->where('work_date',  '<', $workDate)
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc');

        if ($excludeId) {
            $query->where('id', '!=', (int) $excludeId);
        }

        $prev = $query->first();

        return response()->json([
            'success' => (bool) $prev,
            'data'    => $prev ? [
                'work_date'      => $prev->work_date,
                'total_distance' => $prev->total_distance,
            ] : null,
        ]);
    }
}