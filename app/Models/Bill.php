<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'tblbill'; // กำหนดชื่อของตาราง

    public function customer()
    {
        return $this->belongsTo(tblcustomer::class, 'customer_id', 'customer_id');
    }
    // ระบุคอลัมน์ที่สามารถกรอกข้อมูลได้
    protected $fillable = ['column_name'];
}
