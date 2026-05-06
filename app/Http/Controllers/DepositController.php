<?php

namespace App\Http\Controllers;

use App\Models\deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use setasign\Fpdi\Tcpdf\Fpdi;

class DepositController extends Controller
{
    private $adminUsers = ['kanitin2', 'dev'];
    private const PDF_TEMPLATE_REL = 'deposit_templates/templates.pdf';
    private const PDF_OUTPUT_DIR = 'deposit_templates';
    public function insertdeposit()
    {
        return view('deposit.insertdeposit');
    }
    public function dashboarddeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));
        $keyword   = trim($request->get('keyword', ''));

        $query = deposit::query();

        if ($soKeyword !== '') {
            $query->where('so_id', 'like', "%{$soKeyword}%");
        }

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('sale_name', 'like', "%{$keyword}%")
                  ->orWhere('contactso', 'like', "%{$keyword}%")
                  ->orWhere('customer_id', 'like', "%{$keyword}%")
                  ->orWhere('deposit_bill_id', 'like', "%{$keyword}%")
                  ->orWhere('tax_id', 'like', "%{$keyword}%");
            });
        }

        $deposits = $query
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('deposit.dashboarddeposit', compact('deposits'));
    }

    public function detail($so_id)
    {
        $items = deposit::where('so_id', $so_id)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'items'   => $items,
        ]);
    }

    public function showBill($deposit_bill_id)
    {
        $items = deposit::where('deposit_bill_id', $deposit_bill_id)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            abort(404, 'ไม่พบใบมัดจำเลขที่ ' . $deposit_bill_id);
        }

        $header = $items->first();

        $totalDeposit = $items->sum('dep_price');
        $grandTotal   = (float) $header->grand_total;
        $netRemaining = max(0, $grandTotal - $totalDeposit);

        return view('deposit.billform', compact(
            'items', 'header', 'totalDeposit', 'grandTotal', 'netRemaining', 'deposit_bill_id'
        ));
    }

    public function updateStatus(Request $request)
    {
        $changedBy = strtolower(trim($request->input('changed_by', '')));

        if (!in_array($changedBy, $this->adminUsers)) {
            return response()->json([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์เปลี่ยนสถานะ',
            ], 403);
        }

        $newStatus = $request->input('new_status');
        $allowed   = ['รอยืนยัน', 'ยืนยัน'];

        if (!in_array($newStatus, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'สถานะไม่ถูกต้อง',
            ], 422);
        }

        $depositId = $request->input('deposit_id');
        $soId      = $request->input('so_id');

        if (empty($depositId) && empty($soId)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีข้อมูลใบมัดจำที่จะอัปเดต',
            ], 422);
        }

        try {
            $query = deposit::query();

            if (!empty($depositId)) {
                $query->where('id', $depositId);
            } else {
                $query->where('so_id', $soId);
            }

            $updateData = [
                'status' => $newStatus,
            ];

            if ($newStatus === 'ยืนยัน') {
                $updateData['time_check'] = now();
            } else {
                $updateData['time_check'] = null;
            }

            $affected = $query->update($updateData);

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรายการที่จะอัปเดต',
                ], 404);
            }

            Log::info('Deposit status updated', [
                'so_id'      => $soId,
                'deposit_id' => $depositId,
                'new_status' => $newStatus,
                'changed_by' => $changedBy,
                'time_check' => $updateData['time_check'],
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'อัปเดตสถานะเรียบร้อย',
                'time_check' => $updateData['time_check'],
            ]);

        } catch (\Throwable $e) {
            Log::error('Update deposit status failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markPrinted(Request $request)
    {
        $depositId = $request->input('deposit_id');
        $soId      = $request->input('so_id');
        $billNo    = trim($request->input('bill_no', ''));
        $printedBy = trim($request->input('printed_by', 'unknown'));

        if (empty($depositId) && empty($soId)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีข้อมูลใบมัดจำ',
            ], 422);
        }

        try {
            $query = deposit::query();

            if (!empty($depositId)) {
                $query->where('id', $depositId);
            } else {
                $query->where('so_id', $soId);
            }

            $updateData = [
                'print_time' => now(),
            ];

            if ($billNo !== '') {
                $updateData['status_bill'] = $billNo;
            }

            $affected = $query->update($updateData);

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรายการที่จะอัปเดต',
                ], 404);
            }

            Log::info('Deposit marked as printed', [
                'so_id'      => $soId,
                'deposit_id' => $depositId,
                'bill_no'    => $billNo,
                'printed_by' => $printedBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกการพิมพ์เรียบร้อย',
            ]);

        } catch (\Throwable $e) {
            Log::error('Mark printed failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markPrintedBulk(Request $request)
    {
        $ids = $request->input('deposit_ids', []);
        $printedBy = trim($request->input('printed_by', 'unknown'));

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีรายการที่จะบันทึก',
            ], 422);
        }

        try {
            $affected = deposit::whereIn('id', $ids)
                ->update(['print_time' => now()]);

            Log::info('Deposit bulk marked as printed', [
                'deposit_ids' => $ids,
                'count'       => $affected,
                'printed_by'  => $printedBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => "บันทึก {$affected} รายการสำเร็จ",
                'count'   => $affected,
            ]);

        } catch (\Throwable $e) {
            Log::error('Mark printed bulk failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function botdeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));

        $query = deposit::query()
            ->where('status', 'ยืนยัน');

        if ($soKeyword !== '') {
            $query->where('so_id', 'like', "%{$soKeyword}%");
        }

        $deposits = $query
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('deposit.botdeposit', compact('deposits'));
    }

    private function generateDepositBillId()
    {
        $now      = Carbon::now();
        $yearBE   = $now->year + 543;
        $yy       = substr((string)$yearBE, -2);
        $mm       = $now->format('m');
        $prefix   = "RD{$yy}{$mm}-";

        $latest = DB::table('deposit')
            ->where('deposit_bill_id', 'like', $prefix . '%')
            ->orderByDesc('deposit_bill_id')
            ->lockForUpdate()
            ->value('deposit_bill_id');

        if ($latest) {
            $lastNum = (int) substr($latest, strlen($prefix));
            $next    = $lastNum + 1;
        } else {
            $next = 1;
        }

        $running = str_pad((string)$next, 5, '0', STR_PAD_LEFT);
        return $prefix . $running;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_id'              => 'required|string|max:50',
            'sell_date'          => 'nullable|string',
            'customer_id'        => 'nullable|string|max:50',
            'customer_name'      => 'nullable|string|max:255',
            'contactso'          => 'required|string|max:255',
            'customer_tel'       => 'nullable|string|max:50',
            'customer_address'   => 'nullable|string',
            'note'            => 'nullable|string|max:1000',
            'emp_name'           => 'nullable|string|max:150',
            'tax_id'             => 'nullable|string|max:150',
            'sale_name'          => 'nullable|string|max:150',
            'po_document'        => 'nullable|string|max:100',
            'grand_total'        => 'required|numeric|min:0',
            'deposits'           => 'required|array|min:1',
            'deposits.*.type'    => 'required|in:product,service,shipping',
            'deposits.*.percent' => 'required|numeric|min:0|max:100',
            'deposits.*.amount'  => 'required|numeric|min:0',
        ]);

        $dateDep = null;
        if (!empty($validated['sell_date'])) {
            $parts = explode('-', $validated['sell_date']);
            if (count($parts) === 3) {
                if (strlen($parts[0]) === 2) {
                    $dateDep = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                } else {
                    $dateDep = $validated['sell_date'];
                }
            }
        }

        $netGrandTotal = (float)$validated['grand_total'];

        DB::beginTransaction();
        try {
            $depositBillId = $this->generateDepositBillId();

            $inserted = [];

            foreach ($validated['deposits'] as $dep) {
                if ((float)$dep['percent'] <= 0 && (float)$dep['amount'] <= 0) {
                    continue;
                }

                $row = deposit::create([
                    'so_id'            => $validated['so_id'],
                    'date_dep'         => $dateDep,
                    'customer_id'      => $validated['customer_id']      ?? null,
                    'customer_name'    => $validated['customer_name']    ?? null,
                    'contactso'        => $validated['contactso'],
                    'customer_tel'     => $validated['customer_tel']     ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'note'          => $validated['note']          ?? null,
                    'sale_name'        => $validated['sale_name']        ?? null,
                    'po_document'      => $validated['po_document']      ?? null,
                    'emp_name'         => $validated['emp_name']         ?? 'Guest',
                    'dep_type'         => $dep['type'],
                    'dep_per'          => $dep['percent'],
                    'dep_price'        => $dep['amount'],
                    'grand_total'      => $netGrandTotal,
                    'time'             => now(),
                    'tax_id'           => $validated['tax_id']           ?? null,
                    'print_time'       => null,
                    'status'           => 'รอยืนยัน',
                    'status_bill'      => null,
                    'deposit_bill_id'  => $depositBillId,
                    'time_check'       => null,
                    'deposit_bill'     => null,
                ]);

                $inserted[] = $row->id;
            }

            if (empty($inserted)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่มีรายการมัดจำที่ถูกต้อง',
                ], 422);
            }

            $pdfPath = null;
            $pdfUrl  = null;
            try {
                $pdfData = $validated;
                $pdfData['billid']          = $depositBillId;
                $pdfData['deposit_bill_id'] = $depositBillId;
                $pdfPath = $this->generateDepositPdf($pdfData);
                $pdfUrl  = asset('storage/' . $pdfPath);

                // ✅ อัปเดตชื่อไฟล์ PDF เก็บไว้ในคอลัมน์ deposit_bill (เก็บเฉพาะชื่อไฟล์ ไม่มี path)
                if ($pdfPath) {
                    $fileNameOnly = basename($pdfPath); // เช่น RD6905-00004.pdf
                    deposit::whereIn('id', $inserted)
                        ->update(['deposit_bill' => $fileNameOnly]);
                }
            } catch (\Throwable $pdfErr) {
                Log::error('Generate deposit PDF failed: ' . $pdfErr->getMessage(), [
                    'so_id' => $validated['so_id'],
                    'trace' => $pdfErr->getTraceAsString(),
                ]);
            }

            DB::commit();

            Log::info('Deposit created', [
                'so_id'           => $validated['so_id'],
                'deposit_bill_id' => $depositBillId,
                'po_document'     => $validated['po_document'] ?? null,
                'count'           => count($inserted),
                'pdf_path'        => $pdfPath,
            ]);

            return response()->json([
                'success'         => true,
                'message'         => 'บันทึกใบมัดจำเรียบร้อยแล้ว',
                'so_id'           => $validated['so_id'],
                'deposit_bill_id' => $depositBillId,
                'inserted_ids'    => $inserted,
                'count'           => count($inserted),
                'pdf_path'        => $pdfPath,
                'pdf_url'         => $pdfUrl,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Deposit store failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function preview(Request $request)
    {
        $data = $request->all();

        if (isset($data['deposits']) && is_string($data['deposits'])) {
            $data['deposits'] = json_decode($data['deposits'], true) ?? [];
        }
        $data['deposits']    = $data['deposits']    ?? [];
        $data['grand_total'] = $data['grand_total'] ?? 0;

        try {
            $binary = $this->buildDepositPdfBinary($data);
            return response($binary, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview.pdf"',
                'Cache-Control'       => 'no-store, no-cache, must-revalidate',
                'Pragma'              => 'no-cache',
            ]);
        } catch (\Throwable $e) {
            Log::error('Deposit preview failed: ' . $e->getMessage());
            $tpl = storage_path('app/public/' . self::PDF_TEMPLATE_REL);
            if (file_exists($tpl)) {
                return response()->file($tpl, ['Content-Type' => 'application/pdf']);
            }
            abort(500, $e->getMessage());
        }
    }

    // ============================================================================
    //  ====================  PDF GENERATION HELPERS  ==============================
    // ============================================================================

    protected function generateDepositPdf(array $data): string
    {
        $pdfBinary = $this->buildDepositPdfBinary($data);
        $safeName = $this->sanitizeDepositFilename(
            $data['deposit_bill_id'] ?? ($data['billid'] ?? ('deposit_' . date('YmdHis')))
        );
        $relPath  = self::PDF_OUTPUT_DIR . '/' . $safeName . '.pdf';
        $absPath  = storage_path('app/public/' . $relPath);

        $dir = dirname($absPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($absPath, $pdfBinary);
        return $relPath;
    }

    protected function buildDepositPdfBinary(array $data): string
    {
        $templateAbs = storage_path('app/public/' . self::PDF_TEMPLATE_REL);
        if (!file_exists($templateAbs)) {
            throw new \RuntimeException('ไม่พบไฟล์ template: ' . self::PDF_TEMPLATE_REL);
        }

        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetCreator('Logistic System');
        $pdf->SetAuthor($data['emp_name'] ?? 'System');
        $pdf->SetTitle('ใบมัดจำ ' . ($data['so_id'] ?? ''));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        $pageCount = $pdf->setSourceFile($templateAbs);
        for ($p = 1; $p <= $pageCount; $p++) {
            $tplId = $pdf->importPage($p);
            $size  = $pdf->getTemplateSize($tplId);
            $pdf->AddPage($size['orientation'] ?? 'P', [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            if ($p === 1) {
                $this->drawDepositOverlay($pdf, $data, (float)$size['width'], (float)$size['height']);
            }
        }

        return $pdf->Output('', 'S');
    }

    protected function drawDepositOverlay(Fpdi $pdf, array $data, float $w, float $h): void
    {
        $pdf->SetTextColor(1, 1, 1);
        $pdf->SetFont('freeserif', '', 10);

        $pdf->SetXY(22, 51.5);
        $pdf->Cell(60, 6, $this->safeText($data['customer_id'] ?? ''), 0, 0, 'L');

        $pdf->SetXY(22, 58);
        $pdf->Cell(60, 6, $this->safeText($data['customer_name'] ?? ''), 0, 0, 'L');

        $pdf->SetXY(15, 65.7);
        $pdf->MultiCell(80, 5,
            $this->safeText($data['customer_address'] ?? ''),
            0, 'L'
        );

        $pdf->SetXY(15, 77.7);
        $pdf->Cell(60, 6, $this->safeText($data['customer_tel'] ?? ''), 0, 0, 'L');

        $pdf->SetXY(136, 51.7);
        $pdf->Cell(60, 6, $this->safeText($data['tax_id'] ?? ''), 0, 0, 'L');

        // ✅ วาดเลขที่ใบมัดจำ (deposit_bill_id) — จะมีค่าเฉพาะตอนกดบันทึกเท่านั้น
        $pdf->SetXY(113, 58.1);
        $pdf->Cell(60, 6, $this->safeText($data['deposit_bill_id'] ?? ''), 0, 0, 'L');

        $pdf->SetXY(113, 64.5);
        $pdf->Cell(60, 6, $this->safeText(Carbon::now()->format('d-m-') . (Carbon::now()->year + 543)), 0, 0, 'L');

        // ✅ วาดหมายเหตุ (note)
        $pdf->SetXY(22, 173.5);
        $pdf->MultiCell(110, 5,
            $this->safeText($data['note'] ?? ''),
            0, 'L'
        );

        // ============================================================
        //  ✅ วาดรายการ deposits
        // ============================================================
        $startY = 93;
        $rowH   = 6;

        $deposits   = $data['deposits'] ?? [];
        $poDocument = $data['po_document'] ?? '';

        // Loop 1: วาดประโยค "รับเงินค่ามัดจำ ..."
        $grandTotalData = (float)($data['grand_total'] ?? 0);

        $y = $startY;
        foreach ($deposits as $dep) {
            $percent = (float)($dep['percent'] ?? 0);
            $amount  = (float)($dep['amount']  ?? 0);

            // ✅ คำนวณยอดเต็มย้อนกลับจาก amount และ percent
            //    เช่น มัดจำ 11% = 1,193.50 → ยอดเต็ม = 1,193.50 × 100 / 11 = 10,850.00
            //    ถ้าคำนวณไม่ได้ (percent = 0) ค่อย fallback เป็น grand_total
            if ($percent > 0 && $amount > 0) {
                $fullPrice = $amount * 100 / $percent;
            } else {
                $fullPrice = $grandTotalData;
            }

            $line = sprintf(
                'รับเงินค่ามัดจำ %s%% %s – (%s)',
                rtrim(rtrim(number_format($percent, 2), '0'), '.'),
                $poDocument,
                number_format($fullPrice, 2)
            );

            $pdf->SetXY(30, $y);
            $pdf->Cell(150, $rowH, $this->safeText($line), 0, 0, 'L');

            $y += $rowH;
        }

        // Loop 2: วาดยอดมัดจำแต่ละแถว
        $y = $startY;
        foreach ($deposits as $dep) {
            $amount = (float)($dep['amount'] ?? 0);

            $pdf->SetXY(162, $y);
            $pdf->Cell(40, $rowH, number_format($amount, 2), 0, 0, 'R');

            $y += $rowH;
        }

        // คำนวณยอดรวม + VAT
        $totalAmount = array_sum(array_column($deposits, 'amount'));
        $vat         = $totalAmount * 0.07;
        $grandTotal  = $totalAmount + $vat;

        // 💰 ยอดรวมก่อน VAT
        $pdf->SetXY(162, 174);
        $pdf->Cell(40, $rowH, number_format($totalAmount, 2), 0, 0, 'R');

        // 💰 VAT 7%
        $pdf->SetXY(162, 183);
        $pdf->Cell(40, $rowH, number_format($vat, 2), 0, 0, 'R');

        // 💰 ยอดรวม VAT
        $pdf->SetXY(162, 192);
        $pdf->Cell(40, $rowH, number_format($grandTotal, 2), 0, 0, 'R');

        // 📝 จำนวนเงินเป็นตัวอักษรไทย เช่น "หนึ่งพันห้าสิบสี่บาทห้าสิบสตางค์"
        $pdf->SetXY(55, 192);                                          // 👈 ปรับ X, Y ให้ตรงช่อง
        $pdf->Cell(100, $rowH, $this->bahtText($grandTotal), 0, 0, 'L');
    }

    protected function sanitizeDepositFilename(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[\/\\\\:*?"<>|]/u', '_', $name);
        $name = preg_replace('/\s+/', '_', $name);
        return $name !== '' ? $name : 'deposit_' . date('YmdHis');
    }

    protected function safeText($text): string
    {
        if ($text === null) return '';
        return (string) $text;
    }

    /**
     * แปลงตัวเลขเป็นคำอ่านภาษาไทย
     * เช่น 3000 → "สามพันบาทถ้วน", 1054.50 → "หนึ่งพันห้าสิบสี่บาทห้าสิบสตางค์"
     */
    protected function bahtText($amount): string
    {
        $number = number_format((float)$amount, 2, '.', '');
        [$baht, $satang] = explode('.', $number);

        $txtnum1 = ['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
        $txtnum2 = ['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'];

        $convert = function ($num) use ($txtnum1, $txtnum2) {
            $num = (string)(int)$num;
            $len = strlen($num);
            $result = '';
            for ($i = 0; $i < $len; $i++) {
                $digit = (int)$num[$i];
                $pos   = $len - $i - 1;
                if ($digit === 0) continue;

                if ($pos === 0 && $digit === 1 && $len > 1) {
                    $result .= 'เอ็ด';
                } elseif ($pos === 1) {
                    if ($digit === 1)     $result .= 'สิบ';
                    elseif ($digit === 2) $result .= 'ยี่สิบ';
                    else                  $result .= $txtnum1[$digit] . 'สิบ';
                } else {
                    $result .= $txtnum1[$digit] . $txtnum2[$pos];
                }
            }
            return $result;
        };

        $bahtText = '';
        if ((int)$baht === 0) {
            $bahtText = 'ศูนย์';
        } else {
            $remain = $baht;
            while (strlen($remain) > 7) {
                $head     = substr($remain, 0, strlen($remain) - 6);
                $remain   = substr($remain, -6);
                $bahtText .= $convert($head) . 'ล้าน';
            }
            $bahtText .= $convert($remain);
        }
        $bahtText .= 'บาท';

        if ((int)$satang === 0) {
            $bahtText .= 'ถ้วน';
        } else {
            $bahtText .= $convert($satang) . 'สตางค์';
        }

        return '(' . $bahtText . ')';   // 👈 ครอบวงเล็บตรงนี้
    }
}