<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WheelSpin extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'player_id',
        'prize_amount',
        'prize_type',
        'bonus_id',
        'prize_description',
    ];

    protected $casts = [
        'prize_amount' => 'decimal:2',
    ];

    // Relaciones
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }
}