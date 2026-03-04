<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pooutsidereturn extends Model
{
    use HasFactory;
    protected $table = 'Pooutsidereturn'; 
    protected $fillable = ['return_id','return_date','po','vendor','status','reason','note'];
    protected $primaryKey = 'return_id';
    public    $keyType    = 'string'; 
    public $incrementing = false;
    public $timestamps = false; 
}