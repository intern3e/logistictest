<?php

namespace App\Http\Controllers;
use setasign\Fpdi\Fpdi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use setasign\Fpdi\TcpdfFpdi;

class PoDocumentController extends Controller
{
    public function addSoDetailIdToPoDocument($so_detail_id, $POdocument): JsonResponse
    {
        try {
            ob_start();

            $filePath = storage_path("public/storage/po_documents/{$POdocument}");
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

            $outputPath = storage_path("public/storage/po_documents/{$POdocument}");
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

            $outputPath = storage_path("public/storage/doc_document/{$billid}.pdf");
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
            $outputPath = storage_path("public/storage/billissue_document/{$bill_issue_no}.pdf");
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

        $templatePath = storage_path('public/storage/template/template.pdf');
        $dataPdfPath = storage_path("public/storage/doc_document/{$billId}.pdf");
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
}
