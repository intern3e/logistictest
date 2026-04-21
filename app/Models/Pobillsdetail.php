<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pobillsdetail extends Model
{
    use HasFactory;
    protected $table = 'pobills_detail';
    protected $primaryKey = 'po_detail_id';
    public $timestamps = false;
    protected $fillable = [
        'po_detail_id',
        'po_id',
        'item_id',
        'item_name',
        'quantity',
        'unit_price',
    ];
}
