<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuth extends Model
{
    protected $table      = 'user_auth';
    protected $primaryKey = 'id_emp';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = ['id_emp', 'name', 'username', 'password', 'auth','page'];
    public static function nextIdEmp(): string
    {
        $last = static::orderByRaw("CAST(SUBSTRING(id_emp, 5) AS UNSIGNED) DESC")->value('id_emp');
        $num  = $last ? (int) substr($last, 4) + 1 : 1;
        return 'emp-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}