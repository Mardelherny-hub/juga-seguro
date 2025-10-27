<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'custom_domain',
        'logo_url',
        'primary_color',
        'secondary_color',
        'whatsapp_token',
        'whatsapp_number',
        'settings',
        'bank_accounts',
        'is_active',
        'subscription_type',
        'monthly_fee',
        'chips_balance',
        'chip_price',
        'welcome_bonus_enabled',
        'welcome_bonus_amount',
        'referral_bonus_enabled',
        'referral_bonus_amount',        
        'referral_bonus_enabled' => 'boolean',
        'referral_bonus_amount' => 'decimal:2',
    ];

    protected $casts = [
        'settings' => 'array',
        'bank_accounts' => 'array',
        'is_active' => 'boolean',
        'welcome_bonus_enabled' => 'boolean',
        'welcome_bonus_amount' => 'decimal:2',
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helpers
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function getLogoAttribute()
    {
        return $this->logo_url ?? asset('images/default-logo.png');
    }

    /**
     * Obtener la URL activa del tenant
     */
    public function getActiveUrl(): string
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain;
        }
        
        return 'https://' . $this->domain . '.' . config('app.domain');
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function activeBankAccount()
    {
        return $this->hasOne(BankAccount::class)->where('is_active', true)->where('status', 'active');
    }
}