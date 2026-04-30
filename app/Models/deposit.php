<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit extends Model
{
    protected $table = 'deposit';
    public $timestamps = false;
    protected $fillable = [
        'so_id',
        'date_dep',        
        'customer_id',
        'customer_name',
        'contactso',
        'customer_tel',
        'customer_address',
        'sale_name',
        'emp_name',
        'dep_type',   
        'dep_per',       
        'dep_price',      
        'grand_total',     
        'time',
        'print_time',      
        'status',          
        'status_bill', 
        'deposit_bill_id',
        'time_check',
        'deposit_bill',
        'po_document'
    ];
}