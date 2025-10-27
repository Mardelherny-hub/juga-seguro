<?php

namespace App\Livewire\Player;

use App\Models\Player;
use Livewire\Component;

class PlayerReferrals extends Component
{
    public function getReferrals()
    {
        $player = auth()->guard('player')->user();
        
        return Player::where('referred_by', $player->id)
            ->withCount(['transactions as deposits_count' => function($q) {
                $q->where('type', 'deposit')->where('status', 'completed');
            }])
            ->withSum(['transactions as deposits_sum' => function($q) {
                $q->where('type', 'deposit')->where('status', 'completed');
            }], 'amount')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStats()
    {
        $player = auth()->guard('player')->user();
        $referrals = $this->getReferrals();
        
        // Bonos ganados por referidos
        $referralBonuses = $player->bonuses()
            ->where('type', 'referral')
            ->sum('amount');
        
        return [
            'total_referrals' => $referrals->count(),
            'active_referrals' => $referrals->where('status', 'active')->count(),
            'total_deposits' => $referrals->sum('deposits_sum'),
            'referral_bonuses' => $referralBonuses,
        ];
    }

    public function copyReferralCode()
    {
        $this->dispatch('code-copied');
        session()->flash('copied', true);
    }

    public function render()
    {
        $player = auth()->guard('player')->user();
        
        return view('livewire.player.player-referrals', [
            'player' => $player,
            'referrals' => $this->getReferrals(),
            'stats' => $this->getStats(),
        ]);
    }
}