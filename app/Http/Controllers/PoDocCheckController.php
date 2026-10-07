<?php

namespace App\Http\Controllers;

use App\Models\PoDocCheck;
use Illuminate\Http\Request;

/**
 * เช็คเอกสาร PO — บันทึกจากหน้า mobile_app, สรุปที่หน้า account/po_doc_check
 */
class PoDocCheckController extends Controller
{
    /** สิทธิ์เข้าหน้าสรุป (admin/accounting) */
    private function canUse($user): bool
    {
        return in_array($user->role ?? '', ['admin', 'accounting'], true);
    }

    /** หน้าสรุป */
    public function index(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        return view('account.po_doc_check', ['loginName' => $user->name ?? '']);
    }

    /** ข้อมูลสรุป (AJAX) — filter: has(มีเอกสาร)/none(ไม่มี) + ชนิดเอกสาร */
    public function data(Request $request)
    {
        $user = $this->requireLogin($request);
        if (!$this->canUse($user)) return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์'], 403);

        $status = trim((string) $request->input('status', ''));  // '' | has | none
        $doc    = trim((string) $request->input('doc', ''));     // '' | tax | receipt | delivery
        $q      = trim((string) $request->input('q', ''));

        $query = PoDocCheck::query();
        if ($status === 'has')  $query->where('has_document', true);
        elseif ($status === 'none') $query->where('has_document', false);

        if (in_array($doc, PoDocCheck::DOC_TYPES, true)) {
            $query->whereRaw('FIND_IN_SET(?, doc_types)', [$doc]);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('po_id', 'LIKE', "%{$q}%")
                  ->orWhere('so_id', 'LIKE', "%{$q}%")
                  ->orWhere('vendor_name', 'LIKE', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('checked_at')->orderByDesc('id')->limit(5000)->get()
            ->map(fn ($r) => $this->rowOut($r));

        return response()->json([
            'ok'      => true,
            'rows'    => $rows,
            'summary' => $this->summary(),
        ]);
    }

    /** บันทึกการเช็คเอกสารจาก mobile_app */
    public function save(Request $request)
    {
        $user = $this->requireLogin($request);
        $data = $request->validate([
            'po_id'         => 'required|string|max:50',
            'so_id'         => 'nullable|string|max:50',
            'vendor_name'   => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'has_document'  => 'required|boolean',
            'doc_types'     => 'nullable|array',
            'doc_types.*'   => 'string|in:tax,receipt,delivery',
            'note'          => 'nullable|string|max:1000',
        ]);

        $has = (bool) $data['has_document'];
        // ชนิดเอกสาร: เรียงตามลำดับมาตรฐาน เก็บเป็น csv ; ถ้าไม่ได้รับเอกสาร -> ว่าง
        $types = $has ? array_values(array_intersect(PoDocCheck::DOC_TYPES, $data['doc_types'] ?? [])) : [];
        $row = PoDocCheck::updateOrCreate(
            ['po_id' => trim($data['po_id'])],
            [
                'so_id'         => $data['so_id'] ?? null,
                'vendor_name'   => $data['vendor_name'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'has_document'  => $has,
                'doc_types'     => $types ? implode(',', $types) : null,
                'note'          => $data['note'] ?? null,
                'checked_by'    => $user->name ?? null,
                'checked_at'    => now(),
            ]
        );

        return response()->json(['ok' => true, 'row' => $this->rowOut($row)]);
    }

    /** ดึงสถานะเอกสารของ PO (ให้ mobile_app แสดงค่าที่เคยติ๊กไว้) */
    public function get(Request $request)
    {
        $this->requireLogin($request);
        $poId = trim((string) $request->query('po_id', ''));
        if ($poId === '') return response()->json(['ok' => false], 400);
        $row = PoDocCheck::where('po_id', $poId)->first();
        return response()->json(['ok' => true, 'row' => $row ? $this->rowOut($row) : null]);
    }

    private function summary(): array
    {
        $base = PoDocCheck::query();
        return [
            'total'        => (clone $base)->count(),
            'has'          => (clone $base)->where('has_document', true)->count(),
            'none'         => (clone $base)->where('has_document', false)->count(),
            'tax_invoice'  => (clone $base)->whereRaw('FIND_IN_SET(?, doc_types)', ['tax'])->count(),
            'receipt'      => (clone $base)->whereRaw('FIND_IN_SET(?, doc_types)', ['receipt'])->count(),
            'delivery'     => (clone $base)->whereRaw('FIND_IN_SET(?, doc_types)', ['delivery'])->count(),
        ];
    }

    private function rowOut(PoDocCheck $r): array
    {
        return [
            'id'                => $r->id,
            'po_id'             => $r->po_id,
            'so_id'             => $r->so_id,
            'vendor_name'       => $r->vendor_name,
            'customer_name'     => $r->customer_name,
            'has_document'      => (bool) $r->has_document,
            'doc_types'         => $r->doc_types ? explode(',', $r->doc_types) : [],
            'note'              => $r->note,
            'checked_by'        => $r->checked_by,
            'checked_at'        => optional($r->checked_at)->format('d/m/Y H:i'),
        ];
    }
}
