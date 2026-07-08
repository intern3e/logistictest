<?php

namespace App\Http\Controllers;
use setasign\Fpdi\Fpdi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use setasign\Fpdi\TcpdfFpdi;
use App\Models\Bill;
use PDF;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser as PdfParser;

class PoDocumentController extends Controller
{
    private $specialCustomers = ['CUS-26039'];
    private $tcusPerBillPage = 6;
    private $depositPdfPath = null; 
    private $dateOverlayPdfPath = null;

    private function isSpecialCustomer($customer_id): bool
    {
        $cleanCustomerId = trim($customer_id ?? '');
        return in_array($cleanCustomerId, $this->specialCustomers);
    }
    private function getTemplatePath(): string
    {
        return storage_path("app/public/template/TCUS-26039.pdf");
    }
    private function saveOutputFiles($pdf, $billid): void
    {
        $output1 = storage_path("app/public/doc_document/{$billid}.pdf");
        $pdf->Output('F', $output1);

        $output2 = storage_path("app/public/bill_document/{$billid}.pdf");
        if (!file_exists(dirname($output2))) {
            mkdir(dirname($output2), 0777, true);
        }
        copy($output1, $output2);
    }
    private function getDepositText($deposit_bill_id)
    {
        if (empty($deposit_bill_id)) {
            return null;
        }

        $deposit = DB::table('deposit')
            ->where('deposit_bill_id', $deposit_bill_id)
            ->first();

        if (!$deposit) {
            Log::warning("ไม่พบข้อมูล deposit: " . $deposit_bill_id);
            return null;
        }

        $depTypeText = '';
        if ($deposit->dep_type === 'product') {
            $depTypeText = 'สินค้า';
        } elseif ($deposit->dep_type === 'service') {
            $depTypeText = 'บริการ';
        } else {
            $depTypeText = $deposit->dep_type ?? '';
        }

        $depPriceFormatted = number_format((float)$deposit->dep_price, 2);

        return "หักค่ามัดจำ{$depTypeText} {$deposit->dep_per}% อ้างอิงใบกำกับภาษีเลขที่ {$deposit_bill_id} จากยอดมูลค่ายอดก่อน VAT – {$depPriceFormatted}";
    }
    private function createDepositPdf(string $depositText): ?string
    {
        if ($this->depositPdfPath !== null && file_exists($this->depositPdfPath)) {
            return $this->depositPdfPath;
        }

        try {
            $fontNormal = base64_encode(file_get_contents(storage_path('fonts/THSarabun.ttf')));
            $fontBold = base64_encode(file_get_contents(storage_path('fonts/THSarabun Bold.ttf')));

            $textEscaped = htmlspecialchars($depositText, ENT_QUOTES, 'UTF-8');

            $html = '
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    @page { margin: 0; size: A4; }

                    @font-face {
                        font-family: "THSarabun";
                        src: url(data:font/truetype;charset=utf-8;base64,' . $fontNormal . ') format("truetype");
                        font-weight: normal;
                    }
                    @font-face {
                        font-family: "THSarabun";
                        src: url(data:font/truetype;charset=utf-8;base64,' . $fontBold . ') format("truetype");
                        font-weight: bold;
                    }

                    body {
                        font-family: "THSarabun", sans-serif;
                        margin: 0;
                        padding: 0;
                    }

                    .deposit-text {
                        position: absolute;
                        top: 196mm;
                        left: 23mm;
                        right: 10mm;
                        font-size: 13pt;
                        font-weight: bold;
                        color: rgb(0, 0, 0);
                    }
                </style>
            </head>
            <body>
                <div class="deposit-text">' . $textEscaped . '</div>
            </body>
            </html>';

            $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');

            $tempPath = storage_path('app/public/temp/deposit_' . uniqid() . '.pdf');
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }

            file_put_contents($tempPath, $pdf->output());

            $this->depositPdfPath = $tempPath;
            Log::info("✅ สร้าง deposit PDF: " . $tempPath);

            return $tempPath;

        } catch (\Exception $e) {
            Log::error("❌ สร้าง deposit PDF ไม่สำเร็จ: " . $e->getMessage());
            return null;
        }
    }
    private function cleanupDepositPdf(): void
    {
        if ($this->depositPdfPath && file_exists($this->depositPdfPath)) {
            @unlink($this->depositPdfPath);
            $this->depositPdfPath = null;
        }
    }
    private function stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage): void
    {
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(155, 12);
        $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');

        $pdf->SetXY(155, 15);
        $pdf->Cell(50, 10, "{$so_id}", 0, 0, 'R');

        if (file_exists($stampImage)) {
            $pdf->Image($stampImage, 170, 257, 22, 0, 'PNG');
        }
    }
    private function overlayDepositPdf($pdf, $depositPdfPath, $pageWidth, $pageHeight): void
    {
        if (!$depositPdfPath || !file_exists($depositPdfPath)) {
            return;
        }

        try {
            $pdf->setSourceFile($depositPdfPath);
            $depositTemplateId = $pdf->importPage(1);
            $pdf->useTemplate($depositTemplateId, 0, 0, $pageWidth, $pageHeight);
        } catch (\Exception $e) {
            Log::error("overlay deposit ไม่สำเร็จ: " . $e->getMessage());
        }
    }
    private function extractAndConvertDates(string $filePath): array
    {
        $parser = new PdfParser();
        $text   = $parser->parseFile($filePath)->getText();

        $result = ['dates' => [], 'day_term' => null];

        // หา dd/mm/25xx → ลบ 543
        if (preg_match_all('/(\d{2}\/\d{2}\/)(25\d{2})/', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $result['dates'][] = [
                    'original'  => $m[0],
                    'converted' => $m[1] . ((int)$m[2] - 543),
                ];
            }
        }
        if (preg_match('/(\d+)\s*วัน/', $text, $dm)) {
            $result['day_term'] = [
                'original'  => $dm[0],
                'converted' => $dm[1] . ' Days',
            ];
        }

        return $result;
    }
