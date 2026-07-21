<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class internal_poline extends Model
{
    protected $table      = 'internal_poline';
    public    $timestamps = false;

    protected $fillable = [
        'internal_id', 'SO_id', 'item_id', 'item_name',
        'item_quantity', 'item_average', 'item_total', 'item_location',
        'status',
        'summit_by',   'timestamp',    // ด่าน 1: จัดเสร็จ
        'location_by', 'location_at',  // ด่าน 2: ระบุตำแหน่ง
        'checkout_by', 'checkout_at',  // ด่าน 3: ของออก
    ];

    protected $casts = [
        'item_quantity' => 'float',
        'item_average'  => 'float',
        'item_total'    => 'float',
    ];

    // ---- สถานะเรียงตาม flow ----
    const ST_PENDING  = 'รอดำเนินการ';     // ยังไม่จัด
    const ST_FINISH   = 'จัดเสร็จแล้ว';     // ผ่านด่าน 1
    const ST_STORED   = 'ระบุตำแหน่งแล้ว';  // ผ่านด่าน 2
    const ST_CHECKOUT = 'เอาของออกแล้ว';   // ผ่านด่าน 3 (จบ)
    const ST_CANCEL   = 'ยกเลิก';

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::ST_FINISH:   return 'blue';
            case self::ST_STORED:   return 'orange';
            case self::ST_CHECKOUT: return 'green';
            case self::ST_CANCEL:   return 'red';
            default:                return 'inherit';
        }
    }

    public function head()
    {
        return $this->belongsTo(internal_po::class, 'internal_id', 'internal_id');
    }
}