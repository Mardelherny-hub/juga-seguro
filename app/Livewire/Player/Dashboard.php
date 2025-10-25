<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Bonus;
use App\Models\Referral;
use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\WithToast;

class Dashboard extends Component
{
    use WithToast;

    public $player;
    public $balance;
    public $activeBonuses;
    public $referralsCount;
    public $referralCode;
    public $recentTransactions;
    public $pendingTransactions;
    
    protected $listeners = ['refreshDashboard' => 'loadData'];

    public function mount()
    {
        $this->player = auth()->guard('player')->user();
        $this->loadData();
    }

    public function loadData()
    {
        // Refrescar el player desde la BD
        $this->player->refresh();
        
        // Cargar datos
        $this->balance = $this->player->balance;
        $this->referralCode = $this->player->referral_code;
        
        // Bonos activos
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
        $this->showToast('Código copiado al portapapeles', 'success');
    }

    public function render()
    {
        return view('livewire.player.dashboard')
            ->layout('components.layouts.player');
    }
}