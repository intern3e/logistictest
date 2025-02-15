<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'tblbill'; 
    protected $fillable = ['so_id', 'status', 'customer_id', 'notes', 'date_of_dali'];
    public $timestamps = false; // ไม่ใช้ timestamps

    public function customer()
    {
        return $this->belongsTo(tblcustomer::class, 'customer_id', 'customer_id');
    }
    public function billDetails() 
    {
        return $this->hasMany(Bill_detail::class, 'so_detail_id', 'so_detail_id'); // ความสัมพันธ์กับ bill_detail
    }
}
