<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class Player extends Authenticatable
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'password',
        'balance',
        'referral_code',
        'referred_by',
        'status',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_activity_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Auto-generar código de referido
    protected static function booted()
    {
        static::creating(function (Player $player) {
            if (!$player->referral_code) {
                $player->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    // Relaciones
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function referrer()
    {
        return $this->belongsTo(Player::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(Player::class, 'referred_by');
    }


    public function messages()
    {
        return $this->hasMany(PlayerMessage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Métodos de negocio
    public function updateActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function suspend($reason = null)
    {
        $this->update(['status' => 'suspended']);
        activity()
            ->performedOn($this)
            ->withProperties(['reason' => $reason])
            ->log('player_suspended');
    }

    public function block($reason = null)
    {
        $this->update(['status' => 'blocked']);
        activity()
            ->performedOn($this)
            ->withProperties(['reason' => $reason])
            ->log('player_blocked');
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
        activity()
            ->performedOn($this)
            ->log('player_activated');
    }

    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function verifyReferralCode(string $code): bool
    {
        return self::where('referral_code', $code)
            ->where('tenant_id', $this->tenant_id)
            ->where('id', '!=', $this->id)
            ->exists();
    }
}