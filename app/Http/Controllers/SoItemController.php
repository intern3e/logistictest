<?php

namespace App\Http\Controllers;

use App\Models\fuzzy_so;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\quotation;
use App\Models\quotationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SoItemController extends Controller
{
    public function dashboard(Request $request)
    {
        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));
        $month  = trim($request->input('month', ''));

        $query = Quotation::with('items')->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_no', 'LIKE', "%{$search}%")
                  ->orWhere('customer_company', 'LIKE', "%{$search}%")
                  ->orWhere('customer_code', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($month) {
            $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m') = ?", [$month]);
        }

        $quotations = $query->paginate(20)->withQueryString();

        return view('sale.dashboardquotations', compact(
            'quotations', 'search', 'status', 'month'
        ));
    }

    /**
     * GET /quotations/{id}/pdf
     */
    public function downloadPdf(int $id)
    {
        $qt = Quotation::findOrFail($id);

        if (!$qt->hasPdf()) {
            abort(404, 'ไม่พบไฟล์ PDF');
        }

        return response()->download(
            $qt->pdf_full_path,
            $qt->quotation_no . '.pdf'
        );
    }

    public function index()
    {
        return view('sale.SoItem');
    }

    public function store(Request $request)
    {
        $request->validate([
            'doc_date'          => 'required|date',
            'customer_code'     => 'nullable|string|max:50',
            'customer_company'  => 'required|string|max:255',
            'customer_address'  => 'required|string',
            'customer_tel'      => 'required|string|max:100',
            'customer_tax'      => 'nullable|string|max:50',
            'customer_branch'   => 'nullable|string|max:100',
            'contact_name'      => 'required|string|max:255',
            'valid_days'        => 'required|integer|min:0',
            'expire_date'       => 'nullable|date',
            'credit_days'       => 'nullable|integer|min:0',
            'note'              => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.desc'      => 'required|string',
            'items.*.qty'       => 'required|numeric|min:0',
            'items.*.unit'      => 'nullable|string|max:50',
            'items.*.price'     => 'required|numeric|min:0',
            'items.*.item_new'  => 'nullable|string',
            'items.*.product_name' => 'nullable|string',
            'items.*.is_new'    => 'nullable|boolean',
            'pdf_base64'        => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // ★ 1) สร้างเลขที่ใบเสนอราคา
                $quotationNo = $this->generateQuotationNo();

                // ★ 2) สร้าง Quotation
                $quotation = Quotation::create([
                    'quotation_no'      => $quotationNo,
                    'doc_date'          => $request->input('doc_date'),
                    'customer_code'     => $request->input('customer_code'),
                    'customer_company'  => $request->input('customer_company'),
                    'customer_address'  => $request->input('customer_address'),
                    'customer_tel'      => $request->input('customer_tel'),
                    'customer_tax'      => $request->input('customer_tax'),
                    'customer_branch'   => $request->input('customer_branch'),
                    'contact_name'      => $request->input('contact_name'),
                    'valid_days'        => $request->input('valid_days', 0),
                    'expire_date'       => $request->input('expire_date'),
                    'credit_days'       => $request->input('credit_days'),
                    'note'              => $request->input('note'),
                    'status'            => 'draft',
                    'gross_amount'      => 0,
                    'vat_amount'        => 0,
                    'grand_total'       => 0,
                ]);

                // ★ 3) สร้าง QuotationItem
                $items = $request->input('items', []);
                foreach ($items as $idx => $item) {
                    $desc  = trim($item['desc'] ?? '');
                    $price = (float) ($item['price'] ?? 0);
                    if (!$desc && $price <= 0) continue;

                    QuotationItem::create([
                        'quotation_id'  => $quotation->id,
                        'line_no'       => $idx + 1,
                        'description'   => $desc,
                        'qty'           => (float) ($item['qty'] ?? 0),
                        'unit'          => $item['unit'] ?? null,
                        'unit_price'    => $price,
                        'item_new'      => $item['item_new'] ?? null,
                        'product_name'  => $item['product_name'] ?? null,
                        'is_new'        => (bool) ($item['is_new'] ?? false),
                        // amount จะ auto คำนวณจาก booted() ใน model
                    ]);
                }

                // ★ 4) คำนวณยอดรวม
                $quotation->load('items');
                $quotation->recalculate();
                $quotation->save();

                // ★ 5) บันทึก PDF
                $pdfBase64 = $request->input('pdf_base64');
                if ($pdfBase64) {
                    $quotation->storePdf($pdfBase64);
                }

                return response()->json([
                    'status'       => 'success',
                    'quotation_no' => $quotation->quotation_no,
                    'id'           => $quotation->id,
                    'grand_total'  => $quotation->grand_total,
                    'pdf_path'     => $quotation->pdf_path,
                    'message'      => "บันทึกใบเสนอราคา {$quotation->quotation_no} สำเร็จ",
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Quotation store error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * สร้างเลขที่ใบเสนอราคา: QT-20260609143022-0001
     * running number (0001) reset กลับ 1 ทุกต้นเดือน
     */
    private function generateQuotationNo(): string
    {
        $now      = now();
        $datetime = $now->format('YmdHis');                       // 20260609143022
        $monthPrefix = 'QT-' . $now->format('Ym');                // QT-202506

        // หา running number ล่าสุดของเดือนนี้
        $last = Quotation::where('quotation_no', 'LIKE', $monthPrefix . '%')
            ->orderByDesc('quotation_no')
            ->value('quotation_no');

        if ($last) {
            // QT-20260609143022-0003 → ตัด 4 ตัวท้าย → 0003 → +1
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        $running = str_pad($seq, 4, '0', STR_PAD_LEFT);          // 0001

        return "QT-{$datetime}-{$running}";                       // QT-20260609143022-0001
    }

    /* ================================================================
     *  POST /SoItem/batch-match
     *
     *  Flow:
     *  1) ค้นประวัติลูกค้ารายนี้ → matchOne() (pipeline กลาง)
     *     ├─ เจอ + มีราคา      → ใช้เลย
     *     ├─ เจอ + ไม่มีราคา   → findRefSuggestions() (logic เดียวกัน)
     *     └─ ไม่เจอ (ใหม่)     → findRefSuggestions() (logic เดียวกัน)
     * ================================================================ */
    public function batchMatch(Request $request)
    {
        $customerCode = trim($request->input('customer_code', ''));
        $items        = $request->input('items', []);

        if (empty($customerCode) || empty($items)) {
            return response()->json([]);
        }

        $allRows = fuzzy_so::where('customer_code', $customerCode)
            ->whereNotNull('product_name')
            ->where('product_name', '!=', '')
            ->orderByDesc('doc_date')
            ->get(['item_new', 'product_name', 'unit_price', 'unit', 'doc_date']);

        $results = [];
        foreach ($items as $inputName) {
            $keyword = trim($inputName);

            $base = [
                'input'           => $keyword,
                'matched'         => false,
                'item_new'        => null,
                'product_name'    => null,
                'unit_price'      => null,
                'has_price'       => false,
                'ref_suggestions' => [],
                'unit'            => null,
                'doc_date'        => null,
                'is_new'          => true,
                'match_keyword'   => null,
            ];

            if (mb_strlen($keyword) < 2) {
                $results[] = $base;
                continue;
            }

            // ★ ขั้น 1: ค้นในประวัติลูกค้ารายนี้ — pipeline กลาง
            $m = $allRows->isNotEmpty() ? $this->matchOne($keyword, $allRows) : null;

            if ($m) {
                $match     = $m['row'];
                $unitPrice = $match->unit_price !== null ? (float) $match->unit_price : null;
                $hasPrice  = $unitPrice !== null && $unitPrice > 0;

                // ★ matched แต่ไม่มีราคา → ค้นลูกค้าอื่นด้วย keyword + logic เดียวกัน
                $refSuggestions = $hasPrice ? [] : $this->findRefSuggestions($keyword);

                $results[] = [
                    'input'           => $keyword,
                    'matched'         => true,
                    'item_new'        => $match->item_new,
                    'product_name'    => $match->product_name,
                    'unit_price'      => $unitPrice,
                    'has_price'       => $hasPrice,
                    'ref_suggestions' => $refSuggestions,
                    'unit'            => $match->unit,
                    'doc_date'        => $match->doc_date
                        ? \Carbon\Carbon::parse($match->doc_date)->format('Y-m-d')
                        : null,
                    'is_new'          => false,
                    'match_keyword'   => $m['keyword'],
                    'similarity'      => $m['similarity'],
                ];
            } else {
                // ★ สินค้าใหม่สำหรับลูกค้ารายนี้ → ค้นลูกค้าอื่นด้วย keyword + logic เดียวกัน
                $base['ref_suggestions'] = $this->findRefSuggestions($keyword);
                $results[] = $base;
            }
        }

        return response()->json($results);
    }

    /* ================================================================
     *  ★★★ Matching pipeline กลาง ★★★
     *  ใช้ทั้งค้นลูกค้ารายนี้ + ลูกค้าอื่น (logic เดียวกันเป๊ะ)
     *
     *  scored → reverse → fuzzy → similarity gate ≥ 50
     *  คืน null ถ้าไม่ผ่านขั้นใดขั้นหนึ่ง
     * ================================================================ */
    private function matchOne(string $keyword, $rows): ?array
    {
        $match   = null;
        $matchKw = '';

        // ===== ขั้น 1: Scored Match =====
        $scored = $this->scoredMatch($keyword, $rows);
        if ($scored) {
            $match   = $scored['row'];
            $matchKw = $scored['keywords'];
        }

        // ===== ขั้น 2: Reverse Exact =====
        if (!$match) {
            $reversed = $this->reverseMatch($keyword, $rows);
            if ($reversed) {
                $match   = $reversed['row'];
                $matchKw = $reversed['keyword'];
            }
        }

        // ===== ขั้น 3: Fuzzy Reverse (Levenshtein ≥ 80%) =====
        if (!$match) {
            $fuzzy = $this->fuzzyReverseMatch($keyword, $rows);
            if ($fuzzy) {
                $match   = $fuzzy['row'];
                $matchKw = $fuzzy['keyword'];
            }
        }

        if (!$match) return null;

        // ===== ขั้น 4: Similarity Gate ≥ 50% =====
        $sim = $this->similarityCheck($keyword, $match->product_name);
        if ($sim < 50) return null;

        return ['row' => $match, 'keyword' => $matchKw, 'similarity' => $sim];
    }

    /* ================================================================
     *  ★★★ ค้นราคาอ้างอิงจากลูกค้าอื่น ★★★
     *  ใช้ keyword เดียวกัน + matching logic เดียวกับค้นลูกค้ารายนี้
     *
     *  1) Prefilter: ดึง candidate rows ด้วย keyword (performance)
     *  2) Group ตามบริษัท → run matchOne() ต่อบริษัท
     *  3) เรียงวันที่ล่าสุด → เอา 3 บริษัท
     * ================================================================ */
    private function findRefSuggestions(string $keyword): array
    {
        // --- 1) Prefilter candidate rows จากทุกลูกค้า ---
        $searchKeywords = $this->extractKeywords($keyword);
        $candidates = collect();

        foreach ($searchKeywords as $kw) {
            if (mb_strlen($kw) < 3) continue;

            $found = fuzzy_so::where('product_name', 'ILIKE', '%' . $kw . '%')
                ->whereNotNull('unit_price')
                ->where('unit_price', '>', 0)
                ->orderByDesc('doc_date')
                ->limit(500)
                ->get(['unit_price', 'so_no', 'customer_code', 'customer_name',
                       'doc_date', 'product_name', 'item_new', 'unit']);

            if ($found->isNotEmpty()) {
                $candidates = $found;
                break;
            }
        }

        if ($candidates->isEmpty()) return [];

        // --- 2) Group ตามบริษัท → run pipeline เดียวกับค้นลูกค้ารายนี้ ---
        $matches = [];
        foreach ($candidates->groupBy('customer_code') as $custCode => $rows) {
            $m = $this->matchOne($keyword, $rows);   // ★ logic เดียวกันเป๊ะ
            if ($m) {
                $matches[] = $m['row'];
            }
        }

        if (empty($matches)) return [];

        // --- 3) เรียงวันที่ล่าสุด → 3 บริษัท ---
        return collect($matches)
            ->sortByDesc(fn($r) => $r->doc_date ? strtotime($r->doc_date) : 0)
            ->take(3)
            ->values()
            ->map(fn($r) => [
                'unit_price'    => (float) $r->unit_price,
                'so_no'         => $r->so_no,
                'customer_name' => $r->customer_name,
                'doc_date'      => $r->doc_date
                    ? \Carbon\Carbon::parse($r->doc_date)->format('d/m/Y')
                    : '-',
                'product_name'  => $r->product_name,
            ])
            ->toArray();
    }

    /* ================================================================
     *  ★ Scored Match — จับหลาย keyword พร้อมกัน
     *
     *  แยก token จาก input → วน loop ทุก DB row
     *  → นับว่า row นั้น hit กี่ token
     *  → model token: weight = ความยาว
     *  → brand token: weight = 1
     *  → เอา row ที่ score สูงสุด
     *
     *  เงื่อนไขผ่าน:
     *  - มี model token → score ≥ 5 (model ยาวตัวเดียวก็พอ)
     *  - ไม่มี model → ต้อง hit brand ≥ 2 ตัว (ตัวเดียวกว้างเกิน)
     * ================================================================ */
    private function scoredMatch(string $keyword, $allRows): ?array
    {
        $searchTokens = $this->extractAllSearchTokens($keyword);
        if (empty($searchTokens)) return null;

        $hasModel = false;
        foreach ($searchTokens as $t) {
            if ($t['is_model']) { $hasModel = true; break; }
        }

        $bestRow    = null;
        $bestScore  = 0;
        $bestHits   = 0;
        $bestKws    = '';
        $bestDate   = null;   // ★ เก็บวันที่ของ best row

        foreach ($allRows as $row) {
            $nameLower   = mb_strtolower($row->product_name);
            $score       = 0;
            $hits        = 0;
            $hitKeywords = [];

            foreach ($searchTokens as $token) {
                $tokenLower = mb_strtolower($token['text']);
                if (mb_strpos($nameLower, $tokenLower) !== false) {
                    $weight = $token['is_model'] ? mb_strlen($token['text']) : 1;
                    $score += $weight;
                    $hits++;
                    $hitKeywords[] = $token['text'];
                }
            }

            if ($score <= 0) continue;

            // ★ score ใกล้เคียง ±1 → เลือก row ใหม่กว่า
            $shouldReplace = false;

            if (!$bestRow) {
                $shouldReplace = true;
            } else {
                $diff = $score - $bestScore;

                if ($diff > 1) {
                    // score สูงกว่าชัดเจน (ห่าง > 1) → แทนที่
                    $shouldReplace = true;
                } elseif ($diff >= -1) {
                    // score ใกล้เคียง (±1) → เลือก row ที่วันที่ใหม่กว่า
                    $rowDate = $row->doc_date ? strtotime($row->doc_date) : 0;
                    $bestDt  = $bestDate     ? strtotime($bestDate)      : 0;
                    if ($rowDate > $bestDt) {
                        $shouldReplace = true;
                    }
                }
                // diff < -1 → row เดิม score สูงกว่าชัดเจน → ไม่แทนที่
            }

            if ($shouldReplace) {
                $bestScore = $score;
                $bestHits  = $hits;
                $bestRow   = $row;
                $bestKws   = implode(' + ', $hitKeywords);
                $bestDate  = $row->doc_date;
            }
        }

        // ★ เงื่อนไขผ่าน
        if ($bestRow) {
            if ($hasModel && $bestScore >= 5) {
                return ['row' => $bestRow, 'keywords' => $bestKws];
            }
            if (!$hasModel && $bestHits >= 2) {
                return ['row' => $bestRow, 'keywords' => $bestKws];
            }
        }

        return null;
    }

    /* ================================================================
     *  Reverse Match Exact
     * ================================================================ */
    private function reverseMatch(string $keyword, $allRows): ?array
    {
        $inputLower = mb_strtolower($keyword);
        $bestRow    = null;
        $bestLen    = 0;
        $bestKw     = '';

        foreach ($allRows as $row) {
            foreach ($this->extractModels($row->product_name) as $model) {
                $modelLower = mb_strtolower($model);
                if (mb_strlen($modelLower) >= 3
                    && mb_strpos($inputLower, $modelLower) !== false
                    && mb_strlen($model) > $bestLen
                ) {
                    $bestLen = mb_strlen($model);
                    $bestRow = $row;
                    $bestKw  = $model;
                }
            }
        }

        return $bestRow ? ['row' => $bestRow, 'keyword' => $bestKw] : null;
    }

    /* ================================================================
     *  Fuzzy Reverse Match — Levenshtein ≥ 80%
     * ================================================================ */
    private function fuzzyReverseMatch(string $keyword, $allRows): ?array
    {
        $inputModels = $this->extractModels($keyword);
        if (empty($inputModels)) return null;

        $bestRow = null;
        $bestSim = 0;
        $bestKw  = '';

        foreach ($allRows as $row) {
            foreach ($this->extractModels($row->product_name) as $dModel) {
                foreach ($inputModels as $iModel) {
                    $sim = $this->levenshteinPercent($iModel, $dModel);
                    if ($sim >= 80 && $sim > $bestSim) {
                        $bestSim = $sim;
                        $bestRow = $row;
                        $bestKw  = $iModel . ' ≈ ' . $dModel . ' (' . round($sim) . '%)';
                    }
                }
            }
        }

        return $bestRow ? ['row' => $bestRow, 'keyword' => $bestKw] : null;
    }

    /* ================================================================
     *  ★ Similarity Gate ≥ 50%
     *
     *  มี model ทั้ง 2 ฝั่ง → best pair
     *  ไม่มี model → brand overlap ratio
     * ================================================================ */
    private function similarityCheck(string $input, string $dbProductName): float
    {
        $inputModels = $this->extractModels($input);
        $dbModels    = $this->extractModels($dbProductName);

        if (!empty($inputModels) && !empty($dbModels)) {
            return $this->bestPairSimilarity($inputModels, $dbModels);
        }

        if (!empty($inputModels) || !empty($dbModels)) {
            $setA = !empty($inputModels) ? $inputModels : $this->extractBrands($input);
            $setB = !empty($dbModels)    ? $dbModels    : $this->extractBrands($dbProductName);
            if (!empty($setA) && !empty($setB)) {
                return $this->bestPairSimilarity($setA, $setB);
            }
        }

        $inputBrands = $this->extractBrands($input);
        $dbBrands    = $this->extractBrands($dbProductName);

        if (empty($inputBrands) || empty($dbBrands)) {
            return $this->levenshteinPercent($input, $dbProductName);
        }

        return $this->overlapRatio($inputBrands, $dbBrands);
    }

    private function bestPairSimilarity(array $setA, array $setB): float
    {
        $bestSim = 0;
        foreach ($setA as $a) {
            $aLower = mb_strtolower($a);
            foreach ($setB as $b) {
                $bLower = mb_strtolower($b);

                if (mb_strpos($bLower, $aLower) !== false || mb_strpos($aLower, $bLower) !== false) {
                    $shorter = min(mb_strlen($aLower), mb_strlen($bLower));
                    $longer  = max(mb_strlen($aLower), mb_strlen($bLower));
                    $sim     = ($shorter / $longer) * 100;
                } else {
                    $sim = $this->levenshteinPercent($a, $b);
                }
                $bestSim = max($bestSim, $sim);
            }
        }
        return round($bestSim, 1);
    }

    private function overlapRatio(array $inputBrands, array $dbBrands): float
    {
        if (empty($inputBrands)) return 0;

        $dbLower = array_map(fn($b) => mb_strtolower($b), $dbBrands);
        $hits    = 0;

        foreach ($inputBrands as $ib) {
            $ibLower = mb_strtolower($ib);
            foreach ($dbLower as $db) {
                if ($ibLower === $db || mb_strpos($db, $ibLower) !== false || mb_strpos($ibLower, $db) !== false) {
                    $hits++;
                    break;
                }
            }
        }

        return round(($hits / count($inputBrands)) * 100, 1);
    }

    private function levenshteinPercent(string $a, string $b): float
    {
        $a = mb_strtolower(trim($a));
        $b = mb_strtolower(trim($b));
        if ($a === $b) return 100.0;
        $maxLen = max(strlen($a), strlen($b));
        if ($maxLen === 0) return 100.0;
        return max(0, (1 - levenshtein($a, $b) / $maxLen) * 100);
    }

    /* ================================================================
     *  GET /SoItem/sales-history/{customerCode}
     * ================================================================ */
    public function salesHistory(string $customerCode, Request $request)
    {
        try {
            $itemNew    = trim($request->input('item_new', ''));
            $rawKeyword = trim($request->input('keyword', ''));

            if (empty($itemNew) && empty($rawKeyword)) {
                return response()->json([]);
            }

            $records = collect();
            $fields  = [
                'so_no', 'doc_date', 'customer_code', 'customer_name',
                'salesperson', 'item_new', 'product_name',
                'qty', 'unit', 'unit_price', 'line_amount', 'so_total',
            ];

            if (!empty($itemNew)) {
                $ref = fuzzy_so::where('customer_code', $customerCode)
                    ->where('item_new', $itemNew)
                    ->first(['product_name']);

                if ($ref && $ref->product_name) {
                    $keywords = $this->extractKeywords($ref->product_name);
                    foreach ($keywords as $kw) {
                        $found = fuzzy_so::where('customer_code', $customerCode)
                            ->where('product_name', 'ILIKE', '%' . $kw . '%')
                            ->orderByDesc('doc_date')
                            ->limit(50)
                            ->get($fields);

                        if ($found->isNotEmpty()) {
                            $records = $found;
                            break;
                        }
                    }
                }
            }

            if ($records->isEmpty() && !empty($rawKeyword)) {
                $keywords = $this->extractKeywords($rawKeyword);
                foreach ($keywords as $kw) {
                    $found = fuzzy_so::where('customer_code', $customerCode)
                        ->where('product_name', 'ILIKE', '%' . $kw . '%')
                        ->orderByDesc('doc_date')
                        ->limit(50)
                        ->get($fields);

                    if ($found->isNotEmpty()) {
                        $records = $found;
                        break;
                    }
                }
            }

            $mapped = $records->map(function ($r) {
                return [
                    'so_no'          => $r->so_no,
                    'doc_date_raw'   => $r->doc_date
                        ? \Carbon\Carbon::parse($r->doc_date)->format('d/m/Y')
                        : '-',
                    'customer_code'  => $r->customer_code,
                    'customer_name'  => $r->customer_name,
                    'salesperson'    => $r->salesperson,
                    'item_new'       => $r->item_new,
                    'product_name'   => $r->product_name,
                    'qty'            => $r->qty !== null ? (float) $r->qty : 0,
                    'unit'           => $r->unit,
                    'unit_price'     => $r->unit_price !== null ? (float) $r->unit_price : 0,
                    'line_amount'    => $r->line_amount !== null ? (float) $r->line_amount : 0,
                    'so_total'       => $r->so_total !== null ? (float) $r->so_total : 0,
                ];
            });

            return response()->json($mapped);

        } catch (\Exception $e) {
            Log::error('SalesHistory error: ' . $e->getMessage());
            return response()->json([
                'error'   => $e->getMessage(),
                'message' => 'ไม่สามารถดึงประวัติการขายได้',
            ], 500);
        }
    }

    /* ================================================================
     *  TOKEN EXTRACTION
     * ================================================================ */

    private function extractAllSearchTokens(string $input): array
    {
        $clean  = $this->cleanQuotes($input);
        $tokens = $this->smartSplit($clean);
        $tokens = array_values(array_filter($tokens, fn($t) => mb_strlen($t) >= 2));

        $models = [];
        $brands = [];

        foreach ($tokens as $t) {
            if (preg_match('/^\d{1,3}$/', $t)) continue;
            if ($this->isElecSpec($t)) continue;

            if (preg_match('/[0-9]/', $t) && preg_match('/[A-Za-z]/u', $t)) {
                // ★ dimension เช่น 4x4, 2x4 → treat as brand (low weight)
                //   ไม่ใช่ model เพราะมันปรากฏในสินค้าต่างประเภทได้
                if ($this->isPureDimension($t)) {
                    $brands[] = $t;
                } else {
                    $models[] = $t;
                }
            } elseif (!preg_match('/[0-9]/', $t)) {
                $brands[] = $t;
            }
        }

        usort($models, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        $result = [];

        if (!empty($brands) && !empty($models)) {
            $mainBrand = $brands[0];
            foreach ($models as $m) {
                $result[] = ['text' => $mainBrand . ' ' . $m, 'is_model' => true];
            }
        }

        foreach ($models as $m) {
            $result[] = ['text' => $m, 'is_model' => true];
        }
        foreach ($brands as $b) {
            $result[] = ['text' => $b, 'is_model' => false];
        }

        return $result;
    }

    private function extractKeywords(string $input): array
    {
        $clean    = $this->cleanQuotes($input);
        $keywords = [];

        if (mb_strlen($clean) <= 30) {
            $keywords[] = $clean;
        }

        $tokens = $this->smartSplit($clean);
        $tokens = array_filter($tokens, fn($t) => mb_strlen($t) >= 2);

        $models = [];
        $brands = [];

        foreach ($tokens as $t) {
            if (preg_match('/^\d{1,3}$/', $t)) continue;
            if ($this->isElecSpec($t)) continue;

            if (preg_match('/[0-9]/', $t) && preg_match('/[A-Za-z]/u', $t)) {
                $models[] = $t;
            } elseif (!preg_match('/[0-9]/', $t)) {
                $brands[] = $t;
            }
        }

        usort($models, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
        usort($brands, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        foreach ($models as $m) {
            if (!in_array($m, $keywords)) $keywords[] = $m;
        }
        if (empty($models)) {
            foreach ($brands as $b) {
                if (!in_array($b, $keywords)) $keywords[] = $b;
            }
        }

        return $keywords;
    }

    private function extractModels(string $text): array
    {
        $clean  = $this->cleanQuotes($text);
        $tokens = $this->smartSplit($clean);

        $models = [];
        foreach ($tokens as $t) {
            if (preg_match('/^\d{1,3}$/', $t)) continue;
            if ($this->isElecSpec($t)) continue;

            if (mb_strlen($t) >= 3
                && preg_match('/[0-9]/', $t)
                && preg_match('/[A-Za-z]/u', $t)
                && !$this->isPureDimension($t)  // ★ ไม่ใช้ 4x4, 2x4 เป็น model
            ) {
                $models[] = $t;
            }
        }

        usort($models, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
        return $models;
    }

    private function extractBrands(string $text): array
    {
        $clean  = $this->cleanQuotes($text);
        $tokens = $this->smartSplit($clean);

        $brands = [];
        foreach ($tokens as $t) {
            if (mb_strlen($t) >= 2 && !preg_match('/[0-9]/', $t)) {
                $brands[] = $t;
            }
        }
        return $brands;
    }

    /* ================================================================
     *  smartSplit + camelBoundarySplit
     * ================================================================ */
    private function smartSplit(string $text): array
    {
        $rawTokens = preg_split('/[\s:,\(\)]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $result = [];
        foreach ($rawTokens as $token) {
            foreach ($this->camelBoundarySplit($token) as $s) {
                $s = trim($s);
                if (mb_strlen($s) >= 1) $result[] = $s;
            }
        }
        return $result;
    }

    private function camelBoundarySplit(string $token): array
    {
        if (mb_strlen($token) <= 3) return [$token];
        if (preg_match('/^[A-Z0-9\-\/\.]+$/u', $token)) return [$token];

        $chars = preg_split('//u', $token, -1, PREG_SPLIT_NO_EMPTY);
        $parts = [];
        $buf   = $chars[0];

        for ($i = 1; $i < count($chars); $i++) {
            $prev = $chars[$i - 1];
            $curr = $chars[$i];
            $cut  = false;

            if (preg_match('/[a-z]/', $prev) && preg_match('/[A-Z]/', $curr)) {
                $cut = true;
            }

            if (!$cut && preg_match('/[0-9]/', $prev) && preg_match('/[A-Z]/', $curr)) {
                if (!preg_match('/^[VAWHKPDC]$/i', $curr)) {
                    $cut = true;
                }
            }

            if (!$cut && preg_match('/[A-Z]/', $prev) && preg_match('/[A-Z]/', $curr)) {
                if (isset($chars[$i + 1]) && preg_match('/[a-z]/', $chars[$i + 1])) {
                    $cut = true;
                }
            }

            if (!$cut && preg_match('/[A-Za-z]/', $prev) && preg_match('/[0-9]/', $curr)) {
                $alphaRun = 0;
                for ($j = $i - 1; $j >= 0; $j--) {
                    if (preg_match('/[A-Za-z]/', $chars[$j])) $alphaRun++;
                    else break;
                }
                if ($alphaRun >= 3) $cut = true;
            }

            if ($cut) { $parts[] = $buf; $buf = $curr; }
            else       { $buf .= $curr; }
        }

        if ($buf !== '') $parts[] = $buf;
        return $parts;
    }

    /* ================================================================
     *  isElecSpec
     * ================================================================ */
    private function isElecSpec(string $t): bool
    {
        $t = trim($t);

        if (preg_match('/^[AD]C\d+[-\/]?\d*V?$/i', $t)) return true;
        if (preg_match('/^\d+V(DC|AC)?$/i', $t)) return true;
        if (preg_match('/^\d+[-\/]\d+V(DC|AC)?$/i', $t)) return true;
        if (preg_match('/^\d+(\/\d+)?Hz\.?$/i', $t)) return true;
        if (preg_match('/^\d+A$/i', $t)) return true;
        if (preg_match('/^\d+(\.\d+)?(W|KW|MW|HP)$/i', $t)) return true;
        if (preg_match('/^\d+P[Hh]?$/i', $t)) return true;
        if (preg_match('/^\d+PHASE$/i', $t)) return true;
        if (preg_match('/^\d+(nc|no)$/i', $t)) return true;

        return false;
    }

    private function cleanQuotes(string $text): string
    {
        return str_replace(['"', "'", "\xe2\x80\x9c", "\xe2\x80\x9d", '`'], '', trim($text));
    }

    /* ================================================================
     *  isPureDimension — 4x4, 2x4, 1.5x2.5 ไม่ใช่ model
     * ================================================================ */
    private function isPureDimension(string $t): bool
    {
        $t = trim($t);
        // NxN, NxNxN, N.NxN.N (รวม optional " หรือ ' ท้าย)
        if (preg_match('/^\d+(\.\d+)?[xX×]\d+(\.\d+)?([xX×]\d+(\.\d+)?)?["\']?$/u', $t)) return true;
        return false;
    }
}