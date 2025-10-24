<?php

namespace App\Livewire\Agent\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreatePlayer extends Component
{
    public $showModal = false;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $initial_balance = 0;

    #[On('openCreatePlayer')]
    
    public function openModal()
    {
        $this->reset(['name', 'email', 'phone', 'initial_balance']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function createPlayer()
    {
        $tenant = auth()->user()->tenant;
        $tenantId = $tenant->id;

        $this->validate([
            'name' => 'required|min:3',
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
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.unique' => 'Este teléfono ya está registrado',
            'initial_balance.numeric' => 'El saldo debe ser un número',
            'initial_balance.min' => 'El saldo no puede ser negativo',
        ]);

        // Generar código de referido único
        do {
            $referralCode = strtoupper(Str::random(6));
        } while (Player::where('referral_code', $referralCode)->exists());

        // Crear jugador
        $player = Player::create([
            'tenant_id' => $tenantId,
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone,
            'balance' => $this->initial_balance ?? 0,
            'referral_code' => $referralCode,
            'status' => 'active',
        ]);

        // Activity log
        activity()
            ->performedOn($player)
            ->causedBy(auth()->user())
            ->log('Jugador creado desde panel admin');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Jugador creado correctamente'
        ]);

        $this->closeModal();
        $this->dispatch('playerCreated');
        $this->dispatch('$refresh');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'email', 'phone', 'initial_balance']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.agent.players.create-player');
    }
}