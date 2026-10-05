<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * สรุป PO ที่เชื่อมกับ SO (ตัด PO ยกเลิก) + ไส้ในรายการสินค้า + VAT ต่อ PO
 * export เป็น Excel (.xls) และ PDF — ข้อมูลอ่านจาก 3e(polist) + MSSQL(POHD/PODT) อย่างเดียว
 */
class SoPoExportController extends Controller
{
    const LEGACY = 'mysql_3e';
    const ERP    = 'mssql_account03';

    private function normSo(string $so): string
    {
        return preg_replace('/^SO/i', '', trim($so));
    }

    /** ดึงข้อมูล PO + ไส้ใน ของ SO (ตัด PO ยกเลิก) */
    public function buildData(string $soNum): array
    {
        $soNum = $this->normSo($soNum);

        // PO ที่เชื่อมกับ SO นี้ (ตัดยกเลิก)
        $plist = DB::connection(self::LEGACY)->table('polist')
            ->where('SONum', $soNum)
            ->where('POstatus', '<>', 'CANCELLED')
            ->get(['PONum', 'VendorID', 'VendorName', 'POstatus', 'DeliveryDate']);

        $pos = [];
        $poNums = [];
        foreach ($plist as $p) {
            $po = trim((string) $p->PONum);
            if ($po === '' || isset($pos[$po])) continue;
            $poNums[] = $po;
            $pos[$po] = [
                'po'          => $po,
                'vendor_id'   => trim((string) $p->VendorID),
                'vendor_name' => trim((string) $p->VendorName),
                'status'      => trim((string) $p->POstatus),
                'date'        => null,
                'before'      => 0.0,
                'vat'         => 0.0,
                'after'       => 0.0,
                'lines'       => [],
            ];
        }

        if (!empty($poNums)) {
            // POHD: DocuNo = 'PO' + PONum  -> POID, วันที่, ยอด
            $docuNos = array_map(fn ($p) => 'PO' . $p, $poNums);
            $poidToPo = [];
            foreach (array_chunk($docuNos, 1000) as $chunk) {
                $hds = DB::connection(self::ERP)->table('POHD')
                    ->whereIn('DocuNo', $chunk)
                    ->selectRaw('POID, RTRIM(DocuNo) as docu_no, DocuDate, SumGoodAmnt, VATAmnt, NetAmnt')
                    ->get();
                foreach ($hds as $h) {
                    $po = preg_replace('/^PO/i', '', (string) $h->docu_no);
                    if (!isset($pos[$po])) continue;
                    $net = round((float) $h->NetAmnt, 2);
                    $vat = round((float) $h->VATAmnt, 2);
                    $pos[$po]['date']   = $h->DocuDate ? Carbon::parse($h->DocuDate)->format('Y-m-d') : null;
                    $pos[$po]['after']  = $net;
                    $pos[$po]['vat']    = $vat;
                    $pos[$po]['before'] = round($net - $vat, 2);
                    $poidToPo[$h->POID]  = $po;
                }
            }

            // ชุด GoodID ที่เป็นบริการ (EMGood.GoodTypeFlag='S') — ไว้จัดประเภท PO
            $serviceGoodIds = [];
            try {
                $serviceGoodIds = array_flip(
                    DB::connection(self::ERP)->table('EMGood')->where('GoodTypeFlag', 'S')
                        ->pluck('GoodID')->map(fn ($x) => (int) $x)->all()
                );
            } catch (\Throwable $e) {}

            // PODT: ไส้ในราย PO (ตาม POID)
            if (!empty($poidToPo)) {
                foreach (array_chunk(array_keys($poidToPo), 1000) as $chunk) {
                    $dts = DB::connection(self::ERP)->table('PODT')
                        ->whereIn('POID', $chunk)
                        ->where('CancelFlag', '<>', 'Y')
                        ->selectRaw('POID, GoodID, RTRIM(GoodName) as good_name, GoodQty2 as qty, GoodPrice2 as price, GoodAmnt as amnt')
                        ->orderBy('POID')->orderBy('ListNo')
                        ->get();
                    foreach ($dts as $d) {
                        $po = $poidToPo[$d->POID] ?? null;
                        if ($po === null || !isset($pos[$po])) continue;
                        $pos[$po]['lines'][] = [
                            'good_id' => (int) $d->GoodID,
                            'name'    => trim((string) $d->good_name),
                            'qty'     => (float) $d->qty,
                            'price'   => round((float) $d->price, 2),
                            'amount'  => round((float) $d->amnt, 2),
                        ];
                    }
                }
            }

            // จัดประเภทจาก "ชื่อรายการ" เป็นหลัก (GoodID ใน ERP เชื่อถือไม่ได้ — คนคีย์สินค้าใต้โค้ดบริการ/กลับกันบ่อย)
            //   บรรทัดเป็น "บริการ" เมื่อชื่อขึ้นต้นด้วย ค่า / งาน (ค่าขนส่ง ค่าแรง ค่าบริการ งานติดตั้ง งานรื้อถอน ฯลฯ)
            $isServiceLine = function ($ln) {
                $n = ltrim((string) ($ln['name'] ?? ''));
                return (bool) preg_match('/^(ค่า|งาน)/u', $n);
            };

            // จัดประเภทแต่ละ PO: มีบรรทัดที่เป็น "สินค้า" อย่างน้อย 1 = สินค้า ; ทุกบรรทัดเป็นบริการ = บริการ
            foreach ($pos as $po => &$pp) {
                $type = 'สินค้า';
                if (!empty($pp['lines'])) {
                    $hasGoods = false;
                    foreach ($pp['lines'] as $ln) {
                        if (!$isServiceLine($ln)) { $hasGoods = true; break; }
                    }
                    $type = $hasGoods ? 'สินค้า' : 'บริการ';
                }
                $pp['type'] = $type;
            }
            unset($pp);
        }

        $pos = array_values($pos);
        usort($pos, fn ($a, $b) => strcmp($a['po'], $b['po']));

        // แยกกลุ่ม สินค้า / บริการ + ยอดรวมแต่ละกลุ่ม
        $goods = array_values(array_filter($pos, fn ($p) => ($p['type'] ?? 'สินค้า') !== 'บริการ'));
        $service = array_values(array_filter($pos, fn ($p) => ($p['type'] ?? '') === 'บริการ'));
        $sum = function ($arr) {
            $s = ['before' => 0.0, 'vat' => 0.0, 'after' => 0.0, 'count' => count($arr)];
            foreach ($arr as $p) { $s['before'] += $p['before']; $s['vat'] += $p['vat']; $s['after'] += $p['after']; }
            return $s;
        };

        return [
            'so' => $soNum, 'pos' => $pos,
            'goods' => $goods, 'service' => $service,
            'goods_total' => $sum($goods), 'service_total' => $sum($service),
            'totals' => $sum($pos),
            'printed_at' => Carbon::now()->addYears(543)->format('d/m/Y H:i'),
        ];
    }

