<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class internal_po extends Model
{
    protected $table        = 'internal_po';
    protected $primaryKey   = 'internal_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'internal_id', 'SO_id', 'customer_code',
        'customer_name', 'create_by', 'timestamp',
    ];

    public function lines()
    {
        return $this->hasMany(internal_poline::class, 'internal_id', 'internal_id');
    }

    public static function genInternalId()
    {
        $yy     = (date('Y') + 543) % 100;
        $prefix = sprintf('%02d%02d-A', $yy, date('m'));

        $last = static::where('internal_id', 'LIKE', $prefix . '%')
            ->lockForUpdate()
            ->orderBy('internal_id', 'desc')
            ->value('internal_id');

        $run = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . sprintf('%04d', $run);
    }
}