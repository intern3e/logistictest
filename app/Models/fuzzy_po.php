<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class fuzzy_po extends Model
{
    protected $connection = 'pgsql'; 
    protected $table = 'fuzzy_po';

    public $timestamps = false;

    protected $fillable = [
        'doc_date',
        'vendor_name',
        'item_new',
        'product_name',
        'qty',
        'unit',
        'unit_price',
        'discount',
        'line_amount',
        'amount_before_tax',
        'vat',
        'total',
    ];

    protected $casts = [
        'qty'               => 'decimal:4',
        'unit_price'        => 'decimal:4',
        'discount'          => 'decimal:4',
        'line_amount'       => 'decimal:4',
        'amount_before_tax' => 'decimal:4',
        'vat'               => 'decimal:4',
        'total'             => 'decimal:4',
    ];

    // ---------- Scopes ----------

    public function scopeByVendor(Builder $query, string $vendor): Builder
    {
        return $query->where('vendor_name', $vendor);
    }

    public function scopeByItem(Builder $query, string $itemNew): Builder
    {
        return $query->where('item_new', $itemNew);
    }

    public function scopeByDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('doc_date', [$from, $to]);
    }
}
