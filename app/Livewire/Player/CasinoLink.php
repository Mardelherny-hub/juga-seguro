<?php

namespace App\Livewire\Player;

use Livewire\Component;

class CasinoLink extends Component
{
    public $showModal = false;
    public $player;
    public $tenant;

    protected $listeners = ['openCasinoModal' => 'openModal'];

    public function mount()
    {
        $this->player = auth()->guard('player')->user();
        $this->tenant = $this->player->tenant;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function logCasinoAccess()
    {
        activity()
            ->performedOn($this->player)
            ->causedBy($this->player)
            ->log('Accedió al casino');
        
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.player.casino-link');
    }
}