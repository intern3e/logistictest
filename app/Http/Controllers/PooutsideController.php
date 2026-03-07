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

    public function dashboard(Request $request)
    {
        return view('pooutside.dashboard');
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

        // 1) ดึงข้อมูลจาก DB
        $localData = Pooutside::where('ponum', $ponum)
            ->orderBy('date_invoice', 'desc')
            ->get()
            ->toArray();

        // 2) ดึงข้อมูลจาก ERP
        $erpData = $this->fetchErpPO($ponum);
        if (!$erpData) {
            return response()->json(['success' => false, 'message' => 'ไม่พบเลข PO'], 404);
        }

        // 3) Build response
        return response()->json([
            'success'  => true,
            'vendor'   => $this->buildVendorInfo($erpData),
            'timeline' => $this->buildTimeline($erpData, $localData),
            'notes'    => $this->collectUniqueNotes($localData),
            'items'    => $this->buildItemList($erpData, $localData),
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

    private function buildItemList(array $erp, array $localData): array
    {
        $apiItems = $erp['ms_podt'] ?? [];
        $matched  = $this->matchProducts($localData, $apiItems);

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
        }, $matched);
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
                $apiName   = strtoupper(trim($apiItem['GoodName']));
                $maxLen    = 0;
                $score     = 0;

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
                    'dbIdx'    => $dbIdx,
                    'apiIdx'   => $apiIdx,
                    'score'    => $score,
                    'maxLen'   => $maxLen,
                    'dbNameLen' => strlen($dbName),
                ];
            }
        }

        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        foreach ($scores as $row) {
            if (in_array($row['dbIdx'], $usedDbIndices, true)) continue;

            $threshold  = $row['dbNameLen'] * 0.20;
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

        // API items ที่ไม่มี DB match เลย
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
}