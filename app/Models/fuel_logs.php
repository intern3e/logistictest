<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class fuel_Logs extends Model
{
    protected $table = 'fuel_logs'; // ปรับตามชื่อ table จริง

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

    // cast work_date เป็น date เพื่อให้ Carbon::parse ทำงานถูกต้อง
    protected $casts = [
        'work_date'       => 'date:Y-m-d',
        'liters'          => 'float',
        'price_per_liter' => 'float',
        'total_price'     => 'float',
        'total_distance'  => 'float',
    ];
}