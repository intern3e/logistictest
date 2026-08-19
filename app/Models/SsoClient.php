<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoClient extends Model
{
    protected $table = 'sso_clients';
    protected $primaryKey = 'client_key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['client_key', 'client_secret', 'allowed_callbacks','callback_url'];

    /**
     * ตรวจสอบว่า URL นี้อยู่ใน allowed list หรือไม่
     */
    public function isCallbackAllowed(string $url): bool
    {
        $allowed = json_decode($this->allowed_callbacks, true) ?? [];
        foreach ($allowed as $pattern) {
            if (str_starts_with($url, $pattern)) {
                return true;
            }
        }
        return false;
    }
}