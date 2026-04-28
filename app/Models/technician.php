<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class technician extends Model
{
    protected $primaryKey = 'emp_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;
    const     CREATED_AT    = 'created_at';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'emp_name_eng',
        'emp_nickname',
        'emp_phone',
        'date_of_birth',
        'img',
        'status',
        'emp_company',
        'emp_position',
        'emp_team',
        'emp_skill',
        'licenses',
        'core_competencies',
        'software_tools',
    ];

    protected $casts = [
        'created_at'        => 'datetime',
        'date_of_birth'     => 'date',
        'licenses'          => 'array',
        'core_competencies' => 'array',
        'software_tools'    => 'array',
    ];
}