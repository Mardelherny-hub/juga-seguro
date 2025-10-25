<?php

namespace App\Livewire\Player;

use Livewire\Component;

class BalanceDisplay extends Component
{
    public $balance;

    protected $listeners = [
        'refreshBalance' => 'updateBalance',
        'balanceUpdated' => 'updateBalance',
        'transactionProcessed' => 'updateBalance'
    ];
    public function mount()
    {
        $this->updateBalance();
    }

    public function updateBalance()
    {
        $player = auth()->guard('player')->user();
        if ($player) {
            $player->refresh();
            $this->balance = $player->balance;
            $this->dispatch('$refresh');
        }
    }

    public function render()
    {
        return view('livewire.player.balance-display');
    }
}