<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public    $timestamps   = false;
    const     CREATED_AT    = 'created_at';
    protected $appends      = ['job_type', 'clean_note'];

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

    public function getJobTypeAttribute(): string
    {
        $note = (string) ($this->attributes['note'] ?? '');

        if (preg_match('/^\s*\[([a-zA-Z0-9_-]+)\]/', $note, $matches)) {
            return $matches[1] ?: 'general';
        }

        return 'general';
    }

    public function getCleanNoteAttribute(): string
    {
        $note = (string) ($this->attributes['note'] ?? '');
        return trim(preg_replace('/^\s*\[[a-zA-Z0-9_-]+\]\s*/', '', $note) ?? $note);
    }
}
