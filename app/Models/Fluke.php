<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fluke extends Model
{
    protected $table = 'fluke';   
    public $timestamps = false;    

    protected $primaryKey = 'iditem'; 
    public $incrementing = false;     
    protected $keyType = 'string';   

    protected $fillable = [
        'iditem', 'pic', 'name', 'model', 
        'priceUSD', 'priceTHB', 'category', 
        'Availability', 'quantity'
    ];
}
