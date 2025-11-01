<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;
use Illuminate\Support\Facades\DB;

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

        $this->isProcessing = true;

        try {
            $transactionService = app(TransactionService::class);
            $user = auth()->user();
            
            // Usar el servicio para aprobar
            $transactionService->approveTransaction($this->transaction, $user);
            
            $this->showToast('Transacción aprobada correctamente', 'success');
            $this->dispatch('transactionUpdated');
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->showToast($e->getMessage(), 'error');
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-approval');
    }
}