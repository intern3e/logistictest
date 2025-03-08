<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docbills extends Model // เปลี่ยนจาก docbills -> Docbills
{
    use HasFactory;

    protected $table = 'docbills'; // ระบุชื่อตารางในฐานข้อมูล
}
