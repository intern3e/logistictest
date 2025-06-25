<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;

class PdfController extends Controller
{
    public function mergePdf(Request $request)
    {
        Log::info('🟡 เริ่ม mergePdf()');

        $billid = $request->input('billid');
        Log::info("📦 billid ที่ได้รับ: {$billid}");

        $existingPath = storage_path("app/public/doc_document/{$billid}.pdf");
        $templatePath = storage_path("app/public/template/template_copy.pdf");
        $outputPath = storage_path("app/public/doc_document/{$billid}.pdf");

        Log::info("📄 Path ไฟล์บิล: {$existingPath}");
        Log::info("📄 Path ไฟล์เทมเพลต: {$templatePath}");

        if (!file_exists($existingPath)) {
            Log::error("❌ ไม่พบไฟล์บิล: {$existingPath}");
            return response()->json(['success' => false, 'error' => "ไม่พบไฟล์บิล: {$billid}.pdf"]);
        }

        if (!file_exists($templatePath)) {
            Log::error("❌ ไม่พบไฟล์เทมเพลต: {$templatePath}");
            return response()->json(['success' => false, 'error' => "ไม่พบไฟล์ Template: template_copy.pdf"]);
        }

        try {
            $pdf = new Fpdi();

            // Merge บิล
            $pageCount1 = $pdf->setSourceFile($existingPath);
            Log::info("📄 จำนวนหน้าของไฟล์บิล: {$pageCount1}");

            for ($pageNo = 1; $pageNo <= $pageCount1; $pageNo++) {
                $tpl = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }

            // Merge Template
            $pageCount2 = $pdf->setSourceFile($templatePath);
            Log::info("📄 จำนวนหน้าของ Template: {$pageCount2}");

            for ($pageNo = 1; $pageNo <= $pageCount2; $pageNo++) {
                $tpl = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }

            $pdf->Output($outputPath, 'F');
            Log::info("✅ บันทึกไฟล์ PDF สำเร็จที่: {$outputPath}");

            return response()->json([
                'success' => true,
                'url' => asset("storage/doc_document/{$billid}.pdf")
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Exception ใน mergePdf(): ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
