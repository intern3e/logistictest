<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoDetail extends Model
{
    protected $table = 'so_detail';
    protected $connection = 'pgsql';
    public $timestamps = false;

    protected $fillable = [
        'doc_date_raw',
        'customer_code',
        'customer_name',
        'salesperson',
        'item_new',
        'item_new_name',
        'item_seq',
        'product_name',
        'qty',
        'unit',
        'unit_price',
        'line_amount',
        'so_total',
        'source_file',
    ];

    protected $casts = [
        'qty'         => 'decimal:2',
        'unit_price'  => 'decimal:2',
        'line_amount' => 'decimal:2',
        'so_total'    => 'decimal:2',
    ];

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Fuzzy search product_name ด้วย pg_trgm
     * SoDetail::fuzzy('Q6BAT')->get()
     */
    public function scopeFuzzy($query, string $search)
    {
        return $query->whereRaw("product_name % ?", [$search])
                     ->orderByRaw("similarity(product_name, ?) DESC", [$search]);
    }

    /**
     * ค้นด้วย SKU
     * SoDetail::bySku('SKU-42')->get()
     */
    public function scopeBySku($query, string $sku)
    {
        return $query->where('item_new', $sku);
    }

    /**
     * ค้นด้วย customer
     * SoDetail::byCustomer('C001')->get()
     */
    public function scopeByCustomer($query, string $code)
    {
        return $query->where('customer_code', $code);
    }

    /**
     * LIKE search
     * SoDetail::search('NF30')->get()
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('product_name', 'ILIKE', "%{$search}%");
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function fuzzyItems()
    {
        return $this->hasMany(FuzzyItem::class, 'group_id', 'group_id');
    }
}