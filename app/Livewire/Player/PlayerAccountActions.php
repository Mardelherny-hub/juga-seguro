<?php

namespace App\Livewire\Player;

use App\Models\Player;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Livewire\Traits\WithToast;

class PlayerAccountActions extends Component
{
        use WithToast;

    #[Reactive]
    public Player $player;
    public bool $showCreateUserModal = false;
    public bool $showUnlockModal = false;
    public bool $showPasswordResetModal = false;
    // Opción de creación de cuenta
    public string $accountCreationType = 'new'; // 'new' o 'existing'
    public string $existingUsername = '';

    public function requestAccountCreation()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->showToast('Ya tienes una solicitud pendiente. Espera a que sea procesada.'. 'error');
            return;
        }

        // Validar según el tipo
        if ($this->accountCreationType === 'existing') {
            // Validar que ingresó username
            if (empty($this->existingUsername)) {
                $this->showToast('Debes ingresar tu nombre de usuario existente.', 'error');
                return;
            }
            
            // Validar formato
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]{3,14}$/', $this->existingUsername)) {
                $this->showToast('El formato del usuario no es válido.', 'error');
                return;
            }
            
            $notes = "YA TIENE USUARIO: {$this->existingUsername}";
        } else {
            $notes = 'Solicitud de creación de usuario en plataforma de juego';
        }

        Transaction::create([
            'tenant_id' => $this->player->tenant_id,
            'player_id' => $this->player->id,
            'type' => 'account_creation',
            'amount' => 0,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance,
            'status' => 'pending',
            'notes' => $notes,
            'transaction_hash' => Str::uuid(),
        ]);

        activity()
            ->performedOn($this->player)
            ->causedBy(auth()->user())
            ->log('Solicitud de creación de usuario generada');

        $this->showToast('Solicitud generada correctamente.', 'success');
        
        
        // Resetear campos
        $this->accountCreationType = 'new';
        $this->existingUsername = '';
        $this->showCreateUserModal = false;
    }

    public function requestAccountUnlock()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Ya tienes una solicitud pendiente. Espera a que sea procesada.'
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
            'message' => 'Solicitud de desbloqueo generada correctamente.',
            'persistent' => true
        ]);

        $this->showUnlockModal = false;
    }

    public function requestPasswordReset()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->showToast('Ya tienes una solicitud pendiente. Espera a que sea procesada.', 'error');
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

        $this->showToast('Solicitud de cambio de contraseña generada correctamente.', 'succes');

        $this->showPasswordResetModal = false;
    }

    public function openCreateUserModal()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->showToast('Ya tienes una solicitud pendiente. Espera a que sea procesada.', 'error');
            return;
        }

        $this->showCreateUserModal = true;
    }

    public function openUnlockModal()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->showToast('Ya tienes una solicitud pendiente. Espera a que sea procesada.', 'error');
            return;
        }

        $this->showUnlockModal = true;
    }

    public function openPasswordResetModal()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $pending = Transaction::where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            $this->showToast('Ya tienes una solicitud pendiente. Espera a que sea procesada.', 'error');
            return;
        }

        $this->showPasswordResetModal = true;
    }

    public function render()
    {
        return view('livewire.player.player-account-actions');
    }
}