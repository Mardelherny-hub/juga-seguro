<?php

namespace App\Livewire\Agent\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithTenantContext;

class CreatePlayer extends Component
{

    use WithToast;
    use WithTenantContext;

    public $showModal = false;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $initial_balance = 0;
    public $username = '';
    public $password = '';

    #[On('openCreatePlayer')]
    
    public function openModal()
    {
        $this->reset(['name', 'email', 'phone', 'initial_balance', 'username']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function createPlayer()
    {
        $tenant = auth()->user()->tenant;
        $tenantId = $tenant->id;

        $this->validate([
            'name' => 'required|min:3',
            'username' => [  // NUEVO
                'required',
                'min:4',
                'max:15',
                'regex:/^[a-zA-Z][a-zA-Z0-9]*$/',
                Rule::unique('players', 'username')
                    ->where('tenant_id', $tenantId)
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('players', 'email')
                    ->where('tenant_id', $tenantId)
            ],
            'phone' => [
                'required',
                Rule::unique('players', 'phone')
                    ->where('tenant_id', $tenantId)
            ],
            'initial_balance' => 'nullable|numeric|min:0',
            'password' => 'nullable|min:6',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'username.required' => 'El nombre de usuario es obligatorio',  // NUEVO
            'username.min' => 'El usuario debe tener al menos 4 caracteres',  // NUEVO
            'username.max' => 'El usuario no puede tener más de 15 caracteres',  // NUEVO
            'username.regex' => 'El usuario debe empezar con letra y solo contener letras y números',  // NUEVO
            'username.unique' => 'Este nombre de usuario ya está registrado',  // NUEVO
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.unique' => 'Este teléfono ya está registrado',
            'initial_balance.numeric' => 'El saldo debe ser un número',
            'initial_balance.min' => 'El saldo no puede ser negativo',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);

        // Generar código de referido único
        do {
            $referralCode = strtoupper(Str::random(8));
        } while (Player::where('referral_code', $referralCode)->exists());

        // Crear jugador
        $player = Player::create([
            'tenant_id' => $tenantId,
            'name' => $this->name,
            'username' => $this->username,  // NUEVO
            'email' => $this->email ?: null,
            'phone' => $this->phone,
            'balance' => $this->initial_balance ?? 0,
            'referral_code' => $referralCode,
            'status' => 'active',
            'casino_linked' => true,
            'password' => $this->password ? bcrypt($this->password) : null,
        ]);

        // Activity log
        activity()
            ->performedOn($player)
            ->causedBy(auth()->user())
            ->log('Jugador creado desde panel admin');

        $this->showToast('Jugador creado correctamente', 'success');

        $this->closeModal();
        
        // AGREGAR ESTAS LÍNEAS:
        $this->dispatch('playerCreated');
        $this->dispatch('playerUpdated'); // ← IMPORTANTE: esto refresca la lista
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'email', 'phone', 'initial_balance', 'username', 'password']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.agent.players.create-player');
    }
}