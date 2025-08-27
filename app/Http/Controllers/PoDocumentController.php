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

    public function addIdToDocument($so_detail_id, $billid): JsonResponse
    {
        try {
            ob_start();

            $filePath = storage_path("app/public/doc_document/{$billid}.pdf");
            Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

            if (!file_exists($filePath)) {
                Log::error("ไม่พบไฟล์: {$filePath}");
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ไฟล์ ไม่พบ']);
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
                
                $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                
                // เพิ่ม SO ID ที่หัวกระดาษทุกหน้า
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0,0,0); // สีดำ
                $pdf->SetXY(155, 4);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');
            }

            $outputPath = storage_path("app/public/doc_document/{$billid}.pdf");
            $pdf->Output('F', $outputPath);

            ob_end_clean();
            Log::info("เขียน PDF สำเร็จ: " . $outputPath);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิลลงในเอกสาร bill สำเร็จ',
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
        public function addIdToissueDocument($so_detail_id, $bill_issue_no): JsonResponse
    {
        try {
            ob_start();
            $filePath = storage_path("app/public/billissue_document/{$bill_issue_no}.pdf");
            Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

            if (!file_exists($filePath)) {
                Log::error("ไม่พบไฟล์: {$filePath}");
                ob_end_clean();
                return response()->json(['success' => false, 'error' => 'ไฟล์ ไม่พบ']);
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
                
                $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                
                // เพิ่ม SO ID ที่หัวกระดาษทุกหน้า
                $pdf->SetFont('Helvetica', 'I', 8);
                $pdf->SetTextColor(0,0,0); // สีดำ
                $pdf->SetXY(155, 2);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');
            }
            $outputPath = storage_path("app/public/billissue_document/{$bill_issue_no}.pdf");
            $pdf->Output('F', $outputPath);
            ob_end_clean();
            Log::info("เขียน PDF สำเร็จ: " . $outputPath);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มเลขที่บิลลงในเอกสาร bill สำเร็จ',
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
public function mergeAndOverwrite(Request $request)
{
    $billId = $request->input('billid');

    try {
        if (!$billId) {
            return response()->json(['success' => false, 'message' => 'Bill ID ไม่ถูกต้อง'], 400);
        }

        $templatePath = storage_path('app/public/template/template.pdf');
        $dataPdfPath = storage_path("app/public/doc_document/{$billId}.pdf");
        $outputPath = $dataPdfPath; // บันทึกทับไฟล์เดิม

        if (!file_exists($templatePath)) {
            return response()->json(['success' => false, 'message' => '❌ ไม่พบไฟล์ template']);
        }

        if (!file_exists($dataPdfPath)) {
            return response()->json(['success' => false, 'message' => "❌ ไม่พบไฟล์ PDF ของ billid = $billId"]);
        }

        $pdf = new TcpdfFpdi();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        // ดึงจำนวนหน้า
        $templatePageCount = $pdf->setSourceFile($templatePath);
        $dataPageCount = $pdf->setSourceFile($dataPdfPath);
        $maxPages = max($templatePageCount, $dataPageCount);

        for ($i = 1; $i <= $maxPages; $i++) {
            // โหลดหน้า template
            $pdf->setSourceFile($templatePath);
            $tplTemplatePage = $pdf->importPage(min($i, $templatePageCount));
            $templateSize = $pdf->getTemplateSize($tplTemplatePage);

            // สร้างหน้าใหม่ตามขนาด template
            $pdf->AddPage($templateSize['orientation'], [$templateSize['width'], $templateSize['height']]);
            $pdf->useTemplate($tplTemplatePage, 0, 0, $templateSize['width'], $templateSize['height']);

            // ซ้อนหน้าข้อมูล data PDF ถ้ามี
            if ($i <= $dataPageCount) {
                $pdf->setSourceFile($dataPdfPath);
                $tplDataPage = $pdf->importPage($i);
                $dataSize = $pdf->getTemplateSize($tplDataPage);

                // Fit data PDF ให้ขนาดเดียวกับ template
                $pdf->useTemplate($tplDataPage, 0, 0, $templateSize['width'], $templateSize['height']);
            }
        }

        $pdf->Output($outputPath, 'F');

        return response()->json(['success' => true, 'message' => '✅ รวมและบันทึก PDF สำเร็จ']);
    } catch (\Throwable $e) {
        \Log::error("❌ mergeAndOverwrite error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => '❌ ระบบผิดพลาด: ' . $e->getMessage()
        ], 500);
    }

}
// public function printNotes($so_detail_id)
// {
//     $item = Bill::findOrFail($so_detail_id);

//     // --- ฝังฟอนต์ (เหมือนเดิม) ---
//     $fontNormal     = base64_encode(file_get_contents(storage_path('fonts/THSarabun.ttf')));
//     $fontBold       = base64_encode(file_get_contents(storage_path('fonts/THSarabun Bold.ttf')));
//     $fontItalic     = base64_encode(file_get_contents(storage_path('fonts/THSarabun Italic.ttf')));
//     $fontBoldItalic = base64_encode(file_get_contents(storage_path('fonts/THSarabun BoldItalic.ttf')));

//     // --- ลิงก์ Google Maps จากพิกัด ---
//     $coords  = trim((string)($item->customer_la_long ?? '')); // เช่น "13.713478269263469, 100.47978046740158"
//     $mapLink = 'https://www.google.com/maps?q=' . rawurlencode($coords);

//     // --- สร้าง QR ด้วย API v6: ส่งพารามิเตอร์ผ่าน constructor ---
//     // ต้องเปิด extension=gd ใน php.ini แล้ว restart Apache
//     $qrCode = new QrCode(
//         data: $mapLink,
//         encoding: new Encoding('UTF-8'),
//         errorCorrectionLevel: ErrorCorrectionLevel::High,
//         size: 300,
//         margin: 2,
//         roundBlockSizeMode: RoundBlockSizeMode::Margin
//     );

//     $writer    = new PngWriter();
//     $result    = $writer->write($qrCode);
//     $qrDataUri = $result->getDataUri(); // data:image/png;base64,...

//     // --- HTML สำหรับ Dompdf ---
//     $html = '
//     <html>
//     <head>
//         <meta charset="utf-8">
//         <style>
//             @page { margin: 0; }
//             @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontNormal . ') format("truetype"); font-weight:normal; font-style:normal; }
//             @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontBold . ') format("truetype"); font-weight:bold;   font-style:normal; }
//             @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontItalic . ') format("truetype"); font-weight:normal; font-style:italic; }
//             @font-face { font-family:"THSarabun"; src:url(data:font/truetype;charset=utf-8;base64,' . $fontBoldItalic . ') format("truetype"); font-weight:bold;   font-style:italic; }

//             body{ font-family:"THSarabun", sans-serif; font-size:6pt; line-height:1.2; margin:0; padding:0; position:relative; box-sizing:border-box; }
//             .top-center{ position:absolute; top:2mm; left:80%; transform:translateX(-50%); font-weight:bold; white-space:nowrap; }
//             .content{ width:43mm; margin:8mm auto 18mm auto; page-break-inside: avoid; }
//             table.kv{ width:100%; border-collapse:collapse; margin:0 0 2mm 0; }
//             table.kv th{ text-align:left; font-weight:bold; padding:0.4mm 1mm 0.4mm 0; white-space:nowrap; vertical-align:top; width:15mm; }
//             table.kv td{ padding:0.4mm 0; word-break:break-word; overflow-wrap:anywhere; }
//             .section{ margin-top:1.2mm; }
//             .section .title{ font-weight:bold; padding-top:0.6mm; margin-bottom:0.6mm; border-top:0.25pt solid #999; }
//             .section .body{ word-break:break-word; overflow-wrap:anywhere; }
//             .footer-qr{ position:absolute; left:50%; transform:translateX(-50%); bottom:3mm; width:43mm; text-align:center; }
//             .footer-qr img{ width:48px; height:48px; display:inline-block; }
//             .footer-qr p{ margin-top:1mm; font-size:7.5pt; }
//         </style>
//     </head>
//     <body>
//         <div class="top-center">' . htmlspecialchars((string)$item->so_detail_id) . '</div>
//         <div class="content">
//             <table class="kv">
//                 <tr><th>บิลที่:</th><td>' . htmlspecialchars((string)$item->billid) . '</td></tr>
//                 <tr><th>บริษัท:</th><td>' . htmlspecialchars((string)$item->customer_name) . '</td></tr>
//                 <tr><th>เบอร์:</th><td>' . htmlspecialchars((string)$item->customer_tel) . '</td></tr>
//                 <tr><th>ผู้ขาย:</th><td>' . htmlspecialchars((string)$item->sale_name) . '</td></tr>
//             </table>
//             <div class="section">
//                 <div class="title">รายละเอียด</div>
//                 <div class="body">' . nl2br(htmlspecialchars((string)$item->notes)) . '</div>
//             </div>
//             <div class="section">
//                 <div class="title">ที่อยู่</div>
//                 <div class="body">' . nl2br(htmlspecialchars((string)$item->customer_address)) . '</div>
//             </div>
//         </div>
//         <div class="footer-qr">
//             <img src="' . $qrDataUri . '" alt="QR Code">
//             <p>สแกนเพื่อเปิดแผนที่</p>
//         </div>
//     </body>
//     </html>';

//     $pdf = PDF::loadHTML($html)->setPaper([0, 0, 147.4, 209.8], 'portrait'); // A8
//     return $pdf->stream("notes-{$so_detail_id}.pdf");
// }
// public function printNotes($so_detail_id)
// {
//     $item = Bill::findOrFail($so_detail_id);

//     // --- ฝังฟอนต์ (TH Sarabun) ---
//     $fontNormal     = base64_encode(file_get_contents(storage_path('fonts/THSarabun.ttf')));
//     $fontBold       = base64_encode(file_get_contents(storage_path('fonts/THSarabun Bold.ttf')));
//     $fontItalic     = base64_encode(file_get_contents(storage_path('fonts/THSarabun Italic.ttf')));
//     $fontBoldItalic = base64_encode(file_get_contents(storage_path('fonts/THSarabun BoldItalic.ttf')));

//     // --- ลิงก์ Google Maps จากพิกัด ---
//     $coords  = trim((string)($item->customer_la_long ?? '')); 
//     $mapLink = 'https://www.google.com/maps?q=' . rawurlencode($coords);

//     // --- สร้าง QR Code ---
//     $qrCode = new QrCode(
//         data: $mapLink,
//         encoding: new Encoding('UTF-8'),
//         errorCorrectionLevel: ErrorCorrectionLevel::High,
//         size: 220,
//         margin: 2
//     );
//     $writer    = new PngWriter();
//     $result    = $writer->write($qrCode);
//     $qrDataUri = $result->getDataUri();

    // --- HTML ---
//     $html = '
//     <html>
//     <head>
//         <meta charset="utf-8">
//         <style>
//             @page { margin: 10mm; }

//             @font-face { font-family:"THSarabun"; src:url(data:font/truetype;base64,' . $fontNormal . ') format("truetype"); }
//             @font-face { font-family:"THSarabun"; src:url(data:font/truetype;base64,' . $fontBold . ') format("truetype"); font-weight:bold; }

//             body { font-family:"THSarabun", sans-serif; font-size:18pt; margin:0; padding:0; color:#000; }

//             h1 { font-size:22pt; margin:0; }
//             .subtitle { font-size:12pt; color:#000000; }
//             .header { text-align:left; margin-bottom:5mm; }
//             .bill-box {
//                 border: 2px solid #000;
//                 text-align:center;
//                 padding:10px;
//                 font-size:24pt;
//                 margin:10px 0;
//                 //  background:#f2f2f2;  /* สีเทาอ่อน */
//             }
//             .bill-number { font-size:28pt; font-weight:bold; letter-spacing:2px; }
            
//             table.info { width:100%; border-collapse:collapse; margin-top:5mm; }
//             table.info td { padding:3px; font-size:20pt; vertical-align:top; }
//             table.info td.label { width:25mm; font-weight:bold; }

//             .section {
//                 border: 2px solid #000;
//                 padding:5mm;
//                 margin-top:3mm;   /* ลดลง หรือใส่ 0 ก็ได้ */
//                 // background:#f2f2f2;  /* สีเทาอ่อน */
//             }

//             .section-title {
//                 font-weight:bold;
//                 font-size:20pt;
//                 margin:-20px 0 4px 0;   
//             }
//             .section p { margin:2px 0; font-size:16pt; }

//             .footer-qr {
//                 margin-top:15mm;
//                 text-align:center;
//                 border-top:2px solid #000;
//                 padding-top:15mm;
//             }
//             .footer-qr img { width:200px; height:200px; }
//             .footer-qr p { margin:5px 0; font-size:14pt; }
            
//             /* มุมขวาบน */ .top-right { font-size: 16pt; /* ✅ ปรับตาม */ position: absolute; top: 0; right: 2mm; font-weight: normal; font-style: italic; color: #918f8f; }
//         .line {
//             border: 1px solid black;
//             padding: 15px;
//             position: absolute; 
//             top: -1.5%;    /* ชิดบน */
//             left: -2.5%;   /* ชิดซ้าย */
//             right: -2.5%;  /* ชิดขวา */
//             bottom: -1.5%; /* ชิดล่าง */
//             box-sizing: border-box; /* กัน padding ดันกรอบเกิน */
//         }
                
//         </style>
//     </head>
//     <body>
//        <div class="line">
//     <div class="top-right">' . htmlspecialchars((string)$item->so_detail_id) . '</div>
//     <div class="header">
//         <h1 style="font-size:40px;">ข้อมูลการจัดส่ง</h1>
//         <div class="subtitle">Delivery Note</div>
//         <div class="subtitle">' . htmlspecialchars((string)$item->time) . '</div>
//     </div>

//     <div class="bill-box">
//         <div>เลขที่บิล</div>
//         <div class="bill-number">' . htmlspecialchars((string)$item->billid) . '</div>
//     </div>

//     <table class="info">
//         <tr><td class="label">บริษัท :</td><td>' . htmlspecialchars((string)$item->customer_name) . '</td></tr>
//         <tr><td class="label">ที่อยู่ :</td><td>' . nl2br(htmlspecialchars((string)$item->customer_address)) . '</td></tr>
//         <tr><td class="label">ชื่อผู้ติดต่อ :</td><td>' . htmlspecialchars((string)$item->contactso) . '</td></tr>
//         <tr><td class="label">โทรศัพท์ :</td><td>' . htmlspecialchars((string)$item->customer_tel) . '</td></tr>
//     </table>

//     <div class="section">
//         <div class="section-title">รายละเอียด :</div>
//         <div class="">' . nl2br(htmlspecialchars((string)$item->notes)) . '</div>
//     </div>

//     <div class="footer-qr">
//         <img src="' . $qrDataUri . '" alt="QR Code">
//         <h1 style="font-size:25px;">สแกนเพื่อเปิดแผนที่</h1>
//     </div>
// </div>

//     </body>
//     </html>';

//     $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');
//     return $pdf->stream("notes-{$so_detail_id}.pdf");
// }

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
                font-size:25pt;     /* ✅ เปลี่ยนเป็น 25 */
                line-height:1.4; 
                margin:0; 
                padding:0; 
                color:#000; 
            }

            /* มุมขวาบน */


            /* Content */
            .content {
                width: 100%;
                margin-top: -5mm;
            }
            table.kv {
                border-collapse: collapse;
                width: 100%;
            }
            table.kv td {
                padding: 2px 4px;
                vertical-align: top;
                font-size: 20pt;   /* ✅ ปรับตาม */
                word-wrap: break-word;
                word-break: break-all;
            }
            table.kv td.label {
                width: 25mm;
                font-weight: bold;
                white-space: nowrap;
                padding-right: 5mm;
            }

            /* Section */
            .section { margin-top: 10mm; }
            .section .title {
                font-weight: bold;
                border-bottom: 2px solid #000;
                margin-bottom: 4mm;
                padding-bottom: 2mm;
                font-size: 25pt;   /* ✅ ปรับตาม */
            }
.section .body {
    font-size: 20pt;
    text-align: left;
    white-space: normal;      /* ✅ ปล่อยให้ห่อบรรทัดอัตโนมัติ */
    word-wrap: break-word;    /* ✅ บังคับตัดคำ */
    word-break: break-word;   /* ✅ กันข้อความยาวเกิน */
}

            .footer-qr {
                margin-top: 10mm;      
                text-align: center;
                border-top: 2px solid #000;
                padding-top: 0mm;   
            }

            .footer-qr img {
                display: block;
                margin: 0 auto;
                width: 180px;   
                height: 180px;  
            }

            .footer-qr p {
                margin-top: 4mm;
                font-size: 25pt;   /* ✅ ปรับตาม */
                font-weight: bold;
            }
            .address {
                 width: 25mm;
                font-weight: bold;
                white-space: nowrap;
                padding-right: 5mm;
            }
                h1 { font-size:22pt; margin:0; }
             h1 { font-size:22pt; margin:0; }
            .subtitle { font-size:12pt; color:#000000; }
            .header { text-align:left; margin-bottom:5mm; }
                        /* มุมขวาบน */ .top-right { font-size: 16pt; /* ✅ ปรับตาม */ position: absolute; top: 2%; right: 4mm; font-weight: normal; font-style: italic; color: #918f8f; }
        .line {
            border: 1px solid black;
            padding: 15px;
            position: absolute; 
            top: 2%;    /* ชิดบน */
            left: -5%;   /* ชิดซ้าย */
            right: -5%;  /* ชิดขวา */
            bottom: 2%; /* ชิดล่าง */
            box-sizing: border-box; /* กัน padding ดันกรอบเกิน */
        }
                
        </style>
    </head>
    <body>
          <div class="line">
        <!-- มุมขวาบน -->
        <div class="top-right">' . htmlspecialchars((string)$item->so_detail_id) . '</div>
         <div class="header">
        <h1 style="font-size:40px;">ข้อมูลการจัดส่ง</h1>
        <div class="subtitle">Delivery Note</div>
         <div class="subtitle">' . htmlspecialchars((string)$item->time) . '</div>
            </div>
        <!-- Content -->
        <div class="content">
            <table class="kv">
                <tr><td class="label">เลขที่บิล :</td><td>' . htmlspecialchars((string)$item->billid) . '</td></tr>
                <tr><td class="label">บริษัท :</td><td>' . htmlspecialchars((string)$item->customer_name) . '</td></tr>
                <tr><td class="label">ที่อยู่ :</td><td>' . nl2br(htmlspecialchars((string)$item->customer_address)) . '</td></tr>
                <tr><td class="label">ชื่อผู้ติดต่อ :</td><td>' . htmlspecialchars((string)$item->contactso) . '</td></tr>
                <tr><td class="label">เบอร์ติดต่อ :</td><td>' . htmlspecialchars((string)$item->customer_tel) . '</td></tr>
            </table>

            <div class="section">
                <div class="title">รายละเอียดเพิ่มเติม :</div>
                <div class="body">' . nl2br(htmlspecialchars((string)$item->notes)) . '</div>
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
