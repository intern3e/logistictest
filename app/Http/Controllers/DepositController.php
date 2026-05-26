<?php

namespace App\Http\Controllers;

use App\Models\deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use setasign\Fpdi\Tcpdf\Fpdi;
use setasign\Fpdi\Fpdi as FpdiBasic;


class DepositController extends Controller
{
    private const STAMP_IMAGE_REL = 'deposit_templates/ly.png';
    private const STAMP_X         = 4;
    private const STAMP_Y         = 215;
    private const STAMP_WIDTH     = 65;
    private const STAMP_HEIGHT    = 55;
    private const STAMP_PAGE      = 0;
    private $adminUsers = ['kanitin2', 'dev','Aom'];
    private const PDF_TEMPLATE_REL = 'deposit_templates/templates.pdf';
    private const PDF_OUTPUT_DIR = 'deposit_templates';
    private const SLIP_OUTPUT_DIR = 'deposit_templates/deposit_slip';

    private function nowBkk(): Carbon
    {
        return Carbon::now('Asia/Bangkok');
    }

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
            ->paginate(100)
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

        if (!in_array($changedBy, array_map('strtolower', $this->adminUsers))) {
            return response()->json([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์เปลี่ยนสถานะ',
            ], 403);
        }

        $newStatus = $request->input('new_status');
        $allowed   = ['รอยืนยัน', 'ยืนยัน', 'มี WHT'];

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
                $updateData['time_check'] = $this->nowBkk();
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
                'print_time' => $this->nowBkk(),
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
                ->update([
                    'print_time'  => $this->nowBkk(),
                    'status_bill' => 'ok',
                    'status' => 'ยืนยัน',
                ]);

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

