<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class fuzzy_po extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'fuzzy_po';
    public $timestamps = false;

    protected $fillable = [
        'doc_date', 'doc_no', 'vendor_name', 'vendor_note',
        'product_code', 'product_name', 'qty', 'unit',
        'unit_price', 'unit_price_thb', 'currency',
        'item_discount_pct', 'item_discount_amt', 'item_amount',
        'po_total', 'bill_discount_pct', 'bill_discount_amt',
        'before_tax', 'input_tax', 'grand_total',
    ];

    protected $casts = [
        'doc_date'       => 'date',
        'qty'            => 'decimal:4',
        'unit_price'     => 'decimal:4',
        'unit_price_thb' => 'decimal:4',
        'grand_total'    => 'decimal:4',
    ];

    /* ================================================================
       ★ scopeKeywordSearch — %keyword% ILIKE แยกคำ AND
       ================================================================ */
    public function scopeKeywordSearch($query, string $keyword)
    {
        $words = preg_split('/\s+/', trim($keyword));
        $words = array_filter($words, fn($w) => mb_strlen($w) >= 1);

        foreach ($words as $word) {
            $like = '%' . $word . '%';
            $query->where(function ($q) use ($like) {
                $q->where('product_name', 'ILIKE', $like)
                  ->orWhere('product_code', 'ILIKE', $like);
            });
        }

        return $query;
    }

    /* ================================================================
       ★ scopeFuzzySearch — pg_trgm similarity (ค้นแบบพิมพ์ผิดได้)
       ================================================================
       ใช้เมื่อ ILIKE ไม่เจอ
       เปรียบเทียบ similarity ≥ 0.3 (ปรับได้)

       ตัวอย่าง:
         lcld32m7  → similarity กับ lc1d32m7 ≈ 0.7  → เจอ
         q6bat     → similarity กับ Q6BAT    ≈ 1.0  → เจอ
    */
    public function scopeFuzzySearch($query, string $keyword, float $threshold = 0.3)
    {
        /* รวมคำทั้งหมดเป็นก้อนเดียว (ไม่แยกคำ) เพื่อเทียบ similarity */
        $clean = preg_replace('/\s+/', '', trim($keyword));

        $query->where(function ($q) use ($clean, $threshold) {
            $q->whereRaw(
                'similarity(product_name, ?) >= ?',
                [$clean, $threshold]
            )->orWhereRaw(
                'similarity(product_code, ?) >= ?',
                [$clean, $threshold]
            );
        });

        /* เรียงตามความคล้ายมากสุดก่อน */
        $query->orderByRaw(
            'GREATEST(similarity(product_name, ?), similarity(product_code, ?)) DESC',
            [$clean, $clean]
        );

        return $query;
    }

    /* ★ scopeVendorSearch */
    public function scopeVendorSearch($query, string $keyword)
    {
        $words = preg_split('/\s+/', trim($keyword));
        $words = array_filter($words, fn($w) => mb_strlen($w) >= 1);

        foreach ($words as $word) {
            $query->where('vendor_name', 'ILIKE', '%' . $word . '%');
        }

        return $query;
    }
}