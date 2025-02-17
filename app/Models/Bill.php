<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'tblbill'; 
    protected $fillable = ['so_id', 'status', 'customer_id','customer_tel','customer_address','customer_la_long' ,'notes', 'date_of_dali','emp_name'];
    
    public $timestamps = false; // ปิด timestamps

    public function customer()
    {
        return $this->belongsTo(tblcustomer::class, 'customer_id', 'customer_id');
    }
    public function billDetails()
    {
        // ความสัมพันธ์ One-to-Many กับตาราง BillDetail
        return $this->hasMany(Bill_Detail::class, 'so_detail_id');
    }
}

