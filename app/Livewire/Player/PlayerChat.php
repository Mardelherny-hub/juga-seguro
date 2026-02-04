<?php

namespace App\Livewire\Player;

use App\Services\MessageService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class PlayerChat extends Component
{
    use WithFileUploads;
    
    public $isOpen = false;
    public $newMessage = '';
    public $messageImage;
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
            'newMessage' => 'required_without:messageImage|min:1|max:1000',
            'messageImage' => 'nullable|image|max:2048',
        ], [
            'newMessage.required_without' => 'Escribe un mensaje o adjunta una imagen',
            'newMessage.max' => 'El mensaje es muy largo',
            'messageImage.image' => 'El archivo debe ser una imagen',
            'messageImage.max' => 'La imagen no debe superar 2MB',
        ]);

        $player = auth()->guard('player')->user();
        
        $imagePath = null;
        if ($this->messageImage) {
            $imagePath = $this->messageImage->store('messages/' . $player->tenant_id, 'public');
        }
        
        $this->messageService->sendPlayerMessage(
            $player,
            $this->newMessage ?: '📷 Imagen',
            'support',
            $imagePath
        );

        $this->newMessage = '';
        $this->messageImage = null;
        
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
                ? 'https://wa.me/' . $tenant->whatsapp_number . '?text=' . urlencode("Hola, soy {$player->name} (ID: {$player->id}). Necesito ayuda con:")
                : null,
        ]);
    }
}