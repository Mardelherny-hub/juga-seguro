<?php

namespace App\Livewire\Player;

use App\Services\MessageService;
use Livewire\Component;
use Livewire\Attributes\On;

class PlayerChat extends Component
{
    public $isOpen = false;
    public $newMessage = '';
    protected $messageService;

    public function boot(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    #[On('toggle-chat')]
    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        
        if ($this->isOpen) {
            // Marcar mensajes como leídos al abrir
            $player = auth()->guard('player')->user();
            $this->messageService->markAsReadByPlayer($player);
        }
    }

    public function closeChat()
    {
        $this->isOpen = false;
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|min:1|max:1000',
        ], [
            'newMessage.required' => 'Escribe un mensaje',
            'newMessage.max' => 'El mensaje es muy largo',
        ]);

        $player = auth()->guard('player')->user();
        
        $this->messageService->sendPlayerMessage(
            $player,
            $this->newMessage,
            'support'
        );

        $this->newMessage = '';
        
        $this->dispatch('message-sent');
    }

    public function getMessages()
    {
        $player = auth()->guard('player')->user();
        return $this->messageService->getPlayerMessages($player, 100);
    }

    public function getUnreadCount()
    {
        $player = auth()->guard('player')->user();
        return $this->messageService->getUnreadCountForPlayer($player);
    }

    public function render()
    {
        $player = auth()->guard('player')->user();
        $tenant = $player->tenant;

        return view('livewire.player.player-chat', [
            'messages' => $this->getMessages(),
            'unreadCount' => $this->getUnreadCount(),
            'whatsappNumber' => $tenant->whatsapp_number,
            'whatsappLink' => $tenant->whatsapp_number 
                ? 'https://wa.me/' . $tenant->whatsapp_number . '?text=' . urlencode("Hola, soy {$player->display_name} (ID: {$player->id}). Necesito ayuda con:")
                : null,
        ]);
    }
}