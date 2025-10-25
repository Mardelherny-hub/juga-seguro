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
        $this->validate();

        $this->isProcessing = true;

        try {
            $transactionService = app(TransactionService::class);
            $transactionService->rejectTransaction(
                $this->transaction, 
                auth()->user(),
                $this->rejectionReason
            );

            $this->showToast('Transacción rechazada correctamente', 'success');

            // Refrescar componentes
            $this->dispatch('refreshPending');
            $this->dispatch('transactionProcessed');

            $this->closeModal();

            $this->dispatch('$refresh')->to('agent.transactions.pending-transactions');
            $this->dispatch('$refresh')->to('agent.transactions.transaction-history');


        } catch (\Exception $e) {
            $this->showToast('Error al rechazar: ' . $e->getMessage(), 'error');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-rejection');
    }
}