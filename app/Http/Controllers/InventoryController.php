<?php

namespace App\Http\Controllers;

use App\Models\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    private string $baseUrl = 'https://api.hikaripower.com';
    private string $apiKey  = 'hikari20259f3c6e1b0f2d9c9c0e5e0b4d8b4e6e9c0c6c2f3e7b8a9f1d2e3c4b5a6f7d8e9';
    public function entry(Request $request)
    {
        $authUser = $this->checkAuth($request);
        $role = $authUser['auth'] ?? 'viewer';
        $q = ['create_by' => $authUser['username']]; 

        return $role === 'viewer'
            ? redirect()->route('inventory.item', $q)
            : redirect()->route('inventory.transaction', $q);
    }
    private function api(string $method, string $path, array $data = null)
    {
        $url = $this->baseUrl . $path;
        $req = Http::withHeaders(['Accept' => 'application/json', 'x-api-key' => $this->apiKey])->timeout(30);
        $res = match (strtoupper($method)) {
            'GET'    => $req->get($url),
            'POST'   => $req->post($url, $data ?? []),
            'PUT'    => $req->put($url, $data ?? []),
            'PATCH'  => $req->patch($url, $data ?? []),
            'DELETE' => $req->delete($url),
            default  => $req->get($url),
        };
        if ($res->failed()) { Log::error("API {$method} {$path} → {$res->status()}"); abort($res->status(), 'API error'); }
        return $res->json();
    }

    private function guardRole(array $allowed): void
    {
        if (!in_array(Session::get('user.auth', 'viewer'), $allowed)) abort(403, 'ไม่มีสิทธิ์');
    }

    /**
     * ตรวจ ?create_by=username → หาใน DB → เก็บ session
     * เรียกใน controller แทน middleware
     */
    private function checkAuth(Request $request): array
    {
        if ($request->has('create_by')) {
            $user = UserAuth::where('username', $request->query('create_by'))->first();
            if (!$user) abort(403, 'ไม่พบผู้ใช้: ' . $request->query('create_by'));
            Session::put('user', [
                'id_emp' => $user->id_emp, 'name' => $user->name,
                'username' => $user->username, 'auth' => $user->auth,
            ]);
        }
        if (!Session::has('user')) abort(403, 'กรุณาเข้าระบบผ่าน ?create_by=username');
        return Session::get('user');
    }

    // ═══════════════ VIEWS ═══════════════
    public function transactionDashboard(Request $request)
    {
        $authUser = $this->checkAuth($request);
        return view('inventory.transactiondashboard', [
            'authUser' => $authUser,
            'authRole' => $authUser['auth'] ?? 'viewer',
        ]);
    }

    public function inventoryDashboard(Request $request)
    {
        $authUser = $this->checkAuth($request);
        return view('inventory.inventorydashboard', [
            'authUser' => $authUser,
            'authRole' => $authUser['auth'] ?? 'viewer',
        ]);
    }

    // ═══════════════ ITEMS ═══════════════
    public function getPageData()
    {
        try {
            $items = collect($this->api('GET', '/items') ?? [])->map(fn($r) => [
                'iditem'    => $r['iditem'] ?? $r['item_id'] ?? '',
                'name'      => $r['name'] ?? $r['item_name'] ?? '',
                'quantity'  => $r['quantity'] ?? $r['item_quantity'] ?? 0,
                'typeitem'  => $r['typeitem'] ?? $r['item_type'] ?? '',
                'location'  => $r['location'] ?? $r['item_location'] ?? '',
                'brand'     => $r['brand'] ?? $r['item_brand'] ?? '',
                'privilege' => $r['privilege'] ?? $r['item_privilege'] ?? '',
            ])->sortBy(fn($i) => (str_starts_with(strtoupper($i['iditem']), 'SKU-') ? '0' : '1')
                . explode('-', $i['iditem'])[0]
                . str_pad(intval(last(explode('-', explode('.', $i['iditem'])[0]))), 10, '0', STR_PAD_LEFT)
            )->values();

            $brands    = collect($this->api('GET', '/predicted/brands') ?? [])->map(fn($r) => is_string($r) ? $r : ($r['brand'] ?? ''))->filter()->values();
            $locations = collect($this->api('GET', '/predicted/locations') ?? [])->map(fn($r) => is_string($r) ? $r : ($r['location'] ?? ''))->filter()->values();

            return response()->json(compact('items', 'brands', 'locations'));
        } catch (\Throwable $e) {
            return response()->json(['items' => [], 'brands' => [], 'locations' => []], 500);
        }
    }

    public function addProduct(Request $request)
    {
        $this->guardRole(['admin', 'user']);
        $d = $request->all();
        $this->ensureBrand($d['brand'] ?? ''); $this->ensureLocation($d['location'] ?? '');
        return response()->json(['success' => true, 'data' => $this->api('POST', '/items', [
            'name' => $d['name'], 'quantity' => intval($d['quantity'] ?? 0),
            'typeitem' => $d['typeitem'] ?? 'คลัง', 'location' => $d['location'] ?? '-',
            'brand' => $d['brand'] ?? '', 'privilege' => $d['privilege'] ?? '',
        ])]);
    }

    public function addSubProduct(Request $request)
    {
        $this->guardRole(['admin', 'user']);
        $d = $request->all();
        $this->ensureBrand($d['brand'] ?? ''); $this->ensureLocation($d['location'] ?? '');
        return response()->json(['success' => true, 'data' => $this->api('POST', '/items/sub', [
            'parentId' => $d['parentId'], 'name' => $d['name'],
            'quantity' => intval($d['quantity'] ?? 0), 'typeitem' => $d['typeitem'] ?? 'คลัง',
            'location' => $d['location'] ?? '-', 'brand' => $d['brand'] ?? '', 'privilege' => $d['privilege'] ?? '',
        ])]);
    }

    public function updateProduct(Request $request, string $id)
    {
        $this->guardRole(['admin']);
        $d = $request->all();
        $this->ensureBrand($d['brand'] ?? ''); $this->ensureLocation($d['location'] ?? '');
        $this->api('PUT', '/items/' . urlencode($id), [
            'name' => $d['name'], 'quantity' => intval($d['quantity'] ?? 0),
            'typeitem' => $d['typeitem'] ?? '', 'location' => $d['location'] ?? '-',
            'brand' => $d['brand'] ?? '', 'privilege' => $d['privilege'] ?? '',
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteProduct(string $id)
    {
        $this->guardRole(['admin']);
        try {
            foreach (($this->api('GET', '/transaction') ?? []) as $tx) {
                if (($tx['item_id'] ?? '') === $id) {
                    try { $this->api('DELETE', '/transaction/' . urlencode($tx['transaction_id'])); } catch (\Throwable $e) {}
                }
            }
        } catch (\Throwable $e) {}
        $this->api('DELETE', '/items/' . urlencode($id));
        $this->clearTxCache();
        return response()->json(['success' => true]);
    }

    public function countTxByItem(string $id)
    {
        try { return response()->json(['count' => collect($this->api('GET', '/transaction') ?? [])->where('item_id', $id)->count()]); }
        catch (\Throwable $e) { return response()->json(['count' => 0]); }
    }

    // ═══════════════ TRANSACTIONS ═══════════════

    /**
     * ดึง transaction ทั้งหมดจาก API → cache 5 นาที
     * ใช้ Http::pool() ยิงขนานสูงสุด 50 page
     */
    private function fetchAllTransactions(): array
    {
        return Cache::remember('all_transactions', 300, function () {
            $limit = 5000;
            $headers = ['Accept' => 'application/json', 'x-api-key' => $this->apiKey];

            $first = Http::withHeaders($headers)->timeout(60)
                         ->get($this->baseUrl . "/transaction?page=1&limit={$limit}");
            $all = $first->ok() ? ($first->json() ?? []) : [];
            if (count($all) < $limit) return $all;

            // ยิง page 2-50 พร้อมกัน
            $responses = Http::pool(function ($pool) use ($headers, $limit) {
                for ($p = 2; $p <= 50; $p++) {
                    $pool->as("p{$p}")
                         ->withHeaders($headers)->timeout(60)
                         ->get($this->baseUrl . "/transaction?page={$p}&limit={$limit}");
                }
            });

            foreach ($responses as $res) {
                if (!$res->ok()) continue;
                $rows = $res->json() ?? [];
                if (empty($rows)) break;
                $all = array_merge($all, $rows);
                if (count($rows) < $limit) break;
            }

            // map ครั้งเดียวตอน cache
            return collect($all)->map(fn($r) => $this->mapTx($r))->values()->all();
        });
    }

    private function clearTxCache(): void { Cache::forget('all_transactions'); }

    /**
     * Server-side filter + pagination
     * Browser ส่ง: page, limit, fDate, fOp, fBill, fItem, fType, fShelf
     * Return: { data: [...100 rows], total, page, lastPage }
     */
    public function getTransactionPage(Request $request)
    {
        $all = collect($this->fetchAllTransactions());

        // ── Filter ฝั่ง server ──
        $fDate  = $request->input('fDate', '');   // dd/mm/yyyy
        $fOp    = mb_strtolower($request->input('fOp', ''));
        $fBill  = mb_strtolower($request->input('fBill', ''));
        $fItem  = mb_strtolower($request->input('fItem', ''));
        $fType  = $request->input('fType', '');
        $fShelf = mb_strtolower($request->input('fShelf', ''));

        if ($fDate || $fOp || $fBill || $fItem || $fType || $fShelf) {
            $all = $all->filter(function ($r) use ($fDate, $fOp, $fBill, $fItem, $fType, $fShelf) {
                if ($fDate  && !str_starts_with($r['Timestamp'] ?? '', $fDate)) return false;
                if ($fOp    && !str_contains(mb_strtolower($r['ชื่อผู้ดำเนินงาน'] ?? ''), $fOp)) return false;
                if ($fBill  && !str_contains(mb_strtolower($r['หมายเลขเอกสาร'] ?? ''), $fBill)) return false;
                if ($fItem  && !str_contains(mb_strtolower($r['รายการ'] ?? ''), $fItem)) return false;
                if ($fType  && ($r['ประเภทข้อมูล'] ?? '') !== $fType) return false;
                if ($fShelf && !str_contains(mb_strtolower($r['ชั้นวาง'] ?? ''), $fShelf)) return false;
                return true;
            });
        }

        // ── Paginate ฝั่ง server ──
        $total   = $all->count();
        $perPage = intval($request->input('limit', 100));
        $page    = max(1, intval($request->input('page', 1)));
        $lastPage = max(1, (int) ceil($total / $perPage));

        $data = $all->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json(compact('data', 'total', 'page', 'lastPage'));
    }

    public function getTransactionByItemId(string $itemId)
    {
        // ลอง API filter ตรงก่อน
        try {
            $d = $this->api('GET', '/transaction?item_id=' . urlencode($itemId));
            if (!empty($d) && is_array($d) && ($d[0]['item_id'] ?? '') === $itemId)
                return response()->json(collect($d)->map(fn($r) => $this->mapTx($r))->values());
        } catch (\Throwable $e) {}

        // fallback: ใช้ cache
        return response()->json(
            collect($this->fetchAllTransactions())->where('item_id', $itemId)->values()
        );
    }

    public function updateTransaction(Request $request, string $id)
    {
        $this->guardRole(['admin']);
        $d = $request->all();
        $this->api('PUT', '/transaction/' . urlencode($id), [
            'addby' => $d['operator'] ?? '', 'transaction_type' => $d['type'] ?? '',
            'document_id' => $d['bill'] ?? '', 'item_quantity' => floatval($d['quantity'] ?? 0),
            'currency_price' => ($d['price'] !== '' && $d['price'] !== null) ? floatval($d['price']) : null,
            'item_location' => $d['shelf'] ?? null, 'pic' => $d['image'] ?? null,
            'transaction_note' => $d['note'] ?? null, 'oldQuantity' => floatval($d['oldQuantity'] ?? 0),
            'oldType' => $d['oldType'] ?? '', 'oldItemId' => $d['oldItemId'] ?? '',
        ]);
        $this->clearTxCache();
        return response()->json(['success' => true]);
    }

    public function deleteTransaction(string $id)
    {
        $this->guardRole(['admin']);
        $this->api('DELETE', '/transaction/' . urlencode($id));
        $this->clearTxCache();
        return response()->json(['success' => true]);
    }

    // ═══════════════ USERS ═══════════════
    public function getUsers()       { return response()->json(UserAuth::all()); }
    public function addUser(Request $request)
    {
        return response()->json(['success' => true, 'data' => UserAuth::create([
            'id_emp' => UserAuth::nextIdEmp(), 'name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'auth' => $request->input('auth', 'viewer'),
        ])]);
    }
    public function updateUser(Request $request, string $id)
    {
        $u = UserAuth::findOrFail($id);
        $u->fill($request->only(['name', 'username', 'auth']));
        if ($request->filled('password')) $u->password = Hash::make($request->input('password'));
        $u->save();
        return response()->json(['success' => true]);
    }
    public function deleteUser(string $id)
    {
        UserAuth::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ═══════════════ HELPERS ═══════════════
    private function mapTx(array $r): array
    {
        return [
            'transaction_id'   => $r['transaction_id'] ?? '',
            'Timestamp'        => $this->fmtTs($r['timestamp'] ?? $r['Timestamp'] ?? ''),
            'ชื่อผู้ดำเนินงาน' => $r['addby'] ?? '',
            'ประเภทข้อมูล'     => $r['transaction_type'] ?? '',
            'หมายเลขเอกสาร'   => $r['document_id'] ?? '',
            'item_id'          => $r['item_id'] ?? '',
            'รายการ'           => $r['item_name'] ?? '',
            'ชั้นวาง'          => $r['item_location'] ?? '',
            'จำนวน'            => $r['item_quantity'] ?? '',
            'ราคาต่อหน่วย'     => $r['currency_price'] ?? '',
            'รูปประกอบ'        => $r['pic'] ?? '',
            'ประเภททรัพย์สิน'  => $r['item_type'] ?? '',
            'บริษัท'           => $r['item_privilege'] ?? '',
            'หมายเหตุ'         => $r['transaction_note'] ?? '',
        ];
    }
    private function fmtTs($ts): string
    {
        if (empty($ts)) return '';
        try { return (new \DateTime($ts))->format('d/m/Y H:i:s'); } catch (\Throwable $e) { return (string)$ts; }
    }
    private function ensureBrand(string $b): void { $b = trim($b); if ($b && $b !== '-') try { $this->api('POST', '/predicted/brands', ['brand' => $b]); } catch (\Throwable $e) {} }
    private function ensureLocation(string $l): void { $l = trim($l); if ($l && $l !== '-') try { $this->api('POST', '/predicted/locations', ['location' => $l]); } catch (\Throwable $e) {} }
}