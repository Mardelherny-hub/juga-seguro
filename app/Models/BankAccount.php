<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class BankAccount extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'account_holder',
        'bank_name',
        'alias',
        'cbu',
        'cvu',
        'notes',
        'is_active',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relación con tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Scope para cuenta activa
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }
}