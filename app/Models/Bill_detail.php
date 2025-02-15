<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill_detail extends Model
{
    use HasFactory;

    protected $table = 'bill_detail'; 
    protected $fillable = ['so_id', 'item_id', 'item_name', 'quantity', 'unit_price', 'so_detail_id']; // ใส่ค่า 'so_detail_id' ที่จะอ้างอิงถึงบิล

    public $timestamps = false; // ไม่ใช้ timestamps

    public function bill() 
    {
        return $this->belongsTo(Bill::class, 'so_detail_id', 'so_detail_id'); // ความสัมพันธ์กับ bill
    }
}
