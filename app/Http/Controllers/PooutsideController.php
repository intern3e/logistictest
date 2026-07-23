<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Pooutside;
use Carbon\Carbon;

class PooutsideController extends Controller
{
    /** จำนวน PO สูงสุดต่อ 1 batch request */
    private const BATCH_MAX = 60;

    /** ยิง ERP พร้อมกันครั้งละกี่ใบ (กัน ERP รับไม่ไหว) */
    private const POOL_SIZE = 10;

    /** URL ของ ERP */
    private const ERP_URL = 'http://server_update:8000/api/getPODetail';

    // ─── Views ────────────────────────────────────────────────────────────────

  public function dashboard(Request $request, $name = 'Guest')
{
    $userName = urldecode($name);

    return view('pooutside.dashboard', [
        'userName' => $userName
    ]);
}

    // ─── API Endpoints ────────────────────────────────────────────────────────

    public function search(Request $request): JsonResponse
    {
        $ponum = trim($request->input('ponum'));

        if (empty($ponum)) {
            return response()->json(['success' => false, 'message' => 'กรุณากรอกเลข PO'], 422);
        }

        $localData = Pooutside::where('ponum', $ponum)
            ->orderBy('date_invoice', 'desc')
            ->get()
            ->toArray();

        $erpData = $this->fetchErpPO($ponum);
        if (!$erpData) {
            return response()->json(['success' => false, 'message' => 'ไม่พบเลข PO'], 404);
        }

        return response()->json($this->buildPoPayload($localData, $erpData));
    }

    /* =====================================================================
       ✅ ใหม่: ค้นหาหลาย PO ในครั้งเดียว
       POST /pooutside/search-batch   body: { "ponums": ["PO001","PO002", ...] }

       - DB query ครั้งเดียว (whereIn) แทนการวน query ทีละใบ
       - ยิง ERP แบบขนานด้วย Http::pool ครั้งละ 10 ใบ
       - ตอบกลับ: { success: true, data: { "PO001": {...}, "PO002": {...} } }
         PO ที่ ERP ไม่มีข้อมูล จะไม่อยู่ใน data (frontend เช็คเองว่าไม่มี = แสดง —)
       ===================================================================== */
    public function searchBatch(Request $request): JsonResponse
    {
        $raw = $request->input('ponums', []);

        if (is_string($raw)) {
            $raw = array_map('trim', explode(',', $raw));
        }

        if (!is_array($raw)) {
            return response()->json(['success' => false, 'message' => 'ponums ต้องเป็น array'], 422);
        }

        // ล้างค่าว่าง + ตัดซ้ำ + จำกัดจำนวน
        $ponums = collect($raw)
            ->map(fn($p) => trim((string) $p))
            ->filter(fn($p) => $p !== '')
            ->unique()
            ->take(self::BATCH_MAX)
            ->values()
            ->all();

        if (empty($ponums)) {
            return response()->json(['success' => true, 'data' => (object) [], 'count' => 0]);
        }

        // 1) ดึงข้อมูล local ทีเดียวทั้งชุด แล้วค่อยจัดกลุ่มตาม ponum
        $localGrouped = Pooutside::whereIn('ponum', $ponums)
            ->orderBy('date_invoice', 'desc')
            ->get()
            ->groupBy('ponum')
            ->map(fn($rows) => $rows->map(fn($r) => $r->toArray())->all());

        // 2) ยิง ERP แบบขนานเป็นก้อน ๆ
        $erpAll = $this->fetchErpPOBatch($ponums);

        // 3) ประกอบผลลัพธ์
        $data = [];
        foreach ($ponums as $ponum) {
            $erpData = $erpAll[$ponum] ?? null;
            if (!$erpData) {
                continue; // ไม่พบใน ERP → ข้าม
            }

            $localData = $localGrouped[$ponum] ?? [];
            $data[$ponum] = $this->buildPoPayload($localData, $erpData);
        }

        return response()->json([
            'success'   => true,
            'data'      => (object) $data,
            'count'     => count($data),
            'requested' => count($ponums),
        ]);
    }

