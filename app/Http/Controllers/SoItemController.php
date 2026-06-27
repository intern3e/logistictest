<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\fuzzy_so;
use App\Models\historyquotation;
use App\Models\quotation;
use App\Models\quotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
class SoItemController extends Controller
{
    private const CE_MIN_SCORE = 70.0;

    private function postJson(string $url, array $payload, int $timeout = 60): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        $raw = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        return ($status === 200 && $raw) ? (json_decode($raw, true) ?: []) : [];
    }

    private function getJson(string $url, int $timeout = 10): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 5]);
        $raw = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        return ($status === 200 && $raw) ? (json_decode($raw, true) ?: []) : [];
    }
    public function index(Request $request) { return view('sale.SoItem'); }

    public function store(Request $request)
    {
        $request->validate([
            'doc_date'             => 'required|date',
            'customer_code'        => 'nullable|string|max:50',
            'customer_company'     => 'required|string|max:255',
            'customer_address'     => 'required|string',
            'customer_tel'         => 'required|string|max:100',
            'customer_tax'         => 'nullable|string|max:50',
            'customer_branch'      => 'nullable|string|max:100',
            'contact_name'         => 'required|string|max:255',
            'valid_days'           => 'required|integer|min:0',
            'expire_date'          => 'nullable|date',
            'credit_days'          => 'nullable|integer|min:0',
            'note'                 => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.desc'         => 'required|string',
            'items.*.qty'          => 'required|numeric|min:0',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.price'        => 'required|numeric|min:0',
            'pdf_base64'           => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $quotationNo = $this->generateQuotationNo();
                $quotation = Quotation::create([
                    'quotation_no'     => $quotationNo,
                    'doc_date'         => $request->input('doc_date'),
                    'customer_code'    => $request->input('customer_code'),
                    'customer_company' => $request->input('customer_company'),
                    'customer_address' => $request->input('customer_address'),
                    'customer_tel'     => $request->input('customer_tel'),
                    'customer_tax'     => $request->input('customer_tax'),
                    'customer_branch'  => $request->input('customer_branch'),
                    'contact_name'     => $request->input('contact_name'),
                    'valid_days'       => $request->input('valid_days', 0),
                    'expire_date'      => $request->input('expire_date'),
                    'credit_days'      => $request->input('credit_days'),
                    'note'             => $request->input('note'),
                    'status'           => 'draft',
                    'gross_amount'     => 0, 'vat_amount' => 0, 'grand_total' => 0,
                ]);
                foreach ($request->input('items', []) as $idx => $item) {
                    $desc  = trim($item['desc'] ?? '');
                    $price = (float) ($item['price'] ?? 0);
                    if (!$desc && $price <= 0) continue;
                    QuotationItem::create([
                        'quotation_no' => $quotation->quotation_no, 'line_no' => $idx + 1,
                        'description'  => $desc, 'qty' => (float) ($item['qty'] ?? 0),
                        'unit' => $item['unit'] ?? null, 'unit_price' => $price,
                    ]);
                }
                $quotation->load('items');
                $quotation->recalculate();
                $quotation->save();
                if ($pdfBase64 = $request->input('pdf_base64')) $quotation->storePdf($pdfBase64);
                return response()->json([
                    'status' => 'success', 'quotation_no' => $quotation->quotation_no,
                   'id' => $quotation->quotation_no, 'grand_total'=> $quotation->grand_total,
                    'pdf_path' => $quotation->pdf_path,
                    'message' => "บันทึกใบเสนอราคา {$quotation->quotation_no} สำเร็จ",
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Quotation store error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'บันทึกไม่สำเร็จ: ' . $e->getMessage()], 500);
        }
    }

    private function generateQuotationNo(): string
    {
        $monthPrefix = 'QT' . now()->format('ym');
        $last = Quotation::where('quotation_no', 'LIKE', $monthPrefix . '-%')
            ->orderByDesc('quotation_no')->value('quotation_no');
        $seq = $last ? ((int) substr($last, -4) + 1) : 1;
        return "{$monthPrefix}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function uploadPdf(string $quotationNo, Request $request)
    {
        $request->validate(['pdf_base64' => 'required|string']);
        $quotation = Quotation::where('quotation_no', $quotationNo)->firstOrFail();
        try {
            $pdfData = base64_decode($request->input('pdf_base64'), true);
            if ($pdfData === false) return response()->json(['status' => 'error', 'message' => 'Invalid base64'], 400);
            $file = 'quotations/' . $quotation->quotation_no . '.pdf';
            Storage::disk('public')->put($file, $pdfData);
            $quotation->update(['pdf_path' => $file]);
            return response()->json(['status' => 'success', 'pdf_path' => $quotation->pdf_path]);
        } catch (\Exception $e) {
            Log::error('Upload PDF error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════
    // OCR
    // ══════════════════════════════════════════════════
    public function ocrProcess(Request $request)
    {
        set_time_limit(0);
        $request->validate(['files' => 'nullable|array|max:20', 'files.*' => 'file|max:51200', 'text' => 'nullable|string|max:100000']);
        $textInput = trim($request->input('text', ''));
        $files = $request->file('files', []);
        if (empty($files) && empty($textInput)) return response()->json(['status' => 'error', 'message' => 'กรุณาอัพโหลดไฟล์หรือวางข้อความ'], 422);
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
        $boundary = '----FormBoundary' . bin2hex(random_bytes(8));
        $body = '';
        foreach ($files as $file) {
            $body .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"files[]\"; filename=\"{$file->getClientOriginalName()}\"\r\nContent-Type: " . ($file->getMimeType() ?: 'application/octet-stream') . "\r\n\r\n" . file_get_contents($file->getRealPath()) . "\r\n";
        }
        $body .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"text\"\r\n\r\n{$textInput}\r\n--{$boundary}--\r\n";
        $ch = curl_init("{$pythonUrl}/api/ocr/process");
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_HTTPHEADER => ["Content-Type: multipart/form-data; boundary={$boundary}"], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 60]);
        $raw = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); $curlErr = curl_error($ch); curl_close($ch);
        if ($curlErr) return response()->json(['status' => 'error', 'message' => 'เชื่อมต่อ Python ไม่ได้: ' . $curlErr], 503);
        $result = json_decode($raw, true);
        if ($status !== 200 || empty($result['job_name'])) return response()->json(['status' => 'error', 'message' => 'Python ตอบกลับผิดพลาด: ' . substr($raw, 0, 200)], 502);
        return response()->json(['status' => 'processing', 'job_name' => $result['job_name'], 'message' => 'กำลังประมวลผล...']);
    }

    public function ocrStatus(string $jobName)
    {
        $jobName = preg_replace('/[^a-zA-Z0-9_]/', '', $jobName);
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
        $ch = curl_init("{$pythonUrl}/api/ocr/status/{$jobName}");
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_CONNECTTIMEOUT => 5]);
        $raw = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        if (!$raw || $status !== 200) return response()->json(['status' => 'processing']);
        $result = json_decode($raw, true);
        if (($result['status'] ?? '') !== 'done') return response()->json(['status' => 'processing']);
        return response()->json(['status' => 'done', 'job_name' => $jobName, 'table' => $result['table'] ?? [], 'ocr_text' => $result['ocr_text'] ?? '', 'message' => $result['message'] ?? 'OCR สำเร็จ']);
    }

    public function ocrDownload(string $type, string $filename)
    {
        if (!in_array($type, ['ocr', 'output'])) abort(404);
        $path = storage_path("app/public/quotation/{$type}/" . basename($filename));
        if (!file_exists($path)) abort(404);
        return response()->download($path, basename($filename), ['Content-Type' => $type === 'ocr' ? 'text/plain' : 'text/csv']);
    }

    // ══════════════════════════════════════════════════
    // CUSTOMER SEARCH + RELATED
    // ══════════════════════════════════════════════════
    public function searchCustomers(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 2) return response()->json([]);
        $rows = customers::where(function ($query) use ($q) {
            $query->where('customer_name', 'LIKE', "%{$q}%")->orWhere('customer_code', 'LIKE', "%{$q}%")->orWhere('contact_name', 'LIKE', "%{$q}%");
        })->limit(20)->get(['customer_code','customer_name','address','phone','tax_id','branch','branch_type','contact_name','email']);
        return response()->json($rows->map(fn($c) => [
            'customer_code' => $c->customer_code, 'customer_name' => $c->customer_name,
            'address' => $c->address, 'phone' => $c->phone, 'tax_id' => $c->tax_id,
            'branch' => $c->branch, 'branch_type' => $c->branch_type,
            'contact_name' => $c->contact_name, 'email' => $c->email,
        ]));
    }

    public function relatedCustomers(string $customerCode)
    {
        $dot = strpos($customerCode, '.');
        $headCode = $dot !== false ? substr($customerCode, 0, $dot) : $customerCode;
        $rows = DB::connection('pgsql')->table('fuzzy_so')
            ->where(function($q) use ($headCode) {
                $q->where('customer_code', $headCode)->orWhere('customer_code', 'LIKE', $headCode.'.%');
            })
            ->where('customer_code', '!=', $customerCode)
            ->whereNotNull('customer_name')
            ->select('customer_code','customer_name')
            ->distinct()->orderBy('customer_code')->get();
        return response()->json($rows->unique('customer_code')->values()->map(fn($c) => [
            'customer_code' => $c->customer_code, 'customer_name' => $c->customer_name,
        ]));
    }

    // ══════════════════════════════════════════════════
    // ★ HELPER: เรียก Python CE rerank
    // ══════════════════════════════════════════════════
    private function callCeRerank(array $items, int $topK = 5, float $minScore = 1.0): array
    {
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');

        $ch = curl_init("{$pythonUrl}/api/batch-rerank");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'items'     => $items,
                'top_k'     => $topK,
                'min_score' => $minScore,
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 180,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $raw    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($err || $status !== 200) {
            Log::warning("[CE] rerank failed: HTTP={$status} err={$err}");
            return [];
        }

        $resp = json_decode($raw, true);
        $timing = $resp['timing_ms'] ?? '?';
        $count  = count($resp['results'] ?? []);
        Log::info("[CE] rerank OK: {$count} items in {$timing}ms");

        return $resp['results'] ?? [];
    }

    // ══════════════════════════════════════════════════
    // ★ HELPER: tokenize สำหรับ DB query
    // ══════════════════════════════════════════════════
    private function extractSearchTerms(string $text): array
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }
        $text = str_replace(['"', "'", '`', '%', '_'], ' ', $text);
        $raw  = preg_split('/[\s,\(\)\[\]:\/]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $terms = [];
        foreach ($raw as $t) {
            $t = trim($t, '.-·•');
            $t = mb_convert_encoding($t, 'UTF-8', 'UTF-8');
            if (!mb_check_encoding($t, 'UTF-8')) continue;
            if (preg_match('/[\x80-\xff]/', $t) && !preg_match('//u', $t)) continue;
            if (mb_strlen($t) < 3) continue;
            if (preg_match('/^\d+(\.\d+)?$/', $t)) continue;
            if (preg_match('/^(EA|PCS?|SET|BOX|ROL|KG|ชิ้น|อัน|เส้น|ม้วน|แผ่น|กล่อง|ชุด)\.?$/iu', $t)) continue;
            $terms[] = $t;
        }

        $terms = array_values(array_unique($terms));
        usort($terms, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        return $terms;
    }

    // ══════════════════════════════════════════════════
    // ★ HELPER: embed search (ตอนนี้ redirect ไป pg_trgm)
    //   เก็บไว้เพื่อ backward compat — Python ตอบ pg_trgm แทน
    // ══════════════════════════════════════════════════
    private function callEmbedSearch(array $queries, int $topK = 20, float $minScore = 30): array
    {
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');

        $ch = curl_init("{$pythonUrl}/api/embed-search");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'queries'   => $queries,
                'top_k'     => $topK,
                'min_score' => $minScore,
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $raw    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($err || $status !== 200) {
            Log::warning("[EMBED] search failed: HTTP={$status} err={$err}");
            return [];
        }

        $resp = json_decode($raw, true);
        return $resp['results'] ?? [];
    }

    private function ensureEmbedIndex(): bool
    {
        // ★ pg_trgm ไม่ต้อง build index — return true เสมอ
        return true;
    }

    public function buildEmbedIndex()
    {
        // ★ pg_trgm ไม่ต้อง build — ตอบ OK เลย
        return response()->json(['status' => 'ready', 'message' => 'pg_trgm uses live DB, no build needed']);
    }

    // ══════════════════════════════════════════════════
    // ★ FLOW 1: BATCH MATCH (💰 ราคา — fuzzy_so กลุ่มลูกค้า)
    //   ★ ใช้ Qwen ตัดสินเหมือน Flow 2 (ไม่ใช่ CE อย่างเดียว)
    //   Python _match_one() จะ: CE score → Qwen เลือก (ดู CE + doc_date)
    // ══════════════════════════════════════════════════
    public function batchMatch(Request $request)
    {
        set_time_limit(120);
        $customerCodes = (array) $request->input('customer_codes', []);
        $items         = (array) $request->input('items', []);

        if (empty($customerCodes) || empty($items)) return response()->json([]);

        // ── 1) Preload ทุก product ของ customer group ──
        $allRows = fuzzy_so::whereIn('customer_code', $customerCodes)
            ->whereNotNull('product_name')
            ->where('product_name', '!=', '')
            ->orderByDesc('doc_date')
            ->get(['product_name','unit_price','unit','doc_date','customer_code','so_no','customer_name']);

        Log::info("[batchMatch] codes=" . count($customerCodes) . " items=" . count($items) . " db_rows=" . $allRows->count());

        // ── 2) สร้าง candidates จากกลุ่มลูกค้า ──
        $batchPayload   = [];
        $candNamesPerItem = [];

        foreach ($items as $i => $itemName) {
            $keyword = trim($itemName);
            if (mb_strlen($keyword) < 2) {
                $batchPayload[]     = ['keyword' => $keyword, 'candidates' => []];
                $candNamesPerItem[] = [];
                continue;
            }

            $terms = $this->extractSearchTerms($keyword);
            if (empty($terms)) {
                $batchPayload[]     = ['keyword' => $keyword, 'candidates' => []];
                $candNamesPerItem[] = [];
                continue;
            }

            // ILIKE filter จาก customer group
            $filtered = $allRows->filter(function ($row) use ($terms) {
                $nameLower = mb_strtolower($row->product_name);
                foreach ($terms as $tok) {
                    if (mb_strpos($nameLower, mb_strtolower($tok)) !== false) return true;
                }
                return false;
            });

            $uniq      = $filtered->unique('product_name')->take(20);
            $candNames = $uniq->pluck('product_name')->values()->toArray();

            $batchPayload[]     = [
                'keyword'    => $keyword,
                'candidates' => array_map(fn($n) => ['product_name' => $n], $candNames),
            ];
            $candNamesPerItem[] = $candNames;
        }

        DB::disconnect('pgsql');
        DB::disconnect();

        // ── 3) ★ ส่ง Python /api/match-product (CE + Qwen + DB search) ──
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
        $resp = $this->postJson("{$pythonUrl}/api/match-product", ['items' => $batchPayload], 120);

        $llmResults = $resp['results'] ?? [];

        // ── 4) Map ผลลัพธ์ → ราคาจาก allRows (กลุ่มลูกค้า) ──
        $results = [];
        foreach ($items as $i => $itemName) {
            $llm        = $llmResults[$i] ?? ['index' => -1];
            $matchedIdx = (int) ($llm['index'] ?? -1);
            $ceScore    = (float) ($llm['ce_score'] ?? 0);
            $source     = $llm['source'] ?? 'unknown';
            $candNames  = $candNamesPerItem[$i] ?? [];

            // ★ หา matchedName: จาก PHP candidates หรือ Python DB search
            $matchedName = null;
            if ($matchedIdx >= 0 && isset($candNames[$matchedIdx])) {
                $matchedName = $candNames[$matchedIdx];
            } elseif (!empty($llm['matched']['product_name'])) {
                $matchedName = $llm['matched']['product_name'];
            }

            if (!$matchedName) {
                $results[] = ['input' => $itemName, 'matches' => []];
                continue;
            }

            // ── ดึงราคาจาก allRows (กลุ่มลูกค้า) ก่อน ──
            $rows = $allRows->where('product_name', $matchedName)->sortByDesc('doc_date');

            // ★ ถ้ากลุ่มลูกค้าไม่มี → ค้นจาก DB ทั้งหมด
            if ($rows->isEmpty()) {
                try {
                    $rows = fuzzy_so::where('product_name', $matchedName)
                        ->whereNotNull('unit_price')->where('unit_price', '>', 0)
                        ->orderByDesc('doc_date')->limit(5)
                        ->get(['product_name','unit_price','unit','doc_date','customer_code','so_no','customer_name']);
                } catch (\Exception $e) {
                    $rows = collect();
                }
            }

            $matches = [];
            $seen = [];
            foreach ($rows as $row) {
                $key = $row->product_name . '|' . ((float)$row->unit_price) . '|' . $row->so_no;
                if (isset($seen[$key])) continue;
                $seen[$key] = true;

                $label = ($source === 'db_search' || $matchedIdx < 0)
                    ? "🤖 CE:{$ceScore}% ({$source})"
                    : "CE {$ceScore}%";

                $matches[] = [
                    'product_name'  => $row->product_name,
                    'unit_price'    => $row->unit_price !== null ? (float) $row->unit_price : null,
                    'unit'          => $row->unit,
                    'doc_date'      => $row->doc_date ? $row->doc_date->format('d/m/Y') : '-',
                    'customer_code' => $row->customer_code,
                    'customer_name' => $row->customer_name,
                    'so_no'         => $row->so_no,
                    'similarity'    => $ceScore,
                    'matched_tokens'=> [$label],
                ];

                if (count($matches) >= 3) break;
            }

            $results[] = ['input' => $itemName, 'matches' => array_slice($matches, 0, 3)];
        }

        return response()->json($results);
    }

    // ══════════════════════════════════════════════════
    // ★ FLOW 2: AI FALLBACK MATCH (🤖)
    //   Python _match_one() ค้น DB เองแล้ว
    //   อาจคืน index=-1 + source="db_search" + matched.product_name
    // ══════════════════════════════════════════════════
   public function aiFallbackMatch(Request $request)
{
    set_time_limit(120);
    $items         = (array) $request->input('items', []);
    $customerCodes = (array) $request->input('customer_codes', []);
    $type          = $request->input('type', 'price');
    if (empty($items)) return response()->json(['status' => 'done', 'results' => []]);

    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');

    // ── 1) build candidates (DB) ──
    $batchPayload = [];
    $candNamesPerItem = [];
    foreach ($items as $item) {
        $keyword = trim($item['name'] ?? '');
        $terms   = mb_strlen($keyword) >= 2 ? $this->extractSearchTerms($keyword) : [];
        if (empty($terms)) { $batchPayload[] = ['keyword'=>$keyword,'candidates'=>[]]; $candNamesPerItem[] = []; continue; }

        $candidates = $type === 'price'
            ? $this->buildAiPriceCandidates($keyword, $terms, $customerCodes)
            : $this->buildAiDocCandidates($keyword, $terms);

        $candNames = $candidates->map(fn($c) => $c->product_name)->values()->toArray();
        $batchPayload[]     = ['keyword'=>$keyword, 'candidates'=>array_map(fn($n)=>['product_name'=>$n], $candNames)];
        $candNamesPerItem[] = $candNames;
    }

    // ── ปล่อย DB ก่อนคุย Python ──
    DB::disconnect('pgsql'); DB::disconnect();

    // ★ ส่งทุก item ไป Python (แม้ candidates ว่าง — Python จะค้น DB เอง)
    $resp = $this->postJson("{$pythonUrl}/api/match-product", ['items'=>$batchPayload], 120);

    // Python คืน results ตรงๆ (≤5 รายการ) → map เลย
    if (isset($resp['results'])) {
        return response()->json(['status'=>'done',
            'results'=>$this->mapAiResults($items, $resp['results'], $candNamesPerItem, $type)]);
    }

    // Python คืน job_id → เก็บ candidate ไว้ใน cache แล้วให้ browser มา poll
    if (isset($resp['job_id'])) {
        Cache::put("aimatch:{$resp['job_id']}", [
            'items'=>$items, 'candNames'=>$candNamesPerItem, 'type'=>$type,
        ], now()->addMinutes(10));
        return response()->json(['status'=>'processing', 'job_id'=>$resp['job_id']]);
    }

    return response()->json(['status'=>'done', 'results'=>array_map(fn()=>['matches'=>[]], $items)]);
}

    // ── AI candidates helpers ──
    private function buildAiPriceCandidates(string $keyword, array $terms, array $customerCodes): \Illuminate\Support\Collection
    {
        usort($terms, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        $allFound = collect();

        foreach ($terms as $term) {
            if (mb_strlen($term) < 3) continue;
            if (!mb_check_encoding($term, 'UTF-8') || !preg_match('//u', $term)) continue;
            try {
                $found = fuzzy_so::where('product_name', 'ILIKE', '%' . $term . '%')
                    ->whereNotNull('unit_price')->where('unit_price', '>', 0)
                    ->orderByDesc('doc_date')->limit(100)
                    ->get(['product_name','unit_price','unit','doc_date','customer_code','so_no','customer_name']);

                if ($found->isNotEmpty()) {
                    $allFound = $allFound->merge($found);
                }
            } catch (\Exception $e) {
                Log::warning('[aiPriceCand] error: ' . mb_substr($e->getMessage(), 0, 80));
                continue;
            }

            if ($allFound->unique('product_name')->count() >= 20) break;
        }

        if ($allFound->isEmpty()) return collect();

        return $allFound->unique('product_name')->take(15)->values();
    }

    private function buildAiDocCandidates(string $keyword, array $terms): \Illuminate\Support\Collection
    {
        usort($terms, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        $allMerged = collect();

        foreach ($terms as $term) {
            if (mb_strlen($term) < 3) continue;
            if (!mb_check_encoding($term, 'UTF-8') || !preg_match('//u', $term)) continue;
            try {
                $hq = historyquotation::where('product', 'ILIKE', '%' . $term . '%')
                    ->orderByDesc('quotation_date')->limit(30)->get(['product', 'quotation_date']);
                $qi = QuotationItem::join('quotations', 'quotation_items.quotation_no', '=', 'quotations.quotation_no')
                    ->where('quotation_items.description', 'LIKE', '%' . $term . '%')
                    ->orderByDesc('quotations.doc_date')->limit(30)
                    ->select(['quotation_items.description as product', 'quotations.doc_date as quotation_date'])->get();

                $allMerged = $allMerged->merge($hq)->merge($qi);
            } catch (\Exception $e) {
                Log::warning('[aiDocCand] error: ' . $e->getMessage());
            }

            if ($allMerged->unique('product')->count() >= 10) break;
        }

        if ($allMerged->isEmpty()) return collect();

        return $allMerged->unique('product')->take(10)->map(fn($r) => (object) [
            'product_name' => $r->product,
            'doc_date'     => $r->quotation_date,
        ])->values();
    }
public function aiFallbackStatus(string $jobId)
{
    $jobId = preg_replace('/[^a-zA-Z0-9\-]/', '', $jobId);
    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
    $poll = $this->getJson("{$pythonUrl}/api/match-status/{$jobId}", 10);

    if (($poll['status'] ?? '') !== 'done') return response()->json(['status'=>'processing']);

    $cached = Cache::pull("aimatch:{$jobId}");
    if (!$cached) return response()->json(['status'=>'done', 'results'=>[]]);

    return response()->json(['status'=>'done',
        'results'=>$this->mapAiResults($cached['items'], $poll['results'] ?? [], $cached['candNames'], $cached['type'])]);
}

// ══════════════════════════════════════════════════
// ★ mapAiResults — รองรับ db_search จาก Python
//   Python อาจคืน:
//     index >= 0  → match จาก PHP candidates (เหมือนเดิม)
//     index = -1 + matched.product_name → match จาก DB search ของ Python
// ══════════════════════════════════════════════════
private function mapAiResults(array $items, array $llmResults, array $candNamesPerItem, string $type): array
{
    $output = [];
    foreach ($items as $j => $item) {
        $keyword    = trim($item['name'] ?? '');
        $coreTokens = array_slice($this->extractSearchTerms($keyword), 0, 3);
        $llm        = $llmResults[$j] ?? ['index'=>-1];
        $matchedIdx = (int) ($llm['index'] ?? -1);
        $ceScore    = (float) ($llm['ce_score'] ?? 0);
        $source     = $llm['source'] ?? 'unknown';
        $candNames  = $candNamesPerItem[$j] ?? [];

        // ★ หา matchedName: จาก PHP candidates หรือจาก Python DB search
        $matchedName = null;

        if ($matchedIdx >= 0 && isset($candNames[$matchedIdx])) {
            // match จาก PHP candidates
            $matchedName = $candNames[$matchedIdx];
        } elseif (!empty($llm['matched']['product_name'])) {
            // ★ match จาก Python DB search (index=-1 แต่มี matched.product_name)
            $matchedName = $llm['matched']['product_name'];
            Log::info("[mapAi] #{$j} '{$keyword}' → DB search match: '{$matchedName}' CE={$ceScore}%");
        }

        if (!$matchedName) {
            $output[] = ['matches'=>[], 'search_tokens'=>$coreTokens, 'candidates'=>$candNames,
                         'llm_picked'=>-1, 'ce_score'=>$ceScore, 'source'=>$source];
            continue;
        }

        $label = "🤖 CE:{$ceScore}% ({$source})";

        if ($type === 'price') {
            // ★ ค้นจาก DB ด้วย matchedName (ไม่จำกัดแค่ candNames เดิม)
            $searchNames = array_unique(array_merge($candNames, [$matchedName]));
            $rows = fuzzy_so::whereIn('product_name', $searchNames)
                ->whereNotNull('unit_price')->where('unit_price', '>', 0)
                ->orderByDesc('doc_date')->limit(50)
                ->get(['product_name','unit_price','unit','doc_date','customer_code','so_no','customer_name'])
                ->sortBy(fn($r) => [$r->product_name === $matchedName ? 0 : 1, $r->doc_date ? -strtotime($r->doc_date) : 0]);
            $matches = $rows->unique('so_no')->take(3)->map(fn($r) => [
                'product_name'=>$r->product_name, 'unit_price'=>(float)$r->unit_price, 'unit'=>$r->unit,
                'doc_date'=>$r->doc_date ? $r->doc_date->format('d/m/Y') : '-',
                'customer_code'=>$r->customer_code, 'customer_name'=>$r->customer_name, 'so_no'=>$r->so_no,
                'similarity'=>$ceScore, 'matched_tokens'=>[$label],
            ])->values()->toArray();
        } else {
            $matches = $this->searchQuotationHistory($matchedName, 3);
        }

        $output[] = ['matches'=>$matches, 'search_tokens'=>$coreTokens, 'candidates'=>$candNames,
                     'llm_picked'=>$matchedIdx, 'llm_matched'=>$matchedName, 'ce_score'=>$ceScore, 'source'=>$source];
    }
    return $output;
}
    // ══════════════════════════════════════════════════
    // ★ FLOW 3: QUOTATION HISTORY (📁 เอกสาร)
    // ══════════════════════════════════════════════════
    public function quotationHistory(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));
        return response()->json($this->searchQuotationHistory($keyword));
    }

    public function batchQuotationHistory(Request $request)
    {
        set_time_limit(120);
        $items = (array) $request->input('items', []);
        if (empty($items)) return response()->json([]);

        // ★ DB search (pg_trgm) — แทน embed
        $embedResults = $this->callEmbedSearch($items, 10, 30);

        // ── 1) ดึง candidates ทั้งหมดจาก DB ──
        $ceItems  = [];
        $rowsMeta = [];

        foreach ($items as $idx => $itemName) {
            $keyword = trim((string) $itemName);
            if (mb_strlen($keyword) < 2) {
                $ceItems[]  = ['query' => $keyword, 'candidates' => []];
                $rowsMeta[] = ['hq' => collect(), 'qi' => collect()];
                continue;
            }

            $terms = $this->extractSearchTerms($keyword);
            if (empty($terms)) {
                $ceItems[]  = ['query' => $keyword, 'candidates' => []];
                $rowsMeta[] = ['hq' => collect(), 'qi' => collect()];
                continue;
            }

            $searchTerms = array_filter($terms, fn($t) =>
                mb_check_encoding($t, 'UTF-8') && preg_match('//u', $t)
            );
            if (empty($searchTerms)) {
                $ceItems[]  = ['query' => $keyword, 'candidates' => []];
                $rowsMeta[] = ['hq' => collect(), 'qi' => collect()];
                continue;
            }

            // ── query history_quotation (PG) ──
            $hqRows = collect();
            try {
                $hqQuery = historyquotation::query();
                $hqQuery->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $tok) $q->orWhere('product', 'ILIKE', '%' . $tok . '%');
                });
                $hqRows = $hqQuery->orderByDesc('quotation_date')->limit(50)->get();
            } catch (\Exception $e) { /* skip */ }

            // ── query quotation_items (MySQL) ──
            $qiRows = collect();
            try {
                $qiQuery = QuotationItem::join('quotations', 'quotation_items.quotation_no', '=', 'quotations.quotation_no');
                $qiQuery->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $tok) $q->orWhere('quotation_items.description', 'LIKE', '%' . $tok . '%');
                });
                $qiRows = $qiQuery->orderByDesc('quotations.doc_date')->limit(50)
                    ->select([
                        'quotations.quotation_no', 'quotations.doc_date as raw_date',
                        'quotations.customer_company', 'quotation_items.description as product',
                        'quotation_items.unit', 'quotation_items.unit_price as price_per_unit',
                    ])->get();
            } catch (\Exception $e) { /* skip */ }

            // ── merge + unique product names ──
            $allProducts = $hqRows->pluck('product')
                ->merge($qiRows->pluck('product'))
                ->filter()->unique()->values();

            // merge DB search results
            $embedHits = $embedResults[$idx] ?? [];
            foreach ($embedHits as $hit) {
                $embedName = $hit['product_name'] ?? '';
                if ($embedName && !$allProducts->contains($embedName)) {
                    $allProducts->push($embedName);
                }
            }

            $ceItems[]  = ['query' => $keyword, 'candidates' => $allProducts->values()->toArray()];
            $rowsMeta[] = ['hq' => $hqRows, 'qi' => $qiRows];
        }
        DB::disconnect('pgsql');
        DB::disconnect();

        // ── 2) CE rerank batch ──
        $ceResults = $this->callCeRerank($ceItems, 5, self::CE_MIN_SCORE);

        // ── 3) Map CE results → full quotation data ──
        $results = [];
        foreach ($items as $i => $itemName) {
            $ranked = $ceResults[$i] ?? [];
            $meta   = $rowsMeta[$i] ?? [];
            $hqRows = $meta['hq'] ?? collect();
            $qiRows = $meta['qi'] ?? collect();

            if (empty($ranked)) {
                $results[] = ['matches' => []];
                continue;
            }

            $qtNos = $hqRows->pluck('quotation_no')->unique()->values()->toArray();
            $companyMap = !empty($qtNos)
                ? Quotation::whereIn('quotation_no', $qtNos)->pluck('customer_company', 'quotation_no')->toArray()
                : [];

            $matches = [];
            $seen = [];

            foreach ($ranked as $r) {
                $candName = $r['candidate'] ?? '';
                $ceScore  = $r['ce_score'] ?? 0;
                if (!$candName) continue;

                foreach ($hqRows->where('product', $candName) as $row) {
                    $dedupKey = mb_strtolower($row->product) . '|' . round((float)$row->price_per_unit, 2);
                    if (isset($seen[$dedupKey])) continue;
                    $seen[$dedupKey] = true;

                    $matches[] = [
                        'quotation_no'     => $row->quotation_no,
                        'quotation_date'   => $row->quotation_date ? $row->quotation_date->format('d/m/Y') : '-',
                        'customer_company' => $companyMap[$row->quotation_no] ?? '-',
                        'product'          => $row->product,
                        'unit'             => $row->unit,
                        'price_per_unit'   => (float) $row->price_per_unit,
                        'similarity'       => $ceScore,
                        'matched_tokens'   => ['CE ' . $ceScore . '%'],
                    ];
                    if (count($matches) >= 3) break 2;
                }

                foreach ($qiRows->where('product', $candName) as $row) {
                    $dedupKey = mb_strtolower($row->product) . '|' . round((float)$row->price_per_unit, 2);
                    if (isset($seen[$dedupKey])) continue;
                    $seen[$dedupKey] = true;

                    $matches[] = [
                        'quotation_no'     => $row->quotation_no,
                        'quotation_date'   => $row->raw_date ? \Carbon\Carbon::parse($row->raw_date)->format('d/m/Y') : '-',
                        'customer_company' => $row->customer_company ?? '-',
                        'product'          => $row->product,
                        'unit'             => $row->unit,
                        'price_per_unit'   => (float) $row->price_per_unit,
                        'similarity'       => $ceScore,
                        'matched_tokens'   => ['CE ' . $ceScore . '%'],
                    ];
                    if (count($matches) >= 3) break 2;
                }
            }

            $results[] = ['matches' => array_slice($matches, 0, 3)];
        }

        return response()->json($results);
    }

    // ── searchQuotationHistory (single item) ──
    private function searchQuotationHistory(string $keyword, int $limit = 10): array
    {
        $keyword = trim($keyword);
        if (mb_strlen($keyword) < 2) return [];

        $terms = $this->extractSearchTerms($keyword);
        if (empty($terms)) return [];

        $searchTerms = array_slice($terms, 0, 3);
        $searchTerms = array_filter($searchTerms, fn($t) => mb_check_encoding($t, 'UTF-8') && preg_match('//u', $t));
        if (empty($searchTerms)) return [];

        $hqRows = collect();
        try {
            $hqQuery = historyquotation::query();
            $hqQuery->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $tok) $q->orWhere('product', 'ILIKE', '%' . $tok . '%');
            });
            $hqRows = $hqQuery->orderByDesc('quotation_date')->limit(30)->get();
        } catch (\Exception $e) { /* skip */ }

        $qiRows = collect();
        try {
            $qiQuery = QuotationItem::join('quotations', 'quotation_items.quotation_no', '=', 'quotations.quotation_no');
            $qiQuery->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $tok) $q->orWhere('quotation_items.description', 'LIKE', '%' . $tok . '%');
            });
            $qiRows = $qiQuery->orderByDesc('quotations.doc_date')->limit(30)
                ->select([
                    'quotations.quotation_no', 'quotations.doc_date as raw_date',
                    'quotations.customer_company', 'quotation_items.description as product',
                    'quotation_items.unit', 'quotation_items.unit_price as price_per_unit',
                ])->get();
        } catch (\Exception $e) { /* skip */ }

        // ── CE rerank ──
        $allProducts = $hqRows->pluck('product')
            ->merge($qiRows->pluck('product'))
            ->filter()->unique()->values()->toArray();

        if (empty($allProducts)) return [];

        $ceResults = $this->callCeRerank(
            [['query' => $keyword, 'candidates' => $allProducts]],
            $limit, self::CE_MIN_SCORE
        );

        $ranked = $ceResults[0] ?? [];
        if (empty($ranked)) return [];

        $qtNos = $hqRows->pluck('quotation_no')->unique()->values()->toArray();
        $companyMap = !empty($qtNos)
            ? Quotation::whereIn('quotation_no', $qtNos)->pluck('customer_company', 'quotation_no')->toArray()
            : [];

        $matches = [];
        $seen = [];

        foreach ($ranked as $r) {
            $candName = $r['candidate'] ?? '';
            $ceScore  = $r['ce_score'] ?? 0;
            if (!$candName) continue;

            foreach ($hqRows->where('product', $candName) as $row) {
                $dk = mb_strtolower($row->product) . '|' . round((float)$row->price_per_unit, 2);
                if (isset($seen[$dk])) continue;
                $seen[$dk] = true;
                $matches[] = [
                    'quotation_no' => $row->quotation_no,
                    'quotation_date' => $row->quotation_date ? $row->quotation_date->format('d/m/Y') : '-',
                    'customer_company' => $companyMap[$row->quotation_no] ?? '-',
                    'product' => $row->product, 'unit' => $row->unit,
                    'price_per_unit' => (float) $row->price_per_unit,
                    'similarity' => $ceScore, 'matched_tokens' => ['CE ' . $ceScore . '%'],
                ];
            }

            foreach ($qiRows->where('product', $candName) as $row) {
                $dk = mb_strtolower($row->product) . '|' . round((float)$row->price_per_unit, 2);
                if (isset($seen[$dk])) continue;
                $seen[$dk] = true;
                $matches[] = [
                    'quotation_no' => $row->quotation_no,
                    'quotation_date' => $row->raw_date ? \Carbon\Carbon::parse($row->raw_date)->format('d/m/Y') : '-',
                    'customer_company' => $row->customer_company ?? '-',
                    'product' => $row->product, 'unit' => $row->unit,
                    'price_per_unit' => (float) $row->price_per_unit,
                    'similarity' => $ceScore, 'matched_tokens' => ['CE ' . $ceScore . '%'],
                ];
            }
        }

        return array_slice($matches, 0, $limit);
    }
}