<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manualbill extends Model
{
    use HasFactory;

    protected $table = 'manualbills';

    // ปิด created_at / updated_at อัตโนมัติของ Laravel
    public $timestamps = false;

    protected $fillable = [
        'billid',
        'sono',
        'docu_date',
        'removed_at',
    ];

    protected $casts = [
        'docu_date'  => 'date',
        'removed_at' => 'datetime',
    ];
}