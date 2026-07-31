<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class internal_poline extends Model
{
    protected $table      = 'internal_poline';
    public    $timestamps = false;

    protected $fillable = [
        'internal_id', 'SO_id', 'item_id', 'item_name',
        'item_quantity', 'item_average', 'item_total'
    ];

    protected $casts = [
        'item_quantity' => 'float',
        'item_average'  => 'float',
        'item_total'    => 'float',
    ];

    public function head()
    {
        return $this->belongsTo(internal_po::class, 'internal_id', 'internal_id');
    }
}