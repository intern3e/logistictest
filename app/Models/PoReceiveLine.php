<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PoReceiveLine extends Model
{
    protected $table = 'po_receives_line';
    public $timestamps = false;

    protected $fillable = [
        'po_id',
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
        'sus_time'
    ];

    protected $casts = [
        'recv_qty'    => 'decimal:2',
        'unit_price'  => 'decimal:2',
        'received_at' => 'datetime',
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