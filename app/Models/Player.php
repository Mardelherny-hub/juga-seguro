<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Player extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'balance',
        'referral_code',
        'referred_by',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_activity_at' => 'datetime',
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
}