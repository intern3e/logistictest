<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class airschedule extends Model
{
     protected $fillable = [
        'aircon_code',
        'brand',
        'model_name',
        'location',
        'service_date',
        'status',
        'cover_image',
        'images',
        'notes',
        'cleaned_at',
    ];

    protected $casts = [
        'images' => 'array',
        'service_date' => 'date',
        'cleaned_at' => 'datetime',
    ];
}
