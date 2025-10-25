<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\MessageService;

class TransactionObserver
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
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
            }

            if ($transaction->status === 'rejected') {
                $reason = $transaction->rejection_reason ?? 'No se especificó un motivo';
                $this->messageService->notifyDepositRejected($transaction, $reason);
            }
        }

        // Retiros
        if ($transaction->type === 'withdrawal') {
            if ($transaction->status === 'completed') {
                $this->messageService->notifyWithdrawalApproved($transaction);
            }

            if ($transaction->status === 'rejected') {
                $reason = $transaction->rejection_reason ?? 'No se especificó un motivo';
                $this->messageService->notifyWithdrawalRejected($transaction, $reason);
            }
        }
    }
}