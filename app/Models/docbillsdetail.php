<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docbillsdetail extends Model
{
    use HasFactory;

    protected $table = 'doc_detail'; // ระบุชื่อตารางในฐานข้อมูล
    public $timestamps = false;
}
