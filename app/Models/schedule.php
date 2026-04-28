<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public    $timestamps   = false;
    const     CREATED_AT    = 'created_at';
    protected $fillable = [
        'so_number',
        'customer_name',
        'job_title',
        'job_location',
        'job_la_long',
        'team_name',
        'start_date',
        'end_date',
        'status',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}