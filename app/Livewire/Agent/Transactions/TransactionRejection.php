<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\Attributes\On;

class TransactionRejection extends Component
{
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
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Esta transacción ya fue procesada'
            ]);
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
        $this->validate();

        $this->isProcessing = true;

        try {
            $transactionService = app(TransactionService::class);
            $transactionService->rejectTransaction(
                $this->transaction, 
                auth()->user(),
                $this->rejectionReason
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Transacción rechazada correctamente'
            ]);

            // Refrescar componentes
            $this->dispatch('refreshPending');
            $this->dispatch('transactionProcessed');

            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al rechazar: ' . $e->getMessage()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-rejection');
    }
}