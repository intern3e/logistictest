<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblsos extends Model
{
    use HasFactory;
    protected $fillable = ['so_id', 'customer_id', 'so_item_id'];
    public function customer()
    {
        return $this->belongsTo(tblcustomer::class, 'customer_id');
    }
}
