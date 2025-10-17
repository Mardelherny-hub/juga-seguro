<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'player_id',
        'type',
        'amount',
        'status',
        'expires_at',
        'used_at',
        'related_transaction_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relaciones
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function relatedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    // Métodos
    public function use()
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
    }
}