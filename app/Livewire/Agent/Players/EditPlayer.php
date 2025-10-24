<?php

namespace App\Livewire\Agent\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;

class EditPlayer extends Component
{
    public $showModal = false;
    public $playerId = null;
    public $player = null;
    
    public $name = '';
    public $email = '';
    public $phone = '';
    
    // Datos originales para el activity log
    public $originalData = [];

    #[On('openEditPlayer')]
    public function openModal($playerId)
    {
        $this->playerId = $playerId;
        $this->player = Player::findOrFail($playerId);
        
        // Cargar datos actuales
        $this->name = $this->player->name;
        $this->email = $this->player->email;
        $this->phone = $this->player->phone;
        
        // Guardar datos originales
        $this->originalData = [
            'name' => $this->player->name,
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
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.unique' => 'Este teléfono ya está registrado',
        ]);

        // Actualizar datos
        $this->player->update([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone,
        ]);

        // Preparar datos para activity log
        $changes = [];
        if ($this->originalData['name'] !== $this->name) {
            $changes['name'] = ['before' => $this->originalData['name'], 'after' => $this->name];
        }
        if ($this->originalData['email'] !== $this->email) {
            $changes['email'] = ['before' => $this->originalData['email'], 'after' => $this->email];
        }
        if ($this->originalData['phone'] !== $this->phone) {
            $changes['phone'] = ['before' => $this->originalData['phone'], 'after' => $this->phone];
        }

        // Registrar en activity log solo si hubo cambios
        if (!empty($changes)) {
            activity()
                ->performedOn($this->player)
                ->causedBy(auth()->user())
                ->withProperties([
                    'changes' => $changes
                ])
                ->log('Información del jugador actualizada');
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Información actualizada correctamente'
        ]);

        $this->closeModal();
        $this->dispatch('playerUpdated');
        return redirect()->route('dashboard.players');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['playerId', 'player', 'name', 'email', 'phone']);
        $this->originalData = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.agent.players.edit-player');
    }
}