private function convertDatesForSpecialCustomer(string $billId, string $stampImage, string $billDate = '', string $dueDate = ''): void
{
    $filePath = storage_path("app/public/doc_document/{$billId}.pdf");
    if (!file_exists($filePath)) return;

    $overlayPath = null;

    try {
        $convertedData = [
            'dates' => [
                ['converted' => $this->convertBuddhistDate($billDate)],
                ['converted' => $this->convertBuddhistDate($dueDate)],  
            ],
            'day_term' => null,
        ];

        Log::info("convertDates [{$billId}]", $convertedData);

        $overlayPath = $this->createDateOverlayPdf($convertedData);
        if (!$overlayPath) return;

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($filePath);

        for ($p = 1; $p <= $pageCount; $p++) {
            $pdf->setSourceFile($filePath);
            $tpl  = $pdf->importPage($p);
            $size = $pdf->getTemplateSize($tpl);

            $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height']);
            $this->overlayDepositPdf($pdf, $overlayPath, $size['width'], $size['height']);

            if (file_exists($stampImage)) {
                $pdf->Image($stampImage, 170, 257, 22, 0, 'PNG');
            }
        }
        $this->saveOutputFiles($pdf, $billId);
        Log::info("✅ convertDates สำเร็จ: {$billId}");

    } catch (\Exception $e) {
        Log::error("❌ convertDates [{$billId}]: " . $e->getMessage());
    } finally {
        if ($overlayPath && file_exists($overlayPath)) {
            @unlink($overlayPath);
            $this->dateOverlayPdfPath = null;
        }
    }
}
private function createDateOverlayPdf(array $convertedData): ?string
{
    try {
        $fontNormal = base64_encode(file_get_contents(storage_path('fonts/THSarabun.ttf')));
        $fontBold   = base64_encode(file_get_contents(storage_path('fonts/THSarabun Bold.ttf')));

        $date1 = htmlspecialchars($convertedData['dates'][0]['converted'] ?? ''); // DATE
        $date2 = htmlspecialchars($convertedData['dates'][1]['converted'] ?? ''); // DUE DATE
        $days  = '30 Days';

       $html = '
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                @page { margin: 0; size: A4; }
                body { margin: 0; padding: 0; }
                .wrap {
                    position: absolute;
                    font-size: 8pt;
                    font-family: Arial, sans-serif;
                }
                td {
                    background-color: white;
                    color: #000;
                    padding: 0 3px;
                    text-align: right;
                    white-space: nowrap;
                }
                .date1 { top: 63mm; left: 148mm; }
                .days  { top: 73mm; left: 166.5mm; }
                .date2 { top: 84mm; left: 158mm; }
            </style>
        </head>
        <body>
            <div class="wrap date1"><table><tr><td>' . $date1 . '</td></tr></table></div>
            <div class="wrap days"><table><tr><td>' . $days . '</td></tr></table></div>
            <div class="wrap date2"><table><tr><td>' . $date2 . '</td></tr></table></div>
        </body>
        </html>';
        $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');

        $tempPath = storage_path('app/public/temp/date_overlay_' . uniqid() . '.pdf');
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        file_put_contents($tempPath, $pdf->output());

        $this->dateOverlayPdfPath = $tempPath;
        Log::info("✅ date overlay: date1={$date1} days={$days} date2={$date2}");

        return $tempPath;

    } catch (\Exception $e) {
        Log::error("❌ createDateOverlayPdf: " . $e->getMessage());
        return null;
    }
}
private function convertBuddhistDate(string $date): string
{
    // dd/mm/25xx → dd/mm/20xx
    return preg_replace_callback(
        '/(\d{2}\/\d{2}\/)(25\d{2})/',
        fn($m) => $m[1] . ((int)$m[2] - 543),
        $date
    );
}


    public function addSoDetailIdToPoDocument($so_detail_id, $POdocument): JsonResponse
    {
        try {
            ob_start();

            $filePath = storage_path("app/public/po_documents/{$POdocument}");

            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json(['success' => true]);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);

            if ($pageCount === 0) {
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF']);
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->SetMargins(0, 0, 0);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(130, 130, 130);
                $pdf->Text(175, 4, "{$so_detail_id}");
            }

            $outputPath = storage_path("app/public/po_documents/{$POdocument}");
            $pdf->Output('F', $outputPath);

            ob_end_clean();

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิลลงในเอกสาร PO สำเร็จ',
                'so_detail_id' => $so_detail_id
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            Log::error("Error in addSoDetailIdToPoDocument: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'ระบบพบข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
    public function addIdToDocument(Request $request): JsonResponse
    {
        try {
            ob_start();

            $so_detail_id = $request->input('so_detail_id');
            $billid = $request->input('billid');
            $so_id = $request->input('so_id');
            $customer_id = $request->input('customer_id');
            $deposit_bill_id = $request->input('deposit_bill_id');
            $bill_Date = $request->input('bill_Date', '');
            $due_date  = $request->input('due_date', '');

            Log::info("addIdToDocument - Customer: [{$customer_id}] | Deposit: [{$deposit_bill_id}]");

            if (!$so_detail_id || !$billid || !$so_id) {
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
            }

            $filePath = storage_path("app/public/doc_document/{$billid}.pdf");
            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json(['success' => true]);
            }

            $stampImage = storage_path("app/public/template/ly.png");
            $isSpecialCustomer = $this->isSpecialCustomer($customer_id);
            $templatePdfPath = $this->getTemplatePath();
            $useTemplate = $isSpecialCustomer && file_exists($templatePdfPath);

            $depositText = $this->getDepositText($deposit_bill_id);
            $hasDeposit = !empty($depositText);
            $depositPdfPath = $hasDeposit ? $this->createDepositPdf($depositText) : null;

            $pdf = new Fpdi();
            $billPageCount = $pdf->setSourceFile($filePath);

            if ($billPageCount === 0) {
                ob_end_clean();
                $this->cleanupDepositPdf();
                return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF']);
            }

            if ($useTemplate) {
                for ($billPageNo = 1; $billPageNo <= $billPageCount; $billPageNo++) {
                    for ($tcusOffset = 1; $tcusOffset <= $this->tcusPerBillPage; $tcusOffset++) {
                        $tcusPageNo = $tcusOffset;

                        $pdf->setSourceFile($filePath);
                        $templateBillId = $pdf->importPage($billPageNo);
                        $sizeBill = $pdf->getTemplateSize($templateBillId);

                        $pageWidth = $sizeBill['width'];
                        $pageHeight = $sizeBill['height'];

                        $pdf->addPage($sizeBill['orientation'], [$pageWidth, $pageHeight]);

                        $pdf->setSourceFile($templatePdfPath);
                        $templateSpecialId = $pdf->importPage($tcusPageNo);
                        $pdf->useTemplate($templateSpecialId, 0, 0, $pageWidth, $pageHeight);

                        $pdf->setSourceFile($filePath);
                        $templateBillId = $pdf->importPage($billPageNo);
                        $pdf->useTemplate($templateBillId, 0, 0, $pageWidth, $pageHeight);

                        $this->stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage);
                        $isLastPage = ($tcusOffset == $this->tcusPerBillPage && $billPageNo == $billPageCount);

                        if ($hasDeposit && $depositPdfPath && $isLastPage) {
                            $this->overlayDepositPdf($pdf, $depositPdfPath, $sizeBill['width'], $sizeBill['height']);
                        }

                        if ($isSpecialCustomer && $isLastPage) {
                            $this->stampNoWithholdingTax($pdf);
                        }
                    }
                }
            } else {
                for ($pageNo = 1; $pageNo <= $billPageCount; $pageNo++) {
                    $pdf->setSourceFile($filePath);
                    $templateBillId = $pdf->importPage($pageNo);
                    $sizeBill = $pdf->getTemplateSize($templateBillId);

                    $pdf->addPage($sizeBill['orientation'], [$sizeBill['width'], $sizeBill['height']]);
                    $pdf->useTemplate($templateBillId);

                    // ปั้มข้อมูลทั่วไป (ทุกหน้า)
                    $this->stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage);

                    // ✨ ซ้อน deposit เฉพาะหน้าสุดท้าย
                    $isLastPage = ($pageNo == $billPageCount);
                    if ($hasDeposit && $depositPdfPath && $isLastPage) {
                        $this->overlayDepositPdf($pdf, $depositPdfPath, $sizeBill['width'], $sizeBill['height']);
                    }
                }
            }

            $this->saveOutputFiles($pdf, $billid);

            // ✨ แปลงวันที่ พ.ศ. → ค.ศ. เฉพาะ specialCustomers
if ($isSpecialCustomer) {
    $this->convertDatesForSpecialCustomer($billid, $stampImage, $bill_Date, $due_date);
}

            $this->cleanupDepositPdf();
            ob_end_clean();

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิล + รูปปั้ม ลงในเอกสารสำเร็จ',
                'has_deposit' => $hasDeposit,
                'bill_pages' => $billPageCount,
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            $this->cleanupDepositPdf();
            Log::error('Error in addIdToDocument', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

private function stampNoWithholdingTax($pdf): void
{
    $pdf->SetFont('Helvetica', '', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(10, 194);
    $pdf->Cell(0, 10, 'No withholding tax is required', 0, 0, 'L');
}
    public function addIdToDocument3(Request $request): JsonResponse
    {
        try {
            ob_start();

            $so_detail_id = $request->input('so_detail_id');
            $billid = $request->input('billid');
            $so_id = $request->input('so_id');
            $customer_id = $request->input('customer_id');
            $deposit_bill_id = $request->input('deposit_bill_id');
            $bill_Date = $request->input('bill_Date', '');
            $due_date  = $request->input('due_date', '');

            Log::info("addIdToDocument3 - Customer: [{$customer_id}] | Deposit: [{$deposit_bill_id}]");

            if (!$so_detail_id || !$billid || !$so_id) {
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
            }

            $filePath = storage_path("app/public/doc_document/{$billid}.pdf");
            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json(['success' => true]);
            }

            $stampImage1 = storage_path("app/public/template/ly.png");
            $stampImage2 = storage_path("app/public/template/3.png");

            $isSpecialCustomer = $this->isSpecialCustomer($customer_id);
            $templatePdfPath = $this->getTemplatePath();
            $useTemplate = $isSpecialCustomer && file_exists($templatePdfPath);

            $depositText = $this->getDepositText($deposit_bill_id);
            $hasDeposit = !empty($depositText);
            $depositPdfPath = $hasDeposit ? $this->createDepositPdf($depositText) : null;

            $pdf = new Fpdi();
            $billPageCount = $pdf->setSourceFile($filePath);

            if ($billPageCount === 0) {
                ob_end_clean();
                $this->cleanupDepositPdf();
                return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF']);
            }

            if ($useTemplate) {
                for ($tcusOffset = 1; $tcusOffset <= $this->tcusPerBillPage; $tcusOffset++) {
                    for ($billPageNo = 1; $billPageNo <= $billPageCount; $billPageNo++) {
                        $tcusPageNo = $tcusOffset;

                        $pdf->setSourceFile($filePath);
                        $templateBillId = $pdf->importPage($billPageNo);
                        $sizeBill = $pdf->getTemplateSize($templateBillId);

                        $pageWidth = $sizeBill['width'];
                        $pageHeight = $sizeBill['height'];

                        $pdf->addPage($sizeBill['orientation'], [$pageWidth, $pageHeight]);

                        // Layer 1: TCUS
                        $pdf->setSourceFile($templatePdfPath);
                        $templateSpecialId = $pdf->importPage($tcusPageNo);
                        $pdf->useTemplate($templateSpecialId, 0, 0, $pageWidth, $pageHeight);

                        // Layer 2: บิลเดิม
                        $pdf->setSourceFile($filePath);
                        $templateBillId = $pdf->importPage($billPageNo);
                        $pdf->useTemplate($templateBillId, 0, 0, $pageWidth, $pageHeight);

                        // Layer 3: ปั้มข้อมูลทั่วไป (ทุกหน้า)
                        $this->stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage1);

                        // Layer 4: รูป 3.png (ทุกหน้า)
                        if (file_exists($stampImage2)) {
                            $pdf->Image($stampImage2, 10, 194, 40, 0, 'PNG');
                        }

                        // ✨ Layer 5: ซ้อน deposit เฉพาะหน้าสุดท้าย
                        $isLastPage = ($tcusOffset == $this->tcusPerBillPage && $billPageNo == $billPageCount);
                        if ($hasDeposit && $depositPdfPath && $isLastPage) {
                            $this->overlayDepositPdf($pdf, $depositPdfPath, $pageWidth, $pageHeight);
                        }
                    }
                }
            } else {
                for ($pageNo = 1; $pageNo <= $billPageCount; $pageNo++) {
                    $pdf->setSourceFile($filePath);
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);

                    $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);

                    // ปั้มข้อมูลทั่วไป (ทุกหน้า)
                    $this->stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage1);

                    // รูป 3.png (ทุกหน้า)
                    if (file_exists($stampImage2)) {
                        $pdf->Image($stampImage2, 10, 194, 40, 0, 'PNG');
                    }

                    // ✨ ซ้อน deposit เฉพาะหน้าสุดท้าย
                    $isLastPage = ($pageNo == $billPageCount);
                    if ($hasDeposit && $depositPdfPath && $isLastPage) {
                        $this->overlayDepositPdf($pdf, $depositPdfPath, $size['width'], $size['height']);
                    }
                }
            }

            $this->saveOutputFiles($pdf, $billid);

            // ✨ แปลงวันที่ พ.ศ. → ค.ศ. เฉพาะ specialCustomers
