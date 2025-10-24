<?php

namespace App\Livewire\Agent\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\On;

class PlayerDetail extends Component
{
    public $showModal = false;
    public $player = null;
    public $transactions = [];
    public $referrals = [];
    public $activityLog = [];

    #[On('openPlayerDetail')]
    public function openModal($playerId)
    {
        $this->player = Player::with([
            'referrer:id,name',
            'referrals:id,name,balance,status,created_at',
            'transactions' => fn($q) => $q->latest()->limit(10),
            'bonuses' => fn($q) => $q->latest()->limit(10)
        ])->findOrFail($playerId);

        $this->transactions = $this->player->transactions;
        $this->referrals = $this->player->referrals;
        
        // Obtener activity log
        $this->activityLog = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', Player::class)
            ->where('subject_id', $this->player->id)
            ->latest()
            ->limit(10)
            ->get();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->player = null;
        $this->transactions = [];
        $this->referrals = [];
        $this->activityLog = [];
    }

    public function copyReferralCode()
    {
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Código copiado al portapapeles'
        ]);
    }

    public function getTotalDeposits()
    {
        if (!$this->player) return 0;
        
        return $this->player->transactions()
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTotalWithdrawals()
    {
        if (!$this->player) return 0;
        
        return $this->player->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getTotalBonuses()
    {
        if (!$this->player) return 0;
        
        return $this->player->bonuses()
            ->where('status', 'used')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.agent.players.player-detail', [
            'totalDeposits' => $this->getTotalDeposits(),
            'totalWithdrawals' => $this->getTotalWithdrawals(),
            'totalBonuses' => $this->getTotalBonuses(),
        ]);
    }
}