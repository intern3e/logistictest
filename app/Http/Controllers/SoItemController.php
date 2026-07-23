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

    // ★ ปลดล็อก session file ทันที กัน tab อื่นค้างรอ
    session()->save();

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
    private function callCeRerank(array $items, int $topK = 5, float $minScore = 0.0): array
    {
        // ★ เปลี่ยน: min_score default = 0.0 (เดิมเป็น 1.0 ซึ่ง Python แปลงเป็น %)
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
    
        $ch = curl_init("{$pythonUrl}/api/batch-rerank");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'items'     => $items,
                'top_k'     => $topK,
                'min_score' => $minScore,  // ★ ส่ง 0 = ไม่ตัด
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
            if (preg_match('/^\d+(\.\d+)?$/', $t) && mb_strlen($t) < 4) continue;
            if (preg_match('/^(EA|PCS?|SET|BOX|ROL|KG|ชิ้น|อัน|เส้น|ม้วน|แผ่น|กล่อง|ชุด)\.?$/iu', $t)) continue;
            $terms[] = $t;
        }

        $terms = array_values(array_unique($terms));
        usort($terms, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        return $terms;
    }

// ══════════════════════════════════════════════════
    // ★ HELPER: DB search (pg_trgm)
    // ══════════════════════════════════════════════════
private function callAiExtractKeywords(array $keywords): array
{
    $cacheKey = 'aiextract:' . md5(implode('|', $keywords));
    $cached = Cache::get($cacheKey);
    if ($cached !== null) return $cached;

    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');

    $start = $this->postJson("{$pythonUrl}/api/extract-keywords-start", ['keywords' => $keywords], 10);

    if (isset($start['results'])) {
        $result = $start['results'];
        Cache::put($cacheKey, $result, 30);
        return $result;
    }

    if (!isset($start['job_id'])) {
        Log::warning("[AI-EXTRACT] start failed — no job_id, items=" . count($keywords));
        return array_fill(0, count($keywords), []);
    }

    $jobId = $start['job_id'];
    // ★ ลดจาก 600 → 90 วิ ให้สั้นกว่า set_time_limit ของฟังก์ชันที่เรียกเสมอ (กัน fatal timeout)
    //   เกิน 90 วิ = ปล่อยให้ fallback ไปใช้ extractSearchTerms() (regex) แทน ดีกว่าทำให้ request ทั้งชุดตาย
    $maxWaitSec      = 90;
    $pollIntervalSec = 2;
    $elapsed         = 0;

    while ($elapsed < $maxWaitSec) {
        usleep($pollIntervalSec * 1000000);
        $elapsed += $pollIntervalSec;

        $poll = $this->getJson("{$pythonUrl}/api/extract-keywords-status/" . urlencode($jobId), 10);

        if (($poll['status'] ?? '') === 'done') {
            $result = $poll['results'] ?? array_fill(0, count($keywords), []);
            Cache::put($cacheKey, $result, 30);
            return $result;
        }
        if (($poll['status'] ?? '') === 'not_found') {
            Log::warning("[AI-EXTRACT] job not found job_id={$jobId}");
            break;
        }
    }

    Log::warning("[AI-EXTRACT] timeout after {$elapsed}s, items=" . count($keywords) . " job_id={$jobId} — fallback regex");
    return array_fill(0, count($keywords), []);
}
    private function callDbSearch(array $queries, int $topK = 20, float $minScore = 30): array
    {
        $pythonUrl = config('services.ocr.url', 'http://localhost:8010');

        $ch = curl_init("{$pythonUrl}/api/db-search");
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
            Log::warning("[DB-SEARCH] search failed: HTTP={$status} err={$err}");
            return [];
        }

        $resp = json_decode($raw, true);
        return $resp['results'] ?? [];
    }
    // ══════════════════════════════════════════════════
    // ★ FLOW 1: BATCH MATCH (💰 ราคา — fuzzy_so กลุ่มลูกค้า)
    //   ★ ใช้ Qwen ตัดสินเหมือน Flow 2 (ไม่ใช่ CE อย่างเดียว)
    //   Python _match_one() จะ: CE score → Qwen เลือก (ดู CE + doc_date)
    // ══════════════════════════════════════════════════
