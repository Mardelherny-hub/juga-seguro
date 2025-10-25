<?php

namespace App\Observers;

use App\Models\Player;
use App\Services\MessageService;

class PlayerObserver
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        // Mensaje de bienvenida automático
        $this->messageService->notifyWelcome($player);
    }

    /**
     * Handle the Player "updated" event.
     */
    public function updated(Player $player): void
    {
        // Solo si cambió el estado
        if (!$player->wasChanged('status')) {
            return;
        }

        if ($player->status === 'suspended') {
            $reason = 'Contacta a soporte para más información';
            $this->messageService->notifyAccountSuspended($player, $reason);
        }

        if ($player->status === 'active' && $player->getOriginal('status') === 'suspended') {
            $this->messageService->notifyAccountActivated($player);
        }
    }
}