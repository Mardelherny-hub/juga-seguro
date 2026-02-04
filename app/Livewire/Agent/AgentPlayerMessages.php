<?php

namespace App\Livewire\Agent;

use App\Models\Player;
use App\Models\PlayerMessage;
use App\Services\MessageService;
use Livewire\Component;
use App\Livewire\Traits\WithTenantContext;
use Livewire\WithFileUploads;

class AgentPlayerMessages extends Component
{
    use WithTenantContext;
    use WithFileUploads;
    
    public $selectedPlayerId = null;
    public $newMessage = '';
    public $search = '';
    public $messageImage;
    
    // Templates de respuesta rápida
    public $quickReplies = [
        'En revisión' => 'Gracias por tu mensaje. Estamos revisando tu solicitud y te responderemos pronto.',
        'Más información' => 'Para ayudarte mejor, necesitamos más información. ¿Podrías proporcionar más detalles?',
        'Procesando' => 'Tu solicitud está siendo procesada. Te notificaremos cuando esté lista.',
        'Aprobado' => '✅ Tu solicitud ha sido aprobada. Los cambios se verán reflejados en breve.',
        'Completado' => 'Tu solicitud ha sido completada exitosamente. ¿Hay algo más en lo que podamos ayudarte?',
    ];

    protected $messageService;

    public function boot(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function mount()
    {
        // Seleccionar el primer jugador con mensajes no leídos si existe
        $firstPlayer = $this->getPlayersWithMessages()->first();
        if ($firstPlayer) {
            $this->selectedPlayerId = $firstPlayer->id;
            $this->markMessagesAsRead();
        }
    }

    public function selectPlayer($playerId)
    {
        $this->selectedPlayerId = $playerId;
        $this->markMessagesAsRead();
        $this->dispatch('playerSelected');
    }

    public function markMessagesAsRead()
    {
        if (!$this->selectedPlayerId) {
            return;
        }

        $player = Player::find($this->selectedPlayerId);
        if ($player) {
            $this->messageService->markAsReadByAgent($player);
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|min:1|max:1000',
            'messageImage' => 'nullable|image|max:2048',
        ], [
            'newMessage.required' => 'Escribe un mensaje',
            'newMessage.max' => 'El mensaje es muy largo',
            'messageImage.image' => 'El archivo debe ser una imagen',
            'messageImage.max' => 'La imagen no debe superar 2MB',
        ]);

        $player = Player::find($this->selectedPlayerId);
        if (!$player) {
            session()->flash('error', 'Jugador no encontrado');
            return;
        }

        $imagePath = null;
        if ($this->messageImage) {
            $imagePath = $this->messageImage->store('messages/' . $player->tenant_id, 'public');
        }

        $agent = auth()->user();
        
        $this->messageService->sendAgentMessage(
            $player,
            $agent,
            $this->newMessage,
            'support',
            $imagePath
        );

        $this->newMessage = '';
        $this->messageImage = null;
        
        session()->flash('message-sent', true);
    }

    public function sendQuickReply($message)
    {
        $this->newMessage = $message;
        $this->sendMessage();
    }

    public function getPlayersWithMessages()
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Jugadores que tienen mensajes, ordenados por última actividad
        $playerIds = PlayerMessage::where('tenant_id', $tenantId)
            ->select('player_id')
            ->distinct()
            ->pluck('player_id');

        $query = Player::whereIn('id', $playerIds);

        // Búsqueda
        // Búsqueda (ILIKE para case-insensitive en PostgreSQL)
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(username) LIKE LOWER(?)', [$searchTerm])
                ->orWhere('phone', 'like', $searchTerm)
                ->orWhere('email', 'ilike', $searchTerm);
            });
        }

        return $query->withCount([
            'messages as unread_count' => function($q) {
                $q->where('sender_type', 'player')
                ->whereNull('read_by_agent_at');
            }
        ])
        ->with(['messages' => function($q) {
            $q->latest()->limit(1);
        }])
        ->withMax('messages', 'created_at')
        ->orderByDesc('messages_max_created_at')
        ->get();
    }

    public function getSelectedPlayerMessages()
    {
        if (!$this->selectedPlayerId) {
            return collect();
        }

        $player = Player::find($this->selectedPlayerId);
        if (!$player) {
            return collect();
        }

        return $this->messageService->getPlayerMessages($player, 100);
    }

    public function getSelectedPlayer()
    {
        if (!$this->selectedPlayerId) {
            return null;
        }

        return Player::find($this->selectedPlayerId);
    }

    public function getTotalUnreadCount()
    {
        $tenantId = auth()->user()->tenant_id;
        return $this->messageService->getUnreadCountForAgent($tenantId);
    }

    public function render()
    {
        return view('livewire.agent.agent-player-messages', [
            'players' => $this->getPlayersWithMessages(),
            'selectedPlayer' => $this->getSelectedPlayer(),
            'messages' => $this->getSelectedPlayerMessages(),
            'totalUnreadCount' => $this->getTotalUnreadCount(),
        ]);
    }

    /**
     * Enviar mensaje masivo a todos los jugadores activos del tenant
     */
    public function broadcastMessage(int $tenantId, User $sender, string $message): int
    {
        $players = Player::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        $count = 0;
        foreach ($players as $player) {
            PlayerMessage::create([
                'tenant_id' => $tenantId,
                'player_id' => $player->id,
                'sender_type' => 'agent',
                'sender_id' => $sender->id,
                'message' => $message,
                'category' => 'general',
            ]);
            $count++;
        }

        // Activity log
        activity()
            ->causedBy($sender)
            ->withProperties([
                'recipients' => $count,
                'message' => \Str::limit($message, 100)
            ])
            ->log('Mensaje masivo enviado');

        return $count;
    }
}