// ═══ START: ยิง job แล้วคืน job_id ทันที ไม่รอ ═══
public function batchMatchStart(Request $request)
{
    session()->save();

    $customerCodes = (array) $request->input('customer_codes', []);
    $items         = (array) $request->input('items', []);
    if (empty($items)) return response()->json(['status' => 'done', 'results' => []]);

    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
    $batchPayload = array_map(fn($name) => ['keyword' => trim((string) $name)], $items);

    DB::disconnect('pgsql'); DB::disconnect();

    $resp = $this->postJson("{$pythonUrl}/api/match-product", [
        'items'          => $batchPayload,
        'customer_codes' => $customerCodes,
    ], 300);

    if (isset($resp['results'])) {
        return response()->json(['status' => 'done',
            'results' => $this->buildAgentMatchOutput($items, $resp['results'])]);
    }

    if (isset($resp['job_id'])) {
        Cache::put("batchmatch:{$resp['job_id']}", ['items' => $items], now()->addMinutes(10));
        return response()->json(['status' => 'processing', 'job_id' => $resp['job_id']]);
    }

    return response()->json(['status' => 'done', 'results' => array_map(fn() => ['matches' => []], $items)]);
}

// ═══ STATUS: browser poll endpoint นี้ — เบา ไม่ block worker ═══
public function batchMatchStatus(string $jobId)
{
    $jobId = preg_replace('/[^a-zA-Z0-9\-]/', '', $jobId);
    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
    $poll = $this->getJson("{$pythonUrl}/api/match-status/{$jobId}", 10);

    if (($poll['status'] ?? '') !== 'done') return response()->json(['status' => 'processing']);

    $cached = Cache::pull("batchmatch:{$jobId}");
    if (!$cached) return response()->json(['status' => 'done', 'results' => []]);

    return response()->json(['status' => 'done',
        'results' => $this->buildAgentMatchOutput($cached['items'], $poll['results'] ?? [])]);
}

// ═══ HELPER: ประกอบผลลัพธ์จากคำตอบ agent (source: group|all|history|none) ═══
private function buildAgentMatchOutput(array $items, array $agentResults): array
{
    $results = [];
    foreach ($items as $i => $itemName) {
        $r           = $agentResults[$i] ?? ['index' => -1];
        $matchedName = $r['matched_name'] ?? ($r['matched']['product_name'] ?? '');
        $source      = $r['source'] ?? 'none';
        $ceScore     = (float) ($r['ce_score'] ?? 0);

        if (!$matchedName || $source === 'none' || $source === 'history') {
            $results[] = ['input' => $itemName, 'matches' => [], 'source' => 'none', 'ce_score' => $ceScore];
            continue;
        }

        try {
            // ★ ดึงมาเผื่อ 10 แถว (กันเคสซ้ำ so_no/ราคา) แต่ orderByDesc('doc_date') เรียงใหม่สุดขึ้นก่อนเสมอ
            $priceRows = fuzzy_so::where('product_name', $matchedName)
                ->whereNotNull('unit_price')->where('unit_price', '>', 0)
                ->orderByDesc('doc_date')
                ->limit(10)
                ->get(['product_name','unit_price','unit','doc_date','customer_code','so_no','customer_name']);
        } catch (\Exception $e) {
            Log::error("[buildAgentMatchOutput] price query EXCEPTION for '{$matchedName}': " . $e->getMessage());
            $priceRows = collect();
        }

        $matches = $this->buildMatchResults($priceRows, $matchedName, $ceScore, $source);

        $results[] = ['input' => $itemName, 'matches' => $matches, 'source' => $source,
                       'ce_score' => $ceScore, 'matched_name' => $matchedName];
    }
    return $results;
}

