<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\Attributes\On;

class TransactionApproval extends Component
{
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
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Esta transacción ya fue procesada'
            ]);
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
        // Validación de saldo para retiros
        if ($this->transaction->type === 'withdrawal' && !$this->hasSufficientBalance) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'El jugador no tiene saldo suficiente para este retiro'
            ]);
            return;
        }

        $this->isProcessing = true;

        try {
            $transactionService = app(TransactionService::class);
            $transactionService->approveTransaction($this->transaction, auth()->user());

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Transacción aprobada correctamente'
            ]);

            // Refrescar componentes
            $this->dispatch('refreshPending');
            $this->dispatch('transactionProcessed');

            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al aprobar: ' . $e->getMessage()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-approval');
    }
}