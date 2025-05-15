<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docbills extends Model // เปลี่ยนจาก docbills -> Docbills
{
    protected $table = 'docbills';
    protected $primaryKey = 'doc_id';   // กำหนด primary key
    public $incrementing = false;       // เพราะเป็น string ไม่ใช่ auto increment
    protected $keyType = 'string';      // กำหนด type เป็น string
    public $timestamps = false;         // ถ้าไม่มี created_at, updated_at
}
