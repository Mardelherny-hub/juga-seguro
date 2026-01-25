<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use App\Livewire\Traits\WithTenantContext;

class BonusSettings extends Component
{
    use WithTenantContext;
    public $showModal = false;
    
    // Bono de bienvenida
    public $welcome_bonus_enabled = false;
    public $welcome_bonus_amount = 0;
    public $welcome_bonus_is_percentage = false;
    public $welcome_bonus_max = null;
    
    // Bono de referido
    public $referral_bonus_enabled = false;
    public $referral_bonus_amount = 0;
    public $referral_bonus_target = 'both';

    // Configuración de retiros
    public $min_withdrawal = 500;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant) {
            $this->welcome_bonus_enabled = (bool) $tenant->welcome_bonus_enabled;
            $this->welcome_bonus_amount = (float) $tenant->welcome_bonus_amount;
            $this->welcome_bonus_is_percentage = (bool) $tenant->welcome_bonus_is_percentage;
            $this->welcome_bonus_max = $tenant->welcome_bonus_max;
            $this->referral_bonus_enabled = (bool) $tenant->referral_bonus_enabled;
            $this->referral_bonus_amount = (float) $tenant->referral_bonus_amount;
            $this->referral_bonus_target = $tenant->referral_bonus_target ?? 'both';
            $this->min_withdrawal = (float) ($tenant->min_withdrawal ?? 500);
        }
        
        // Debug
        logger('Bonus settings loaded', [
            'welcome_enabled' => $this->welcome_bonus_enabled,
            'welcome_amount' => $this->welcome_bonus_amount,
            'referral_enabled' => $this->referral_bonus_enabled,
            'referral_amount' => $this->referral_bonus_amount,
        ]);
    }

    public function openModal()
    {
        $this->loadSettings();
        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        logger('Saving bonus settings', [
            'welcome_enabled' => $this->welcome_bonus_enabled,
            'welcome_amount' => $this->welcome_bonus_amount,
            'referral_enabled' => $this->referral_bonus_enabled,
            'referral_amount' => $this->referral_bonus_amount,
        ]);
        
        $this->validate([
            'welcome_bonus_amount' => 'nullable|numeric|min:0|max:999999',
            'referral_bonus_amount' => 'nullable|numeric|min:0|max:999999',
            'min_withdrawal' => 'required|numeric|min:0|max:999999',
        ], [
            'welcome_bonus_amount.numeric' => 'El monto debe ser un número',
            'welcome_bonus_amount.min' => 'El monto no puede ser negativo',
            'welcome_bonus_amount.max' => 'El monto máximo es $999,999',
            'referral_bonus_amount.numeric' => 'El monto debe ser un número',
            'referral_bonus_amount.min' => 'El monto no puede ser negativo',
            'referral_bonus_amount.max' => 'El monto máximo es $999,999',
            'min_withdrawal.required' => 'El monto mínimo es obligatorio',
            'min_withdrawal.min' => 'El monto no puede ser negativo',
        ]);

        $tenant = auth()->user()->tenant;
        
        $tenant->update([
            'welcome_bonus_enabled' => $this->welcome_bonus_enabled,
            'welcome_bonus_amount' => $this->welcome_bonus_amount ?? 0,
            'welcome_bonus_is_percentage' => $this->welcome_bonus_is_percentage,
            'welcome_bonus_max' => $this->welcome_bonus_max,
            'referral_bonus_enabled' => $this->referral_bonus_enabled,
            'referral_bonus_amount' => $this->referral_bonus_amount ?? 0,
            'referral_bonus_target' => $this->referral_bonus_target,
            'min_withdrawal' => $this->min_withdrawal ?? 500,
        ]);

        // Activity log
        activity()
            ->performedOn($tenant)
            ->causedBy(auth()->user())
            ->withProperties([
                'welcome_bonus_enabled' => $this->welcome_bonus_enabled,
                'welcome_bonus_amount' => $this->welcome_bonus_amount,
                'referral_bonus_enabled' => $this->referral_bonus_enabled,
                'referral_bonus_amount' => $this->referral_bonus_amount,
                'referral_bonus_target' => $this->referral_bonus_target,
            ])
            ->log('Configuración de bonos actualizada');

        session()->flash('success', 'Configuración de bonos actualizada correctamente');
        
        $this->closeModal();
        $this->dispatch('bonusSettingsUpdated');
    }

    public function render()
    {
        return view('livewire.agent.bonus-settings');
    }
}