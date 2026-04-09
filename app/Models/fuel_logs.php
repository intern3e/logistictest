<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class fuel_Logs extends Model
{
    protected $table = 'fuel_logs';

    const UPDATED_AT = null; // ไม่มี updated_at

    protected $fillable = [
        'driver_name',
        'vehicle_id',
        'work_date',
        'start_time',
        'end_time',
        'liters',
        'price_per_liter',
        'total_price',
        'total_distance',
        'note',
    ];

    protected $casts = [
        'liters'          => 'float',
        'price_per_liter' => 'float',
        'total_price'     => 'float',
        'total_distance'  => 'float',
        'created_at'      => 'datetime',
    ];
}