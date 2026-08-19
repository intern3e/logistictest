<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAuth extends Authenticatable
{
    protected $table      = 'user_auth';
    protected $primaryKey = 'id_emp';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'id_emp', 'name', 'username', 'password',
        'auth', 'role', 'permissions', 'page',
        'auth_version', 'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // ★ สำคัญ: บอก Laravel ว่าคอลัมน์ password ชื่ออะไร
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function hasPermission(string $permission): bool
    {
        $perms = $this->permissions ?? [];
        return in_array($permission, $perms);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAuth(string $auth): bool
    {
        return $this->auth === $auth;
    }

    public function incrementAuthVersion(): void
    {
        $this->increment('auth_version');
    }

    public static function nextIdEmp(): string
    {
        $last = static::orderByRaw("CAST(id_emp AS UNSIGNED) DESC")->value('id_emp');
        $num  = $last ? (int) $last + 1 : 1;
        return str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}