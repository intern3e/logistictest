<?php

namespace App\Http\Controllers;

use App\Models\internal_po;
use App\Models\internal_poline;
use App\Models\PoReceive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    const ALLOWED_USERS = ['test101'];

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

    /* ==================== ด่าน 2: ระบุตำแหน่ง (เฉพาะภายใน — ภายนอกระบุชั้นวางตอนรับเข้าแล้ว) ==================== */
    public function locationDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        $heads = $this->loadHeads($request, [
            internal_po::ST_FINISH,
            internal_po::ST_STORED,
            internal_po::ST_CHECKOUT,
        ], internal_po::ST_FINISH);
        $locations = $this->recentLocations();

        return view('store.store_location', compact('heads', 'locations', 'creator'));
    }

    public function locationSubmit(Request $request)
    {
        $request->validate([
            'ids'      => 'required|array|min:1',
            'ids.*'    => 'string',
            'user'     => 'required|string|max:100',
            'location' => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $ids      = $request->input('ids');
        $location = $request->input('location');

        try {
            $updated = DB::transaction(function () use ($ids, $user, $location) {
                return internal_po::whereIn('internal_id', $ids)
                    ->where('status', internal_po::ST_FINISH)
                    ->update([
                        'status'      => internal_po::ST_STORED,
                        'location_by' => $user,
                        'location'    => $location,                          // ← ชื่อชั้นวาง เก็บที่นี่
                        'location_at' => Carbon::now()->toDateTimeString(),  // ← เวลา เก็บแยกต่างหาก
                    ]);
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ระบุตำแหน่งไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ระบุตำแหน่ง ' . $updated . ' ใบ']);
    }
    /* ==================== ด่าน 3: ของออก (รวมภายใน internal_po + ภายนอก PoReceive) ==================== */
    public function checkoutDashboard(Request $request)
    {
        $creator = $request->input('create_by');
        if (!$this->allowed($creator)) abort(403, 'ไม่มีสิทธิ์เข้าใช้งาน');

        // ── ภายใน: internal_po ที่ระบุตำแหน่งแล้ว (ST_STORED) หรือเอาของออกแล้ว (ST_CHECKOUT) ──
        $internalHeads = internal_po::with('lines')
            ->whereIn('status', [internal_po::ST_STORED, internal_po::ST_CHECKOUT])
            ->when($request->filled('SONum'), function ($q) use ($request) {
                $q->where('SO_id', 'LIKE', '%' . $request->input('SONum') . '%');
            })
            ->get()
            ->map(function ($h) {
                $items = $h->lines;
                return (object) [
                    'type'          => 'internal',
                    'id'            => $h->internal_id,
                    'so_id'         => $h->SO_id,
                    'customer_name' => $h->customer_name,
                    'items'         => $items->map(fn ($it) => (object) [
                        'item_name'     => $it->item_name,
                        'item_quantity' => $it->item_quantity,
                    ]),
                    'location'      => $h->location,
                    'done_by'       => $h->location_by,
                    'done_at'       => $h->location_at,
                    'status'        => $h->status,
                    'status_color'  => $h->status_color,
                    'todo'          => $h->status === internal_po::ST_STORED,
                ];
            });

        // ── ภายนอก: PoReceive (รับเข้าจาก mobile) ที่ยังไม่กดของออก (checkout_by ยังว่าง) หรือกดแล้ว ──
        $externalHeads = PoReceive::with('lines')
            ->when($request->filled('SONum'), function ($q) use ($request) {
                $q->where('so_id', 'LIKE', '%' . $request->input('SONum') . '%');
            })
            ->get()
            ->map(function ($h) {
                $items = $h->lines;
                $todo  = is_null($h->checkout_by);
                $first = $items->first();
                return (object) [
                    'type'          => 'external',
                    'id'            => $h->po_id,
                    'so_id'         => $h->so_id,
                    'customer_name' => null, // ไม่ได้เก็บชื่อลูกค้าไว้ในตาราง po_receives
                    'items'         => $items->map(fn ($it) => (object) [
                        'item_name'     => $it->good_name,
                        'item_quantity' => $it->recv_qty,
                    ]),
                    'location'      => optional($first)->shelf,
                    'done_by'       => optional($first)->received_by,
                    'done_at'       => optional($first)->received_at,
                    'status'        => $todo ? 'รับเข้าแล้ว (รอของออก)' : 'เอาของออกแล้ว',
                    'status_color'  => $todo ? 'orange' : 'green',
                    'todo'          => $todo,
                ];
            });

        $heads = $internalHeads->concat($externalHeads)
            ->sortByDesc('todo')
            ->values();

        $locations = $this->recentLocations();

        return view('store.store_checkout', compact('heads', 'locations', 'creator'));
    }

    public function checkoutSubmit(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'string',
            'user'  => 'required|string|max:100',
        ]);
        $user = $request->input('user');
        if (!$this->allowed($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        // แยก id ตามชนิด: "internal:6907-A0001" / "external:PO123"
        $internalIds = [];
        $externalIds = [];
        foreach ($request->input('ids') as $raw) {
            [$type, $id] = array_pad(explode(':', $raw, 2), 2, null);
            if ($id === null) continue;
            if ($type === 'internal') $internalIds[] = $id;
            if ($type === 'external') $externalIds[] = $id;
        }

        if (!$internalIds && !$externalIds) {
            return response()->json(['ok' => false, 'message' => 'รายการที่เลือกไม่ถูกต้อง'], 422);
        }

        $updated = 0;
        try {
            DB::transaction(function () use ($internalIds, $externalIds, $user, &$updated) {
                if ($internalIds) {
                    $updated += internal_po::whereIn('internal_id', $internalIds)
                        ->where('status', internal_po::ST_STORED)
                        ->update([
                            'status'      => internal_po::ST_CHECKOUT,
                            'checkout_by' => $user,
                            'checkout_at' => Carbon::now()->toDateTimeString(),
                        ]);
                }
                if ($externalIds) {
                    $updated += PoReceive::whereIn('po_id', $externalIds)
                        ->whereNull('checkout_by')
                        ->update([
                            'checkout_by'   => $user,
                            'checkout_time' => Carbon::now(),
                        ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'ของออกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }

        if ($updated === 0) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการที่พร้อมดำเนินการ'], 404);
        }

        return response()->json(['ok' => true, 'message' => 'ของออก ' . $updated . ' ใบ']);
    }
}