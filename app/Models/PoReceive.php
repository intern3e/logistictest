<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'checkout_time' => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    /**
     * ซ่อนรายการที่ถูกยกเลิก (รับเข้าผิด) ออกจากทุก query โดยอัตโนมัติ
     * -> หน้าอื่น ๆ ที่แสดงผลจะไม่ดึงรายการ cancel/รับเข้าผิดไปแสดง
     * ถ้าต้องการรวมรายการที่ยกเลิกด้วย ใช้ PoReceive::withCancelled()
     */
    protected static function booted(): void
    {
        static::addGlobalScope('notCancelled', function (Builder $builder) {
            $builder->whereNull($builder->getModel()->getTable() . '.cancelled_at');
        });
    }

    public function scopeWithCancelled(Builder $query): Builder
    {
        return $query->withoutGlobalScope('notCancelled');
    }

    // join ด้วยเลข PO จริง (po_id ของทั้งสองตารางเป็นค่าเดียวกัน) ไม่ใช่ id
    public function lines()
    {
        return $this->hasMany(PoReceiveLine::class, 'po_id', 'po_id');
    }
}
