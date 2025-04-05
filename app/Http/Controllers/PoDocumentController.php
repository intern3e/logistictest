<?php

namespace App\Http\Controllers;

use setasign\Fpdi\Fpdi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
                
                $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                
                // เพิ่ม SO ID ที่หัวกระดาษทุกหน้า
                $pdf->SetFont('Arial', '', 12);
                $pdf->SetTextColor(0, 0, 0); // สีดำ
                $pdf->SetXY(150, 10);
                $pdf->Cell(50, 10, "{$so_detail_id}", 0, 0, 'R');
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
}