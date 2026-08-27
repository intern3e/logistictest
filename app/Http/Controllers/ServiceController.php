<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\service_car;
use App\Models\UserAuth;
use App\Models\SsoTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /* ==================== AUTH ====================
     * ใช้ pattern เดียวกับ fuellogsController::resolveOilUser / resolveOilEditor
     * (copy มาไว้ในไฟล์นี้โดยตรง ไม่แก้ไฟล์อื่น)
     */
    private function resolveServiceUser(Request $request): UserAuth
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

    private function serviceEditableRoles(): array
    {
        return ['admin', 'store', 'accounting'];
    }

    private function isServiceEditor($user): bool
    {
        return $user && in_array($user->role, $this->serviceEditableRoles(), true);
    }

    private function resolveServiceEditor(Request $request): UserAuth
    {
        $user = $this->resolveServiceUser($request);

        if (!$this->isServiceEditor($user)) {
            abort(403, 'คุณไม่มีสิทธิ์บันทึก/แก้ไข/ลบข้อมูลเซอร์วิสรถ (ต้องเป็น admin, store หรือ accounting)');
        }

        return $user;
    }

    private function serviceUserName($user): string
    {
        return $user->name ?? $user->emp_name ?? $user->username ?? ($user->id_emp ?? 'ผู้ใช้งาน');
    }

    /* ==================== หน้าเว็บ / อ่านข้อมูล (login อย่างเดียว) ==================== */

    public function index(Request $request)
    {
        $authUser = $this->resolveServiceUser($request);

        return view('driver.service', [
            'creator'      => $this->serviceUserName($authUser),
            'isPrivileged' => $this->isServiceEditor($authUser),
        ]);
    }

    public function list(Request $request)
    {
        // endpoint แบบ fetch/AJAX -> ตอบ 401 JSON แทนการ redirect (redirect จะทำให้ res.json() พังฝั่ง JS)
        if (!Auth::guard('web')->check()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }

        $q      = $request->query('q', '');
        $type   = $request->query('type', '');
        $status = $request->query('status', '');

        $query = service_car::query()->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('driver', 'like', "%{$q}%")
                   ->orWhere('plate',  'like', "%{$q}%")
                   ->orWhere('detail', 'like', "%{$q}%");
            });
        }
        if ($type)   $query->where('type',   $type);
        if ($status) $query->where('status', $status);

        $records = $query->get()->map(function ($r) {
            $r->image_urls = collect($r->images ?? [])->map(
                fn($p) => asset('storage/' . $p)
            )->values();
            return $r;
        });

        $all       = service_car::all();
        $totalCost = $all->sum('cost');
        $total     = $all->count();
        $cars      = $all->pluck('plate')->unique()->count();

        return response()->json([
            'records' => $records,
            'metrics' => [
                'total'    => $total,
                'totalCost'=> $totalCost,
                'avg'      => $total ? round($totalCost / $total) : 0,
                'cars'     => $cars,
            ],
        ]);
    }

    /* ==================== บันทึก/แก้ไข/ลบ (เฉพาะ admin, store, accounting) ==================== */

    public function store(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        $user = Auth::guard('web')->user();
        if (!$this->isServiceEditor($user)) {
            return response()->json(['error' => 'คุณไม่มีสิทธิ์บันทึกข้อมูล'], 403);
        }

        $request->validate([
            'date'   => 'required|date',
            'driver' => 'required|string|max:100',
            'plate'  => 'required|string|max:50',
            'type'   => 'required|string|max:100',
            'cost'   => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'detail' => 'nullable|string',
            'images.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $paths = $this->uploadImages($request);

        $record = service_car::create([
            'date'   => $request->date,
            'driver' => $request->driver,
            'plate'  => $request->plate,
            'type'   => $request->type,
            'cost'   => $request->cost ?? 0,
            'status' => $request->status,
            'detail' => $request->detail,
            'images' => $paths,
        ]);

        $record->image_urls = collect($paths)->map(
            fn($p) => asset('storage/' . $p)
        )->values();

        return response()->json(['success' => true, 'record' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        $user = Auth::guard('web')->user();
        if (!$this->isServiceEditor($user)) {
            return response()->json(['error' => 'คุณไม่มีสิทธิ์แก้ไขข้อมูล'], 403);
        }

        $record = service_car::findOrFail($id);

        $request->validate([
            'date'    => 'required|date',
            'driver'  => 'required|string|max:100',
            'plate'   => 'required|string|max:50',
            'type'    => 'required|string|max:100',
            'cost'    => 'nullable|numeric|min:0',
            'status'  => 'required|string|max:50',
            'detail'  => 'nullable|string',
            'images.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'keep_images' => 'nullable|string',
        ]);

        $keep = json_decode($request->input('keep_images', '[]'), true) ?? [];

        $old = $record->images ?? [];
        foreach ($old as $p) {
            if (!in_array($p, $keep)) {
                Storage::disk('public')->delete($p);
            }
        }

        $newPaths = $this->uploadImages($request);
        $allPaths = array_merge($keep, $newPaths);

        $record->update([
            'date'   => $request->date,
            'driver' => $request->driver,
            'plate'  => $request->plate,
            'type'   => $request->type,
            'cost'   => $request->cost ?? 0,
            'status' => $request->status,
            'detail' => $request->detail,
            'images' => $allPaths,
        ]);

        $record->image_urls = collect($allPaths)->map(
            fn($p) => asset('storage/' . $p)
        )->values();

        return response()->json(['success' => true, 'record' => $record]);
    }

    public function destroy(Request $request, $id)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่'], 401);
        }
        $user = Auth::guard('web')->user();
        if (!$this->isServiceEditor($user)) {
            return response()->json(['error' => 'คุณไม่มีสิทธิ์ลบข้อมูล'], 403);
        }

        $record = service_car::findOrFail($id);

        foreach ($record->images ?? [] as $p) {
            Storage::disk('public')->delete($p);
        }

        $record->delete();

        return response()->json(['success' => true]);
    }

    private function uploadImages(Request $request): array
    {
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('service_car', 'public');
                $paths[] = $path;
            }
        }
        return $paths;
    }
}