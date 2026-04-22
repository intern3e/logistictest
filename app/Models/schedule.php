<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'so_number',
        'customer_name',
        'job_title',
        'job_location',
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