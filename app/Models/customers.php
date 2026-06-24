<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customers extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $primaryKey = 'ID';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'seller_code',
        'seller_name',
        'customer_code',
        'customer_name',
        'address',
        'tax_id',
        'branch',
        'branch_type',
        'contact_name',
        'email',
        'phone',
        'fax'
    ];
}