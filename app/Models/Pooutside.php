<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pooutside extends Model
{
    use HasFactory;
    protected $table = 'pooutside'; 
    protected $fillable = ['date_invice','invice','name','quantity','ponum','idvendor','name_vendor'];
    public $incrementing = false;
    public $timestamps = false; 
}
