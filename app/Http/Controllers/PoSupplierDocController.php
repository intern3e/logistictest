<?php

namespace App\Http\Controllers;

use App\Models\PoSupplierDoc;
use App\Models\PoDocCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * เอกสาร PO จากซัพ (สแกน PDF) :
 *   อัปโหลด 1 ไฟล์ = 1 แถว (ไฟล์เดียวมีได้หลายหน้า/หลายบิล)
 *   OCR รายหน้า (typhoon บน .148) -> judge สกัดต่อหน้า -> เก็บรวมในแถวเดียว
 *   กด txt = เห็นผลครบทุกหน้าในที่เดียว
 */
class PoSupplierDocController extends Controller
{
    private const BASE = 'po_supplier_docs';

    private function canUse($user): bool
    {
        return in_array($user->role ?? '', ['admin', 'accounting'], true);
    }

    public function index(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        return view('account.po_supplier_docs', ['loginName' => $user->name ?? '']);
    }

    /** รายการเอกสาร (AJAX) — แสดงในหน้า /podoccheck */
    public function data(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $status = trim((string) $request->input('match', ''));
        $q      = trim((string) $request->input('q', ''));

        $query = PoSupplierDoc::query();
        if (in_array($status, [PoSupplierDoc::MATCH_NONE, PoSupplierDoc::MATCH_AUTO, PoSupplierDoc::MATCH_MANUAL], true)) {
            $query->where('match_status', $status);
        }
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('po_id', 'LIKE', "%{$q}%")
                  ->orWhere('ocr_po_number', 'LIKE', "%{$q}%")
                  ->orWhere('vendor_name', 'LIKE', "%{$q}%")
                  ->orWhere('original_name', 'LIKE', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('id')->limit(5000)->get()->map(fn ($r) => $this->rowOut($r));
        return response()->json(['ok' => true, 'rows' => $rows]);
    }

    /** อัปโหลด — 1 ไฟล์ = 1 แถว, แยกหน้าเก็บไว้ (ยังไม่ OCR) */
    public function upload(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $request->validate([
            'files'   => 'required|array|min:1',
            'files.*' => 'file|mimetypes:application/pdf|max:51200',   // 50MB/ไฟล์
        ]);

        $paths    = $request->input('paths', []);
        $created  = [];
        $skipped  = 0;
        $inboxDir = self::BASE . '/_inbox/' . date('Ymd');

        foreach ($request->file('files') as $i => $file) {
            $orig = $file->getClientOriginalName();
            $relFolder = $paths[$i] ?? null;
            if ($relFolder) $relFolder = trim(dirname(str_replace('\\', '/', $relFolder)), '/.');

            $hash = sha1_file($file->getRealPath());
            if (PoSupplierDoc::where('file_hash', $hash)->exists()) { $skipped++; continue; }

            $safe   = Str::random(8) . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
            $stored = $file->storeAs($inboxDir, $safe, 'public');

            $doc = PoSupplierDoc::create([
                'original_name' => $orig,
                'rel_folder'    => $relFolder ?: null,
                'stored_path'   => $stored,
                'file_hash'     => $hash,
                'ocr_status'    => PoSupplierDoc::OCR_PENDING,
                'match_status'  => PoSupplierDoc::MATCH_NONE,
                'uploaded_by'   => $user->name ?? null,
                'page_count'    => 1,
                'pages_done'    => 0,
            ]);

            // แยกหน้าเก็บไว้ -> รู้จำนวนหน้า
            $doc->page_count = $this->splitDocPages($doc);
            $doc->save();

            $created[] = $this->rowOut($doc);
        }

        return response()->json([
            'ok'      => true,
            'created' => $created,
            'skipped' => $skipped,
            'message' => 'อัปโหลด ' . count($created) . ' ไฟล์' . ($skipped ? " (ข้ามซ้ำ {$skipped})" : ''),
        ]);
    }

    /** OCR ทีละหน้า — เก็บผลรายหน้าแยกไฟล์ (รันพร้อมกันหลายหน้าได้ ไม่ race) */
    public function processPage(Request $request, $id, $page)
    {
        @set_time_limit(120);
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $doc  = PoSupplierDoc::findOrFail($id);
        $page = max(1, (int) $page);

        if ($doc->ocr_status === PoSupplierDoc::OCR_PENDING) {
            PoSupplierDoc::where('id', $doc->id)->update(['ocr_status' => PoSupplierDoc::OCR_PROCESSING]);
        }

        $ok = true; $msg = null;
        try {
            $pagePdf = $this->pageFileAbs($doc, $page);
            $data    = $this->ocrInvoice($pagePdf);
            $pg = [
                'page'      => $page,
                'company'   => $this->pickStr($data, ['company', 'vendor', 'vendor_name', 'company_name']),
                'address'   => $this->pickStr($data, ['address', 'company_address']),
                'po_number' => $this->pickStr($data, ['po_number', 'po', 'po_no']),
                'invoice_no'=> $this->pickStr($data, ['invoice_no', 'invoice', 'inv_no', 'invoice_number', 'tax_invoice_no']),
                'subtotal'  => $this->pickNum($data, ['subtotal', 'amount', 'price_before_vat']),
                'vat'       => $this->pickNum($data, ['vat', 'tax']),
                'total'     => $this->pickNum($data, ['total', 'grand_total', 'total_with_vat']),
                'items'     => is_array($data['items'] ?? null) ? $data['items'] : [],
            ];
            $pg = $this->cleanupAmounts($pg);
            // snap เลข PO ที่ OCR อ่านเพี้ยน -> PO จริงใน po_receives (fuzzy)
            [$mpo, $dist] = $this->matchPoReceives($pg['po_number'] ?? null);
            if ($mpo !== null && $dist !== null && $dist <= 3) {
                $pg['po_ocr']    = $pg['po_number'];
                $pg['po_number'] = $mpo;
                $pg['po_dist']   = $dist;
            }
        } catch (\Throwable $e) {
            $ok = false; $msg = $e->getMessage();
            $pg = ['page' => $page, 'error' => $msg, 'items' => []];
        }

        // เขียนผลหน้านี้เป็นไฟล์ของตัวเอง (กัน race ตอนรันพร้อมกัน)
        Storage::disk('public')->put(self::BASE . "/_pages/{$doc->id}/res-{$page}.json", json_encode($pg, JSON_UNESCAPED_UNICODE));

        // นับจำนวนหน้าที่เสร็จจากไฟล์จริง
        $resGlob = Storage::disk('public')->path(self::BASE . "/_pages/{$doc->id}") . DIRECTORY_SEPARATOR . 'res-*.json';
        $done = count(glob($resGlob) ?: []);
        PoSupplierDoc::where('id', $doc->id)->update(['pages_done' => $done]);

        $fresh = PoSupplierDoc::find($doc->id);
        if ($done >= $fresh->page_count && $fresh->ocr_status !== PoSupplierDoc::OCR_DONE) {
            $this->finalize($fresh);
            $fresh->save();
        }

        return response()->json(['ok' => $ok, 'message' => $msg, 'row' => $this->rowOut(PoSupplierDoc::find($doc->id))], 200);
    }

    /** อ่านผลรายหน้าจากไฟล์ res-*.json -> [page => data] */
    private function readPageResults(PoSupplierDoc $doc): array
    {
        $dir = Storage::disk('public')->path(self::BASE . "/_pages/{$doc->id}");
        $out = [];
        foreach (glob($dir . DIRECTORY_SEPARATOR . 'res-*.json') ?: [] as $f) {
            $d = json_decode((string) file_get_contents($f), true);
            if (is_array($d)) $out[(int) ($d['page'] ?? 0)] = $d;
        }
        ksort($out);
        return $out;
    }

    /** รวมผลทุกหน้า -> ocr_text + ตั้งค่าแถว (ใช้หน้าแรกเป็นตัวแทน) + จับคู่ PO */
    private function finalize(PoSupplierDoc $doc): void
    {
        $pages = $this->readPageResults($doc);
        ksort($pages);
        $doc->pages_json = json_encode($pages, JSON_UNESCAPED_UNICODE);
        $doc->ocr_text = $this->buildCombinedTxt($doc, $pages);

        $first = $pages[1] ?? reset($pages) ?: [];
        $doc->vendor_name    = $first['company']   ?? null;
        $doc->vendor_address = $first['address']   ?? null;
        $doc->ocr_po_number  = $first['po_number'] ?? null;
        $doc->invoice_no     = $first['invoice_no'] ?? null;
        $doc->subtotal       = $first['subtotal']  ?? null;
        $doc->vat            = $first['vat']        ?? null;
        $doc->total          = $first['total']     ?? null;
        $doc->items_json     = !empty($first['items']) ? json_encode($first['items'], JSON_UNESCAPED_UNICODE) : null;

        [$poId, $soId, $mstatus, $conf] = $this->matchPo($doc);
        if ($poId) {
            $doc->po_id = $poId; $doc->so_id = $soId;
            $doc->match_status = $mstatus; $doc->match_confidence = $conf;
        }
        $doc->ocr_status = PoSupplierDoc::OCR_DONE;
        $doc->ocr_error  = null;
    }

    public function pdf(Request $request, $id)
    {
        $this->requireLogin($request);
        $doc = PoSupplierDoc::findOrFail($id);
        $abs = Storage::disk('public')->path($doc->stored_path);
        if (!is_file($abs)) abort(404);
        return response()->file($abs, ['Content-Type' => 'application/pdf']);
    }

    public function txt(Request $request, $id)
    {
        $this->requireLogin($request);
        $doc = PoSupplierDoc::findOrFail($id);
        return response()->json([
            'ok'         => true,
            'text'       => $doc->ocr_text ?? '',
            'po_id'      => $doc->po_id,
            'page_count' => $doc->page_count,
            'pages_done' => $doc->pages_done,
        ]);
    }

    public function assign(Request $request, $id)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $data = $request->validate(['po_id' => 'required|string|max:50', 'so_id' => 'nullable|string|max:50']);
        $doc = PoSupplierDoc::findOrFail($id);
        $doc->po_id = trim($data['po_id']);
        $doc->so_id = $data['so_id'] ?? $this->lookupSo($doc->po_id);
        $doc->match_status = PoSupplierDoc::MATCH_MANUAL;
        $doc->match_confidence = null;
        $doc->save();

        return response()->json(['ok' => true, 'row' => $this->rowOut($doc)]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $doc = PoSupplierDoc::findOrFail($id);
        if ($doc->stored_path && Storage::disk('public')->exists($doc->stored_path)) {
            Storage::disk('public')->delete($doc->stored_path);
        }
        // ลบโฟลเดอร์หน้าแยก
        $pagesDir = self::BASE . '/_pages/' . $doc->id;
        if (Storage::disk('public')->exists($pagesDir)) Storage::disk('public')->deleteDirectory($pagesDir);
        $doc->delete();
        return response()->json(['ok' => true]);
    }

    // ───────────────────────── helpers ─────────────────────────

    /** แยกไฟล์เป็นหน้า ๆ เก็บที่ _pages/{id}/page-N.pdf — คืนจำนวนหน้า (>=1) */
    private function splitDocPages(PoSupplierDoc $doc): int
    {
        $src = Storage::disk('public')->path($doc->stored_path);
        $destRel = self::BASE . '/_pages/' . $doc->id;
        $destAbs = Storage::disk('public')->path($destRel);
        @mkdir($destAbs, 0775, true);
        try {
            $bin = $this->popplerBin('pdfseparate');
            $res = Process::timeout(120)->run([$bin, $src, $destAbs . DIRECTORY_SEPARATOR . 'page-%d.pdf']);
            if (!$res->successful()) return 1;
            $files = glob($destAbs . DIRECTORY_SEPARATOR . 'page-*.pdf') ?: [];
            return max(1, count($files));
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /** path รูป/ไฟล์ของหน้า N (ถ้าแยกไม่ได้ ใช้ทั้งไฟล์) */
    private function pageFileAbs(PoSupplierDoc $doc, int $page): string
    {
        $pageRel = self::BASE . '/_pages/' . $doc->id . '/page-' . $page . '.pdf';
        if (Storage::disk('public')->exists($pageRel)) {
            return Storage::disk('public')->path($pageRel);
        }
        return Storage::disk('public')->path($doc->stored_path);
    }

    private function decodePages(PoSupplierDoc $doc): array
    {
        $p = json_decode((string) $doc->pages_json, true);
        return is_array($p) ? $p : [];
    }

    /**
     * เรียก OCR service (.148) : POST multipart (file) -> JSON (1 หน้า)
     */
    private function ocrInvoice(string $absPath): array
    {
        $url = config('ocr.invoice_url');
        $req = Http::timeout((int) config('ocr.timeout', 180));
        if ($key = config('ocr.api_key')) $req = $req->withHeaders(['X-API-Key' => $key]);

        $resp = $req->attach('file', file_get_contents($absPath), basename($absPath))->post($url);
        if (!$resp->ok()) throw new \RuntimeException('OCR service HTTP ' . $resp->status());
        $json = $resp->json();
        if (!is_array($json)) throw new \RuntimeException('OCR service คืนข้อมูลไม่ใช่ JSON');
        if (!empty($json['error'])) throw new \RuntimeException((string) $json['error']);
        return $json;
    }

    /** รวมผลทุกหน้า — เอาแค่ PO + เลขที่เอกสาร (invoice ซัพ) ต่อหน้า */
    private function buildCombinedTxt(PoSupplierDoc $doc, array $pages): string
    {
        $out = [];
        $out[] = 'ไฟล์: ' . $doc->original_name . '  (' . $doc->page_count . ' หน้า)';
        $out[] = '';
        for ($p = 1; $p <= $doc->page_count; $p++) {
            $pg = $pages[$p] ?? null;
            if (!$pg || !empty($pg['error'])) {
                $out[] = 'หน้า ' . $p . ' : (อ่านไม่สำเร็จ)';
                continue;
            }
            $poTxt = $pg['po_number'] ?: '-';
            if (!empty($pg['po_ocr']) && ($pg['po_dist'] ?? 0) > 0) $poTxt .= ' (OCR:' . $pg['po_ocr'] . ')';
            $out[] = 'หน้า ' . $p
                . ' | PO: ' . $poTxt
                . ' | เลขที่เอกสาร: ' . ($pg['invoice_no'] ?: '-');
        }
        return implode("\n", $out) . "\n";
    }

    private function matchPo(PoSupplierDoc $d): array
    {
        if ($d->ocr_po_number) {
            $norm = $this->normPo($d->ocr_po_number);
            $hit = PoDocCheck::all(['po_id', 'so_id'])->first(fn ($r) => $this->normPo($r->po_id) === $norm);
            if ($hit) return [$hit->po_id, $hit->so_id, PoSupplierDoc::MATCH_AUTO, 'high'];
        }
        if ($d->vendor_name) {
            $cands = PoDocCheck::where('vendor_name', 'LIKE', '%' . trim($d->vendor_name) . '%')->get(['po_id', 'so_id']);
            if ($cands->count() === 1) return [$cands[0]->po_id, $cands[0]->so_id, PoSupplierDoc::MATCH_AUTO, 'low'];
        }
        return [null, null, PoSupplierDoc::MATCH_NONE, null];
    }

    /** snap เลข PO ที่ OCR อ่านได้ -> PO จริงใน po_receives (แก้เลขเพี้ยน) ; คืน [po, distance] */
    private function matchPoReceives(?string $ocrPo): array
    {
        $o = $this->normPo2($ocrPo);
        if ($o === '') return [null, null];

        // index po_receives (cache 5 นาที) : [po_id => normalized]
        $list = \Illuminate\Support\Facades\Cache::remember('po_receives_norm_index', 300, function () {
            $m = [];
            foreach (\App\Models\PoReceive::query()->whereNotNull('po_id')->distinct()->pluck('po_id') as $p) {
                $n = $this->normPo2($p);
                if ($n !== '') $m[$p] = $n;
            }
            return $m;
        });

        // exact ก่อน
        foreach ($list as $po => $n) {
            if ($n === $o) return [$po, 0];
        }
        // ใกล้สุด (levenshtein) — ต้องมีตัวชนะเดี่ยว ๆ ไม่งั้นถือว่าไม่ชัวร์
        $best = null; $bd = 999; $tie = false;
        foreach ($list as $po => $n) {
            $d = levenshtein($o, $n);
            if ($d < $bd) { $bd = $d; $best = $po; $tie = false; }
            elseif ($d === $bd) { $tie = true; }
        }
        if ($tie && $bd > 1) return [null, null];   // ก้ำกึ่ง เลขห่าง -> ไม่เดา
        return [$best, $bd];
    }

    /** normalize PO: ตัด prefix PO/SO/F ออก เหลือเฉพาะ alnum ตัวใหญ่ */
    private function normPo2(?string $s): string
    {
        $s = strtoupper(trim((string) $s));
        $s = preg_replace('/^(PO|SO|F|FPO)\s*/', '', $s);
        return preg_replace('/[^0-9A-Z]/', '', $s);
    }

    private function lookupSo(string $poId): ?string
    {
        return optional(PoDocCheck::where('po_id', $poId)->first())->so_id;
    }

    private function popplerBin(string $name): string
    {
        $cfg = (string) config("ocr.$name");
        if ($cfg !== '' && @is_file($cfg)) return $cfg;
        foreach (glob('C:/Users/*/AppData/Local/Microsoft/WinGet/Packages/*Poppler*/poppler-*/Library/bin/' . $name . '.exe') ?: [] as $g) {
            if (@is_file($g)) return $g;
        }
        return $name;
    }

    /** แก้ค่าเงิน: vat ที่เป็น "อัตรา%" ให้คำนวณใหม่, total ที่เป็น "ยอดก่อน VAT" ให้เติม VAT */
    private function cleanupAmounts(array $pg): array
    {
        $sub = $pg['subtotal'] ?? null;
        $vat = $pg['vat'] ?? null;
        $tot = $pg['total'] ?? null;
        $vatLooksRate = ($vat !== null && $vat > 0 && $vat <= 15 && ($sub ?? 0) > 200);

        if (($sub ?? 0) > 0 && ($tot ?? 0) >= ($sub ?? 0)) {
            // total น่าเชื่อถือ -> vat = total - subtotal (ถ้า vat หาย/เป็นอัตรา)
            if ($vat === null || $vatLooksRate) $pg['vat'] = round($tot - $sub, 2);
        } elseif (($sub ?? 0) > 0) {
            // total หาย/ผิด (น้อยกว่า subtotal) -> vat เงินจริง ไม่งั้น 7%
            $v = ($vat !== null && !$vatLooksRate) ? $vat : round($sub * 0.07, 2);
            $pg['vat']   = $v;
            $pg['total'] = round($sub + $v, 2);
        }
        return $pg;
    }

    private function pickStr(array $a, array $keys): ?string
    {
        foreach ($keys as $k) if (isset($a[$k]) && trim((string) $a[$k]) !== '') return trim((string) $a[$k]);
        return null;
    }
    private function pickNum(array $a, array $keys): ?float
    {
        foreach ($keys as $k) {
            if (!isset($a[$k]) || $a[$k] === '' || $a[$k] === null) continue;
            return (float) preg_replace('/[^0-9.\-]/', '', (string) $a[$k]);
        }
        return null;
    }
    private function fmt($n): string
    {
        return ($n === null || $n === '') ? '-' : number_format((float) $n, 2);
    }
    private function normPo(?string $s): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $s));
    }

