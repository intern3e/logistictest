<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InternalPoController extends Controller
{
    const ALLOWED_USERS = ['test101'];
    const PRINTERS = [
        'TSC TTP-247 internal' => 'ภายใน',
        'TSC TTP-247 store'    => 'สโตร์',
        '\\\\ว้าล\\TSC TTP-247' => 'ภายนอก',
    ];

    private function allowed(?string $user): bool
    {
        return in_array($user, self::ALLOWED_USERS, true);
    }

    private function loadHeads(Request $request, ?array $statuses, string $todoStatus)
    {
        $q = internal_po::with('lines');

        if ($statuses !== null) {
            $q->whereIn('status', $statuses);
        }
        if ($request->filled('SONum')) {
            $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
        }

        return $q->orderByRaw('FIELD(status, ?) DESC', [$todoStatus])
            ->orderBy('internal_id')
            ->get();
    }

    private function recentLocations()
    {
        return internal_po::whereNotNull('location_at')
            ->where('location_at', '<>', '')
            ->orderBy('internal_id', 'desc')
            ->limit(200)
            ->pluck('location_at')
            ->unique()->take(50)->values();
    }

    public function pickDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $heads     = $this->loadHeads($request, null, internal_po::ST_PENDING);
        $locations = $this->recentLocations();
        $printers  = self::PRINTERS;

        return view('internal_po.dashboard', compact('heads', 'locations', 'creator', 'printers'));
    }

    public function pickSubmit(Request $request)
    {
        $request->validate([
            'ids'          => 'required|array|min:1',
            'ids.*'        => 'string',
            'user'         => 'required|string|max:100',
            'printer'      => 'required|string|in:' . implode(',', array_keys(self::PRINTERS)),
            'print_sheets' => 'nullable|integer|min:1|max:20',
        ]);

        $user = $request->input('user');
        if (!$this->allowed($user)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $ids         = $request->input('ids');
        $printer     = $request->input('printer');
        $printSheets = (int) $request->input('print_sheets', 1);

        try {
            [$updated, $heads] = DB::transaction(function () use ($ids) {
                $heads = internal_po::whereIn('internal_id', $ids)
                    ->where('status', internal_po::ST_PENDING)
                    ->lockForUpdate()
                    ->get(['internal_id', 'SO_id', 'POref', 'customer_name']);

                if ($heads->isEmpty()) {
                    return [0, $heads];
                }

                $n = internal_po::whereIn('internal_id', $heads->pluck('internal_id'))
                    ->update([
                        'status'  => internal_po::ST_FINISH,
                        'pick_by' => request('user'),
                        'pick_at' => Carbon::now()->toDateTimeString(),
                    ]);

                return [$n, $heads];
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'จัดเสร็จไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        $this->insertPrintWarehouse($heads, $printer, $printSheets);

        return response()->json([
            'ok'      => true,
            'message' => 'จัดเสร็จ ' . $updated . ' ใบ (สั่งพิมพ์ ' . $printSheets . ' แผ่น/ใบ ที่ ' . $printer . ')',
        ]);
    }

    public function markCancel(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'string',
            'user'  => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        try {
            $updated = DB::transaction(function () use ($request, $user) {
                return internal_po::whereIn('internal_id', $request->input('ids'))
                    ->where('status', internal_po::ST_PENDING)
                    ->update([
                        'status'  => internal_po::ST_CANCEL,
                        'pick_by' => $user,
                        'pick_at' => Carbon::now()->toDateTimeString(),
                    ]);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ยกเลิกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ยกเลิก ' . $updated . ' ใบ']);
    }

    /**
     * ยิง insert เข้า printwarehouse (mysql_3e) — รูปแบบเดียวกับ app mobile
     * แต่ละใบใน $heads พิมพ์ $printQty แผ่น (แยกต่อใบ ไม่รวมทั้งหมด)
     */
    private function insertPrintWarehouse($heads, string $printerName, int $printQty): void
    {
        $rows = [];
        foreach ($heads as $h) {
            for ($i = 0; $i < $printQty; $i++) {
                $rows[] = [
                    'SONum'        => $h->SO_id,
                    'PORef'        => $h->POref,
                    'CustName'     => $h->customer_name,
                    'Print_Qty'    => 1,
                    'Printed_Flag' => 'N',
                    'printerName'  => $printerName,
                ];
            }
        }

        if (!$rows) return;

        try {
            DB::connection('mysql_3e')->table('printwarehouse')->insert($rows);
        } catch (\Exception $e) {
            Log::error('insertPrintWarehouse failed: ' . $e->getMessage());
        }
    }
}