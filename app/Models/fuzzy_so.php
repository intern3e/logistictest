<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class fuzzy_so extends Model
{
    protected $connection = 'pgsql'; 
     protected $table = 'fuzzy_so';

    public $timestamps = false;

    protected $fillable = [
        'so_no',
        'doc_date',
        'customer_code',
        'customer_name',
        'salesperson',
        'item_new',
        'product_name',
        'qty',
        'unit',
        'unit_price',
        'line_amount',
        'so_total',
    ];

    protected $casts = [
        'doc_date'    => 'date',
        'qty'         => 'decimal:4',
        'unit_price'  => 'decimal:4',
        'line_amount' => 'decimal:4',
        'so_total'    => 'decimal:4',
    ];

    // ---------- Scopes ----------

    public function scopeByCustomer(Builder $query, string $customerCode): Builder
    {
        return $query->where('customer_code', $customerCode);
    }

    public function scopeByItem(Builder $query, string $itemNew): Builder
    {
        return $query->where('item_new', $itemNew);
    }

    public function scopeBySoNo(Builder $query, string $soNo): Builder
    {
        return $query->where('so_no', $soNo);
    }

    public function scopeByDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('doc_date', [$from, $to]);
    }

    public function scopeBySalesperson(Builder $query, string $salesperson): Builder
    {
        return $query->where('salesperson', $salesperson);
    }
}
