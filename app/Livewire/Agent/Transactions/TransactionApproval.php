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
        // Validación de saldo para retiros
        if ($this->transaction->type === 'withdrawal' && !$this->hasSufficientBalance) {
            $this->showToast('El jugador no tiene saldo suficiente para este retiro', 'error');
            return;
        }

        $this->isProcessing = true;

        try {
            $transactionService = app(TransactionService::class);
            $transactionService->approveTransaction($this->transaction, auth()->user());

            $this->showToast('Transacción aprobada correctamente', 'success');

            // Refrescar componentes
            $this->dispatch('refreshPending');
            $this->dispatch('transactionProcessed');
            $this->dispatch('balanceUpdated'); // NUEVO - para BalanceDisplay del Player
            $this->dispatch('playerBalanceChanged', playerId: $this->transaction->player_id); // NUEVO - para actualizar player específico

            $this->closeModal();

            // Después de los dispatch exitosos, agregar:
            $this->dispatch('$refresh')->to('agent.transactions.pending-transactions');
            $this->dispatch('$refresh')->to('agent.transactions.transaction-history');


        } catch (\Exception $e) {
            $this->showToast('Error al aprobar: ' . $e->getMessage(), 'error');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-approval');
    }
}