if ($isSpecialCustomer) {
    $this->convertDatesForSpecialCustomer($billid, $stampImage, $bill_Date, $due_date);
}

            $this->cleanupDepositPdf();
            ob_end_clean();

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิล + ปั้ม ลงในเอกสารสำเร็จ',
                'has_deposit' => $hasDeposit,
                'bill_pages' => $billPageCount,
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            $this->cleanupDepositPdf();
            Log::error('Error in addIdToDocument3', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function addIdToDocument5(Request $request): JsonResponse
    {
        try {
            ob_start();

            $so_detail_id = $request->input('so_detail_id');
            $billid = $request->input('billid');
            $so_id = $request->input('so_id');
            $customer_id = $request->input('customer_id');
            $deposit_bill_id = $request->input('deposit_bill_id');
            $bill_Date = $request->input('bill_Date', '');
            $due_date  = $request->input('due_date', '');

            Log::info("addIdToDocument5 - Customer: [{$customer_id}] | Deposit: [{$deposit_bill_id}]");

            if (!$so_detail_id || !$billid || !$so_id) {
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
            }

            $filePath = storage_path("app/public/doc_document/{$billid}.pdf");
            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json(['success' => true]);
            }

            $stampImage1 = storage_path("app/public/template/ly.png");
            $stampImage3 = storage_path("app/public/template/5.png");

            $isSpecialCustomer = $this->isSpecialCustomer($customer_id);
            $templatePdfPath = $this->getTemplatePath();
            $useTemplate = $isSpecialCustomer && file_exists($templatePdfPath);

            $depositText = $this->getDepositText($deposit_bill_id);
            $hasDeposit = !empty($depositText);
            $depositPdfPath = $hasDeposit ? $this->createDepositPdf($depositText) : null;

            $pdf = new Fpdi();
            $billPageCount = $pdf->setSourceFile($filePath);

            if ($billPageCount === 0) {
                ob_end_clean();
                $this->cleanupDepositPdf();
                return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF']);
            }

            if ($useTemplate) {
                for ($billPageNo = 1; $billPageNo <= $billPageCount; $billPageNo++) {
                    for ($tcusOffset = 1; $tcusOffset <= $this->tcusPerBillPage; $tcusOffset++) {
                        $tcusPageNo = (($billPageNo - 1) * $this->tcusPerBillPage) + $tcusOffset;

                        $pdf->setSourceFile($filePath);
                        $templateBillId = $pdf->importPage($billPageNo);
                        $sizeBill = $pdf->getTemplateSize($templateBillId);

                        $pageWidth = $sizeBill['width'];
                        $pageHeight = $sizeBill['height'];

                        $pdf->addPage($sizeBill['orientation'], [$pageWidth, $pageHeight]);

                        // Layer 1: TCUS
                        $pdf->setSourceFile($templatePdfPath);
                        $templateSpecialId = $pdf->importPage($tcusPageNo);
                        $pdf->useTemplate($templateSpecialId, 0, 0, $pageWidth, $pageHeight);

                        // Layer 2: บิลเดิม
                        $pdf->setSourceFile($filePath);
                        $templateBillId = $pdf->importPage($billPageNo);
                        $pdf->useTemplate($templateBillId, 0, 0, $pageWidth, $pageHeight);

                        // Layer 3: ปั้มข้อมูลทั่วไป (ทุกหน้า)
                        $this->stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage1);

                        // Layer 4: รูป 5.png (ทุกหน้า)
                        if (file_exists($stampImage3)) {
                            $pdf->Image($stampImage3, 10, 194, 40, 0, 'PNG');
                        }

                        // ✨ Layer 5: ซ้อน deposit เฉพาะหน้าสุดท้าย
                        $isLastPage = ($tcusOffset == $this->tcusPerBillPage && $billPageNo == $billPageCount);
                        if ($hasDeposit && $depositPdfPath && $isLastPage) {
                            $this->overlayDepositPdf($pdf, $depositPdfPath, $pageWidth, $pageHeight);
                        }
                    }
                }
            } else {
                for ($pageNo = 1; $pageNo <= $billPageCount; $pageNo++) {
                    $pdf->setSourceFile($filePath);
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);

                    $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);

                    // ปั้มข้อมูลทั่วไป (ทุกหน้า)
                    $this->stampCommonInfo($pdf, $so_detail_id, $so_id, $stampImage1);

                    // รูป 5.png (ทุกหน้า)
                    if (file_exists($stampImage3)) {
                        $pdf->Image($stampImage3, 10, 194, 40, 0, 'PNG');
                    }

                    // ✨ ซ้อน deposit เฉพาะหน้าสุดท้าย
                    $isLastPage = ($pageNo == $billPageCount);
                    if ($hasDeposit && $depositPdfPath && $isLastPage) {
                        $this->overlayDepositPdf($pdf, $depositPdfPath, $size['width'], $size['height']);
                    }
                }
            }

            $this->saveOutputFiles($pdf, $billid);

            // ✨ แปลงวันที่ พ.ศ. → ค.ศ. เฉพาะ specialCustomers
