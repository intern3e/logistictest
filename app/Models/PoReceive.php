<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoReceive extends Model
{
    protected $table = 'po_receives';
    public $timestamps = false;

    protected $fillable = [
        'po_id',
        'so_id',
        'cust_name',
        'POref',
        'status',
        'checkout_by',
        'checkout_time',
    ];

    protected $casts = [
        'checkout_time' => 'datetime',
    ];

    // join ด้วยเลข PO จริง (po_id ของทั้งสองตารางเป็นค่าเดียวกัน) ไม่ใช่ id
    public function lines()
    {
        return $this->hasMany(PoReceiveLine::class, 'po_id', 'po_id');
    }
}