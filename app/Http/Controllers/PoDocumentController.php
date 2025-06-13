<?php

namespace App\Http\Controllers;

use setasign\Fpdi\Fpdi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PoDocumentController extends Controller
{
//     public function addSoDetailIdToPoDocument($so_detail_id, $POdocument): JsonResponse
//     {
//         try {
//             ob_start();

//             $filePath = storage_path("app/public/po_documents/{$POdocument}");
//             Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

//             if (!file_exists($filePath)) {
//                 Log::error("ไม่พบไฟล์: {$filePath}");
//                 ob_end_clean();
//                 return response()->json(['success' => false, 'error' => 'ไฟล์ PO ไม่พบ']);
//             }

//             $pdf = new Fpdi();
//             $pageCount = $pdf->setSourceFile($filePath);
//             Log::info("จำนวนหน้าที่เจอ: {$pageCount}");

//             if ($pageCount === 0) {
//                 ob_end_clean();
//                 return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF']);
//             }
    
//             // วนลูปทุกหน้าเพื่อเพิ่ม SO ID
//             for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
//     $templateId = $pdf->importPage($pageNo);
//     $size = $pdf->getTemplateSize($templateId);

//     $pdf->SetMargins(0, 0, 0);
//     $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
//     $pdf->useTemplate($templateId);

//     $pdf->SetFont('Helvetica', 'I', 8);
//     $pdf->SetTextColor(130,130, 130);

//     // คำนวณตำแหน่งใหม่ (หน่วย pt)
//     $x = 175;               // ใกล้ขอบขวา (A4 กว้าง 210 mm)
//     $y = 4;                 // อยู่ห่างจากขอบบน 2 mm
//     $pdf->Text($x, $y, "{$so_detail_id}");
// }

//             $outputPath = storage_path("app/public/po_documents/{$POdocument}");
//             $pdf->Output('F', $outputPath);

//             ob_end_clean();
//             Log::info("เขียน PDF สำเร็จ: " . $outputPath);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'เพิ่มเลขที่บิลลงในเอกสาร PO สำเร็จ',
//                 'so_detail_id' => $so_detail_id
//             ]);
//         } catch (\Exception $e) {
//             ob_end_clean();
//             Log::error("เกิดข้อผิดพลาด: " . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'error' => 'ระบบพบข้อผิดพลาด: ' . $e->getMessage()
//             ]);
//         }
//     }
public function addSoDetailIdToPoDocument($so_detail_id, $POdocument): JsonResponse
{
    try {
        ob_start();

        $filePath = storage_path("app/public/po_documents/{$POdocument}");
        Log::info("กำลังเปิดไฟล์ PDF: " . $filePath);

        // ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
        if (!file_exists($filePath)) {
            Log::error("ไม่พบไฟล์: {$filePath}");
            ob_end_clean();
            return response()->json(['success' => false, 'error' => 'ไฟล์ PO ไม่พบ']);
        }

        // ตรวจสอบ MIME (optional สำหรับ debug)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        Log::info("MIME ของไฟล์: " . $mime);

        $pdf = new Fpdi();

        try {
            $pageCount = $pdf->setSourceFile($filePath);
        } catch (\Exception $e) {
            ob_end_clean();
            return response()->json(['success' => false, 'error' => 'ไม่สามารถโหลดไฟล์ PDF: ' . $e->getMessage()]);
        }

        Log::info("จำนวนหน้าที่เจอ: {$pageCount}");

        if ($pageCount === 0) {
            ob_end_clean();
            return response()->json(['success' => false, 'error' => 'ไม่พบหน้าใดๆ ในไฟล์ PDF']);
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->SetMargins(0, 0, 0);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // เพิ่ม SO ID ที่มุมขวาบน
            $pdf->SetFont('Helvetica', 'I', 8);
            $pdf->SetTextColor(130, 130, 130);

            // พิกัด (ประมาณมุมขวาบนของ A4)
            $x = $size['width'] - 35; // ปรับให้ใกล้ขอบขวา
            $y = 8; // ห่างจากขอบบน

            $pdf->Text($x, $y, "{$so_detail_id}");
        }

        $outputPath = storage_path("app/public/po_documents/{$POdocument}");
        $pdf->Output('F', $outputPath); // บันทึกทับ

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
}   
