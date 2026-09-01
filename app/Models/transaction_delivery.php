<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transaction_delivery extends Model
{
    protected $table = 'transaction_transport';
    public $timestamps = false; 

    protected $fillable = [
        'bill_id',
        'name_pick',
        'time_pick',
        'transport_name',
        'driver_name',
        'check_name',
        'check_time',
        'status',
        'note',
        'delivery_date'
    ];

    protected $casts = [
        'time_pick'  => 'datetime',
        'check_time' => 'datetime',
        'delivery_date' => 'date',
    ];
}