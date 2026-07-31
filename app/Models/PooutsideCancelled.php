<?php
// app/Models/PooutsideCancelled.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PooutsideCancelled extends Model
{
    protected $table = 'pooutside_cancelled';
    public $timestamps = false;

    protected $fillable = [
        'po_id',
        'so_id',
        'cancelled_by',
        'cancelled_at',
        'note',
        'status',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];
}