<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class quotationItem extends Model
{
    protected $table = 'quotation_items';

    protected $fillable = [
        'quotation_no', 'line_no', 'description', 'qty', 'unit',
        'unit_price', 'amount', 'item_new', 'product_name', 'is_new', 'sub_detail',
    ];

    protected $casts = [
        'qty'        => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount'     => 'decimal:2',
        'is_new'     => 'boolean',
    ];
}