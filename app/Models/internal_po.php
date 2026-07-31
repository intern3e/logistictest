<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class internal_po extends Model
{
    protected $table        = 'internal_po';
    protected $primaryKey   = 'internal_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'internal_id', 'SO_id', 'POref','customer_code',
        'customer_name', 'create_by', 'timestamp',
        'status',
        'pick_by',     'pick_at',      // ด่าน 1: จัดเสร็จ
        'location_by', 'location_at',  // ด่าน 2: ระบุตำแหน่ง
        'checkout_by', 'checkout_at',  // ด่าน 3: ของออก
    ];

    const ST_PENDING  = 'รอดำเนินการ';
    const ST_FINISH   = 'จัดเสร็จแล้ว';
    const ST_STORED   = 'ระบุตำแหน่งแล้ว';
    const ST_CHECKOUT = 'เอาของออกแล้ว';
    const ST_CANCEL   = 'ยกเลิก';

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::ST_FINISH   => 'blue',
            self::ST_STORED   => 'orange',
            self::ST_CHECKOUT => 'green',
            self::ST_CANCEL   => 'red',
            default           => 'inherit',
        };
    }

    public function lines()
    {
        return $this->hasMany(internal_poline::class, 'internal_id', 'internal_id');
    }

    public static function genInternalId()
    {
        $yy     = (date('Y') + 543) % 100;
        $prefix = sprintf('%02d%02d-A', $yy, date('m'));

        $last = static::where('internal_id', 'LIKE', $prefix . '%')
            ->lockForUpdate()
            ->orderBy('internal_id', 'desc')
            ->value('internal_id');

        $run = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . sprintf('%04d', $run);
    }
}