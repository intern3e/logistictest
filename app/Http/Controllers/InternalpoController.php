<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use App\Models\internal_poline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternalpoController extends Controller
{
    const ALLOWED_USERS = [
        'test101',
    ];

    public function dashboard(Request $request)
    {
        $creator = $request->input('create_by');

        if (!in_array($creator, self::ALLOWED_USERS, true)) {
            abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');
        }

        $q = internal_poline::query();

        if ($request->filled('SONum')) {
            $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
        }

        $lines = $q->orderByRaw('FIELD(status, ?) DESC', [internal_poline::ST_PENDING])
            ->orderBy('internal_id')
            ->orderBy('id')
            ->get();

        $heads = internal_po::whereIn('internal_id', $lines->pluck('internal_id')->unique())
            ->get()->keyBy('internal_id');

        $locations = internal_poline::whereNotNull('item_location')
            ->where('item_location', '<>', '')
            ->orderBy('timestamp', 'desc')
            ->limit(200)
            ->pluck('item_location')
            ->unique()
            ->take(50)
            ->values();

        return view('internal_po.dashboard', compact('lines', 'heads', 'locations', 'creator'));
    }

    public function markFinish(Request $request)
    {
        $request->validate([
            'ids'      => 'required|array|min:1',
            'ids.*'    => 'integer',
            'location' => 'required|string|max:100',
            'user'     => 'required|string|max:100',
        ]);

        $user = $request->input('user');
        if (!in_array($user, self::ALLOWED_USERS, true)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        try {
            $updated = DB::transaction(function () use ($request, $user) {
                return internal_poline::whereIn('id', $request->input('ids'))
                    ->where('status', internal_poline::ST_PENDING)
                    ->update([
                        'item_location' => $request->input('location'),
                        'status'        => internal_poline::ST_FINISH,
                        'summit_by'     => $user,
                        'timestamp'     => Carbon::now()->toDateTimeString(),
                    ]);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่รอดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'จัดเสร็จ ' . $updated . ' รายการ']);
    }

    public function markCancel(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
            'user'  => 'required|string|max:100',
        ]);

        $user = $request->input('user');
        if (!in_array($user, self::ALLOWED_USERS, true)) {
            return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        try {
            $updated = DB::transaction(function () use ($request, $user) {
                return internal_poline::whereIn('id', $request->input('ids'))
                    ->where('status', internal_poline::ST_PENDING)
                    ->update([
                        'status'    => internal_poline::ST_CANCEL,
                        'summit_by' => $user,
                        'timestamp' => Carbon::now()->toDateTimeString(),
                    ]);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ยกเลิกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่รอดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ยกเลิก ' . $updated . ' รายการ']);
    }
}