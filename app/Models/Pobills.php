<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pobills extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'po_detail_id';
}
