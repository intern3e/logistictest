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

class PoDocumentController extends Controller
{
    public function addSoDetailIdToPoDocument($so_detail_id, $POdocument): JsonResponse
    {
        try {
            ob_start();

            $filePath = storage_path("app/public/po_documents/{$POdocument}");
            Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

            if (!file_exists($filePath)) {
                Log::error("ไม่พบไฟล์: {$filePath}");
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ไฟล์ PO ไม่พบ']);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);
            Log::info("จำนวนหน้าที่เจอ: {$pageCount}");

            if ($pageCount === 0) {
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF']);
            }
    
            // วนลูปทุกหน้าเพื่อเพิ่ม SO ID
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->SetMargins(0, 0, 0);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            $pdf->SetFont('Helvetica', 'I', 8);
            $pdf->SetTextColor(130,130, 130);

            // คำนวณตำแหน่งใหม่ (หน่วย pt)
            $x = 175;               // ใกล้ขอบขวา (A4 กว้าง 210 mm)
            $y = 4;                 // อยู่ห่างจากขอบบน 2 mm
            $pdf->Text($x, $y, "{$so_detail_id}");
        }

            $outputPath = storage_path("app/public/po_documents/{$POdocument}");
            $pdf->Output('F', $outputPath);

            ob_end_clean();
            Log::info("เขียน PDF สำเร็จ: " . $outputPath);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิลลงในเอกสาร PO สำเร็จ',
                'so_detail_id' => $so_detail_id
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            Log::error("เกิดข้อผิดพลาด: " . $e->getMessage());
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

            // ✅ รับค่าจาก Request แทน Parameter
            $so_detail_id = $request->input('so_detail_id');
            $billid = $request->input('billid');
            $so_id = $request->input('so_id');

            // Validate
            if (!$so_detail_id || !$billid || !$so_id) {
                ob_end_clean();
                return response()->json([
                    'success' => false,
                    'error' => 'ข้อมูลไม่ครบถ้วน'
                ]);
            }

            $filePath = storage_path("app/public/doc_document/{$billid}.pdf");

            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json([
                    'success' => false,
                    'error'   => 'ไฟล์ ไม่พบ'
                ]);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);

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

                // เพิ่ม SO Detail ID
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(155, 12);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');

                // เพิ่ม SO ID (ใต้ SO Detail ID)
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(155, 15);
                $pdf->Cell(50, 10, "{$so_id}", 0, 0, 'R');

                // เพิ่มรูปปั้ม
                if (file_exists($stampImage)) {
                    $pdf->Image($stampImage, 170, 257, 22, 0, 'PNG');
                }
            }

            // บันทึกไฟล์ลงโฟลเดอร์ที่ 1
            $output1 = storage_path("app/public/doc_document/{$billid}.pdf");
            $pdf->Output('F', $output1);

            // บันทึกไฟล์ลงโฟลเดอร์ที่ 2
            $output2 = storage_path("app/public/bill_document/{$billid}.pdf");

            if (!file_exists(dirname($output2))) {
                mkdir(dirname($output2), 0777, true);
            }

            copy($output1, $output2);

            ob_end_clean();

