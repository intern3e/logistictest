<?php

namespace App\Http\Controllers;

use App\Models\ng_shipment;
use App\Models\Bill;
use App\Models\Docbills;
use App\Models\transaction_delivery;
use App\Models\UserAuth;
use App\Models\SsoTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class fuellogsController extends Controller
{
    const DELI_STATUS_OK    = 'จัดส่งสำเร็จ';
    const DELI_STATUS_WRONG = 'สินค้าผิด';
    const DELI_STATUS_REDO  = 'ส่งใหม่วันพรุ่งนี้';
    const DELI_STATUS_HOLD  = 'ค้างบิล';

    /* ==================== AUTH ====================
     * สองระดับสิทธิ์:
     *   - resolveOilUser()   : แค่ต้อง login (role ใดก็ได้) — ใช้กับหน้า/endpoint ที่ "ดูได้"
     *   - resolveOilEditor() : login + ต้องเป็น role admin/store/accounting เท่านั้น —
     *                          ใช้กับ endpoint ที่ "บันทึก/แก้ไข/ลบ/ยืนยันข้อมูล"
     */

    /**
     * ตรวจ login เท่านั้น (รองรับ SSO ticket แบบเดียวกับ StoreController::resolveSsoUser)
     * ไม่ login เลย -> redirect ไปหน้า login. role ใดก็เข้าดูหน้าได้ ไม่มีการเช็ค role ที่นี่
     */
    private function resolveOilUser(Request $request): UserAuth
    {
        $ticket = $request->input('ticket');

        if ($ticket && !Auth::guard('web')->check()) {
            $ticketRecord = SsoTicket::where('ticket', $ticket)
                ->where('client_key', '3e')
                ->first();

            if ($ticketRecord && $ticketRecord->markAsUsed()) {
                $user = UserAuth::find($ticketRecord->id_emp);
                if ($user && $user->is_active) {
                    Auth::guard('web')->login($user);
                }
            }
        }

        if (!Auth::guard('web')->check()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->guest(route('login'))
            );
        }

        return Auth::guard('web')->user();
    }

    /** role ที่มีสิทธิ์บันทึก/แก้ไข/ลบ/ยืนยันข้อมูลในระบบน้ำมัน */
    private function oilEditableRoles(): array
    {
        return ['admin', 'store', 'accounting'];
    }

    private function isOilEditor($user): bool
    {
        return $user && in_array($user->role, $this->oilEditableRoles(), true);
    }

    /**
     * ตรวจ login + ต้องเป็น role admin/store/accounting เท่านั้น
     * login แล้วแต่ role ไม่ผ่าน -> abort 403
     * ใช้กับ action ที่มีผลเปลี่ยนแปลงข้อมูล (store/update/destroy/confirmDelivery/updateCell/cleanupGarbage)
     */
    private function resolveOilEditor(Request $request): UserAuth
    {
        $user = $this->resolveOilUser($request);

        if (!$this->isOilEditor($user)) {
            abort(403, 'คุณไม่มีสิทธิ์บันทึก/แก้ไขข้อมูล (ต้องเป็น admin, store หรือ accounting)');
        }

        return $user;
    }

    private function oilUserName($user): string
    {
        return $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');
    }

    private function urlParams(Request $request): array
    {
        // เดิมพก create_by ผ่าน query string — ตอนนี้ผู้ใช้มาจาก Auth::guard('web') จริง
        // ไม่จำเป็นต้องพก create_by ใน URL อีกต่อไป คงฟังก์ชันนี้ไว้เผื่อโค้ดอื่นเรียกใช้อยู่
        return [];
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
            return redirect()->route('oil.report');
        }
        if ($request->input('redirect_to') === 'admin') {
            return redirect()->route('oil.admin');
        }

        return redirect()->route('oil');
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

    /* ==================== หน้าเว็บหลัก ==================== */

    public function oil(Request $request)
    {
        // ดูหน้านี้ได้ทุก role ที่ login แล้ว — isPrivileged เท่านั้นที่กำหนดว่าแก้ไข/บันทึกได้หรือไม่
        $authUser = $this->resolveOilUser($request);

        $data = $this->buildViewData($request);
        $data['authUser']     = $authUser;
        $data['currentUser']  = $this->oilUserName($authUser);
        $data['isPrivileged'] = $this->isOilEditor($authUser);

        return view('driver.oil', $data);
    }

    public function report(Request $request)
    {
        $authUser = $this->resolveOilUser($request);

        $data = $this->buildViewData($request);
        $data['creator']      = $this->oilUserName($authUser);
        $data['isPrivileged'] = $this->isOilEditor($authUser);

        return view('driver.report', $data);
    }

    public function admin(Request $request)
    {
        $this->resolveOilUser($request);
        return view('driver.admin', $this->buildViewData($request));
    }

    /* ==================== บันทึกน้ำมัน ==================== */

