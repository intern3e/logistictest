<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPooutsidereturn extends Model
{
    use HasFactory;
    protected $table = 'DetailPooutsidereturn'; 
    protected $fillable = ['return_id','inovice','product_name','quantity'];
    public $incrementing = false;
    public $timestamps = false; 
}
