<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Pooutside;
use Carbon\Carbon;

class PooutsideController extends Controller
{
    // ─── Views ────────────────────────────────────────────────────────────────

  public function dashboard(Request $request, $name = 'Guest')
{
    // urldecode ช่วยเปลี่ยน %20 ใน URL ให้เป็นช่องว่างปกติ
    $userName = urldecode($name);

    return view('pooutside.dashboard', [
        'userName' => $userName
    ]);
}

    // ─── API Endpoints ────────────────────────────────────────────────────────

    /**
     * GET /pooutside/search?ponum=XXXX-XXXXX
     * รวม local DB + ERP API แล้วส่งข้อมูลสำเร็จรูปกลับ View
     */
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

        // Match ก่อน แล้วค่อยส่งต่อให้ทุก builder
        $matchedProducts = $this->matchProducts($localData, $erpData['ms_podt'] ?? []);

        // ✅ เก็บเฉพาะ dbItems ที่ match จริง (score ≥ 20%) เพื่อคำนวณ timeline
        $validDbItems = collect($matchedProducts)
            ->filter(fn($m) => !$m['lowScore'])
            ->flatMap(fn($m) => $m['dbItems'])
            ->toArray();

        return response()->json([
            'success'  => true,
            'vendor'   => $this->buildVendorInfo($erpData),
            'timeline' => $this->buildTimeline($erpData, $validDbItems), // ✅ ใช้เฉพาะ valid
            'notes'    => $this->collectUniqueNotes($localData),         // notes ยังดูทั้งหมดได้
            'items'    => $this->buildItemsFromMatched($matchedProducts, $erpData),
        ]);
    }
    /**
     * GET /pooutside/check?ponum=XXXX-XXXXX  (ใช้งานเดิม — คงไว้เพื่อ backward compat)
     */
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
            $response = Http::get('http://server_update:8000/api/getPODetail', ['PONum' => $poNum]);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Error fetching PO from ERP: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Private: Builders ────────────────────────────────────────────────────

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

    /**
     * จับคู่ DB items กับ API items ด้วย longest-common-substring + threshold 20%
     */
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
                    'apiNameLen' => strlen($apiName), // ✅ เพิ่ม
                ];
            }
        }

        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        foreach ($scores as $row) {
            if (in_array($row['dbIdx'], $usedDbIndices, true)) continue;

            // ✅ ใช้ชื่อที่สั้นกว่าเป็น base และ threshold 50%
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
    /**
     * Merge invoices: เก็บ record ที่ date_invoice ล่าสุดต่อ name+qty
     */
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

    // ⭐ วันที่ปัจจุบัน (วันที่กดปุ่ม)
    $today = Carbon::now()->format('Y/m/d'); // 2026/02/13

    $url = "https://docs.google.com/spreadsheets/d/1wRmbT3ZkN1Td-EoLfwRBCm5LxxUvkNwPkEo5UZxVysE/export?format=csv&gid=0";

    $rows = array_map('str_getcsv', file($url));
    unset($rows[0]); // ลบ header

    $inserted = 0;
    $errors = [];

    foreach ($rows as $index => $row) {
        try {
            // เช็คว่ามี column ครบ
            if (!isset($row[0], $row[1], $row[2], $row[3], $row[4])) {
                continue;
            }

            $date_invoice = trim($row[0]);
            $invoice      = trim($row[1]);
            $name         = trim($row[2]);
            $quantity     = trim($row[3]);
            $ponum        = trim($row[4]);

            // ⭐ ข้ามถ้า ponum เป็น null หรือว่าง
            if (empty($ponum)) {
                continue;
            }

            // ⭐ ดึงเฉพาะวันที่วันนี้เท่านั้น
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
 
    /**
     * GET /pooutside/invoice-suggest?q=INV
     * Autocomplete: คืน invoice ที่ใกล้เคียง (ไม่เกิน 15 รายการ)
     */
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
 
    /**
     * GET /pooutside/invoice-search?invoice=INV001
     * ดึงทุก row ที่ invoice ตรง
     */
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
}