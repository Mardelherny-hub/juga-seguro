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
        'casino_url',
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
        'min_withdrawal',
        'referral_bonus_enabled' => 'boolean',
        'referral_bonus_amount' => 'decimal:2',
        'referral_bonus_target',
        'maintenance_mode',
        'maintenance_message',
        'maintenance_block_operations',
        'welcome_bonus_is_percentage',
        'welcome_bonus_max',
    ];

    protected $casts = [
        'settings' => 'array',
        'bank_accounts' => 'array',
        'is_active' => 'boolean',
        'welcome_bonus_enabled' => 'boolean',
        'welcome_bonus_amount' => 'decimal:2',
        'min_withdrawal' => 'decimal:2',
        'referral_bonus_target' => 'string',
        'maintenance_mode' => 'boolean',
        'maintenance_block_operations' => 'boolean',
        'welcome_bonus_is_percentage' => 'boolean',
        'welcome_bonus_max' => 'decimal:2',
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

    public function getWhatsappLinkAttribute()
    {
        if (!$this->whatsapp_number) {
            return null;
        }
        
        return "https://wa.me/{$this->whatsapp_number}";
    }

    public function getFormattedWhatsappAttribute()
    {
        if (!$this->whatsapp_number) {
            return null;
        }
        
        // Formato: +54 9 223 4567890 -> +54 9 223 456-7890
        $number = $this->whatsapp_number;
        
        if (strlen($number) >= 10) {
            return '+' . substr($number, 0, 2) . ' ' . 
                substr($number, 2, 1) . ' ' . 
                substr($number, 3, 3) . ' ' . 
                substr($number, 6, 3) . '-' . 
                substr($number, 9);
        }
        
        return '+' . $number;
    }
}