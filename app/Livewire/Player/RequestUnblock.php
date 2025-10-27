<?php

namespace App\Livewire\Player;

use App\Models\UnblockRequest;
use Livewire\Component;
use App\Livewire\Traits\WithToast;

class RequestUnblock extends Component
{
    use WithToast;

    public $showModal = false;
    public $reason = '';
    public $player;
    public $pendingRequest = null;

    public function mount()
    {
        $this->player = auth()->guard('player')->user();
        $this->checkPendingRequest();
    }

    public function checkPendingRequest()
    {
        $this->pendingRequest = $this->player->pendingUnblockRequest;
    }

    public function openModal()
    {
        if ($this->pendingRequest) {
            $this->showToast('Ya tienes una solicitud de desbloqueo pendiente', 'warning');
            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('reason');
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'reason' => 'required|string|min:10|max:500',
        ];
    }

    protected $messages = [
        'reason.required' => 'Por favor explica por qué deseas desbloquear tu cuenta',
        'reason.min' => 'La explicación debe tener al menos 10 caracteres',
        'reason.max' => 'La explicación no puede superar los 500 caracteres',
    ];

    public function submit()
    {
        $this->validate();

        // Crear solicitud
        $request = UnblockRequest::create([
            'tenant_id' => $this->player->tenant_id,
            'player_id' => $this->player->id,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        // Activity log
        activity()
            ->performedOn($request)
            ->causedBy($this->player)
            ->log('Solicitud de desbloqueo creada');

        $this->showToast('Solicitud enviada. El equipo revisará tu caso pronto.', 'success');
        
        $this->closeModal();
        $this->checkPendingRequest();
    }

    public function render()
    {
        return view('livewire.player.request-unblock');
    }
}