public function store(Request $request)
{
    $authUser = $this->resolveOilEditor($request); // บังคับ role admin/store/accounting

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
            return redirect()->route('oil')->with('success', 'มีข้อมูลอยู่แล้ว');
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
        // ⭐ ชื่อผู้บันทึกข้อมูล มาจาก user ที่ login จริงเสมอ ไม่รับจาก client
        'emp_name'        => $this->oilUserName($authUser),
        'created_at'      => now(),
    ]);

    return redirect()->route('oil')->with('success', 'บันทึกข้อมูลน้ำมันสำเร็จ ✅');
}
public function update(Request $request, $id)
{
    $authUser = $this->resolveOilEditor($request); // ⭐ ต้อง capture ผู้ใช้ไว้

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
        'emp_name'        => $this->oilUserName($authUser), // ⭐ จดชื่อผู้แก้ไขล่าสุด
    ]);

    return redirect()->route('oil')->with('success', 'อัปเดตข้อมูลสำเร็จ ✅');
}
    public function destroy(Request $request, $id)
    {
        $this->resolveOilEditor($request);

        $deleted = DB::table('fuel_logs')->where('id', $id)->delete();
        if (!$deleted) {
            return redirect()->route('oil')->with('error', 'ไม่พบรายการที่ต้องการลบ');
        }
        return redirect()->route('oil')->with('success', 'ลบข้อมูลเรียบร้อย');
    }

    public function cleanupGarbage(Request $request)
    {
        $this->resolveOilEditor($request);

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
        return redirect()->route('oil')->with('success', "ลบข้อมูลขยะ {$count} รายการ");
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
        $this->resolveOilUser($request);

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

    /* ==================== Fallback: งานที่ถูกจ่ายจาก DB (transaction_transport) ==================== */

    /**
     * ดึงงานที่ถูกจ่ายไว้แล้วจากตาราง transaction_transport (ผ่าน model transaction_delivery)
     * สำหรับวันที่ระบุ ใช้เป็น fallback เมื่อ API ภายนอก (getDeliveryPersonByDate) ไม่มีข้อมูล/ล่ม
     *
     * รวมรายการฝั่ง "บิล" ตาม billid (ไม่ใช่ so_detail_id ที่เป็น PK) เพราะ billid สามารถซ้ำได้
     * (บิลเดียวกันมีได้หลายแถวใน tblbill) — ถ้าไม่รวม จะเห็นบิลเดียวกันโผล่ซ้ำหลายการ์ดในหน้า UI
     * ฝั่ง "เอกสาร" ใช้ doc_id ตรงๆ เพราะเป็นทั้ง PK และเลขบิลในตัวเอง ไม่มีปัญหาซ้ำ
     */
    public function jobsFallback(Request $request)
    {
        // ดูรายการงานได้ทุก role ที่ login แล้ว (ปุ่ม "รับบิล" ฝั่ง UI จะโชว์เฉพาะ role ที่แก้ไขได้อยู่แล้ว
        // เพราะแผงงานทั้งหมดถูกซ่อนไว้ใต้ $isPrivileged ในหน้า blade)
        $this->resolveOilUser($request);

        $date = $request->get('date');
        if (!$date) {
            return response()->json(['data' => []]);
        }

        $deliveries = transaction_delivery::whereDate('time_pick', $date)->get();
        if ($deliveries->isEmpty()) {
            return response()->json(['data' => [], 'source' => 'db']);
        }

        $ids = $deliveries->pluck('bill_id')->unique()->values();

        // billid ไม่ใช่ PK ของ tblbill (so_detail_id ถึงเป็น PK) — บิลเดียวกัน (billid ซ้ำ)
        // อาจมีได้หลายแถว/หลาย so_detail_id
        $billsBySoDetailId = Bill::whereIn('so_detail_id', $ids)
            ->get(['so_detail_id', 'billid', 'so_id', 'customer_id', 'customer_name', 'statusdeli'])
            ->keyBy('so_detail_id');

        // docbills: doc_id เป็นทั้ง PK และเลขบิลในตัวเอง ไม่มีปัญหาซ้ำแบบ billid
        $docs = Docbills::whereIn('doc_id', $ids)
            ->get(['doc_id', 'id_com', 'com_name', 'contact_name', 'statusdeli'])
            ->keyBy('doc_id');

        $grouped = $deliveries->groupBy(function ($d) {
            $name = trim((string) $d->driver_name);
            return $name !== '' ? $name : 'ไม่ระบุ';
        });

        $data = $grouped->map(function ($driverDeliveries, $driverName) use ($billsBySoDetailId, $docs) {
            $jobs = collect();

            $billDeliveries    = $driverDeliveries->filter(fn ($d) => $billsBySoDetailId->has($d->bill_id));
            $docDeliveries     = $driverDeliveries->filter(fn ($d) => $docs->has($d->bill_id));
            $unknownDeliveries = $driverDeliveries->reject(
                fn ($d) => $billsBySoDetailId->has($d->bill_id) || $docs->has($d->bill_id)
            );

            // ── ฝั่งบิล: รวมตาม billid (ไม่ใช่ so_detail_id) — บิลเดียวกันแม้มีหลายแถว (หลาย
            //    so_detail_id) ก็รวมเป็นการ์ดงานเดียว กันไม่ให้ user เห็นบิลเดียวกันโผล่ซ้ำหลายใบ ──
            $billDeliveries
                ->groupBy(fn ($d) => $billsBySoDetailId->get($d->bill_id)->billid)
                ->each(function ($rows, $billid) use ($billsBySoDetailId, $jobs) {
                    $first = $rows->sortByDesc('time_pick')->first();
                    $b     = $billsBySoDetailId->get($first->bill_id);
                    $jobs->push([
                        'job_key'         => 'bill:' . $billid,
                        'bill_no'         => (string) $billid,
                        'so_id'           => (string) ($b->so_id ?? ''),
                        'customer_name'   => (string) ($b->customer_name ?? ''),
                        'bill_in_by'      => '',
                        'delivery_status' => (string) ($first->status ?? ''),
                        'reason'          => (string) ($first->note ?? ''),
                        'check_name'      => $first->check_name,
                        'check_time'      => optional($first->check_time)->format('Y-m-d H:i'),
                        'confirmed'       => !empty($first->check_time),
                    ]);
                });

            // ── ฝั่งเอกสาร: doc_id ไม่ซ้ำ ใช้ตรงๆ ได้เลย ──
            $docDeliveries->each(function ($d) use ($docs, $jobs) {
                $doc = $docs->get($d->bill_id);
                $jobs->push([
                    'job_key'         => 'doc:' . $d->bill_id,
                    'bill_no'         => (string) $d->bill_id,
                    'so_id'           => '',
                    'customer_name'   => (string) ($doc->com_name ?? ''),
                    'bill_in_by'      => (string) ($doc->contact_name ?? ''),
                    'delivery_status' => (string) ($d->status ?? ''),
                    'reason'          => (string) ($d->note ?? ''),
                    'check_name'      => $d->check_name,
                    'check_time'      => optional($d->check_time)->format('Y-m-d H:i'),
                    'confirmed'       => !empty($d->check_time),
                ]);
            });

            // งานกำพร้า (ไม่พบทั้งสองตาราง) — แสดงไว้แต่กดรับบิลไม่ได้ (job_key = unknown:)
            $unknownDeliveries->each(function ($d) use ($jobs) {
                $jobs->push([
                    'job_key'         => 'unknown:' . $d->bill_id,
                    'bill_no'         => (string) $d->bill_id,
                    'so_id'           => '',
                    'customer_name'   => '',
                    'bill_in_by'      => '',
                    'delivery_status' => (string) ($d->status ?? ''),
                    'reason'          => (string) ($d->note ?? ''),
                    'check_name'      => $d->check_name,
                    'check_time'      => optional($d->check_time)->format('Y-m-d H:i'),
                    'confirmed'       => !empty($d->check_time),
                ]);
            });

            return ['bill_out_by' => $driverName, 'jobs' => $jobs->values()];
        })->values();

        return response()->json(['data' => $data, 'source' => 'db']);
    }
    public function confirmDelivery(Request $request)
    {
        $authUser = $this->resolveOilEditor($request);

        $request->validate([
            'job_key'   => 'required|string',
            'status'    => 'required|string|in:จัดส่งสำเร็จ,สินค้าผิด,ส่งใหม่วันพรุ่งนี้,ค้างบิล',
            'ng_detail' => 'nullable|string|max:1000',
            'work_date' => 'nullable|date',
        ]);

        if ($request->status === self::DELI_STATUS_WRONG && !$request->filled('ng_detail')) {
            return response()->json(['success' => false, 'message' => 'กรุณากรอกรายละเอียดสินค้าที่ผิด'], 422);
        }

        [$type, $rawId] = array_pad(explode(':', $request->job_key, 2), 2, null);
        if (!$rawId || !in_array($type, ['bill', 'doc'], true)) {
            return response()->json(['success' => false, 'message' => 'ไม่พบรายการนี้ (ไม่รองรับงานจาก API ภายนอก)'], 404);
        }

        $billid = null;

        if ($type === 'bill') {
            // $rawId คือ billid (ไม่ใช่ so_detail_id) — บิลเดียวกันอาจมีหลาย so_detail_id
            $billid = $rawId;
            $soIds  = Bill::where('billid', $billid)->pluck('so_detail_id');
            if ($soIds->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'ไม่พบเลขบิลนี้'], 404);
            }
            $deliveries = transaction_delivery::whereIn('bill_id', $soIds)->get();
        } else {
            // docbills: doc_id เป็น PK และเลขบิลในตัวเอง ไม่ต้องแปลง
            $deliveries = transaction_delivery::where('bill_id', $rawId)->get();
        }

        if ($deliveries->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'ไม่พบรายการจ่ายงานนี้'], 404);
        }

        $userName = $this->oilUserName($authUser);
        $now      = Carbon::now();

        DB::transaction(function () use ($request, $type, $billid, $rawId, $deliveries, $userName, $now) {
            // ── อัปเดตสถานะทุกแถว transaction_delivery ที่เกี่ยวข้อง (ทุก so_detail_id ในบิลเดียวกัน) ──
            foreach ($deliveries as $delivery) {
                $delivery->status     = $request->status;
                $delivery->check_name = $userName;
                $delivery->check_time = $now;
                if ($request->filled('ng_detail')) {
                    $delivery->note = $request->ng_detail;
                }
                $delivery->save();
            }

            $update = ['statusdeli' => $request->status];
            if ($request->status === self::DELI_STATUS_WRONG) {
                $update['NG'] = $request->ng_detail;
            }

            if ($type === 'bill') {
                // billid ไม่ใช่ pk ของ tblbill → update ทุกแถวที่ billid ตรงกันในคำสั่งเดียว
                DB::table('tblbill')->where('billid', $billid)->update($update);
            } else {
                // doc_id เป็น pk อยู่แล้ว update แถวเดียวตรงๆ
                DB::table('docbills')->where('doc_id', $rawId)->update($update);
            }

            // ── ส่งใหม่พรุ่งนี้: สร้าง transaction_delivery ใหม่ 1 แถวต่อ so_detail_id เดิม
            //    (ระดับความละเอียดเดียวกับที่ DeliverytrackController ใช้จ่ายงานอยู่แล้ว)
            //    ผู้รับผิดชอบ/วิธีขนส่งใช้ของเดิม ผู้จ่ายงานรอบใหม่ = คนที่กดบันทึกว่า
            //    "ส่งใหม่พรุ่งนี้" (name_pick) เพื่อให้สอดคล้องกับหน้าจ่ายงาน (/deliverytrack) ──
            if ($request->status === self::DELI_STATUS_REDO) {
                $baseDate = $request->filled('work_date') ? Carbon::parse($request->work_date) : Carbon::now();
                $redoTime = $baseDate->copy()->addDay()->setTime(9, 0, 0);

                foreach ($deliveries as $delivery) {
                    transaction_delivery::create([
                        'bill_id'        => $delivery->bill_id,
                        'name_pick'      => $userName,
                        'time_pick'      => $redoTime,
                        'transport_name' => $delivery->transport_name,
                        'driver_name'    => $delivery->driver_name,
                        'check_name'     => null,
                        'check_time'     => null,
                        'status'         => '0',
                        'note'           => 'ส่งใหม่อัตโนมัติจากรายการเดิม (id ' . $delivery->id . ')',
                    ]);
                }
            }
        });

        return response()->json([
            'success'    => true,
            'status'     => $request->status,
            'check_name' => $userName,
            'check_time' => $now->format('Y-m-d H:i'),
        ]);
    }

    /* ==================== ค่าวิ่ง/OT/ค่ายก (ตารางแยกรายวัน) ==================== */

    private array $motorcycleVehicleIds = [];

    public function Deliveryfee(Request $request)
    {
        $authUser = $this->resolveOilUser($request);
        

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
                'db_name'      => $dbName,
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
            'creator'       => $this->oilUserName($authUser),
            'isPrivileged'  => $this->isOilEditor($authUser), // ⭐ เพิ่มบรรทัดนี้
        ]);
    }

public function updateCell(Request $request)
{
    $authUser = $this->resolveOilEditor($request); // ⭐ บังคับ role + capture ผู้ใช้

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
    $empName    = $this->oilUserName($authUser); // ⭐

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
        DB::table('fuel_logs')->where('id', $lastId)->update([
            $dbField   => $value,
            'emp_name' => $empName, // ⭐
        ]);

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
            'driver_name'     => $driverName,
            'vehicle_id'      => $lastVehicle,
            'work_date'       => $workDate,
            'total_price'     => 0,
            'total_distance'  => 0,
            'liters'          => 0,
            'price_per_liter' => 0,
            'delivery_cost'   => $field === 'delivery' ? $value : 0,
            'ot_cost'         => $field === 'ot' ? $value : 0,
            'handling_cost'   => $field === 'handling' ? $value : 0,
            'ok'              => 0,
            'ng'              => 0,
            'emp_name'        => $empName, // ⭐
            'created_at'      => now(),
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
        $authUser = $this->resolveOilUser($request);

        return view('driver.service', [
            'creator'      => $this->oilUserName($authUser),
            'isPrivileged' => $this->isOilEditor($authUser),
        ]);
    }
}