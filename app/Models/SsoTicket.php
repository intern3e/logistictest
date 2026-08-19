<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoTicket extends Model
{
    protected $table = 'sso_tickets';
    public $timestamps = false;

    protected $fillable = ['ticket', 'id_emp', 'client_key', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    /**
     * Mark ticket ว่าใช้แล้ว (Atomic ป้องกัน race condition)
     */
    public function markAsUsed(): bool
    {
        $affected = static::where('id', $this->id)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->update(['used' => true]);

        return $affected === 1;
    }
}