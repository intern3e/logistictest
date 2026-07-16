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
        'status', 'summit_by', 'timestamp',
    ];

    protected $casts = [
        'item_quantity' => 'float',
        'item_average'  => 'float',
        'item_total'    => 'float',
    ];

    const ST_PENDING = 'รอดำเนินการ';
    const ST_FINISH  = 'จัดเสร็จแล้ว';
    const ST_CANCEL  = 'ยกเลิก';

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::ST_FINISH: return 'green';
            case self::ST_CANCEL: return 'red';
            default:              return 'inherit';
        }
    }

    public function head()
    {
        return $this->belongsTo(internal_po::class, 'internal_id', 'internal_id');
    }
}