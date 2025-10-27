<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnblockRequest extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'player_id',
        'reason',
        'status',
        'reviewed_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Relaciones
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Métodos
    public function approve(User $admin, ?string $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        // Desbloquear jugador
        $this->player->activate();

        // Activity log
        activity()
            ->performedOn($this)
            ->causedBy($admin)
            ->withProperties([
                'player_id' => $this->player_id,
                'notes' => $notes,
            ])
            ->log('Solicitud de desbloqueo aprobada');

        return $this;
    }

    public function reject(User $admin, ?string $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        // Activity log
        activity()
            ->performedOn($this)
            ->causedBy($admin)
            ->withProperties([
                'player_id' => $this->player_id,
                'notes' => $notes,
            ])
            ->log('Solicitud de desbloqueo rechazada');

        return $this;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}