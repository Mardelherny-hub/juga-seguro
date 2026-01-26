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
        'username',
        'phone',
        'email',
        'password',
        'balance',
        'referral_code',
        'referred_by',
        'status',
        'casino_linked',
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
        'casino_linked' => 'boolean',
    ];

    // Auto-generar código de referido y normalizar username
    protected static function booted()
    {
        static::creating(function (Player $player) {
            // Auto-generar código de referido
            if (!$player->referral_code) {
                $player->referral_code = strtoupper(Str::random(8));
            }
            
            // Normalizar username a minúsculas
            if ($player->username) {
                $player->username = strtolower($player->username);
            }
        });
        
        static::updating(function (Player $player) {
            // Normalizar username si cambió
            if ($player->isDirty('username')) {
                $player->username = strtolower($player->username);
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

    public function unblockRequests()
    {
        return $this->hasMany(UnblockRequest::class);
    }

    public function pendingUnblockRequest()
    {
        return $this->hasOne(UnblockRequest::class)->where('status', 'pending');
    }

    public function hasPendingUnblockRequest(): bool
    {
        return $this->unblockRequests()->where('status', 'pending')->exists();
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

    public function withdrawalAccounts()
    {
        return $this->hasMany(PlayerWithdrawalAccount::class);
    }

    public function defaultWithdrawalAccount()
    {
        return $this->hasOne(PlayerWithdrawalAccount::class)->where('is_default', true);
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

    public function isCasinoLinked(): bool
    {
        return $this->casino_linked === true;
    }

    public function linkCasino(): void
    {
        $this->update(['casino_linked' => true]);
        
        activity()
            ->performedOn($this)
            ->log('Usuario vinculado al casino');
    }

    /**
     * Obtener el nombre para mostrar (username preferido, name como fallback)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->username ?: $this->name ?: 'Sin nombre';
    }
}