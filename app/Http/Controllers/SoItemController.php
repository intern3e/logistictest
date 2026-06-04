<?php

namespace App\Http\Controllers;

use App\Models\FuzzyItem;
use App\Models\SoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SoItemController extends Controller
{
    public function index()
    {
        return view('sale.SoItem');
    }

    public function store(Request $request)
    {
        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Fuzzy search: ค้นหาชื่อสินค้าจาก fuzzy_item
     * GET /SoItem/fuzzy-search?q=keyword
     */
    public function fuzzySearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        // ===== ลอง pg_trgm ก่อน =====
        try {
            DB::connection('pgsql')->statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

            // DISTINCT ON (group_id) → 1 แถวต่อ SKU เรียงตาม similarity สูงสุด
            $results = DB::connection('pgsql')
                ->select("
                    SELECT * FROM (
                        SELECT DISTINCT ON (group_id)
                            id, product_name, group_id, keyword, item_name,
                            GREATEST(
                                COALESCE(similarity(product_name, ?), 0),
                                COALESCE(similarity(item_name, ?), 0)
                            ) AS score
                        FROM fuzzy_item
                        WHERE product_name % ?
                           OR item_name % ?
                           OR keyword ILIKE ?
                           OR product_name ILIKE ?
                           OR item_name ILIKE ?
                        ORDER BY group_id,
                                 GREATEST(
                                    COALESCE(similarity(product_name, ?), 0),
                                    COALESCE(similarity(item_name, ?), 0)
                                 ) DESC
                    ) sub
                    ORDER BY score DESC
                    LIMIT 20
                ", [$q, $q, $q, $q, "%{$q}%", "%{$q}%", "%{$q}%", $q, $q]);

            return response()->json($results);

        } catch (\Exception $e) {
            Log::warning('Fuzzy trgm failed: ' . $e->getMessage());
        }

        // ===== Fallback: ILIKE + DISTINCT ON =====
        try {
            $results = DB::connection('pgsql')
                ->select("
                    SELECT DISTINCT ON (group_id)
                        id, product_name, group_id, keyword, item_name
                    FROM fuzzy_item
                    WHERE product_name ILIKE ?
                       OR item_name ILIKE ?
                       OR keyword ILIKE ?
                    ORDER BY group_id, product_name
                    LIMIT 20
                ", ["%{$q}%", "%{$q}%", "%{$q}%"]);

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('FuzzySearch ILIKE failed: ' . $e->getMessage());
            return response()->json([
                'error'   => $e->getMessage(),
                'message' => 'ไม่สามารถค้นหาได้ กรุณาตรวจสอบ DB connection และ table fuzzy_item'
            ], 500);
        }
    }

    /**
     * Sales history: ดึงประวัติการขายจาก so_detail ตาม group_id
     * GET /SoItem/sales-history/{groupId}
     */
    public function salesHistory($groupId)
    {
        try {
            $sku = 'SKU-' . $groupId;

            // Subquery: DISTINCT ON เอาแถวเดียวต่อ (วันที่+ลูกค้า)
            // Outer: เรียงปีล่าสุดขึ้นก่อน
            $records = DB::connection('pgsql')
                ->select("
                    SELECT * FROM (
                        SELECT DISTINCT ON (doc_date_raw, customer_code)
                            id, doc_date_raw, customer_code, customer_name,
                            salesperson, item_new, item_new_name, product_name,
                            qty, unit, unit_price, line_amount, so_total, source_file
                        FROM so_detail
                        WHERE item_new = ?
                        ORDER BY doc_date_raw, customer_code, id DESC
                    ) sub
                    ORDER BY doc_date_raw DESC NULLS LAST
                    LIMIT 50
                ", [$sku]);

            return response()->json($records);

        } catch (\Exception $e) {
            Log::error('SalesHistory error: ' . $e->getMessage());
            return response()->json([
                'error'   => $e->getMessage(),
                'message' => 'ไม่สามารถดึงประวัติการขายได้'
            ], 500);
        }
    }
}