    /** หน้าแสดงผล (มีปุ่ม Excel / PDF) */
    public function page(Request $request)
    {
        $so = $request->query('SONum', '');
        if ($so === '') abort(400, 'ต้องระบุ SONum');
        return view('account.so_po_export', $this->buildData($so));
    }

    /** Excel (.xls – HTML table ที่ Excel เปิดได้ รองรับไทย) */
    public function excel(Request $request)
    {
        $so = $request->query('SONum', '');
        if ($so === '') abort(400, 'ต้องระบุ SONum');
        $data = $this->buildData($so);
        $html = view('account.so_po_excel', $data)->render();
        $fname = 'PO_SO_' . str_replace(['/', '\\'], '-', $data['so']) . '.xls';
        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fname . '"',
        ]);
    }

    /** PDF (dompdf + ฟอนต์ไทย) */
    public function pdf(Request $request)
    {
        $so = $request->query('SONum', '');
        if ($so === '') abort(400, 'ต้องระบุ SONum');
        $data = $this->buildData($so);
        // ฝังฟอนต์ไทย THSarabun แบบ data-URI (วิธีเดียวกับ PoDocumentController ที่ใช้งานได้)
        $data['fontNormal'] = $this->fontB64('THSarabun.ttf');
        $data['fontBold']   = $this->fontB64('THSarabun Bold.ttf');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('account.so_po_pdf', $data)
            ->setPaper('a4', 'portrait');
        $fname = 'PO_SO_' . str_replace(['/', '\\'], '-', $data['so']) . '.pdf';
        return $pdf->download($fname);
    }

    /** อ่านฟอนต์เป็น base64 (ลองจาก storage/fonts ก่อน แล้ว resources/fonts) */
    private function fontB64(string $file): string
    {
        foreach ([storage_path('fonts/' . $file), resource_path('fonts/' . $file)] as $p) {
            if (is_file($p)) return base64_encode(file_get_contents($p));
        }
        // fallback: ฟอนต์ไทยที่ bundle ไว้
        $fb = resource_path('fonts/thai.ttf');
        return is_file($fb) ? base64_encode(file_get_contents($fb)) : '';
    }
}
