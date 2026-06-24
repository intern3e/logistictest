<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class historyquotation extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'history_quotation';

    public $timestamps = false;

    protected $fillable = [
        'quotation_no',
        'cust_name',
        'quotation_date',
        'product',
        'unit',
        'price_per_unit',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'price_per_unit' => 'decimal:3',
    ];
}