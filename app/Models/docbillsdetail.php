<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docbillsdetail extends Model
{
    use HasFactory;

    protected $table = 'doc_detail'; // ชื่อตารางในฐานข้อมูล
    public $timestamps = false;
    
    // ถ้าต้องการให้แก้ไขข้อมูล detail ได้ด้วย ให้เพิ่ม fillable ด้านล่างนี้
    protected $fillable = [
        'doc_id',
        'item_name',
        'quantity',
    ];
}