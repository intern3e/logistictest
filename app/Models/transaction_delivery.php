<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class transaction_delivery extends Model
{
    protected $table = 'transaction_transport';
    public $timestamps = false;

    protected $fillable = [
        'bill_id',
        'name_pick',
        'time_pick',
        'transport_name',
        'driver_name',
        'check_name',
        'check_time',
        'status',
        'note',
        'delivery_date',
        'id_transport',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'time_pick'  => 'datetime',
        'check_time' => 'datetime',
        'delivery_date' => 'date',
        'cancelled_at'  => 'datetime',
    ];

    /**
     * งานที่ถูกยกเลิก (soft-cancel) จะถูกซ่อนจากทุก query โดยอัตโนมัติ
     * -> ทุกหน้าที่ดึงงานจ่าย (สรุปงานคนขับ, oil, ฯลฯ) จะไม่เห็นงานที่ยกเลิก
     *    และหน้าจ่ายงานจะถือว่า "ยังไม่จ่าย" -> คืนงานกลับไปจ่ายใหม่ได้
     * ถ้าต้องการรวมงานที่ยกเลิกด้วย ใช้ withoutGlobalScope('notCancelled')
     */
    protected static function booted(): void
    {
        static::addGlobalScope('notCancelled', function (Builder $builder) {
            $builder->whereNull($builder->getModel()->getTable() . '.cancelled_at');
        });
    }
}