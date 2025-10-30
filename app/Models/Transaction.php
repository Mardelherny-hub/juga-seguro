<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'player_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'proof_url',
        'processed_by',
        'processed_at',
        'notes',
        'transaction_hash',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Auto-generar hash único
    protected static function booted()
    {
        static::creating(function (Transaction $transaction) {
            if (!$transaction->transaction_hash) {
                $transaction->transaction_hash = Str::uuid();
            }
        });
    }

    // Relaciones
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    // Métodos de negocio
    public function complete(User $user)
    {
        $this->update([
            'status' => 'completed',
            'processed_by' => $user->id,
            'processed_at' => now(),
        ]);

        activity()
            ->performedOn($this)
            ->causedBy($user)
            ->log('transaction_completed');
    }

    public function reject(User $user, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $user->id,
            'processed_at' => now(),
            'notes' => $reason,
        ]);

        activity()
            ->performedOn($this)
            ->causedBy($user)
            ->withProperties(['reason' => $reason])
            ->log('transaction_rejected');
    }

    // Helpers
    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->type === 'withdrawal';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccountCreation(): bool
    {
        return $this->type === 'account_creation';
    }

    public function isAccountUnlock(): bool
    {
        return $this->type === 'account_unlock';
    }

    public function isPasswordReset(): bool
    {
        return $this->type === 'password_reset';
    }

    public function isAccountRequest(): bool
    {
        return in_array($this->type, ['account_creation', 'account_unlock', 'password_reset']);
    }

    public function requiresCredentials(): bool
    {
        return in_array($this->type, ['account_creation', 'password_reset']);
    }
}