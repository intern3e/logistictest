<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class service_car extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    protected $table = 'service_cars';
    protected $fillable = [
        'date',
        'driver',
        'plate',
        'type',
        'cost',
        'status',
        'detail',
        'images', 
    ];

    protected $casts = [
        'images' => 'array',
        'cost'   => 'float',
    ];
}
