<?php

namespace App\Livewire\Agent\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class PlayerActions extends Component
{
    use WithToast;

    public $showSuspendModal = false;
    public $showBlockModal = false;
    public $showActivateModal = false;
    
    public $playerId = null;
    public $player = null;
    public $reason = '';

    #[On('openSuspendPlayer')]
    public function openSuspendModal($playerId)
    {
        $this->playerId = $playerId;
        $this->player = Player::findOrFail($playerId);
        $this->reason = '';
        $this->showSuspendModal = true;
    }

    #[On('openBlockPlayer')]
    public function openBlockModal($playerId)
    {
        $this->playerId = $playerId;
        $this->player = Player::findOrFail($playerId);
        $this->reason = '';
        $this->showBlockModal = true;
    }

    #[On('openActivatePlayer')]
    public function openActivateModal($playerId)
    {
        $this->playerId = $playerId;
        $this->player = Player::findOrFail($playerId);
        $this->showActivateModal = true;
    }

    public function suspendPlayer()
    {
        if (!$this->player) return;

        $this->player->suspend($this->reason);

        // Activity log
        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $this->reason])
            ->log('Jugador suspendido');

        $this->showToast('Jugador suspendido correctamente', 'success');

        $this->closeSuspendModal();
        $this->dispatch('playerUpdated');
        $this->dispatch('$refresh');
    }

    public function blockPlayer()
    {
        $this->validate([
            'reason' => 'required|min:10',
        ], [
            'reason.required' => 'El motivo es obligatorio',
            'reason.min' => 'El motivo debe tener al menos 10 caracteres',
        ]);

        if (!$this->player) return;

        $this->player->block($this->reason);

        // Activity log
        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $this->reason])
            ->log('Jugador bloqueado');

        $this->showToast('Jugador bloqueado correctamente', 'success');

        $this->closeBlockModal();
        $this->dispatch('playerUpdated');
        $this->dispatch('$refresh');
    }

    public function activatePlayer()
    {
        if (!$this->player) return;

        $this->player->activate();

        // Activity log
        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->log('Jugador activado');

        $this->showToast('Jugador activado correctamente', 'success');

        $this->closeActivateModal();
        $this->dispatch('playerUpdated');
        $this->dispatch('$refresh');
    }

    public function closeSuspendModal()
    {
        $this->showSuspendModal = false;
        $this->reset(['playerId', 'player', 'reason']);
    }

    public function closeBlockModal()
    {
        $this->showBlockModal = false;
        $this->reset(['playerId', 'player', 'reason']);
    }

    public function closeActivateModal()
    {
        $this->showActivateModal = false;
        $this->reset(['playerId', 'player']);
    }

    public function render()
    {
        return view('livewire.agent.players.player-actions');
    }
}