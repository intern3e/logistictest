<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pooutside extends Model
{
    use HasFactory;
    protected $table = 'pooutside'; 
    protected $fillable = ['date_invoice','invoice','name','quantity','ponum'];
    public $incrementing = false;
    public $timestamps = false; 
}