    private function rowOut(PoSupplierDoc $r): array
    {
        // สรุปรายหน้า (ทุก PO ในไฟล์)
        $pagesArr = [];
        $pj = json_decode((string) $r->pages_json, true);
        if (is_array($pj)) {
            ksort($pj);
            foreach ($pj as $pg) {
                $pagesArr[] = [
                    'page'   => $pg['page'] ?? null,
                    'po'     => $pg['po_number'] ?? null,
                    'po_ocr' => $pg['po_ocr'] ?? null,
                    'po_dist'=> $pg['po_dist'] ?? null,
                    'inv'    => $pg['invoice_no'] ?? null,
                    'vendor' => $pg['company'] ?? null,
                    'total'  => $pg['total'] ?? null,
                ];
            }
        }
        $poCount = count(array_filter($pagesArr, fn ($p) => !empty($p['po'])));

        return [
            'id'               => $r->id,
            'original_name'    => $r->original_name,
            'rel_folder'       => $r->rel_folder,
            'ocr_status'       => $r->ocr_status,
            'ocr_error'        => $r->ocr_error,
            'ocr_po_number'    => $r->ocr_po_number,
            'invoice_no'       => $r->invoice_no,
            'vendor_name'      => $r->vendor_name,
            'subtotal'         => $r->subtotal,
            'vat'              => $r->vat,
            'total'            => $r->total,
            'po_id'            => $r->po_id,
            'so_id'            => $r->so_id,
            'match_status'     => $r->match_status,
            'match_confidence' => $r->match_confidence,
            'uploaded_by'      => $r->uploaded_by,
            'page_count'       => (int) $r->page_count,
            'pages_done'       => (int) $r->pages_done,
            'pages'            => $pagesArr,
            'po_count'         => $poCount,
            'created_at'       => optional($r->created_at)->format('d/m/Y H:i'),
        ];
    }
}
