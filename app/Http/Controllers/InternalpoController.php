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

    private function allowed(?string $user): bool
    {
        return in_array($user, self::ALLOWED_USERS, true);
    }

    /* โหลดรายการตามสถานะที่กำหนด + ดันงาน "ที่ยังไม่กด" ขึ้นบน */
    private function loadLines(Request $request, ?array $statuses, string $todoStatus)
    {
        $q = internal_poline::query();

        if ($statuses !== null) {
            $q->whereIn('status', $statuses);          // ด่าน 2/3 ต้องผ่านด่านก่อนหน้าก่อนถึงจะเห็น
        }
        if ($request->filled('SONum')) {
            $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
        }

        return $q->orderByRaw('FIELD(status, ?) DESC', [$todoStatus])
            ->orderBy('internal_id')
            ->orderBy('id')
            ->get();
    }

    private function decorate($lines): array
    {
        $heads = internal_po::whereIn('internal_id', $lines->pluck('internal_id')->unique())
            ->get()->keyBy('internal_id');

        $locations = internal_poline::whereNotNull('item_location')
            ->where('item_location', '<>', '')
            ->orderBy('timestamp', 'desc')
            ->limit(200)
            ->pluck('item_location')
            ->unique()->take(50)->values();

        return [$heads, $locations];
    }

    /* ตัวช่วยเปลี่ยนสถานะ (transaction + ข้อความแบบเดิม) */
    private function applyTransition(array $ids, string $from, array $updates, string $okWord)
    {
        try {
            $updated = DB::transaction(function () use ($ids, $from, $updates) {
                return internal_poline::whereIn('id', $ids)
                    ->where('status', $from)          // กันกดซ้ำ / กดข้ามด่าน
                    ->update($updates);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => $okWord . 'ไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => $okWord . ' ' . $updated . ' รายการ']);
    }

    /* ==================== ด่าน 1: จัดเสร็จ ==================== */
    public function pickDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $lines = $this->loadLines($request, null, internal_poline::ST_PENDING); // ด่าน 1 โหลดทุกสถานะ
        [$heads, $locations] = $this->decorate($lines);

        return view('internal_po.dashboard', compact('lines', 'heads', 'locations', 'creator'));
    }

    public function pickSubmit(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1', 'ids.*' => 'integer',
            'user' => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        return $this->applyTransition(
            $request->input('ids'),
            internal_poline::ST_PENDING,
            [
                'status'    => internal_poline::ST_FINISH,
                'summit_by' => $user,
                'timestamp' => Carbon::now()->toDateTimeString(),
            ],
            'จัดเสร็จ'
        );
    }

    /* ==================== ด่าน 2: ระบุตำแหน่ง (ต้องผ่านด่าน 1) ==================== */
    public function locationDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $lines = $this->loadLines($request, [
            internal_poline::ST_FINISH,
            internal_poline::ST_STORED,
            internal_poline::ST_CHECKOUT,
        ], internal_poline::ST_FINISH);
        [$heads, $locations] = $this->decorate($lines);

        return view('store.store_location', compact('lines', 'heads', 'locations', 'creator'));
    }

    public function locationSubmit(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1', 'ids.*' => 'integer',
            'user' => 'required|string|max:100',
            'location' => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        return $this->applyTransition(
            $request->input('ids'),
            internal_poline::ST_FINISH,
            [
                'status'        => internal_poline::ST_STORED,
                'item_location' => $request->input('location'),
                'location_by'   => $user,
                'location_at'   => Carbon::now()->toDateTimeString(),
            ],
            'ระบุตำแหน่ง'
        );
    }

    public function checkoutDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $lines = $this->loadLines($request, [
            internal_poline::ST_STORED,
            internal_poline::ST_CHECKOUT,
        ], internal_poline::ST_STORED);
        [$heads, $locations] = $this->decorate($lines);

        return view('store.store_checkout', compact('lines', 'heads', 'locations', 'creator'));
    }

    public function checkoutSubmit(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1', 'ids.*' => 'integer',
            'user' => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        return $this->applyTransition(
            $request->input('ids'),
            internal_poline::ST_STORED,
            [
                'status'      => internal_poline::ST_CHECKOUT,
                'checkout_by' => $user,
                'checkout_at' => Carbon::now()->toDateTimeString(),
            ],
            'ของออก'
        );
    }

    /* ==================== ยกเลิก (เฉพาะด่าน 1) ==================== */
    public function markCancel(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1', 'ids.*' => 'integer',
            'user' => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        return $this->applyTransition(
            $request->input('ids'),
            internal_poline::ST_PENDING,
            [
                'status'    => internal_poline::ST_CANCEL,
                'summit_by' => $user,
                'timestamp' => Carbon::now()->toDateTimeString(),
            ],
            'ยกเลิก'
        );
    }
}