            return response()->json([
                'success'       => true,
                'message'       => 'เพิ่มเลขที่บิล + รูปปั้ม ลงในเอกสารสำเร็จ (ทั้ง 2 โฟลเดอร์)',
                'so_detail_id'  => $so_detail_id,
                'so_id'         => $so_id
            ]);

        } catch (\Exception $e) {
            ob_end_clean();

            Log::error('Error in addIdToDocument', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'ระบบพบข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * เพิ่มเลขบิลลงใน PDF (งานบริการ)
     */
    public function addIdToDocument3(Request $request): JsonResponse
    {
        try {
            ob_start();

            // ✅ รับค่าจาก Request แทน Parameter
            $so_detail_id = $request->input('so_detail_id');
            $billid = $request->input('billid');
            $so_id = $request->input('so_id');

            // Validate
            if (!$so_detail_id || !$billid || !$so_id) {
                ob_end_clean();
                return response()->json([
                    'success' => false,
                    'error' => 'ข้อมูลไม่ครบถ้วน'
                ]);
            }

            $filePath = storage_path("app/public/doc_document/{$billid}.pdf");

            if (!file_exists($filePath)) {
                ob_end_clean();
                return response()->json([
                    'success' => false,
                    'error'   => 'ไฟล์ ไม่พบ'
                ]);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);

            if ($pageCount === 0) {
                ob_end_clean();
                return response()->json([
                    'success' => false,
                    'error'   => 'ไม่สามารถโหลดไฟล์ PDF'
                ]);
            }

            $stampImage1 = storage_path("app/public/template/ly.png");
            $stampImage2 = storage_path("app/public/template/3.png");

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->addPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $pdf->useTemplate($templateId);

                // เพิ่ม SO Detail ID
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(155, 12);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');

                // เพิ่ม SO ID
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(155, 15);
                $pdf->Cell(50, 10, "{$so_id}", 0, 0, 'R');

                // เพิ่มรูปปั้มที่ 1
                if (file_exists($stampImage1)) {
                    $pdf->Image($stampImage1, 170, 257, 22, 0, 'PNG');
                }

                // เพิ่มรูปปั้มที่ 2
                if (file_exists($stampImage2)) {
                    $pdf->Image($stampImage2, 10, 194, 40, 0, 'PNG');
                }
            }

            // บันทึกไฟล์ลงโฟลเดอร์ที่ 1
            $output1 = storage_path("app/public/doc_document/{$billid}.pdf");
            $pdf->Output('F', $output1);

            // บันทึกไฟล์ลงโฟลเดอร์ที่ 2
            $output2 = storage_path("app/public/bill_document/{$billid}.pdf");

            if (!file_exists(dirname($output2))) {
                mkdir(dirname($output2), 0777, true);
            }

            copy($output1, $output2);

            ob_end_clean();

            return response()->json([
                'success'       => true,
                'message'       => 'เพิ่มเลขที่บิล + รูปปั้ม 2 อัน ลงในเอกสารสำเร็จ (ทั้ง 2 โฟลเดอร์)',
                'so_detail_id'  => $so_detail_id,
                'so_id'         => $so_id
            ]);

        } catch (\Exception $e) {
            ob_end_clean();

            Log::error('Error in addIdToDocument3', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'ระบบพบข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
    public function addIdToissueDocument($so_detail_id, $bill_issue_no): JsonResponse
        {
        try {
            ob_start();
            $filePath = storage_path("app/public/billissue_document/{$bill_issue_no}.pdf");
            Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

            if (!file_exists($filePath)) {
                Log::error("ไม่พบไฟล์: {$filePath}");
                ob_end_clean();

                return response()->json([
                    'success' => false,
                    'error'   => 'ไฟล์ ไม่พบ'
                ]);
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

            $stampImage = "C:/xampp/htdocs/logistic/storage/app/public/template/ly.png";

            if (!file_exists($stampImage)) {
                Log::error("ไม่พบรูปที่ต้องการวางทับ: {$stampImage}");
            }

            // ===============================
            // วนลูปทุกหน้า เพิ่ม SO + รูป
            // ===============================
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->addPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $pdf->useTemplate($templateId);

                // ---------- เพิ่ม SO ID ----------
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(155, 2);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');

                // ---------- เพิ่มรูปลงบน PDF ----------
                $x = 45;
                $y = 237;
                $w = 22;
                $h = 0;

                if (file_exists($stampImage)) {
                    $pdf->Image($stampImage, $x, $y, $w, $h, 'PNG');
                }
            }

            // ===============================
            // บันทึกไฟล์ PDF
            // ===============================
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

            // ===============================
            // งานที่ 1 : template + doc_document
            // ===============================
            $this->mergePdfWithTemplate(
                storage_path('app/public/template/template.pdf'),
                storage_path("app/public/doc_document/{$billId}.pdf")
            );

            // ===============================
            // งานที่ 2 : templatereceipt + bill_document
            // ===============================
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
            throw new \Exception("ไม่พบไฟล์ template: {$templatePath}");
        }

        if (!file_exists($dataPdfPath)) {
            throw new \Exception("ไม่พบไฟล์ PDF: {$dataPdfPath}");
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

            // โหลด template
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

            // ซ้อน PDF ข้อมูล
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

        // --- ฝังฟอนต์ (TH Sarabun) ---
        $fontNormal     = base64_encode(file_get_contents(storage_path('fonts/THSarabun.ttf')));
        $fontBold       = base64_encode(file_get_contents(storage_path('fonts/THSarabun Bold.ttf')));
        $fontItalic     = base64_encode(file_get_contents(storage_path('fonts/THSarabun Italic.ttf')));
        $fontBoldItalic = base64_encode(file_get_contents(storage_path('fonts/THSarabun BoldItalic.ttf')));

        // --- ลิงก์ Google Maps จากพิกัด ---
        $coords  = trim((string)($item->customer_la_long ?? ''));
        $mapLink = 'https://www.google.com/maps?q=' . rawurlencode($coords);

        // --- สร้าง QR Code ---
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

        // --- เตรียมค่าที่อยู่ + แยกก่อน/หลังคำว่า "สถานที่ส่ง:" ---
        $rawAddress = (string)($item->customer_address ?? '');

        // แยกด้วย regex (อนุญาตเว้นวรรคและโคลอนทั้ง : และ ：) แยกครั้งเดียว
        $parts = preg_split('/สถานที่\s*ส่ง\s*[:：]?\s*/u', $rawAddress, 2);

        if (is_array($parts) && count($parts) === 2) {
            $addressBefore = trim($parts[0]);   // ก่อนคำว่า "สถานที่ส่ง:"
            $shipTo        = trim($parts[1]);   // หลังคำว่า "สถานที่ส่ง:"
        } else {
            // ถ้าไม่พบคีย์เวิร์ด ให้ "ที่อยู่" = ทั้งหมด และ "สถานที่ส่ง" = ทั้งหมด (พฤติกรรมเดิม)
            $addressBefore = $rawAddress;
            $shipTo        = $rawAddress;
        }

        $addressBeforeHtml = nl2br(htmlspecialchars($addressBefore, ENT_QUOTES, 'UTF-8'));
        $shipToHtml        = nl2br(htmlspecialchars($shipTo,        ENT_QUOTES, 'UTF-8'));

        // --- HTML สำหรับ A4 ---
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
                    border-bottom: 2px solid #000; /* เส้นขอบล่างสีดำ */
                    padding-bottom: 4mm;           /* ระยะห่างระหว่างข้อความกับเส้น */
                    margin-bottom: 6mm;            /* เว้นช่องจากส่วนถัดไป */
                }


                .footer-qr {
                    position: fixed;
                    bottom: 2mm; /* ให้อยู่เหนือขอบล่าง 2 มม. */
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
                <!-- มุมขวาบน -->
                <div class="top-right">' . htmlspecialchars((string)$item->so_detail_id, ENT_QUOTES, "UTF-8") . '</div>
                <div class="header">
                    <h1 style="font-size:40px;">ข้อมูลการจัดส่ง</h1>
                    <div class="subtitle">Delivery Note</div>
                    <div class="subtitle">' . htmlspecialchars((string)$item->time, ENT_QUOTES, "UTF-8") . '</div>
                </div>

                <!-- Content -->
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

                <!-- Footer -->
                <div class="footer-qr">
                    <p class="address">ที่อยู่จัดส่ง :</p>
                    <img src="' . $qrDataUri . '" alt="QR Code">
                    <p>สแกนเพื่อเปิดแผนที่</p>
                </div>
            </div>
        </body>
        </html>';

        // ✅ ตั้งค่า A4 แนวตั้ง
        $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->stream("notes-{$so_detail_id}.pdf");
    }


}
