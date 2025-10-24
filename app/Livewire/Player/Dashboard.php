<?php

namespace App\Livewire\Player;

use Livewire\Component;

class Dashboard extends Component
{
    public $player;
    public $balance;
    public $activeBonuses;
    public $referralsCount;
    public $referralCode;
    public $recentTransactions;
    public $pendingTransactions;
    
    public function mount()
    {
        $this->player = auth()->guard('player')->user();
        
        // Cargar datos
        $this->balance = $this->player->balance;
        $this->referralCode = $this->player->referral_code;
        
        // Bonos activos (podemos implementar esto después)
        $this->activeBonuses = $this->player->bonuses()
            ->where('status', 'active')
            ->count();
        
        // Cantidad de referidos
        $this->referralsCount = $this->player->referrals()->count();
        
        // Últimas 5 transacciones
        $this->recentTransactions = $this->player->transactions()
            ->latest()
            ->take(5)
            ->get();
        
        // Transacciones pendientes
        $this->pendingTransactions = $this->player->transactions()
            ->where('status', 'pending')
            ->count();
    }

    public function copyReferralCode()
    {
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Código copiado al portapapeles'
        ]);
    }

    public function render()
    {
        return view('livewire.player.dashboard')
            ->layout('components.layouts.player');
    }
}