if ($isSpecialCustomer) {
    $this->convertDatesForSpecialCustomer($billid, $stampImage, $bill_Date, $due_date);
}

            $this->cleanupDepositPdf();
            ob_end_clean();

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิล + ปั้ม ลงในเอกสารสำเร็จ',
                'has_deposit' => $hasDeposit,
                'bill_pages' => $billPageCount,
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            $this->cleanupDepositPdf();
            Log::error('Error in addIdToDocument5', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function addIdToissueDocument($so_detail_id, $bill_issue_no): JsonResponse
    {
        try {
            ob_start();
            $filePath = storage_path("app/public/billissue_document/{$bill_issue_no}.pdf");
            Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json(['success' => true]);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);
            Log::info("จำนวนหน้าที่เจอ: {$pageCount}");

            if ($pageCount === 0) {
                ob_end_clean();
                return response()->json([
                    'success' => false,
                    'error'   => 'ไม่สามารถโหลดไฟล์ PDF'
                ]);
            }

            $stampImage = storage_path("app/public/template/ly.png");

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->addPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $pdf->useTemplate($templateId);

                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(155, 2);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');

                $x = 45;
                $y = 237;
                $w = 22;
                $h = 0;

                if (file_exists($stampImage)) {
                    $pdf->Image($stampImage, $x, $y, $w, $h, 'PNG');
                }
            }

            $outputPath = storage_path("app/public/billissue_document/{$bill_issue_no}.pdf");
            $pdf->Output('F', $outputPath);

            ob_end_clean();

            Log::info("เขียน PDF สำเร็จ: " . $outputPath);

            return response()->json([
                'success'       => true,
                'message'       => 'เพิ่มเลขที่บิล + เพิ่มรูปภาพ ลงในเอกสาร bill issue สำเร็จ',
                'so_detail_id'  => $so_detail_id
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            Log::error("เกิดข้อผิดพลาด: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error'   => 'ระบบพบข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
    public function mergeAndOverwrite(Request $request)
    {
        $billId = $request->input('billid');

        try {
            if (!$billId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill ID ไม่ถูกต้อง'
                ], 400);
            }

            $this->mergePdfWithTemplate(
                storage_path('app/public/template/template.pdf'),
                storage_path("app/public/doc_document/{$billId}.pdf")
            );

            $this->mergePdfWithTemplate(
                storage_path('app/public/template/templatereceipt.pdf'),
                storage_path("app/public/bill_document/{$billId}.pdf")
            );

            return response()->json([
                'success' => true,
                'message' => '✅ รวมและปั้ม PDF ทั้ง 2 ไฟล์สำเร็จ'
            ]);

        } catch (\Throwable $e) {
            \Log::error("❌ mergeAndOverwrite error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '❌ ระบบผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
    private function mergePdfWithTemplate(string $templatePath, string $dataPdfPath): void
    {
        if (!file_exists($templatePath)) {
            return;
        }

        if (!file_exists($dataPdfPath)) {
            return;
        }
 
        $pdf = new TcpdfFpdi();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $templatePageCount = $pdf->setSourceFile($templatePath);
        $dataPageCount     = $pdf->setSourceFile($dataPdfPath);
        $maxPages          = max($templatePageCount, $dataPageCount);

        for ($i = 1; $i <= $maxPages; $i++) {

            $pdf->setSourceFile($templatePath);
            $tplTemplatePage = $pdf->importPage(min($i, $templatePageCount));
            $templateSize = $pdf->getTemplateSize($tplTemplatePage);

            $pdf->AddPage(
                $templateSize['orientation'],
                [$templateSize['width'], $templateSize['height']]
            );

            $pdf->useTemplate(
                $tplTemplatePage,
                0,
                0,
                $templateSize['width'],
                $templateSize['height']
            );

            if ($i <= $dataPageCount) {
                $pdf->setSourceFile($dataPdfPath);
                $tplDataPage = $pdf->importPage($i);

                $pdf->useTemplate(
                    $tplDataPage,
                    0,
                    0,
                    $templateSize['width'],
                    $templateSize['height']
                );
            }
        }
        $pdf->Output($dataPdfPath, 'F');
    }
    public function printNotes($so_detail_id)
    {
        $item = Bill::findOrFail($so_detail_id);

        $fontNormal     = base64_encode(file_get_contents(storage_path('fonts/THSarabun.ttf')));
        $fontBold       = base64_encode(file_get_contents(storage_path('fonts/THSarabun Bold.ttf')));
        $fontItalic     = base64_encode(file_get_contents(storage_path('fonts/THSarabun Italic.ttf')));
        $fontBoldItalic = base64_encode(file_get_contents(storage_path('fonts/THSarabun BoldItalic.ttf')));

        $coords  = trim((string)($item->customer_la_long ?? ''));
        $mapLink = 'https://www.google.com/maps?q=' . rawurlencode($coords);

        $qrCode = new QrCode(
            data: $mapLink,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 200,
            margin: 2,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );
        $writer    = new PngWriter();
        $result    = $writer->write($qrCode);
        $qrDataUri = $result->getDataUri();

        $rawAddress = (string)($item->customer_address ?? '');

        $parts = preg_split('/สถานที่\s*ส่ง\s*[:：]?\s*/u', $rawAddress, 2);

        if (is_array($parts) && count($parts) === 2) {
            $addressBefore = trim($parts[0]);
            $shipTo        = trim($parts[1]);
        } else {
            $addressBefore = $rawAddress;
            $shipTo        = $rawAddress;
        }

        $addressBeforeHtml = nl2br(htmlspecialchars($addressBefore, ENT_QUOTES, 'UTF-8'));
        $shipToHtml        = nl2br(htmlspecialchars($shipTo,        ENT_QUOTES, 'UTF-8'));

        $html = '
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                @page { margin: 0mm 15mm 0mm 15mm; }

                @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontNormal . ') format("truetype"); font-weight:normal; font-style:normal; }
                @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontBold . ') format("truetype"); font-weight:bold; font-style:normal; }
                @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontItalic . ') format("truetype"); font-weight:normal; font-style:italic; }
                @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontBoldItalic . ') format("truetype"); font-weight:bold; font-style:italic; }

                body {
                    font-family:"THSarabun", sans-serif;
                    font-size:22pt;
                    line-height:1.4;
                    margin:0;
                    padding:0;
                    color:#000;
                }

                .content { width: 100%; margin-top: -5mm; }
                table.kv { border-collapse: collapse; width: 100%; }
                table.kv td {
                    padding: 2px 4px;
                    vertical-align: top;
                    font-size: 18pt;
                    word-wrap: break-word;
                    word-break: break-word;
                }
                table.kv td.label {
                    width: 25mm;
                    font-weight: bold;
                    white-space: nowrap;
                    padding-right: 5mm;
                }

                .section { margin-top: 10mm; }
                .section .title {
                    font-weight: bold;
                    border-bottom: 2px solid #000;
                    margin-bottom: 4mm;
                    padding-bottom: 2mm;
                    font-size: 22pt;
                }
                .section .body {
                    font-size: 18pt;
                    text-align: left;
                    white-space: normal;
                    word-wrap: break-word;
                    word-break: break-word;
                    border-bottom: 2px solid #000;
                    padding-bottom: 4mm;
                    margin-bottom: 6mm;
                }

                .footer-qr {
                    position: fixed;
                    bottom: 2mm;
                    left: 0;
                    right: 0;
                    text-align: center;
                    padding-top: 2mm;
                }
                .footer-qr img {
                    display: block;
                    margin: 0 auto;
                    width: 130px;
                    height: 130px;
                }
                .footer-qr p {
                    margin-top: 4mm;
                    font-size: 22pt;
                    font-weight: bold;
                }
                .address {
                    width: 25mm;
                    font-weight: bold;
                    white-space: nowrap;
                    padding-right: 5mm;
                }
                h1 { font-size:22pt; margin:0; }
                .subtitle { font-size:12pt; color:#000000; }
                .header { text-align:left; margin-bottom:5mm; }
                .top-right {
                    font-size: 16pt;
                    position: absolute;
                    top: 2%;
                    right: 4mm;
                    font-weight: normal;
                    font-style: italic;
                    color: #918f8f;
                }
                .line {
                    border: 1px solid black;
                    padding: 15px;
                    position: absolute;
                    top: 2%;
                    left: -5%;
                    right: -5%;
                    bottom: 2%;
                    box-sizing: border-box;
                }
            </style>
        </head>
        <body>
            <div class="line">
                <div class="top-right">' . htmlspecialchars((string)$item->so_detail_id, ENT_QUOTES, "UTF-8") . '</div>
                <div class="header">
                    <h1 style="font-size:40px;">ข้อมูลการจัดส่ง</h1>
                    <div class="subtitle">Delivery Note</div>
                    <div class="subtitle">' . htmlspecialchars((string)$item->time, ENT_QUOTES, "UTF-8") . '</div>
                </div>

                <div class="content">
                    <table class="kv">
                        <tr><td class="label">เลขที่บิล :</td><td>' . htmlspecialchars((string)$item->billid, ENT_QUOTES, "UTF-8") . '</td></tr>
                        <tr><td class="label">บริษัท :</td><td>' . htmlspecialchars((string)$item->customer_name, ENT_QUOTES, "UTF-8") . '</td></tr>
                        <tr><td class="label">ที่อยู่ :</td><td>' . $addressBeforeHtml . '</td></tr>
                        <tr><td class="label">สถานที่ส่ง :</td><td>' . $shipToHtml . '</td></tr>
                        <tr><td class="label">ชื่อผู้ติดต่อ :</td><td>' . htmlspecialchars((string)$item->contactso, ENT_QUOTES, "UTF-8") . '</td></tr>
                        <tr><td class="label">เบอร์ติดต่อ :</td><td>' . htmlspecialchars((string)$item->customer_tel, ENT_QUOTES, "UTF-8") . '</td></tr>
                    </table>

                    <div class="section">
                        <div class="title">รายละเอียดเพิ่มเติม :</div>
                        <div class="body">' . nl2br(htmlspecialchars((string)$item->notes, ENT_QUOTES, "UTF-8")) . '</div>
                    </div>
                </div>

                <div class="footer-qr">
                    <p class="address">ที่อยู่จัดส่ง :</p>
                    <img src="' . $qrDataUri . '" alt="QR Code">
                    <p>สแกนเพื่อเปิดแผนที่</p>
                </div>
            </div>
        </body>
        </html>';

        $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->stream("notes-{$so_detail_id}.pdf");
    }
}