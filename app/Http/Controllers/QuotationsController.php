<?php

namespace App\Http\Controllers;

use App\Models\quotation;
use App\Models\quotationItem;
use App\Models\log_quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * QuotationsController
 * ──────────────────────────────────────────────
 * จัดการใบเสนอราคา "ที่บันทึกแล้ว"
 *   - dashboard: รายการใบเสนอราคาทั้งหมด (sale.dashboardquotations)
 *   - downloadPdf: ดาวน์โหลด PDF
 *   - updateQuotation: แก้ไข + เก็บ log การเปลี่ยนแปลง
 *
 * ส่วนหน้าสร้างใบเสนอราคา / OCR / จับคู่ราคา อยู่ใน SoItemController
 */
class QuotationsController extends Controller
{
    // ══════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════
    public function dashboard(Request $request)
    {
        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));
        $month  = trim($request->input('month', ''));

        $query = Quotation::with(['items', 'logs'])->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_no', 'LIKE', "%{$search}%")
                  ->orWhere('customer_company', 'LIKE', "%{$search}%")
                  ->orWhere('customer_code', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%");
            });
        }
        if ($status) $query->where('status', $status);
        if ($month)  $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m') = ?", [$month]);

        $quotations = $query->paginate(20)->withQueryString();
        return view('sale.dashboardquotations', compact('quotations', 'search', 'status', 'month'));
    }

    // ══════════════════════════════════════════════════
    // DOWNLOAD PDF
    // ══════════════════════════════════════════════════
    public function downloadPdf(int $id)
    {
        $qt = Quotation::findOrFail($id);
        if (!$qt->hasPdf()) abort(404, 'ไม่พบไฟล์ PDF');
        return response()->download($qt->pdf_full_path, $qt->quotation_no . '.pdf');
    }

    // ══════════════════════════════════════════════════
    // UPDATE QUOTATION
    // ══════════════════════════════════════════════════
    public function updateQuotation(Request $request, int $id)
    {
        $request->validate([
            'doc_date' => 'required|date', 'customer_code' => 'nullable|string|max:50',
            'customer_company' => 'required|string|max:255', 'customer_address' => 'required|string',
            'customer_tel' => 'required|string|max:100', 'customer_tax' => 'nullable|string|max:50',
            'customer_branch' => 'nullable|string|max:100', 'contact_name' => 'required|string|max:255',
            'valid_days' => 'required|integer|min:0', 'expire_date' => 'nullable|date',
            'credit_days' => 'nullable|integer|min:0', 'note' => 'nullable|string',
            'status' => 'nullable|in:draft,approved,cancelled',
            'items' => 'required|array|min:1', 'items.*.desc' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0', 'items.*.unit' => 'nullable|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        try {
            return DB::transaction(function () use ($request, $id) {
                $quotation = Quotation::with('items')->findOrFail($id);
                $headerFields = ['doc_date','customer_code','customer_company','customer_address','customer_tel','customer_tax','customer_branch','contact_name','valid_days','expire_date','credit_days','note','status'];
                $oldHeader = [];
                foreach ($headerFields as $f) { $v = $quotation->$f; if ($v instanceof \Carbon\Carbon) $v = $v->format('Y-m-d'); $oldHeader[$f] = trim((string)($v ?? '')); }
                $oldItemsSnap = $quotation->items->map(fn($i) => [
                    'line'  => (int) $i->line_no,
                    'desc'  => trim((string) $i->description),
                    'qty'   => round((float) $i->qty, 2),
                    'unit'  => trim((string) ($i->unit ?? '')),
                    'price' => round((float) $i->unit_price, 2),
                ])->values()->all();
                $quotation->fill([
                    'doc_date'=>$request->input('doc_date'),'customer_code'=>$request->input('customer_code'),
                    'customer_company'=>$request->input('customer_company'),'customer_address'=>$request->input('customer_address'),
                    'customer_tel'=>$request->input('customer_tel'),'customer_tax'=>$request->input('customer_tax'),
                    'customer_branch'=>$request->input('customer_branch'),'contact_name'=>$request->input('contact_name'),
                    'valid_days'=>$request->input('valid_days',0),'expire_date'=>$request->input('expire_date'),
                    'credit_days'=>$request->input('credit_days'),'note'=>$request->input('note'),
                    'status'=>$request->input('status',$quotation->status),
                ]);
                $quotation->items()->delete();
                foreach ($request->input('items',[]) as $idx => $item) {
                    $desc=trim($item['desc']??'');$price=(float)($item['price']??0);
                    if(!$desc&&$price<=0)continue;
                    QuotationItem::create([
                        'quotation_no' => $quotation->quotation_no, 'line_no' => $idx + 1,
                        'description'  => $desc, 'qty' => (float) ($item['qty'] ?? 0),
                        'unit' => $item['unit'] ?? null, 'unit_price' => $price,
                    ]);
                }
                $quotation->load('items'); $quotation->recalculate(); $quotation->save();
                $newInputMap = ['doc_date'=>$request->input('doc_date',''),'customer_code'=>trim($request->input('customer_code','')),'customer_company'=>trim($request->input('customer_company','')),'customer_address'=>trim($request->input('customer_address','')),'customer_tel'=>trim($request->input('customer_tel','')),'customer_tax'=>trim($request->input('customer_tax','')),'customer_branch'=>trim($request->input('customer_branch','')),'contact_name'=>trim($request->input('contact_name','')),'valid_days'=>(string)((int)$request->input('valid_days',0)),'expire_date'=>$request->input('expire_date',''),'credit_days'=>$request->input('credit_days')!==null?(string)((int)$request->input('credit_days')):'','note'=>trim($request->input('note','')),'status'=>trim($request->input('status',''))];
                foreach($headerFields as $f){$old=$oldHeader[$f];$new=$newInputMap[$f]??'';if($old!==$new)log_quotation::record($quotation->quotation_no,'edit','',$f,$old,$new);}
                $newItemsSnap = $quotation->items->map(fn($i) => [
                    'line'  => (int) $i->line_no,
                    'desc'  => trim((string) $i->description),
                    'qty'   => round((float) $i->qty, 2),
                    'unit'  => trim((string) ($i->unit ?? '')),
                    'price' => round((float) $i->unit_price, 2),
                ])->values()->all();

                $this->logItemChanges($quotation->quotation_no, $oldItemsSnap, $newItemsSnap);
                return response()->json(['status'=>'success','message'=>"บันทึกการแก้ไข {$quotation->quotation_no} สำเร็จ",'grand_total'=>$quotation->grand_total]);
            });
        } catch (\Exception $e) {
            Log::error('Quotation update error: '.$e->getMessage());
            return response()->json(['status'=>'error','message'=>'แก้ไขไม่สำเร็จ: '.$e->getMessage()],500);
        }
    }

    // ══════════════════════════════════════════════════
    // ★ LOG HELPERS — เก็บประวัติการแก้ไขรายการสินค้า
    // ══════════════════════════════════════════════════
    private function logItemChanges(string $quotationNo, array $old, array $new): void
    {
        $usedOld = array_fill(0, count($old), false);
        $usedNew = array_fill(0, count($new), false);

        $pairs       = [];  // [oldIdx, newIdx] ชื่อตรงกัน — เช็คแค่ qty/price/unit
        $renamePairs = [];  // [oldIdx, newIdx] เปลี่ยนชื่อ

        // ── Pass A: ชื่อตรงกันเป๊ะ ──
        foreach ($new as $ni => $n) {
            foreach ($old as $oi => $o) {
                if ($usedOld[$oi] || $usedNew[$ni]) continue;
                if ($o['desc'] === $n['desc'] && $o['desc'] !== '') {
                    $usedOld[$oi] = $usedNew[$ni] = true;
                    $pairs[] = [$oi, $ni];
                    break;
                }
            }
        }

        // ── Pass B1: เดา rename จาก qty+ราคา+หน่วยเหมือนกัน (สัญญาณแรงสุด) ──
        foreach ($new as $ni => $n) {
            if ($usedNew[$ni]) continue;
            $sigN = $n['qty'] . '|' . $n['price'] . '|' . $n['unit'];
            foreach ($old as $oi => $o) {
                if ($usedOld[$oi]) continue;
                if (($o['qty'] . '|' . $o['price'] . '|' . $o['unit']) === $sigN) {
                    $usedOld[$oi] = $usedNew[$ni] = true;
                    $renamePairs[] = [$oi, $ni];
                    break;
                }
            }
        }

        // ── Pass B2: เดา rename จาก line_no เดียวกัน (fallback) ──
        foreach ($new as $ni => $n) {
            if ($usedNew[$ni]) continue;
            foreach ($old as $oi => $o) {
                if ($usedOld[$oi]) continue;
                if ($o['line'] === $n['line']) {
                    $usedOld[$oi] = $usedNew[$ni] = true;
                    $renamePairs[] = [$oi, $ni];
                    break;
                }
            }
        }

        // ── ที่เหลือ = ลบ / เพิ่ม ──
        $removed = [];
        foreach ($old as $oi => $o) if (!$usedOld[$oi]) $removed[] = $oi;
        $added = [];
        foreach ($new as $ni => $n) if (!$usedNew[$ni]) $added[] = $ni;

        // ── formatters ──
        $qtyFmt = function ($q) {
            $s = rtrim(rtrim(number_format($q, 2), '0'), '.');
            return $s === '' ? '0' : $s;
        };
        $lineDesc = function ($it) use ($qtyFmt) {
            $unit  = $it['unit'] !== '' ? ' ' . $it['unit'] : '';
            $total = $it['qty'] * $it['price'];
            return $qtyFmt($it['qty']) . $unit . ' × ' . number_format($it['price'], 2)
                 . ' = ' . number_format($total, 2);
        };

        // ── รวม entries ก่อน เผื่อต้อง fallback เป็นสรุป ──
        // แต่ละ entry = [note, field, oldVal, newVal]
        $entries = [];

        foreach ($removed as $oi) {
            $o = $old[$oi];
            $entries[] = [$o['desc'], 'item_remove', $lineDesc($o), ''];
        }
        foreach ($added as $ni) {
            $n = $new[$ni];
            $entries[] = [$n['desc'], 'item_add', '', $lineDesc($n)];
        }
        foreach ($renamePairs as [$oi, $ni]) {
            $o = $old[$oi]; $n = $new[$ni];
            $entries[] = ['', 'item_rename', $o['desc'], $n['desc']];
            $this->collectFieldDiffs($entries, $n['desc'], $o, $n, $qtyFmt);
        }
        foreach ($pairs as [$oi, $ni]) {
            $this->collectFieldDiffs($entries, $new[$ni]['desc'], $old[$oi], $new[$ni], $qtyFmt);
        }

        if (empty($entries)) return; // ไม่มีอะไรเปลี่ยน — ไม่ต้อง log

        // ── ถ้าแก้เยอะเกิน (เช่นแทบทั้งบิล) ยุบเป็นสรุปบรรทัดเดียว กัน log ท่วม ──
        $maxItemLogs = 80;
        if (count($entries) > $maxItemLogs) {
            $msg = sprintf('แก้ไขรายการสินค้า: เพิ่ม %d, ลบ %d, แก้ไข %d รายการ',
                count($added), count($removed), count($pairs) + count($renamePairs));
            log_quotation::record($quotationNo, 'edit', $msg, 'items', '', '');
            return;
        }

        foreach ($entries as [$note, $field, $oldVal, $newVal]) {
            log_quotation::record($quotationNo, 'edit', $note, $field, $oldVal, $newVal);
        }
    }

    private function collectFieldDiffs(array &$entries, string $name, array $o, array $n, callable $qtyFmt): void
    {
        if (number_format($o['price'], 2) !== number_format($n['price'], 2)) {
            $entries[] = [$name, 'item_price', number_format($o['price'], 2), number_format($n['price'], 2)];
        }
        if (number_format($o['qty'], 2) !== number_format($n['qty'], 2)) {
            $entries[] = [$name, 'item_qty', $qtyFmt($o['qty']), $qtyFmt($n['qty'])];
        }
        if ($o['unit'] !== $n['unit']) {
            $entries[] = [$name, 'item_unit', $o['unit'] !== '' ? $o['unit'] : '-', $n['unit'] !== '' ? $n['unit'] : '-'];
        }
    }
}