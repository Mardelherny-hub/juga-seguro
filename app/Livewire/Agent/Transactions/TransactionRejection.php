<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class TransactionRejection extends Component
{
    use WithToast;

    public $showModal = false;
    public $transaction = null;
    public $player = null;
    public $isProcessing = false;
    
    // Campo de motivo
    public $rejectionReason = '';
    
    // Motivos comunes sugeridos
    public $commonReasons = [
        'Comprobante no válido o ilegible',
        'Datos bancarios incorrectos',
        'Fondos insuficientes',
        'Solicitud duplicada',
        'Operación sospechosa',
        'Información incompleta',
    ];

    protected $rules = [
        'rejectionReason' => 'required|min:10|max:500',
    ];

    protected $messages = [
        'rejectionReason.required' => 'El motivo del rechazo es obligatorio',
        'rejectionReason.min' => 'El motivo debe tener al menos 10 caracteres',
        'rejectionReason.max' => 'El motivo no puede exceder 500 caracteres',
    ];

    #[On('openTransactionRejection')]
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
        
        $this->rejectionReason = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['transaction', 'player', 'isProcessing', 'rejectionReason']);
        $this->resetValidation();
    }

    public function selectCommonReason($reason)
    {
        $this->rejectionReason = $reason;
    }

    public function reject()
    {
        if (!$this->transaction) {
            return;
        }

        $this->validate();
        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                $user = auth()->user();
                
                $this->transaction->update([
                    'status' => 'rejected',
                    'processed_by' => $user->id,
                    'processed_at' => now(),
                    'notes' => $this->reason,
                ]);

                // Notificar al jugador sobre el rechazo
                if ($this->transaction->isAccountRequest()) {
                    $this->notifyPlayer($this->transaction, 'rejected');
                }

                activity()
                    ->performedOn($this->transaction)
                    ->causedBy($user)
                    ->withProperties([
                        'reason' => $this->reason,
                        'type' => $this->transaction->type
                    ])
                    ->log('Solicitud rechazada: ' . $this->getTypeLabel());
            });

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $this->getTypeLabel() . ' rechazada correctamente'
            ]);
            
            $this->dispatch('transactionProcessed');
            $this->showModal = false;
            $this->reset(['transaction', 'reason']);

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
                'rejected' => '❌ Tu solicitud de creación de usuario fue rechazada. Motivo: ' . $this->reason
            ],
            'account_unlock' => [
                'rejected' => '❌ Tu solicitud de desbloqueo fue rechazada. Motivo: ' . $this->reason
            ],
            'password_reset' => [
                'rejected' => '❌ Tu solicitud de cambio de contraseña fue rechazada. Motivo: ' . $this->reason
            ],
        ];

        $message = $messages[$transaction->type][$status] ?? 'Tu solicitud fue rechazada.';
        
        $messageService->sendSystemMessage(
            $transaction->player,
            $message,
            'account',
            $transaction
        );
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-rejection');
    }
}