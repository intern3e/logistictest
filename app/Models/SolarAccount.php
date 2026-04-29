<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolarAccount extends Model
{
    protected $table = 'solar_accounts';

    protected $fillable = [
        'plane',
        'username',
        'password',
        'email',
        'app_password',
        'customer',
        'inverter',
    ];
}