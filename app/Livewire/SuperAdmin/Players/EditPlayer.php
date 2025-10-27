<?php

namespace App\Livewire\SuperAdmin\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Livewire\Traits\WithToast;
use Illuminate\Support\Facades\Hash;

class EditPlayer extends Component
{
    use WithToast;

    public $showModal = false;
    public $playerId = null;
    public $player = null;
    
    public $name = '';
    public $email = '';
    public $phone = '';
    public $username = '';
    
    // Campos de contraseña
    public $password = '';
    public $password_confirmation = '';
    
    // Datos originales para el activity log
    public $originalData = [];

    #[On('openEditPlayer')]
    public function openModal($playerId)
    {
        $this->playerId = $playerId;
        $this->player = Player::findOrFail($playerId);
        
        // Cargar datos actuales
        $this->name = $this->player->name;
        $this->username = $this->player->username;
        $this->email = $this->player->email;
        $this->phone = $this->player->phone;
        
        // Resetear campos de contraseña
        $this->password = '';
        $this->password_confirmation = '';
        
        // Guardar datos originales
        $this->originalData = [
            'name' => $this->player->name,
            'username' => $this->player->username,
            'email' => $this->player->email,
            'phone' => $this->player->phone,
        ];
        
        $this->showModal = true;
    }

    public function updatePlayer()
    {
        $tenantId = $this->player->tenant_id;
        
        $this->validate([
            'name' => 'required|min:3',
            'username' => [
                'required',
                'min:4',
                'max:15',
                'regex:/^[a-zA-Z][a-zA-Z0-9]*$/',
                Rule::unique('players', 'username')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->playerId)
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('players', 'email')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->playerId)
            ],
            'phone' => [
                'required',
                Rule::unique('players', 'phone')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->playerId)
            ],
            'password' => 'nullable|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.min' => 'El usuario debe tener al menos 4 caracteres',
            'username.max' => 'El usuario no puede tener más de 15 caracteres',
            'username.regex' => 'El usuario debe empezar con letra y solo contener letras y números',
            'username.unique' => 'Este nombre de usuario ya está registrado',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.unique' => 'Este teléfono ya está registrado',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Preparar datos para actualizar
        $updateData = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'phone' => $this->phone,
        ];

        // Solo agregar password si se ingresó
        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        // Actualizar datos
        $this->player->update($updateData);

        // Preparar datos para activity log
        $changes = [];
        if ($this->originalData['name'] !== $this->name) {
            $changes['name'] = ['before' => $this->originalData['name'], 'after' => $this->name];
        }
        if ($this->originalData['username'] !== $this->username) {
            $changes['username'] = ['before' => $this->originalData['username'], 'after' => $this->username];
        }
        if ($this->originalData['email'] !== $this->email) {
            $changes['email'] = ['before' => $this->originalData['email'], 'after' => $this->email];
        }
        if ($this->originalData['phone'] !== $this->phone) {
            $changes['phone'] = ['before' => $this->originalData['phone'], 'after' => $this->phone];
        }
        
        // Registrar cambio de contraseña
        if (!empty($this->password)) {
            $changes['password'] = 'Contraseña actualizada por Super Admin';
        }

        // Registrar en activity log solo si hubo cambios
        if (!empty($changes)) {
            activity()
                ->performedOn($this->player)
                ->causedBy(auth()->user())
                ->withProperties([
                    'changes' => $changes,
                    'updated_by' => 'super_admin'
                ])
                ->log('Información del jugador actualizada por Super Admin');
        }

        $this->showToast('Información actualizada correctamente', 'success');

        $this->closeModal();
        $this->dispatch('playerUpdated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['playerId', 'player', 'name', 'username', 'email', 'phone', 'password', 'password_confirmation']);
        $this->originalData = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.super-admin.players.edit-player');
    }
}