    public function uploadBillPdf(Request $request)
    {
        $request->validate([
            'deposit_id'      => 'required',
            'deposit_bill_id' => 'required|string|max:50',
            'pdf_file'        => 'required|file|mimes:pdf|max:10240',
            'printed_by'      => 'nullable|string|max:150',
        ]);

        $depositId     = $request->input('deposit_id');
        $depositBillId = trim($request->input('deposit_bill_id'));
        $printedBy     = trim($request->input('printed_by', 'unknown'));
        $file          = $request->file('pdf_file');

        try {
            $safeName = $this->sanitizeDepositFilename($depositBillId);
            $fileName = $safeName . '.pdf';

            $relPath = self::PDF_OUTPUT_DIR . '/' . $fileName;
            $absDir  = storage_path('app/public/' . self::PDF_OUTPUT_DIR);
            $absPath = storage_path('app/public/' . $relPath);

            if (!is_dir($absDir)) {
                mkdir($absDir, 0775, true);
            }

            if (file_exists($absPath)) {
                @unlink($absPath);
            }
            $file->move($absDir, $fileName);

            $this->stampPdfWithImage($absPath);

            $affected = deposit::where('deposit_bill_id', $depositBillId)
                ->update([
                    'print_time'   => $this->nowBkk(),
                    'status_bill'  => 'ok',
                    'deposit_bill' => $fileName,
                ]);

            Log::info('Deposit PDF uploaded & marked printed', [
                'deposit_id'      => $depositId,
                'deposit_bill_id' => $depositBillId,
                'file_name'       => $fileName,
                'rows_affected'   => $affected,
                'printed_by'      => $printedBy,
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'อัปโหลดและบันทึกการพิมพ์สำเร็จ',
                'file_name' => $fileName,
                'file_url'  => asset('storage/' . $relPath),
            ]);

        } catch (\Throwable $e) {
            Log::error('Upload deposit PDF failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ botdeposit — WHT ที่ bot ทำเสร็จ (print_time > wht_time) หายจากตาราง
    public function botdeposit(Request $request)
    {
        $soKeyword = trim($request->get('so_keyword', ''));

        $query = deposit::query()
            ->where(function ($q) {
                // กลุ่ม 1: ยืนยัน + ยังไม่เคยผ่าน bot (status_bill ยังว่าง)
                $q->where(function ($q2) {
                    $q2->where('status', 'ยืนยัน')
                       ->whereNull('status_bill');
                })
                // กลุ่ม 2: มี WHT → bot ต้องกลับมาแก้ไข
                // ✅ ยกเว้นถ้า print_time > wht_time (bot ทำ WHT เสร็จแล้ว → หายจากตาราง)
                ->orWhere(function ($q3) {
                    $q3->where('status', 'มี WHT')
                       ->where(function ($q4) {
                           $q4->whereNull('print_time')
                              ->orWhereColumn('print_time', '<=', 'wht_time');
                       });
                });
            });

        if ($soKeyword !== '') {
            $query->where('so_id', 'like', "%{$soKeyword}%");
        }

        $deposits = $query
            ->orderBy('deposit_bill_id', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(100)
            ->appends($request->query());

        return view('deposit.botdeposit', compact('deposits'));
    }

    private function generateDepositBillId()
    {
        $now      = $this->nowBkk();
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
            'note'               => 'nullable|string|max:1000',
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
                    'note'             => $validated['note']             ?? null,
                    'sale_name'        => $validated['sale_name']        ?? null,
                    'po_document'      => $validated['po_document']      ?? null,
                    'emp_name'         => $validated['emp_name']         ?? 'Guest',
                    'dep_type'         => $dep['type'],
                    'dep_per'          => $dep['percent'],
                    'dep_price'        => $dep['amount'],
                    'grand_total'      => $netGrandTotal,
                    'time'             => $this->nowBkk(),
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

                if ($pdfPath) {
                    $fileNameOnly = basename($pdfPath);
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
        $pdf->MultiCell(80, 5, $this->safeText($data['customer_address'] ?? ''), 0, 'L');

        $pdf->SetXY(15, 77.7);
        $pdf->Cell(60, 6, $this->safeText($data['customer_tel'] ?? ''), 0, 0, 'L');

        $pdf->SetXY(136, 51.7);
        $pdf->Cell(60, 6, $this->safeText($data['tax_id'] ?? ''), 0, 0, 'L');

        $pdf->SetXY(113, 58.1);
        $pdf->Cell(60, 6, $this->safeText($data['deposit_bill_id'] ?? ''), 0, 0, 'L');

        $nowBkk = $this->nowBkk();
        $pdf->SetXY(113, 64.5);
        $pdf->Cell(60, 6, $this->safeText($nowBkk->format('d-m-') . ($nowBkk->year + 543)), 0, 0, 'L');

        $pdf->SetXY(22, 173.5);
        $pdf->MultiCell(110, 5, $this->safeText($data['note'] ?? ''), 0, 'L');

        $startY = 93;
        $rowH   = 6;
        $deposits   = $data['deposits'] ?? [];
        $poDocument = $data['po_document'] ?? $data['poDocument'] ?? $data['po'] ?? '';
        $grandTotalData = (float)($data['grand_total'] ?? 0);

        $y = $startY;
        foreach ($deposits as $dep) {
            $percent = (float)($dep['percent'] ?? 0);
            $amount  = (float)($dep['amount']  ?? 0);

            if ($percent > 0 && $amount > 0) {
                $fullPrice = $amount * 100 / $percent;
            } else {
                $fullPrice = $grandTotalData;
            }

            $typeName = ($dep['type'] ?? '') === 'service' ? 'บริการ' : 'สินค้า';

            $line = sprintf(
                'รับเงินค่ามัดจำ%s %s%% %s – (%s)',
                $typeName,
                rtrim(rtrim(number_format($percent, 2), '0'), '.'),
                $poDocument,
                number_format($fullPrice, 2)
            );

            $pdf->SetXY(30, $y);
            $pdf->Cell(170, $rowH, $this->safeText($line), 0, 0, 'L');
            $y += $rowH;
        }

        $y = $startY;
        foreach ($deposits as $dep) {
            $amount = (float)($dep['amount'] ?? 0);
            $pdf->SetXY(162, $y);
            $pdf->Cell(40, $rowH, number_format($amount, 2), 0, 0, 'R');
            $y += $rowH;
        }

        $totalAmount = array_sum(array_column($deposits, 'amount'));
        $vat         = $totalAmount * 0.07;
        $grandTotal  = $totalAmount + $vat;

        $pdf->SetXY(162, 174);
        $pdf->Cell(40, $rowH, number_format($totalAmount, 2), 0, 0, 'R');
        $pdf->SetXY(162, 183);
        $pdf->Cell(40, $rowH, number_format($vat, 2), 0, 0, 'R');
        $pdf->SetXY(162, 192);
        $pdf->Cell(40, $rowH, number_format($grandTotal, 2), 0, 0, 'R');
        $pdf->SetXY(55, 192);
        $pdf->Cell(100, $rowH, $this->bahtText($grandTotal), 0, 0, 'L');
    }

    protected function sanitizeDepositFilename(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[\/\\\\:*?"<>|]/u', '_', $name);
        $name = preg_replace('/\s+/', '_', $name);
        return $name !== '' ? $name : 'deposit_' . date('YmdHis');
    }

    protected function stampPdfWithImage(string $pdfAbsPath): void
    {
        $stampAbs = storage_path('app/public/' . self::STAMP_IMAGE_REL);

        if (!file_exists($stampAbs)) { Log::warning('Stamp image not found: ' . self::STAMP_IMAGE_REL); return; }
        if (!file_exists($pdfAbsPath)) { Log::warning('PDF to stamp not found: ' . $pdfAbsPath); return; }

        try {
            ob_start();
            $pdf = new FpdiBasic();
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pageCount = $pdf->setSourceFile($pdfAbsPath);

            for ($p = 1; $p <= $pageCount; $p++) {
                $tplId = $pdf->importPage($p);
                $size  = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'] ?? 'P', [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

                $shouldStamp = (self::STAMP_PAGE === 0) || ($p === self::STAMP_PAGE);
                if ($shouldStamp) {
                    $pdf->Image($stampAbs, self::STAMP_X, self::STAMP_Y, self::STAMP_WIDTH, self::STAMP_HEIGHT, 'PNG');
                }
            }

            $pdf->Output('F', $pdfAbsPath);
            ob_end_clean();
            Log::info('Stamp PDF success: ' . basename($pdfAbsPath));

        } catch (\Throwable $e) {
            if (ob_get_level() > 0) ob_end_clean();
            Log::error('Stamp PDF failed: ' . $e->getMessage(), ['pdf' => $pdfAbsPath, 'trace' => $e->getTraceAsString()]);
        }
    }

    protected function safeText($text): string
    {
        if ($text === null) return '';
        return (string) $text;
    }

    protected function bahtText($amount): string
    {
        $number = number_format((float)$amount, 2, '.', '');
        [$baht, $satang] = explode('.', $number);

        $txtnum1 = ['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
        $txtnum2 = ['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'];

        $convert = function ($num) use ($txtnum1, $txtnum2) {
            $num = (string)(int)$num; $len = strlen($num); $result = '';
            for ($i = 0; $i < $len; $i++) {
                $digit = (int)$num[$i]; $pos = $len - $i - 1;
                if ($digit === 0) continue;
                if ($pos === 0 && $digit === 1 && $len > 1) { $result .= 'เอ็ด'; }
                elseif ($pos === 1) { if ($digit === 1) $result .= 'สิบ'; elseif ($digit === 2) $result .= 'ยี่สิบ'; else $result .= $txtnum1[$digit] . 'สิบ'; }
                else { $result .= $txtnum1[$digit] . $txtnum2[$pos]; }
            }
            return $result;
        };

        $bahtText = '';
        if ((int)$baht === 0) { $bahtText = 'ศูนย์'; }
        else { $remain = $baht; while (strlen($remain) > 7) { $head = substr($remain, 0, strlen($remain) - 6); $remain = substr($remain, -6); $bahtText .= $convert($head) . 'ล้าน'; } $bahtText .= $convert($remain); }
        $bahtText .= 'บาท';

        if ((int)$satang === 0) { $bahtText .= 'ถ้วน'; }
        else { $bahtText .= $convert($satang) . 'สตางค์'; }

        return '(' . $bahtText . ')';
    }

    // ============================================================================
    //  =====================  FEE / WHT / DELETE / SLIP  ==========================
    // ============================================================================

    public function updateFee(Request $request)
    {
        $savedBy = strtolower(trim($request->input('saved_by', '')));
        if (!in_array($savedBy, array_map('strtolower', $this->adminUsers))) {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'deposit_id' => 'required|integer',
            'fee_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $fee = (float) ($request->input('fee_amount', 0) ?? 0);
            $affected = deposit::where('id', $request->input('deposit_id'))
                ->update(['fee_amount' => $fee]);

            if ($affected === 0) {
                return response()->json(['success' => false, 'message' => 'ไม่พบรายการ'], 404);
            }

            Log::info('Deposit fee updated', [
                'deposit_id' => $request->input('deposit_id'),
                'fee_amount' => $fee,
                'saved_by'   => $savedBy,
            ]);

            return response()->json(['success' => true, 'message' => 'บันทึกค่าธรรมเนียมสำเร็จ', 'fee_amount' => $fee]);

        } catch (\Throwable $e) {
            Log::error('Update fee failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateWht(Request $request)
    {
        $savedBy = strtolower(trim($request->input('saved_by', '')));
        if (!in_array($savedBy, array_map('strtolower', $this->adminUsers))) {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'deposit_id'      => 'required|integer',
            'deposit_bill_id' => 'nullable|string|max:50',
            'wht_doc_no'      => 'nullable|string|max:60',
            'date_wht'        => 'nullable|date',
        ]);

        try {
            $wht     = trim((string) $request->input('wht_doc_no', ''));
            $dateWht = $request->input('date_wht');
            $now     = $this->nowBkk();

            $updateData = [
                'wht_doc_no' => $wht !== '' ? $wht : null,
                'wht_time'   => $wht !== '' ? $now : null,
                'date_wht'   => ($wht !== '' && $dateWht) ? Carbon::parse($dateWht)->format('Y-m-d') : null,
            ];

            // ✅ เปลี่ยนสถานะเป็น "มี WHT" เพื่อให้ bot เห็นงานใหม่
            if ($wht !== '') {
                $updateData['status'] = 'มี WHT';
            }

            // ✅ update ตาม deposit_bill_id (ไม่ใช่ so_id)
            $depositBillId = $request->input('deposit_bill_id');
            if (!empty($depositBillId)) {
                $affected = deposit::where('deposit_bill_id', $depositBillId)
                    ->update($updateData);
            } else {
                $affected = deposit::where('id', $request->input('deposit_id'))
                    ->update($updateData);
            }

            if ($affected === 0) {
                return response()->json(['success' => false, 'message' => 'ไม่พบรายการ'], 404);
            }

            Log::info('Deposit WHT updated + status changed', [
                'deposit_id'      => $request->input('deposit_id'),
                'deposit_bill_id' => $depositBillId,
                'wht_doc_no'      => $wht,
                'new_status'      => $wht !== '' ? 'มี WHT' : '(ไม่เปลี่ยน)',
                'saved_by'        => $savedBy,
            ]);

            return response()->json([
                'success'  => true,
                'message'  => $wht !== ''
                    ? 'บันทึก WHT สำเร็จ — สถานะเปลี่ยนเป็น "มี WHT"'
                    : 'ลบ WHT สำเร็จ',
                'status'   => $wht !== '' ? 'มี WHT' : null,
                'wht_time' => $wht !== '' ? $now->format('H:i d/m/Y') : null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Update WHT failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteDeposit(Request $request)
    {
        $deletedBy = strtolower(trim($request->input('deleted_by', '')));
        if (!in_array($deletedBy, array_map('strtolower', $this->adminUsers))) {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate(['deposit_id' => 'required|integer']);
        $id = $request->input('deposit_id');

        try {
            $rec = deposit::find($id);
            if (!$rec) return response()->json(['success' => false, 'message' => 'ไม่พบรายการ'], 404);

            Log::warning('Deposit DELETED', [
                'deposit_id' => $id, 'so_id' => $rec->so_id,
                'deposit_bill_id' => $rec->deposit_bill_id,
                'customer_name' => $rec->customer_name,
                'dep_price' => $rec->dep_price, 'deleted_by' => $deletedBy,
            ]);

            $rec->delete();

            return response()->json(['success' => true, 'message' => 'ลบสำเร็จ']);

        } catch (\Throwable $e) {
            Log::error('Delete failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadSlip(Request $request)
    {
        $request->validate([
            'deposit_bill_id' => 'required|string|max:50',
            'slip_file'       => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'slip_date'       => 'nullable|date',
            'uploaded_by'     => 'nullable|string|max:150',
        ]);

        $depositBillId = trim($request->input('deposit_bill_id'));
        $uploadedBy    = trim($request->input('uploaded_by', 'unknown'));
        $file          = $request->file('slip_file');

        $slipDateInput = $request->input('slip_date');
        $slipTime = $slipDateInput
            ? Carbon::parse($slipDateInput)->setTimezone('Asia/Bangkok')->setTime(12, 0, 0)
            : $this->nowBkk();

        try {
            $safeName = $this->sanitizeDepositFilename($depositBillId);
            $ext      = strtolower($file->getClientOriginalExtension());
            $fileName = $safeName . '.' . $ext;
            $absDir   = storage_path('app/public/' . self::SLIP_OUTPUT_DIR);
            $relPath  = self::SLIP_OUTPUT_DIR . '/' . $fileName;

            if (!is_dir($absDir)) mkdir($absDir, 0775, true);
            foreach (['pdf','jpg','jpeg','png','webp'] as $x) {
                $old = $absDir . DIRECTORY_SEPARATOR . $safeName . '.' . $x;
                if (file_exists($old)) @unlink($old);
            }
            $file->move($absDir, $fileName);

            deposit::where('deposit_bill_id', $depositBillId)
                ->update(['deposit_slip' => $fileName, 'slip_time' => $slipTime]);

            Log::info('Slip uploaded', compact('depositBillId', 'fileName', 'slipTime', 'uploadedBy'));

            return response()->json([
                'success' => true, 'message' => 'อัปโหลดสำเร็จ',
                'file_url' => asset('storage/' . $relPath),
            ]);
        } catch (\Throwable $e) {
            Log::error('Slip upload failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}