// ══════════════════════════════════════════════════
// ★ FLOW 2 (🤖): เหมือน Flow 1 แต่บังคับไม่ให้กลุ่ม → agent ค้นทั้งระบบตั้งแต่แรก
// ══════════════════════════════════════════════════
public function aiFallbackMatch(Request $request)
{
    set_time_limit(300); // ★ เพิ่มจาก 120 — searchQuotationHistory วนทีละ item เสี่ยง timeout สะสม
    session()->save();

    $items = (array) $request->input('items', []);
    $type  = $request->input('type', 'price');
    if (empty($items)) return response()->json(['status' => 'done', 'results' => []]);

    try {
        if ($type !== 'price') {
            $results = [];
            foreach ($items as $item) {
                $keyword   = trim($item['name'] ?? '');
                $results[] = ['matches' => $this->searchQuotationHistory($keyword, 3)];
            }
            return response()->json(['status' => 'done', 'results' => $results]);
        }

        $pythonUrl    = config('services.ocr.url', 'http://localhost:8010');
        $keywords     = array_map(fn($it) => trim((string) ($it['name'] ?? '')), $items);
        $batchPayload = array_map(fn($k) => ['keyword' => $k], $keywords);

        DB::disconnect('pgsql'); DB::disconnect();

        $resp = $this->postJson("{$pythonUrl}/api/match-product", [
            'items'          => $batchPayload,
            'customer_codes' => [],
        ], 120);

        if (isset($resp['results'])) {
            return response()->json(['status' => 'done',
                'results' => $this->mapAgentAiResults($keywords, $resp['results'])]);
        }
        if (isset($resp['job_id'])) {
            Cache::put("aimatch:{$resp['job_id']}", ['keywords' => $keywords], now()->addMinutes(10));
            return response()->json(['status' => 'processing', 'job_id' => $resp['job_id']]);
        }
        return response()->json(['status' => 'done', 'results' => array_map(fn() => ['matches' => []], $items)]);

    } catch (\Throwable $e) {
        Log::error('[aiFallbackMatch] error: ' . $e->getMessage());
        return response()->json(['status' => 'done', 'results' => array_map(fn() => ['matches' => []], $items)]);
    }
}

public function aiFallbackStatus(string $jobId)
{
    $jobId = preg_replace('/[^a-zA-Z0-9\-]/', '', $jobId);
    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
    $poll = $this->getJson("{$pythonUrl}/api/match-status/{$jobId}", 10);
    if (($poll['status'] ?? '') !== 'done') return response()->json(['status' => 'processing']);

    $cached = Cache::pull("aimatch:{$jobId}");
    if (!$cached) return response()->json(['status' => 'done', 'results' => []]);

    return response()->json(['status' => 'done',
        'results' => $this->mapAgentAiResults($cached['keywords'], $poll['results'] ?? [])]);
}

