<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot del trait - se ejecuta automáticamente
     */
    protected static function bootBelongsToTenant()
    {
        // Al crear un registro, asignar automáticamente el tenant actual
        static::creating(function (Model $model) {
            if (!$model->tenant_id && app()->has('tenant')) {
                $model->tenant_id = app('tenant')->id;
            }
        });

        // Global scope: solo mostrar registros del tenant actual
        static::addGlobalScope('tenant', function (Builder $query) {
            if (app()->has('tenant')) {
                $query->where('tenant_id', app('tenant')->id);
            }
        });
    }

    /**
     * Relación con Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope para ver todos los tenants (solo super admin)
     */
    public function scopeWithAllTenants(Builder $query)
    {
        return $query->withoutGlobalScope('tenant');
    }
}