    public function checkLocalPO(Request $request): JsonResponse
    {
        $ponum = $request->input('ponum');

        $data = Pooutside::where('ponum', $ponum)
            ->orderBy('date_invoice', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'exists'  => $data->count() > 0,
            'data'    => $data,
            'count'   => $data->count(),
        ]);
    }

    // ─── Private: ERP ─────────────────────────────────────────────────────────

    private function fetchErpPO(string $poNum): ?array
    {
        try {
            $response = Http::timeout(20)->get(self::ERP_URL, ['PONum' => $poNum]);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Error fetching PO from ERP: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ยิง ERP หลายใบพร้อมกัน (ครั้งละ POOL_SIZE ใบ)
     * @return array<string, array|null>  map: ponum => erp payload
     */
    private function fetchErpPOBatch(array $ponums): array
    {
        $result = [];

        foreach (array_chunk($ponums, self::POOL_SIZE) as $chunk) {
            try {
                $responses = Http::pool(function (Pool $pool) use ($chunk) {
                    $requests = [];
                    foreach ($chunk as $i => $ponum) {
                        $requests[] = $pool->as('po_' . $i)
                            ->timeout(20)
                            ->get(self::ERP_URL, ['PONum' => $ponum]);
                    }
                    return $requests;
                });
            } catch (\Exception $e) {
                Log::error('ERP pool error: ' . $e->getMessage());
                continue;
            }

            foreach ($chunk as $i => $ponum) {
                $res = $responses['po_' . $i] ?? null;

                if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                    $json = $res->json();
                    $result[$ponum] = is_array($json) ? $json : null;
                } else {
                    if ($res instanceof \Throwable) {
                        Log::warning("ERP fail [{$ponum}]: " . $res->getMessage());
                    }
                    $result[$ponum] = null;
                }
            }
        }

        return $result;
    }

    // ─── Private: Builders ────────────────────────────────────────────────────

    /**
     * ประกอบ payload ของ PO 1 ใบ (ใช้ร่วมกันทั้ง search() และ searchBatch())
     */
    private function buildPoPayload(array $localData, array $erpData): array
    {
        $matchedProducts = $this->matchProducts($localData, $erpData['ms_podt'] ?? []);

        $validDbItems = collect($matchedProducts)
            ->filter(fn($m) => !$m['lowScore'])
            ->flatMap(fn($m) => $m['dbItems'])
            ->toArray();

        return [
            'success'  => true,
            'vendor'   => $this->buildVendorInfo($erpData),
            'timeline' => $this->buildTimeline($erpData, $validDbItems),
            'notes'    => $this->collectUniqueNotes($localData),
            'items'    => $this->buildItemsFromMatched($matchedProducts, $erpData),
        ];
    }

    private function buildVendorInfo(array $erp): array
    {
        return [
            'code'    => $erp['VendorCode'] ?? '-',
            'name'    => $erp['VendorName'] ?? '-',
            'address' => collect([
                $erp['ContAddr1']     ?? null,
                $erp['ContAddr2']     ?? null,
                $erp['ContDistrict']  ?? null,
                $erp['ContAmphur']    ?? null,
                $erp['ContProvince']  ?? null,
                $erp['ContPostCode']  ?? null,
            ])->filter()->implode(', ') ?: '-',
        ];
    }

    private function buildTimeline(array $erp, array $localData): array
    {
        $status              = $erp['store_status'] ?? '';
        $hasLocal            = count($localData) > 0;
        $closestInvoiceDate  = $this->getLatestInvoiceDate($localData);
        $expectedDelivery    = $closestInvoiceDate
            ? Carbon::parse($closestInvoiceDate)->addDays(15)->format('Y-m-d')
            : null;

        $step = match ($status) {
            'ENTRY'     => $hasLocal ? 4 : 2,
            'PARTIAL'   => 5,
            'COMPLETED' => 5,
            'CANCELLED' => 2,
            default     => 1,
        };

        return [
            'step'              => $step,
            'status'            => $status,
            'date_created'      => $erp['DocuDate'] ?? null,
            'date_invoice'      => $closestInvoiceDate,
            'date_expected'     => $expectedDelivery,
            'show_expected_box' => $status !== 'COMPLETED',
        ];
    }

    private function buildItemsFromMatched(array $matchedProducts, array $erp): array
    {
        return array_map(function (array $match) use ($erp) {
            $apiItem    = $match['apiItem'];
            $dbItems    = $match['dbItems'];
            $lowItems   = $match['lowScoreItems'];
            $isLowScore = $match['lowScore'];

            [$itemStatus, $statusClass] = $this->resolveItemStatus($dbItems, $erp['store_status'] ?? '');

            return [
                'name'         => $apiItem['GoodName'] ?? '',
                'qty_ordered'  => $apiItem['GoodQty2'] ?? 0,
                'status'       => $itemStatus,
                'status_class' => $statusClass,
                'is_low_score' => $isLowScore,
                'invoices'     => $isLowScore
                    ? $this->formatInvoiceTags($lowItems, mismatch: true)
                    : $this->formatInvoiceTags($dbItems, mismatch: false),
                'qty_summary'  => !$isLowScore
                    ? $this->calculateQtySummary($apiItem['GoodQty2'] ?? 0, $dbItems)
                    : null,
            ];
        }, $matchedProducts);
    }

    // ─── Private: Matching ────────────────────────────────────────────────────

    private function matchProducts(array $dbItems, array $apiItems): array
    {
        $scores        = [];
        $usedDbIndices = [];
        $apiToDbMap    = [];

        foreach ($dbItems as $dbIdx => $dbItem) {
            $dbName = strtoupper(trim($dbItem['name']));
            foreach ($apiItems as $apiIdx => $apiItem) {
                $apiName = strtoupper(trim($apiItem['GoodName']));
                $maxLen  = 0;
                $score   = 0;

                if ($apiName === $dbName) {
                    $score  = 100000;
                    $maxLen = strlen($dbName);
                } else {
                    $dbLen = strlen($dbName);
                    for ($i = 0; $i < $dbLen; $i++) {
                        for ($j = $i + 1; $j <= $dbLen; $j++) {
                            $sub = substr($dbName, $i, $j - $i);
                            if (str_contains($apiName, $sub) && strlen($sub) > $maxLen) {
                                $maxLen = strlen($sub);
                            }
                        }
                    }
                    $lengthPenalty = abs(strlen($apiName) - $dbLen);
                    $score         = ($maxLen * 1000) - $lengthPenalty;
                }

                $scores[] = [
                    'dbIdx'      => $dbIdx,
                    'apiIdx'     => $apiIdx,
                    'score'      => $score,
                    'maxLen'     => $maxLen,
                    'dbNameLen'  => strlen($dbName),
                    'apiNameLen' => strlen($apiName),
                ];
            }
        }

        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        foreach ($scores as $row) {
            if (in_array($row['dbIdx'], $usedDbIndices, true)) continue;

            $shorterLen = min($row['dbNameLen'], $row['apiNameLen']);
            $threshold  = $shorterLen * 0.40;
            $isLowScore = $row['maxLen'] < $threshold;

            if (!isset($apiToDbMap[$row['apiIdx']])) {
                $apiToDbMap[$row['apiIdx']] = ['dbItems' => [], 'lowScoreItems' => []];
            }

            if ($isLowScore) {
                $apiToDbMap[$row['apiIdx']]['lowScoreItems'][] = $dbItems[$row['dbIdx']];
            } else {
                $apiToDbMap[$row['apiIdx']]['dbItems'][] = $dbItems[$row['dbIdx']];
            }

            $usedDbIndices[] = $row['dbIdx'];
        }

        $matched = [];
        foreach ($apiToDbMap as $apiIdx => $value) {
            $mergedDb  = $this->mergeInvoices($value['dbItems']);
            $mergedLow = $this->mergeInvoices($value['lowScoreItems']);

            $matched[] = [
                'apiItem'       => $apiItems[$apiIdx],
                'dbItems'       => $mergedDb,
                'lowScoreItems' => $mergedLow,
                'lowScore'      => count($mergedDb) === 0 && count($mergedLow) > 0,
                'apiIndex'      => $apiIdx,
            ];
        }

        $matchedApiIndices = array_keys($apiToDbMap);
        foreach ($apiItems as $apiIdx => $apiItem) {
            if (!in_array($apiIdx, $matchedApiIndices, true)) {
                $matched[] = [
                    'apiItem'       => $apiItem,
                    'dbItems'       => [],
                    'lowScoreItems' => [],
                    'lowScore'      => false,
                    'apiIndex'      => $apiIdx,
                ];
            }
        }

        usort($matched, fn($a, $b) => $a['apiIndex'] <=> $b['apiIndex']);
        return $matched;
    }

    private function mergeInvoices(array $items): array
    {
        $map = [];
        foreach ($items as $item) {
            $key     = strtoupper(trim($item['name'])) . '_' . (float) $item['quantity'];
            $newDate = $item['date_invoice'] ? Carbon::parse($item['date_invoice']) : Carbon::createFromTimestamp(0);

            if (!isset($map[$key])) {
                $map[$key] = $item;
            } else {
                $existingDate = $map[$key]['date_invoice']
                    ? Carbon::parse($map[$key]['date_invoice'])
                    : Carbon::createFromTimestamp(0);

                if ($newDate->gt($existingDate)) {
                    $map[$key] = $item;
                }
            }
        }
        return array_values($map);
    }

    // ─── Private: Helpers ─────────────────────────────────────────────────────

    private function getLatestInvoiceDate(array $items): ?string
    {
        $latest = null;
        foreach ($items as $item) {
            if (empty($item['date_invoice'])) continue;
            if ($latest === null || Carbon::parse($item['date_invoice'])->gt(Carbon::parse($latest))) {
                $latest = $item['date_invoice'];
            }
        }
        return $latest;
    }

    private function collectUniqueNotes(array $items): ?string
    {
        $notes = collect($items)
            ->pluck('note')
            ->filter(fn($n) => !empty(trim((string) $n)))
            ->map(fn($n) => trim($n))
            ->unique()
            ->values();

        return $notes->isNotEmpty() ? $notes->implode(' | ') : null;
    }

    private function calculateQtySummary(float|int $orderedQty, array $dbItems): ?array
    {
        if (empty($dbItems)) return null;

        $totalReceived = array_sum(array_column($dbItems, 'quantity'));
        $ordered       = (float) $orderedQty;

        if ($totalReceived > $ordered) {
            return ['type' => 'excess',   'message' => sprintf('เกิน %.4f หน่วย', $totalReceived - $ordered)];
        }
        if ($totalReceived < $ordered) {
            return ['type' => 'shortage', 'message' => sprintf('ขาด %.4f หน่วย', $ordered - $totalReceived)];
        }
        return null;
    }

    private function resolveItemStatus(array $dbItems, string $storeStatus): array
    {
        if (empty($dbItems)) {
            return ['ไม่มีข้อมูล', 'status-no-data'];
        }
        if ($storeStatus === 'COMPLETED') {
            return ['จัดส่งสำเร็จ', 'status-complete'];
        }
        return ['กำลังจัดส่ง', 'status-pending'];
    }

    private function formatInvoiceTags(array $items, bool $mismatch): array
    {
        return array_map(fn($item) => [
            'invoice'      => $item['invoice']      ?? '-',
            'name'         => $item['name']          ?? '-',
            'date_invoice' => $item['date_invoice']  ?? null,
            'quantity'     => $item['quantity']      ?? 0,
            'mismatch'     => $mismatch,
        ], $items);
    }

   public function pull()
{
    ini_set('max_execution_time', 600);
    set_time_limit(600);

    $today = Carbon::now()->format('Y/m/d');

    $url = "https://docs.google.com/spreadsheets/d/1wRmbT3ZkN1Td-EoLfwRBCm5LxxUvkNwPkEo5UZxVysE/export?format=csv&gid=0";

    $rows = array_map('str_getcsv', file($url));
    unset($rows[0]);

    $inserted = 0;
    $errors = [];

    foreach ($rows as $index => $row) {
        try {
            if (!isset($row[0], $row[1], $row[2], $row[3], $row[4])) {
                continue;
            }

            $date_invoice = trim($row[0]);
            $invoice      = trim($row[1]);
            $name         = trim($row[2]);
            $quantity     = trim($row[3]);
            $ponum        = trim($row[4]);

            if (empty($ponum)) {
                continue;
            }

            if ($date_invoice !== $today) {
                continue;
            }

            $date = Carbon::createFromFormat('Y/m/d', $date_invoice)
                ->format('Y-m-d');

            Pooutside::create([
                'date_invoice' => $date,
                'invoice'      => $invoice,
                'name'        => $name,
                'quantity'    => $quantity,
                'ponum'       => $ponum
            ]);

            $inserted++;

        } catch (\Exception $e) {
            $errors[] = "Row {$index}: " . $e->getMessage();
        }
    }

    return response()->json([
        'status' => true,
        'message' => "ดึง PO วันที่ " . Carbon::now()->format('d/m/Y') . " สำเร็จ {$inserted} รายการ",
        'inserted' => $inserted,
        'errors' => $errors
    ]);
}

 public function download()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMzU2NzczYTQ1MmI3MWI4NWNjNmEzMjYzNmZmMzlmZjYxMzc2OGUwMDU4ZGU5NWQ2MDAyYmEwOTU4ZjM1MGE1NjMzYmQ5ZjAxOTZkYmYzYjMiLCJpYXQiOjE3NzI0MjE5MzMuMDA5NDA4LCJuYmYiOjE3NzI0MjE5MzMuMDA5NDEsImV4cCI6MTgwMzk1NzkzMy4wMDQ2NjcsInN1YiI6IjExNTM2MyIsInNjb3BlcyI6WyIqIl19.ZzpHxzeyk7Yy6YGl4oCZhUVvdInpqUAX066dOpyYsK6giAVBO1AQAg_tbobVFDK7NZ9rZsLcAmTpQjx2RIwQoeIiMowZXgF74v22fL2hJzaiVfTFdX_g00HxO5J37P1zFFT0mARvLHynOhcxM9qO3isKqHx7TnXvXDHQvSBczGo6TLVZcj4lp9e-cppi_RIzP3eiPXxl3Ou8I2tjY71I2SclZAVkbOAqD_3pUpTqsbPbrvuQl7hN7p2iWvfDL9b7dvQ984T4JIQNp2EdvBr7P7KtHrs1RU1HLp_aVLcmtQ46RQBY5Ymrupw9J1qM0DjBJYQC37jUZrUgL3AR8OYKtFFQwLMJ6_jfzyPYrkbJzcnWp6k8WlMS2fc3m4Fxl9yta9zaQarte-dLypWlhaAv2YcU6qdZ0vJF7JkrlrIDi_NaPRmak-GnCj60jSv83SLy_A7p4gnaBprWO7rkR3ctKl-rT0WjTVbn1aPcWnhCjyfZbYsLebOtZTiovvzv_FR6P2dMIe8YEDHf2o8MQ0qjE0XqmwnOOKWmPJ6T3WOgR_ujIKFlvgpvhSWxCZ5eJ9L1AYSR2HDUib_D8FbF0OzP58asPY0IzpuSENjbvtjvy3lx5zAsiVnKz6MjxMtF4--xO7uEeUlvgZUoWIFCpdAls2D7K_qu4UwP1Oj8_rvO-aY';
        $page = 1;
        $size = 20;
        $allData = [];

        do {
            $response = Http::withHeaders([
                'accept' => 'application/json, text/plain, */*',
                'authorization' => 'Bearer ' . $token,
                'client-build-date' => '2026-04-08T11:54:08.831Z',
                'client-lang' => 'th',
                'client-load-at' => now()->toIso8601String(),
                'client-version' => '22332a309a309c372a78b05155d66d318ec0f5f3',
                'origin' => 'https://app.smemove.com',
                'referer' => 'https://app.smemove.com/',
            ])->get('https://api.smemove.com/api/119394/purchase-order', [
                'sort' => 'documentNo',
                'direction' => 'DESC',
                'page' => $page,
                'size' => $size,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'API ERROR',
                    'page' => $page,
                    'body' => $response->body()
                ], 500);
            }

            $json = $response->json();
            $data = $json['data'] ?? [];
            if (empty($data)) break;

            $allData = array_merge($allData, $data);

            $page++;

        } while (true);

        return response()->json([
            'total' => count($allData),
            'data' => $allData
        ]);
    }

        public function invoicePage(Request $request)
    {
        return view('pooutside.invoice_search');
    }

    public function invoiceSuggest(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = Pooutside::where('invoice', 'LIKE', "%{$q}%")
            ->select('invoice', 'name', 'date_invoice')
            ->orderByRaw("CASE WHEN invoice LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('date_invoice', 'desc')
            ->limit(15)
            ->get()
            ->unique('invoice')
            ->values();

        return response()->json(['suggestions' => $suggestions]);
    }

    public function invoiceSearch(Request $request): JsonResponse
    {
        $invoice = trim($request->input('invoice', ''));

        if (empty($invoice)) {
            return response()->json(['success' => false, 'message' => 'กรุณากรอกหมายเลข Invoice'], 422);
        }

        $rows = Pooutside::where('invoice', $invoice)
            ->orderBy('date_invoice', 'desc')
            ->get(['date_invoice', 'invoice', 'name', 'quantity', 'ponum', 'note'])
            ->toArray();

        return response()->json([
            'success' => true,
            'count'   => count($rows),
            'rows'    => $rows,
        ]);
    }

    /* =====================================================================
       ✅ ดึงลิสต์ PO นอก (distinct ponum) จากตาราง Pooutside
       - Server-side pagination 30/หน้า + เรียงใหม่→เก่า + ค้นหา
       - return: { success, data:[{ponum,_m_ponum,_m_date,date_created}], meta:{...} }
       ===================================================================== */
    public function list(Request $request): JsonResponse
    {
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = 30;
        $search  = trim((string) $request->input('search', ''));

        $applySearch = function ($q) use ($search) {
            if ($search !== '') {
                $q->where('ponum', 'LIKE', "%{$search}%");
            }
            return $q;
        };

        // จำนวน PO ทั้งหมด (distinct ponum)
        $total = $applySearch(Pooutside::query())->distinct()->count('ponum');

        // 30 PO ของหน้านี้ + วันที่ invoice ล่าสุดของแต่ละ PO
        $rows = $applySearch(Pooutside::query())
            ->select('ponum')
            ->selectRaw('MAX(date_invoice) as latest_date')
            ->groupBy('ponum')
            ->orderByDesc('latest_date')   // ✅ ใหม่ → เก่า
            ->orderByDesc('ponum')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $data = $rows->map(fn($r) => [
            'ponum'        => $r->ponum,
            '_m_ponum'     => $r->ponum,
            '_m_date'      => $r->latest_date,
            'date_created' => $r->latest_date,
        ])->values();

        $lastPage = max(1, (int) ceil($total / $perPage));
        $from     = $total ? (($page - 1) * $perPage + 1) : 0;
        $to       = min($page * $perPage, $total);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'per_page'     => $perPage,
                'total'        => $total,
                'from'         => $from,
                'to'           => $to,
            ],
        ]);
    }
}