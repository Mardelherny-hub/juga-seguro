<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class RejectTransaction extends Component
{
    public $transaction;
    public $isOpen = false;
    public $rejectionReason = '';

    protected $listeners = ['openRejectionModal' => 'open'];

    public function open($transactionId)
    {
        $this->transaction = Transaction::with('player')->findOrFail($transactionId);
        $this->isOpen = true;
    }

    public function close()
    {
        $this->reset(['rejectionReason']);
        $this->isOpen = false;
    }

    protected function rules()
    {
        return [
            'rejectionReason' => 'required|string|min:10',
        ];
    }

    protected $messages = [
        'rejectionReason.required' => 'Debes indicar el motivo del rechazo',
        'rejectionReason.min' => 'El motivo debe tener al menos 10 caracteres',
    ];

    public function reject()
    {
        $this->validate();

        // Actualizar transacción
        $this->transaction->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'notes' => ($this->transaction->notes ? $this->transaction->notes . ' | ' : '') . 'RECHAZADO: ' . $this->rejectionReason,
        ]);

        // Activity log
        activity()
            ->performedOn($this->transaction)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $this->rejectionReason])
            ->log('transaction_rejected');

        $this->showToast('Transacción rechazada', 'success');

        $this->dispatch('transactionProcessed');
        $this->close();
    }

    public function render()
    {
        return view('livewire.agent.transactions.reject-transaction');
    }
}