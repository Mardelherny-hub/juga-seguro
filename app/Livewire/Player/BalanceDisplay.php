<?php

namespace App\Livewire\Player;

use Livewire\Component;

class BalanceDisplay extends Component
{
    public $balance;

    protected $listeners = ['refreshBalance' => 'updateBalance'];

    public function mount()
    {
        $this->updateBalance();
    }

    public function updateBalance()
    {
        $player = auth()->guard('player')->user();
        $player->refresh();
        $this->balance = $player->balance;
    }

    public function render()
    {
        return view('livewire.player.balance-display');
    }
}