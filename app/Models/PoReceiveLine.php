<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PoReceiveLine extends Model
{
    protected $table = 'po_receives_line';
    public $timestamps = false;

    /**
     * ซ่อน row ที่ถูกยกเลิกรับเข้า (cancelled_at != null) ออกจากทุก query อัตโนมัติ
     * เหมือนตอนที่มันเคยถูก DELETE ทิ้ง — กันข้อมูลนับซ้ำ/โผล่ซ้ำในหน้า store, ชั้นวาง ฯลฯ
     * ถ้าต้องการเห็น row ที่ยกเลิกด้วย ใช้ PoReceiveLine::withoutGlobalScope('notCancelled')
     */
    protected static function booted(): void
    {
        static::addGlobalScope('notCancelled', function (Builder $builder) {
            $builder->whereNull('po_receives_line.cancelled_at');
        });
    }

    protected $fillable = [
        'po_id',
        'po_receive_id',
        'so_id',
        'good_name',
        'recv_qty',
        'unit_price',
        'shelf',
        'photo_path',
        'received_by',
        'received_at',
        'do_it',
        'do_it_time',
        'sus',
        'sus_time',
        'cancelled_at',
        'cancelled_by'
    ];

    protected $casts = [
        'recv_qty'     => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'received_at'  => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // join ด้วยเลข PO จริง ไม่ใช่ id
    public function header()
    {
        return $this->belongsTo(PoReceive::class, 'po_id', 'po_id');
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path
            ? Storage::disk('public')->url($this->photo_path)
            : null;
    }
}