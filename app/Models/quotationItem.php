<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class quotationItem extends Model
{
    protected $table = 'quotation_items';

    protected $fillable = [
        'quotation_id',
        'line_no',
        'description',
        'qty',
        'unit',
        'unit_price',
        'amount',
        'item_new',
        'product_name',
        'is_new',
    ];

    protected $casts = [
        'line_no'    => 'integer',
        'qty'        => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount'     => 'decimal:2',
        'is_new'     => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    /**
     * คำนวณจำนวนเงิน (qty × unit_price)
     */
    public function calcAmount(): self
    {
        $this->amount = round(($this->qty ?? 0) * ($this->unit_price ?? 0), 2);

        return $this;
    }

    protected static function booted()
    {
        static::saving(function (QuotationItem $item) {
            $item->calcAmount();
        });
    }
}