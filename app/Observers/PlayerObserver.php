<?php

namespace App\Observers;

use App\Models\Player;
use App\Services\MessageService;
use App\Services\BonusService;

class PlayerObserver
{
    protected $messageService;
    protected $bonusService;

    public function __construct(MessageService $messageService, BonusService $bonusService)
    {
        $this->messageService = $messageService;
        $this->bonusService = $bonusService;
    }

    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        // Mensaje de bienvenida automático
        $this->messageService->notifyWelcome($player);
        
        $tenant = $player->tenant;
        
        // 1. Bono de bienvenida SOLO si es monto fijo (no porcentaje)
        // Si es porcentaje, se otorga en TransactionObserver al completar primer depósito
        if ($tenant && $tenant->welcome_bonus_enabled && $tenant->welcome_bonus_amount > 0 && !$tenant->welcome_bonus_is_percentage) {
            $this->bonusService->grantWelcomeBonus($player, $tenant->welcome_bonus_amount);
        }
        
        // 2. Bono de referido (si usó código de referido)
        if ($player->referred_by && $tenant && $tenant->referral_bonus_enabled && $tenant->referral_bonus_amount > 0) {
            
            // Obtener el referidor
            $referrer = Player::find($player->referred_by);
            
            if ($referrer && $referrer->isActive()) {
                $target = $tenant->referral_bonus_target ?? 'both';
                
                // Otorgar bono al REFERIDOR (quien compartió el código)
                if (in_array($target, ['referrer', 'both'])) {
                    $this->bonusService->grantReferralBonus(
                        $referrer, 
                        $tenant->referral_bonus_amount,
                        $player->name
                    );
                }
                
                // Otorgar bono al REFERIDO (quien usó el código)
                if (in_array($target, ['referred', 'both'])) {
                    $this->bonusService->grantReferralBonus(
                        $player,
                        $tenant->referral_bonus_amount,
                        $referrer->name
                    );
                }
            }
        }
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