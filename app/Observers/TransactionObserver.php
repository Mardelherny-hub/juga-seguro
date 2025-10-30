<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Player;
use App\Services\MessageService;
use App\Services\BonusService;

class TransactionObserver
{
    protected $messageService;
    protected $bonusService;

    public function __construct(MessageService $messageService, BonusService $bonusService)
    {
        $this->messageService = $messageService;
        $this->bonusService = $bonusService;
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Solo notificar si es una solicitud del jugador (deposit o withdrawal)
        if ($transaction->type === 'deposit' && $transaction->status === 'pending') {
            $this->messageService->notifyDepositRequest($transaction);
        }

        if ($transaction->type === 'withdrawal' && $transaction->status === 'pending') {
            $this->messageService->notifyWithdrawalRequest($transaction);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Solo si cambió el estado
        if (!$transaction->wasChanged('status')) {
            return;
        }

        // Depósitos
        if ($transaction->type === 'deposit') {
            if ($transaction->status === 'completed') {
                $this->messageService->notifyDepositApproved($transaction);
                
                // Verificar si es primer depósito para bono de referido
                $this->checkReferralBonus($transaction);
            }

            if ($transaction->status === 'rejected') {
                // Extraer el motivo del campo notes
                $reason = $transaction->notes ?? 'No se especificó un motivo';
                
                // Si notes contiene el formato "RECHAZADO: motivo", extraer solo el motivo
                if (str_contains($reason, 'RECHAZADO: ')) {
                    $reason = str_replace('RECHAZADO: ', '', $reason);
                    // Si tiene el formato antiguo con pipes, tomar la última parte
                    if (str_contains($reason, ' | ')) {
                        $parts = explode(' | ', $reason);
                        $reason = end($parts);
                        if (str_starts_with($reason, 'RECHAZADO: ')) {
                            $reason = str_replace('RECHAZADO: ', '', $reason);
                        }
                    }
                }
                
                $typeLabel = $transaction->type === 'deposit' ? 'depósito' : 
                            ($transaction->type === 'withdrawal' ? 'retiro' : 
                            ($transaction->type === 'account_creation' ? 'creación de usuario' :
                            ($transaction->type === 'account_unlock' ? 'desbloqueo' :
                            ($transaction->type === 'password_reset' ? 'cambio de contraseña' : 'solicitud'))));
                
                if ($transaction->type === 'deposit') {
                    $this->messageService->notifyDepositRejected($transaction, $reason);
                } elseif ($transaction->type === 'withdrawal') {
                    $this->messageService->notifyWithdrawalRejected($transaction, $reason);
                }
                // Para los tipos de cuenta, ya se maneja en TransactionApproval/Rejection
            }
        }

        // Retiros
        if ($transaction->type === 'withdrawal') {
            if ($transaction->status === 'completed') {
                $this->messageService->notifyWithdrawalApproved($transaction);
            }

            if ($transaction->status === 'rejected') {
                // Extraer el motivo del campo notes
                $reason = $transaction->notes ?? 'No se especificó un motivo';
                
                // Si notes contiene el formato "RECHAZADO: motivo", extraer solo el motivo
                if (str_contains($reason, 'RECHAZADO: ')) {
                    $reason = str_replace('RECHAZADO: ', '', $reason);
                    // Si tiene el formato antiguo con pipes, tomar la última parte
                    if (str_contains($reason, ' | ')) {
                        $parts = explode(' | ', $reason);
                        $reason = end($parts);
                        if (str_starts_with($reason, 'RECHAZADO: ')) {
                            $reason = str_replace('RECHAZADO: ', '', $reason);
                        }
                    }
                }
                
                $typeLabel = $transaction->type === 'deposit' ? 'depósito' : 
                            ($transaction->type === 'withdrawal' ? 'retiro' : 
                            ($transaction->type === 'account_creation' ? 'creación de usuario' :
                            ($transaction->type === 'account_unlock' ? 'desbloqueo' :
                            ($transaction->type === 'password_reset' ? 'cambio de contraseña' : 'solicitud'))));
                
                if ($transaction->type === 'deposit') {
                    $this->messageService->notifyDepositRejected($transaction, $reason);
                } elseif ($transaction->type === 'withdrawal') {
                    $this->messageService->notifyWithdrawalRejected($transaction, $reason);
                }
                // Para los tipos de cuenta, ya se maneja en TransactionApproval/Rejection
            }
        }
    }

    /**
     * Verificar y otorgar bono por referido en primer depósito
     */
    protected function checkReferralBonus(Transaction $transaction): void
    {
        $player = $transaction->player;
        
        // Verificar si es el primer depósito completado
        $isFirstDeposit = Transaction::where('player_id', $player->id)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->count() === 1;
        
        if (!$isFirstDeposit || !$player->referred_by) {
            return;
        }
        
        // Obtener el referidor
        $referrer = Player::find($player->referred_by);
        
        if (!$referrer || !$referrer->isActive()) {
            return;
        }
        
        // Configuración del bono (TODO: hacer configurable por tenant)
        $referralBonusAmount = 200; // $200 para cada uno
        
        // Otorgar bono al referidor
        $this->bonusService->grantReferralBonus(
            $referrer,
            $referralBonusAmount,
            $player->name
        );
        
        // Otorgar bono al referido
        $this->bonusService->grantReferralBonus(
            $player,
            $referralBonusAmount,
            "tu primer depósito"
        );
    }
}