<?php

namespace App\Http\Controllers;

use App\Models\fuzzy_po;
use Illuminate\Http\Request;

class PoitemController extends Controller
{
    public function index()
    {
        return view('sale.poitem');
    }

    /* ================================================================
       ★ search — ILIKE ก่อน → ถ้าไม่เจอ fallback เป็น fuzzy (similarity)
       ================================================================ */
    public function search(Request $request)
    {
        $items = $request->input('items', []);
        if (!is_array($items)) $items = [$items];

        $items = array_values(array_filter(
            array_map('trim', $items),
            fn($v) => mb_strlen($v) >= 1
        ));

        if (empty($items)) {
            return response()->json([]);
        }

        $results = [];

        foreach ($items as $keyword) {

            /* ---- ขั้น 1: ค้นแบบ ILIKE %word% (แม่นยำ) ---- */
            $records = fuzzy_po::keywordSearch($keyword)
                ->orderBy('doc_date', 'desc')
                ->orderBy('doc_no')
                ->get();

            $method = 'exact';

            /* ---- ขั้น 2: ถ้าไม่เจอ → ลอง fuzzy similarity ---- */
            if ($records->isEmpty()) {
                $records = fuzzy_po::fuzzySearch($keyword, 0.3)
                    ->orderBy('doc_date', 'desc')
                    ->orderBy('doc_no')
                    ->limit(200)
                    ->get();

                $method = 'fuzzy';
            }

            $latest = $records->first();

            $results[] = [
                'keyword' => $keyword,
                'method'  => $method,
                'latest'  => $latest,
                'records' => $records,
            ];
        }

        return response()->json($results);
    }

    public function detail(Request $request)
    {
        $docNo       = trim($request->input('doc_no', ''));
        $productCode = trim($request->input('product_code', ''));

        if (!$docNo) return response()->json([]);

        $query = fuzzy_po::where('doc_no', $docNo);
        if ($productCode) $query->where('product_code', $productCode);

        return response()->json($query->orderBy('doc_date', 'desc')->get());
    }
}