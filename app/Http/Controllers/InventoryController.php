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
        $q = ['create_by' => $authUser['name']]; 

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
        try {
            return \Carbon\Carbon::parse($ts, 'UTC')
                ->timezone('Asia/Bangkok')
                ->format('d/m/Y H:i:s');
        } catch (\Throwable $e) {
            return (string) $ts;
        }
    }
    private function ensureBrand(string $b): void { $b = trim($b); if ($b && $b !== '-') try { $this->api('POST', '/predicted/brands', ['brand' => $b]); } catch (\Throwable $e) {} }
    private function ensureLocation(string $l): void { $l = trim($l); if ($l && $l !== '-') try { $this->api('POST', '/predicted/locations', ['location' => $l]); } catch (\Throwable $e) {} }
    private function checkAuth(Request $request): array
    {
        if ($request->has('create_by')) {
            $user = UserAuth::where('name', $request->query('create_by'))->first();   
            if (!$user) abort(403, 'ไม่พบผู้ใช้: ' . $request->query('create_by'));
            Session::put('user', [
                'id_emp'   => $user->id_emp, 'name' => $user->name,
                'username' => $user->username, 'auth' => $user->auth,
                'page'     => $user->page ?? '',
            ]);
        }
        if (!Session::has('user')) abort(403, 'กรุณาเข้าระบบผ่าน ?create_by=ชื่อ');
        return Session::get('user');
    }
    // ═══════════════ VIEWS: STOCKOUT / WITHDRAW / PR ═══════════════
    
    public function stockoutPage(Request $request)
    {
        $authUser = $this->checkAuth($request);
        if (!in_array($authUser['auth'] ?? 'viewer', ['admin', 'user'])) abort(403, 'ไม่มีสิทธิ์');
        return view('inventory.stockout', [
            'authUser' => $authUser,
            'authRole' => $authUser['auth'] ?? 'viewer',
        ]);
    }
    
    public function withdrawPage(Request $request)
    {
        $authUser = $this->checkAuth($request);
        if (!in_array($authUser['auth'] ?? 'viewer', ['admin', 'user'])) abort(403, 'ไม่มีสิทธิ์');
        return view('inventory.withdraw', [
            'authUser' => $authUser,
            'authRole' => $authUser['auth'] ?? 'viewer',
        ]);
    }
    // ═══════════════ SAVE STOCKOUT / WITHDRAW ═══════════════
    // (port มาจาก Apps Script: saveStockout / saveWithdraw)
    
    public function saveStockout(Request $request)
    {
        $this->guardRole(['admin', 'user']);
        $d = $request->all();
        $qty = floatval($d['quantity'] ?? 0);
        if (empty($d['iditem']) || $qty <= 0) {
            return response()->json(['success' => false, 'error' => 'ข้อมูลไม่ครบหรือจำนวนไม่ถูกต้อง'], 422);
        }
        try {
            $txId = $this->generateTransactionId($d['iditem']);
            $this->api('POST', '/transaction/stockout', [
                'transaction_id'   => $txId,
                'timestamp'        => now()->toISOString(),
                'addby'            => $d['addedBy'] ?? Session::get('user.name', ''),
                'transaction_type' => 'ขายสินค้าออก',
                'document_id'      => 'SO ' . ($d['soNumber'] ?? ''),
                'item_id'          => $d['iditem'],
                'item_quantity'    => $qty,
                'transaction_note' => $d['note'] ?? null,
            ]);
            $this->updateItemQuantity($d['iditem'], -$qty);
            $this->clearTxCache();
            return response()->json(['success' => true, 'transaction_id' => $txId]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    public function saveWithdraw(Request $request)
    {
        $this->guardRole(['admin', 'user']);
        $d = $request->all();
        $qty = floatval($d['quantity'] ?? 0);
        if (empty($d['iditem']) || $qty <= 0 || empty($d['namewith']) || empty($d['telwith'])) {
            return response()->json(['success' => false, 'error' => 'ข้อมูลไม่ครบหรือจำนวนไม่ถูกต้อง'], 422);
        }
        try {
            $txId = $this->generateTransactionId($d['iditem']);
            $this->api('POST', '/transaction/withdraw', [
                'transaction_id'   => $txId,
                'timestamp'        => now()->toISOString(),
                // รูปแบบเดียวกับ Apps Script: "ผู้ทำรายการ,ผู้เบิก เบอร์"
                'addby'            => ($d['addedBy'] ?? Session::get('user.name', '')) . ',' . $d['namewith'] . ' ' . $d['telwith'],
                'transaction_type' => 'เบิกของ',
                'document_id'      => null,
                'item_id'          => $d['iditem'],
                'item_quantity'    => $qty,
                'pic'              => $d['pic'] ?? null,
                'transaction_note' => $d['note'] ?? null,
            ]);
            $this->updateItemQuantity($d['iditem'], -$qty);
            $this->clearTxCache();
            return response()->json(['success' => true, 'transaction_id' => $txId]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    // ═══════════════ HELPERS (port จาก GAS) ═══════════════
    
    /** สร้าง transaction id: {itemId}-{ddmmyy}-{HHmmss}-{seq 6 หลัก} */
    private function generateTransactionId(string $itemId): string
    {
        $now  = now();
        $mmyy = $now->format('m') . $now->format('y');
        $seq  = 1;
        try {
            $res = $this->api('GET', '/transaction?mmyy=' . urlencode($mmyy));
            if (!empty($res) && is_array($res)) {
                $parts = explode('-', $res[0]['transaction_id'] ?? '');
                $seq   = (intval(end($parts)) ?: 0) + 1;
            }
        } catch (\Throwable $e) {}
        return $itemId . '-' . $now->format('dmy') . '-' . $now->format('His') . '-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }
    
    /** บวก/ลบจำนวนคงเหลือของสินค้า (addQty ติดลบ = ตัดสต็อก) */
    private function updateItemQuantity(string $itemId, float $addQty): void
    {
        $item    = $this->api('GET', '/items/' . urlencode($itemId));
        $current = floatval($item['quantity'] ?? 0);
        $this->api('PUT', '/items/' . urlencode($itemId), [
            'name'      => $item['name'] ?? '',
            'quantity'  => $current + $addQty,
            'typeitem'  => $item['typeitem'] ?? '',
            'location'  => $item['location'] ?? '-',
            'brand'     => $item['brand'] ?? '',
            'privilege' => $item['privilege'] ?? '',
        ]);
    }
    private string $gasUploadUrl = 'https://script.google.com/macros/s/AKfycbwVM2MoW-7WSVcU0cjI5xhIgvlVQ25BHr1IuGar7841CU_hl_i50U_Q1QXtQIX4pIaTnQ/exec';
 
// ═══════════════ VIEWS: PR ═══════════════
 
/** หน้าสร้างใบขอซื้อ — เฉพาะคนที่มี 'pr' ในคอลัมน์ page */
public function prPage(Request $request)
{
    $authUser = $this->checkAuth($request);
    if (!str_contains($authUser['page'] ?? '', 'pr')) abort(403, 'ไม่มีสิทธิ์เข้าหน้าใบขอซื้อ');
    return view('inventory.pr', [
        'authUser' => $authUser,
        'authRole' => $authUser['auth'] ?? 'viewer',
    ]);
}
 
/** Dashboard ใบขอซื้อ — admin เข้าได้เสมอ (อนุมัติ), คนมีสิทธิ์ pr เข้าดูได้ */
public function prDashboardPage(Request $request)
{
    $authUser = $this->checkAuth($request);
    $role = $authUser['auth'] ?? 'viewer';
    if ($role !== 'admin' && !str_contains($authUser['page'] ?? '', 'pr')) abort(403, 'ไม่มีสิทธิ์');
    return view('inventory.dashboardpr', [
        'authUser' => $authUser,
        'authRole' => $role,
    ]);
}

 
// ═══════════════ PR: UPLOAD IMAGE → GOOGLE DRIVE ═══════════════
// browser ส่ง base64 มาที่ Laravel → Laravel ส่งต่อให้ GAS → GAS เซฟลง Drive → คืน URL
 
public function uploadPrImage(Request $request)
{
    $this->guardRole(['admin', 'user']);
    $b64  = $request->input('image', '');
    $name = preg_replace('/[^A-Za-z0-9_\-]/', '', $request->input('fileName', 'img_' . time()));
    if (strlen($b64) < 100) return response()->json(['url' => '']);
    try {
        $res = Http::asForm()->timeout(90)->post($this->gasUploadUrl, [
            'action'   => 'upload',
            'image'    => $b64,
            'fileName' => $name,
        ]);
        return response()->json(['url' => $res->json('url') ?? '']);
    } catch (\Throwable $e) {
        Log::warning('uploadPrImage → GAS failed: ' . $e->getMessage());
        return response()->json(['url' => '']);
    }
}
 
// ═══════════════ PR: SAVE (สร้างใบขอซื้อ) ═══════════════
 
public function savePr(Request $request)
{
    $this->guardRole(['admin', 'user']);
    $d = $request->all();
    try {
        $itemsReady = [];
        foreach (($d['items'] ?? []) as $item) {
            $itemId = $item['iditem'] ?? $item['item_id'] ?? '';
            $isNew  = empty($itemId);
            if ($isNew && !empty($item['name'])) {
                try {
                    $created = $this->api('POST', '/items', [
                        'name' => $item['name'], 'quantity' => 0, 'typeitem' => 'ทรัพย์สินบริษัท',
                        'location' => '', 'brand' => '', 'privilege' => $item['company'] ?? '',
                    ]);
                    $itemId = $this->extractItemId($created);
                } catch (\Throwable $e) { Log::warning('savePr addProduct: ' . $e->getMessage()); }
            }
            $itemsReady[] = [
                'item_id'    => $itemId,
                'name'       => $item['name'] ?? '',
                'company'    => $item['company'] ?? '',
                'qty'        => $item['qty'] ?? 0,
                'price'      => $item['price'] ?? 0,
                'currency'   => $item['currency'] ?? 'บาท',
                'thb_price'  => $item['thb_price'] ?? 0,
                'image_url'  => $item['image_url'] ?? '',
                'doc_images' => is_array($item['doc_images'] ?? null) ? $item['doc_images'] : [],
                'is_new'     => $isNew,
            ];
        }
 
        $result = $this->api('POST', '/pr', [
            'pr_id'      => '',
            'requester'  => $d['requester'] ?? Session::get('user.name', ''),
            'buyer_name' => $d['buyerName'] ?? '',
            'phone'      => $d['phone'] ?? '',
            'po_number'  => $d['po_number'] ?? '',
            'date'       => $d['date'] ?? '',
            'reason'     => $d['reason'] ?? '',
            'note'       => $d['note'] ?? '',
            'items'      => $itemsReady,
        ]);
 
        return response()->json(['success' => true, 'pr_id' => $result['pr_id'] ?? '']);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
 
// ═══════════════ PR: LIST ═══════════════
 
public function getPrList()
{
    try {
        $rows = $this->api('GET', '/pr') ?? [];
        $data = collect($rows)->map(function ($r) {
            $items = $r['items'] ?? [];
            if (is_string($items)) $items = json_decode($items, true) ?: [];
            return [
                'pr_id'         => $r['pr_id'] ?? '',
                'requester'     => $r['requester'] ?? '',
                'buyer_name'    => $r['buyer_name'] ?? '',
                'phone'         => $r['phone'] ?? '',
                'po_number'     => $r['po_number'] ?? '',
                'date'          => $r['date'] ?? '',
                'reason'        => $r['reason'] ?? '',
                'note'          => $r['note'] ?? '',
                'items'         => $items,
                'status'        => $r['status'] ?? 'รอดำเนินการ',
                'action_by'     => $r['action_by'] ?? '',
                'action_date'   => $r['action_date'] ?? '',
                'reject_reason' => $r['reject_reason'] ?? '',
            ];
        })->values();
        return response()->json($data);
    } catch (\Throwable $e) {
        return response()->json([]);
    }
}
 
// ═══════════════ PR: APPROVE (admin) ═══════════════
// สร้าง transaction stockin ต่อทุกรายการ + บวกสต็อก + เปลี่ยนสถานะ
 
public function approvePr(string $prId)
{
    $this->guardRole(['admin']);
    $adminName = Session::get('user.name', 'Admin');
    try {
        $pr = $this->api('GET', '/pr/' . urlencode($prId));
        if (!$pr) return response()->json(['success' => false, 'error' => 'ไม่พบ PR ' . $prId], 404);
 
        $items = $pr['items'] ?? [];
        if (is_string($items)) $items = json_decode($items, true) ?: [];
 
        foreach ($items as $item) {
            $itemId = $item['item_id'] ?? '';
            if (!$itemId && !empty($item['name'])) {
                try {
                    $created = $this->api('POST', '/items', [
                        'name' => $item['name'], 'quantity' => 0, 'typeitem' => 'ทรัพย์สินบริษัท',
                        'location' => '', 'brand' => '', 'privilege' => $item['company'] ?? '',
                    ]);
                    $itemId = $this->extractItemId($created);
                } catch (\Throwable $e) { Log::warning('approvePr addProduct: ' . $e->getMessage()); }
            }
            if (!$itemId) continue;
 
            $txnId = $this->generateTransactionId($itemId);
            $qty   = intval($item['qty'] ?? 0);
 
            $this->api('POST', '/transaction/stockin', [
                'transaction_id'   => $txnId,
                'addby'            => $adminName,
                'transaction_type' => 'รับเข้าสต็อก',
                'document_id'      => $prId . (!empty($pr['po_number']) ? ' /' . $pr['po_number'] : ''),
                'item_id'          => $itemId,
                'item_quantity'    => $qty,
                'item_unit_price'  => floatval($item['price'] ?? 0),
                'currency_type'    => $item['currency'] ?? 'บาท',
                'currency_price'   => floatval($item['thb_price'] ?? 0),
                'pic'              => $item['image_url'] ?? '',
                'transaction_note' => 'อนุมัติจาก ' . $prId . ' | ชื่อทีมช่าง: ' . ($pr['buyer_name'] ?? ''),
            ]);
 
            $this->updateItemQuantity($itemId, $qty);
        }
 
        $this->api('PATCH', '/pr/' . urlencode($prId) . '/status', [
            'status'    => 'อนุมัติแล้ว',
            'action_by' => $adminName,
        ]);
 
        $this->clearTxCache();
        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
 
// ═══════════════ PR: REJECT (admin) ═══════════════
 
public function rejectPr(Request $request, string $prId)
{
    $this->guardRole(['admin']);
    try {
        $this->api('PATCH', '/pr/' . urlencode($prId) . '/status', [
            'status'        => 'ไม่อนุมัติ',
            'action_by'     => Session::get('user.name', 'Admin'),
            'reject_reason' => $request->input('reason', ''),
        ]);
        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
public function getExchangeRates()
{
    try {
        return response()->json(Cache::remember('fx_thb', 3600, function () {
            $res = Http::timeout(15)->get('https://api.frankfurter.app/latest', [
                'base' => 'THB', 'symbols' => 'JPY,CNY,USD',
            ]);
            $raw = $res->json('rates') ?? [];
            return [
                'บาท'    => 1,
                'เยน'    => isset($raw['JPY']) ? 1 / $raw['JPY'] : 1,
                'หยวน'   => isset($raw['CNY']) ? 1 / $raw['CNY'] : 1,
                'ดอลล่า' => isset($raw['USD']) ? 1 / $raw['USD'] : 1,
            ];
        }));
    } catch (\Throwable $e) {
        return response()->json(['บาท' => 1, 'เยน' => 1, 'หยวน' => 1, 'ดอลล่า' => 1]);
    }
}

// ═══════════════ HELPER ═══════════════

/** ดึง item id จาก response ตอนสร้างสินค้าใหม่ (รองรับหลายรูปแบบ) */
private function extractItemId($created): string
{
    if (!is_array($created)) return '';
    return data_get($created, 'item.item_id')
        ?? data_get($created, 'item.iditem')
        ?? $created['iditem'] ?? $created['item_id'] ?? $created['id'] ?? '';
}
 
public function manageUsersPage(Request $request)
{
    $authUser = $this->checkAuth($request);
    if (($authUser['auth'] ?? '') !== 'admin') abort(403, 'เฉพาะผู้ดูแลระบบ');
    return view('inventory.manauser', [
        'authUser' => $authUser,
        'authRole' => 'admin',
    ]);
}
 
// ── GET /api/users : list จาก DB local ──
public function getUsers()
{
    $this->guardRole(['admin']);
    return response()->json(
        UserAuth::orderBy('id_emp')->get(['id_emp', 'username', 'password', 'name', 'auth', 'page'])
    );
}
 
// ── POST /api/users : เพิ่มใน DB local ──
public function addUser(Request $request)
{
    $this->guardRole(['admin']);
    $d = $request->all();
    if (empty($d['username']) || empty($d['password']) || empty($d['name'])) {
        return response()->json(['success' => false, 'error' => 'ข้อมูลไม่ครบ'], 422);
    }
    if (UserAuth::where('username', $d['username'])->exists()) {
        return response()->json(['success' => false, 'error' => 'username นี้มีอยู่แล้ว'], 422);
    }
    UserAuth::create([
        'id_emp'   => UserAuth::nextIdEmp(),
        'name'     => $d['name'],
        'username' => $d['username'],
        'password' => $d['password'],          // ← varchar ธรรมดา ไม่ Hash
        'auth'     => $d['auth'] ?? 'user',
        'page'     => $d['page'] ?? null,
    ]);
    return response()->json(['success' => true]);
}
 
// ── PUT /api/users/{id} : id = id_emp ──
public function updateUser(Request $request, string $id)
{
    $this->guardRole(['admin']);
    $d = $request->all();
    $u = UserAuth::findOrFail($id);
    $u->username = $d['username'];
    $u->name     = $d['name'];
    $u->auth     = $d['auth'];
    $u->password = $d['password'];             // ← varchar ธรรมดา ไม่ Hash
    $u->page     = $d['page'] ?? null;
    $u->save();
    return response()->json(['success' => true]);
}
 
// ── DELETE /api/users/{id} : id = id_emp ──
public function deleteUser(string $id)
{
    $this->guardRole(['admin']);
    UserAuth::findOrFail($id)->delete();
    return response()->json(['success' => true]);
}
public function getOneItem(string $id)
{
    try {
        return response()->json($this->api('GET', '/items/' . urlencode($id)));
    } catch (\Throwable $e) {
        return response()->json(null, 404);
    }
}
public function clearTransactionCache()
{
    $this->clearTxCache();
    return response()->json(['success' => true]);
}
}