private function mapAgentAiResults(array $keywords, array $agentResults): array
{
    $output = [];
    foreach ($keywords as $j => $keyword) {
        $r           = $agentResults[$j] ?? ['index' => -1];
        $matchedName = $r['matched_name'] ?? ($r['matched']['product_name'] ?? '');
        $ceScore     = (float) ($r['ce_score'] ?? 0);
        $source      = $r['source'] ?? 'none';

        if (!$matchedName || $source === 'none' || $source === 'history') {
            $output[] = ['matches' => [], 'llm_picked' => -1, 'ce_score' => $ceScore, 'source' => $source];
            continue;
        }

        // ★ orderByDesc('doc_date') ให้แถวใหม่สุดมาก่อนเสมอ
        $rows = fuzzy_so::where('product_name', $matchedName)
            ->whereNotNull('unit_price')->where('unit_price', '>', 0)
            ->orderByDesc('doc_date')
            ->limit(10)
            ->get(['product_name','unit_price','unit','doc_date','customer_code','so_no','customer_name']);

        $label   = "🤖 {$ceScore}% ({$source})";
        // ★ unique('so_no') คงลำดับเดิม (doc_date desc) ไว้ แล้ว take(3) = ใหม่สุด 3 แถว
        $matches = $rows->unique('so_no')->take(3)->map(fn($row) => [
            'product_name'  => $row->product_name,
            'unit_price'    => (float) $row->unit_price,
            'unit'          => $row->unit,
            'doc_date'      => $row->doc_date ? $row->doc_date->format('d/m/Y') : '-',
            'customer_code' => $row->customer_code,
            'customer_name' => $row->customer_name,
            'so_no'         => $row->so_no,
            'similarity'    => $ceScore,
            'matched_tokens'=> [$label],
        ])->values()->toArray();

        $output[] = ['matches' => $matches, 'llm_picked' => 0, 'llm_matched' => $matchedName,
                     'ce_score' => $ceScore, 'source' => $source];
    }
    return $output;
}
private function pollMatchJob(string $jobId, int $timeoutSec = 150): array
{
    $pythonUrl = config('services.ocr.url', 'http://localhost:8010');
    $start = time();

    while (time() - $start < $timeoutSec) {
        $poll = $this->getJson("{$pythonUrl}/api/match-status/{$jobId}", 10);
        if (($poll['status'] ?? '') === 'done') {
            return $poll['results'] ?? [];
        }
        usleep(800000); // 0.8s
    }

    Log::warning("[batchMatch] poll timeout job_id={$jobId}");
    return [];
}
private function buildMatchResults($rows, string $matchedName, float $ceScore, string $source): array
{
    $matches = [];
    $seen    = [];
    foreach ($rows as $row) {
        $key = $row->product_name . '|' . ((float)$row->unit_price) . '|' . $row->so_no;
        if (isset($seen[$key])) continue;
        $seen[$key] = true;

        $label = $source === 'all'
            ? "🤖 CE:{$ceScore}%"
            : "💰 CE:{$ceScore}%";

        $matches[] = [
            'product_name'   => $row->product_name,
            'unit_price'     => $row->unit_price !== null ? (float) $row->unit_price : null,
            'unit'           => $row->unit,
            'doc_date'       => $row->doc_date ? $row->doc_date->format('d/m/Y') : '-',
            'customer_code'  => $row->customer_code,
            'customer_name'  => $row->customer_name,
            'so_no'          => $row->so_no,
            'similarity'     => $ceScore,
            'matched_tokens' => [$label],
        ];

        if (count($matches) >= 3) break;   // ★ หยุดที่ 3 แถวพอดี
    }

    return $matches;
}
// ══════════════════════════════════════════════════
// ★ HELPER: historyquotation batch search (PG) — UNION ALL แทนวนลูป
// ══════════════════════════════════════════════════
private function historyQuotationBatchSearch(array $termsPerItem): array
{
    $parts    = [];
    $bindings = [];

    $model      = new historyquotation();
    $connection = $model->getConnectionName() ?: config('database.default');
    $table      = $model->getTable();

    foreach ($termsPerItem as $idx => $terms) {
        if (empty($terms)) continue;

        $conds = [];
        foreach ($terms as $t) {
            if (mb_strlen($t) < 2) continue;
            $conds[]    = 'product ILIKE ?';   // ★ column นี้ถูกต้องอยู่แล้ว
            $bindings[] = '%' . $t . '%';
        }
        if (empty($conds)) continue;

        $idxSafe = (int) $idx;
        $where   = implode(' OR ', $conds);
        $parts[] = "(SELECT {$idxSafe} AS item_idx,
                quotation_no,
                quotation_date,
                cust_name AS customer_company,
                product,
                unit,
                price_per_unit
            FROM {$table}
            WHERE {$where}
            ORDER BY quotation_date DESC
            LIMIT 50)";
    }

    if (empty($parts)) return [];

    $sql = implode(' UNION ALL ', $parts);
    try {
        $rows = DB::connection($connection)->select($sql, $bindings);
    } catch (\Exception $e) {
        Log::warning('[historyQuotationBatchSearch] error: ' . $e->getMessage());
        return [];
    }

    $grouped = [];
    foreach ($rows as $r) {
        $grouped[$r->item_idx][] = (object) [
            'quotation_no'     => $r->quotation_no,
            'quotation_date'   => $r->quotation_date ? \Carbon\Carbon::parse($r->quotation_date) : null,
            'customer_company' => $r->customer_company,
            'product'          => $r->product,
            'unit'             => $r->unit,
            'price_per_unit'   => $r->price_per_unit,
        ];
    }
    return $grouped;
}
// ══════════════════════════════════════════════════
// ★ HELPER: quotation_items batch search (MySQL) — UNION ALL แทนวนลูป
// ══════════════════════════════════════════════════
private function quotationItemBatchSearch(array $termsPerItem): array
{
    $parts    = [];
    $bindings = [];

    $itemModel  = new quotationItem();
    $connection = $itemModel->getConnectionName() ?: config('database.default');
    $itemTable  = $itemModel->getTable();
    $qtTable    = (new quotation())->getTable();

    foreach ($termsPerItem as $idx => $terms) {
        if (empty($terms)) continue;

        $conds = [];
        foreach ($terms as $t) {
            if (mb_strlen($t) < 2) continue;
            $conds[]    = 'qi.description LIKE ?';
            $bindings[] = '%' . $t . '%';
        }
        if (empty($conds)) continue;

        $idxSafe = (int) $idx;
        $where   = implode(' OR ', $conds);
        $parts[] = "(SELECT {$idxSafe} AS item_idx,
                q.quotation_no AS quotation_no,
                q.doc_date AS raw_date,
                q.customer_company AS customer_company,
                qi.description AS product,
                qi.unit AS unit,
                qi.unit_price AS price_per_unit
            FROM {$itemTable} qi
            INNER JOIN {$qtTable} q ON qi.quotation_no = q.quotation_no
            WHERE {$where}
            ORDER BY q.doc_date DESC
            LIMIT 50)";
    }

    if (empty($parts)) return [];

    $sql = implode(' UNION ALL ', $parts);
    try {
        $rows = DB::connection($connection)->select($sql, $bindings);
    } catch (\Exception $e) {
        Log::warning('[quotationItemBatchSearch] error: ' . $e->getMessage());
        return [];
    }

    $grouped = [];
    foreach ($rows as $r) {
        $grouped[$r->item_idx][] = (object) [
            'quotation_no'     => $r->quotation_no,
            'raw_date'         => $r->raw_date,
            'customer_company' => $r->customer_company,
            'product'          => $r->product,
            'unit'             => $r->unit,
            'price_per_unit'   => $r->price_per_unit,
        ];
    }
    return $grouped;
}
public function batchQuotationHistory(Request $request)
{
    // ★ ให้พอกับ worst case: AI extract (~90s) + dbSearch (30s) + CE rerank (180s) + เผื่อ
    set_time_limit(400);
    session()->save();

    $items = (array) $request->input('items', []);
    if (empty($items)) return response()->json([]);

    try {
        $keywordsRaw = array_map(fn($it) => trim((string) $it), $items);
        $aiTermsPerItem = $this->callAiExtractKeywords($keywordsRaw);
        foreach ($keywordsRaw as $idx => $kw) {
            if (empty($aiTermsPerItem[$idx])) {
                $aiTermsPerItem[$idx] = $this->extractSearchTerms($kw);
            }
        }

        $termsPerItem = [];
        foreach ($items as $idx => $itemName) {
            $keyword = trim((string) $itemName);
            if (mb_strlen($keyword) < 2) { $termsPerItem[$idx] = []; continue; }
            $terms = array_filter($aiTermsPerItem[$idx] ?? [], fn($t) =>
                mb_check_encoding($t, 'UTF-8') && preg_match('//u', $t)
            );
            $termsPerItem[$idx] = array_values($terms);
        }

        $dbSearchResults = $this->callDbSearch($items, 10, 30);
        $hqGrouped = $this->historyQuotationBatchSearch($termsPerItem);
        $qiGrouped = $this->quotationItemBatchSearch($termsPerItem);

        $ceItems  = [];
        $rowsMeta = [];

        foreach ($items as $idx => $itemName) {
            $keyword = trim((string) $itemName);
            $hqRows  = collect($hqGrouped[$idx] ?? []);
            $qiRows  = collect($qiGrouped[$idx] ?? []);

            if (empty($termsPerItem[$idx])) {
                $ceItems[]  = ['query' => $keyword, 'candidates' => []];
                $rowsMeta[] = ['hq' => collect(), 'qi' => collect()];
                continue;
            }

            $allProducts = $hqRows->pluck('product')
                ->merge($qiRows->pluck('product'))
                ->filter()->unique()->values();

            $dbHits = $dbSearchResults[$idx] ?? [];
            foreach ($dbHits as $hit) {
                $dbName = $hit['product_name'] ?? '';
                if ($dbName && !$allProducts->contains($dbName)) {
                    $allProducts->push($dbName);
                }
            }

            $ceItems[]  = ['query' => $keyword, 'candidates' => $allProducts->values()->toArray()];
            $rowsMeta[] = ['hq' => $hqRows, 'qi' => $qiRows];
        }

        DB::disconnect('pgsql');
        DB::disconnect();

        $ceResults = $this->callCeRerank($ceItems, 5);

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

    } catch (\Throwable $e) {
        // ★ กัน exception (DB/curl) ทำให้ chunk นี้กลาย 500 แล้วหายไปเงียบๆ — คืน matches ว่างแทน
        Log::error('[batchQuotationHistory] error: ' . $e->getMessage());
        return response()->json(array_fill(0, count($items), ['matches' => []]));
    }
}
private function searchQuotationHistory(string $keyword, int $limit = 10): array
{
    $keyword = trim($keyword);
    if (mb_strlen($keyword) < 2) return [];

    // ★ ใช้ AI แทน regex — เรียกทีละตัว เพราะฟังก์ชันนี้รับ keyword เดียว
    $terms = $this->callAiExtractKeywords([$keyword])[0] ?? [];
    if (empty($terms)) {
        $terms = $this->extractSearchTerms($keyword);
    }
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
            $limit
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