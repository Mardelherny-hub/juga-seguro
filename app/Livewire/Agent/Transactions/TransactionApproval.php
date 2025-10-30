<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class TransactionApproval extends Component
{
    use WithToast;

    public $showModal = false;
    public $transaction = null;
    public $player = null;
    public $isProcessing = false;
    
    // Cálculos para retiros
    public $currentBalance = 0;
    public $newBalance = 0;
    public $hasSufficientBalance = true;

    #[On('openTransactionApproval')]
    public function openModal($transactionId)
    {
        $this->transaction = Transaction::with(['player', 'processor'])
            ->findOrFail($transactionId);
        
        $this->player = $this->transaction->player;
        
        // Validar que esté pendiente
        if ($this->transaction->status !== 'pending') {
            $this->showToast('Esta transacción ya fue procesada', 'error');
            return;
        }
        
        // Cálculos para retiros
        if ($this->transaction->type === 'withdrawal') {
            $this->currentBalance = $this->player->balance;
            $this->newBalance = $this->currentBalance - $this->transaction->amount;
            $this->hasSufficientBalance = $this->currentBalance >= $this->transaction->amount;
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['transaction', 'player', 'isProcessing', 'currentBalance', 'newBalance', 'hasSufficientBalance']);
    }

    public function approve()
    {
        if (!$this->transaction) {
            return;
        }

        $this->validate();
        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                $user = auth()->user();
                
                // Para solicitudes de cuenta, no modificar el saldo
                if ($this->transaction->isAccountRequest()) {
                    $this->transaction->update([
                        'status' => 'completed',
                        'processed_by' => $user->id,
                        'processed_at' => now(),
                        'notes' => $this->notes ?: $this->transaction->notes,
                    ]);
                    
                    // Crear notificación para el jugador
                    $this->notifyPlayer($this->transaction, 'approved');
                    
                    // Log de actividad
                    activity()
                        ->performedOn($this->transaction)
                        ->causedBy($user)
                        ->withProperties([
                            'type' => $this->transaction->type,
                            'player_id' => $this->transaction->player_id
                        ])
                        ->log('Solicitud de ' . $this->getTypeLabel() . ' aprobada');
                        
                } else {
                    // Para depósitos y retiros, usar el servicio existente
                    $service = app(TransactionService::class);
                    
                    if ($this->transaction->isDeposit()) {
                        $service->processDeposit(
                            $this->transaction->player,
                            $this->transaction->amount,
                            $this->transaction->proof_url,
                            $user
                        );
                    } elseif ($this->transaction->isWithdrawal()) {
                        $service->processWithdrawal(
                            $this->transaction->player,
                            $this->transaction->amount,
                            $user
                        );
                    }
                    
                    $this->transaction->update([
                        'status' => 'completed',
                        'processed_by' => $user->id,
                        'processed_at' => now(),
                        'notes' => $this->notes ?: $this->transaction->notes,
                    ]);
                }
            });

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $this->getTypeLabel() . ' aprobada correctamente'
            ]);
            
            $this->dispatch('transactionProcessed');
            $this->showModal = false;
            $this->reset(['transaction', 'notes']);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    private function getTypeLabel()
    {
        return match($this->transaction->type) {
            'deposit' => 'Depósito',
            'withdrawal' => 'Retiro',
            'account_creation' => 'Creación de usuario',
            'account_unlock' => 'Desbloqueo de usuario',
            'password_reset' => 'Cambio de contraseña',
            default => 'Transacción'
        };
    }

    private function notifyPlayer($transaction, $status)
    {
        $messageService = app(\App\Services\MessageService::class);
        
        $messages = [
            'account_creation' => [
                'approved' => '✅ Tu solicitud de creación de usuario fue aprobada. ' . ($this->notes ?: 'Consulta con el administrador tus credenciales.'),
                'rejected' => '❌ Tu solicitud de creación de usuario fue rechazada. Motivo: ' . $this->notes
            ],
            'account_unlock' => [
                'approved' => '✅ Tu usuario fue desbloqueado. Ya puedes ingresar a la plataforma.',
                'rejected' => '❌ Tu solicitud de desbloqueo fue rechazada. Motivo: ' . $this->notes
            ],
            'password_reset' => [
                'approved' => '✅ Tu contraseña fue cambiada a: bet123. Guárdala en un lugar seguro.',
                'rejected' => '❌ Tu solicitud de cambio de contraseña fue rechazada. Motivo: ' . $this->notes
            ],
        ];

        $message = $messages[$transaction->type][$status] ?? 'Tu solicitud fue procesada.';
        
        $messageService->sendSystemMessage(
            $transaction->player,
            $message,
            'account',
            $transaction
        );
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-approval');
    }
}