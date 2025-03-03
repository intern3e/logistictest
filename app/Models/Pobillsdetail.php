<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pobillsdetail extends Model
{
    use HasFactory;

    // กำหนดชื่อตารางหากมันไม่ตรงกับชื่อโมเดล
    protected $table = 'pobills_detail';

    // หากต้องการใช้ชื่อ primary key ที่ไม่ใช่ id
    protected $primaryKey = 'po_detail_id';

    // ถ้าคุณไม่ต้องการให้ Eloquent จัดการกับ timestamps
    public $timestamps = false;

    // กำหนด attributes ที่สามารถทำการ fill ได้ (mass assignment)
    protected $fillable = [
        'po_detail_id',
        'po_id',
        'item_id',
        'item_name',
        'quantity',
        'unit_price',
    ];
}
