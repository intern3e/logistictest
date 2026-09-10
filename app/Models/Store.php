<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $connection = 'mysql_3e'; // ต่อ DB เก่า (3e)
    protected $table = 'store';
    public $timestamps = false;
    protected $guarded = [];
}