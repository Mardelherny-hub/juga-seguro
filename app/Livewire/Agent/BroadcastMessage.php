<?php

namespace App\Livewire\Agent;

use App\Models\Player;
use App\Services\MessageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class BroadcastMessage extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $message = '';
    public $activePlayersCount = 0;
    public $broadcastImage;

    protected $messageService;

    public function boot(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function openModal()
    {
        $this->activePlayersCount = Player::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->count();
        
        $this->message = '';
        $this->broadcastImage = null;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->message = '';
        $this->broadcastImage = null;
    }

    public function send()
    {
        $this->validate([
            'message' => 'required|min:5|max:1000',
            'broadcastImage' => 'nullable|image|max:2048',
        ], [
            'message.required' => 'El mensaje es obligatorio',
            'message.min' => 'El mensaje debe tener al menos 5 caracteres',
            'message.max' => 'El mensaje no puede superar los 1000 caracteres',
            'broadcastImage.image' => 'El archivo debe ser una imagen',
            'broadcastImage.max' => 'La imagen no debe superar 2MB',
        ]);

        $imagePath = null;
        if ($this->broadcastImage) {
            $imagePath = $this->broadcastImage->store('broadcasts/' . auth()->user()->tenant_id, 'public');
        }

        $count = $this->messageService->broadcastMessage(
            auth()->user()->tenant_id,
            auth()->user(),
            $this->message,
            $imagePath
        );

        $this->closeModal();
        
        session()->flash('success', "Mensaje enviado a {$count} jugadores");
        
        $this->dispatch('broadcastSent');
    }

    public function render()
    {
        return view('livewire.agent.broadcast-message');
    }
}
