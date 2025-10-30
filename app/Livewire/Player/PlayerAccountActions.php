<?php

namespace App\Livewire\Player;

use App\Models\Player;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\Component;

class PlayerAccountActions extends Component
{
    #[Reactive]
    public Player $player;
    public bool $showCreateUserModal = false;
    public bool $showUnlockModal = false;
    public bool $showPasswordResetModal = false;

    public function requestAccountCreation()
    {
        // Verificar que no tenga solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('type', 'account_creation')
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Ya existe una solicitud de creación de usuario pendiente.'
            ]);
            return;
        }

        Transaction::create([
            'tenant_id' => $this->player->tenant_id,
            'player_id' => $this->player->id,
            'type' => 'account_creation',
            'amount' => 0,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance,
            'status' => 'pending',
            'notes' => 'Solicitud de creación de usuario en plataforma de juego',
            'transaction_hash' => Str::uuid(),
        ]);

        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->log('Solicitud de creación de usuario generada');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Solicitud de creación de usuario generada correctamente.'
        ]);

        $this->showCreateUserModal = false;
    }

    public function requestAccountUnlock()
    {
        // Verificar que no tenga solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('type', 'account_unlock')
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Ya existe una solicitud de desbloqueo pendiente.'
            ]);
            return;
        }

        Transaction::create([
            'tenant_id' => $this->player->tenant_id,
            'player_id' => $this->player->id,
            'type' => 'account_unlock',
            'amount' => 0,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance,
            'status' => 'pending',
            'notes' => 'Solicitud de desbloqueo de usuario en plataforma de juego',
            'transaction_hash' => Str::uuid(),
        ]);

        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->log('Solicitud de desbloqueo de usuario generada');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Solicitud de desbloqueo generada correctamente.'
        ]);

        $this->showUnlockModal = false;
    }

    public function requestPasswordReset()
    {
        // Verificar que no tenga solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('type', 'password_reset')
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Ya existe una solicitud de cambio de contraseña pendiente.'
            ]);
            return;
        }

        Transaction::create([
            'tenant_id' => $this->player->tenant_id,
            'player_id' => $this->player->id,
            'type' => 'password_reset',
            'amount' => 0,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance,
            'status' => 'pending',
            'notes' => 'Solicitud de cambio de contraseña - Nueva contraseña: bet123',
            'transaction_hash' => Str::uuid(),
        ]);

        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->log('Solicitud de cambio de contraseña generada');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Solicitud de cambio de contraseña generada correctamente.'
        ]);

        $this->showPasswordResetModal = false;
    }

    public function render()
    {
        return view('livewire.player.player-account-actions');
    }
}