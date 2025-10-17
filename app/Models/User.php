<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }
}