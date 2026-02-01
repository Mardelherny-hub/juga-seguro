<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMessage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'player_id',
        'sender_type',
        'sender_id',
        'message',
        'image_path',
        'category',
        'transaction_id',
        'read_by_player_at',
        'read_by_agent_at',
    ];

    protected $casts = [
        'read_by_player_at' => 'datetime',
        'read_by_agent_at' => 'datetime',
    ];

    // Relaciones
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sender()
    {
        // Devuelve Player o User según sender_type
        if ($this->sender_type === 'player') {
            return $this->belongsTo(Player::class, 'sender_id');
        }
        
        if ($this->sender_type === 'agent') {
            return $this->belongsTo(User::class, 'sender_id');
        }
        
        return null; // system no tiene sender
    }

    // Métodos
    public function markAsReadByPlayer()
    {
        if (!$this->read_by_player_at) {
            $this->update(['read_by_player_at' => now()]);
        }
    }

    public function markAsReadByAgent()
    {
        if (!$this->read_by_agent_at) {
            $this->update(['read_by_agent_at' => now()]);
        }
    }

    public function isFromSystem(): bool
    {
        return $this->sender_type === 'system';
    }

    public function isFromPlayer(): bool
    {
        return $this->sender_type === 'player';
    }

    public function isFromAgent(): bool
    {
        return $this->sender_type === 'agent';
    }

    // Scopes
    public function scopeUnreadByPlayer($query)
    {
        return $query->whereNull('read_by_player_at');
    }

    public function scopeUnreadByAgent($query)
    {
        return $query->whereNull('read_by_agent_at');
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        
        return asset('storage/' . $this->image_path);
    }
}