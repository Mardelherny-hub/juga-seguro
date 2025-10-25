<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class ApproveTransaction extends Component
{
    use WithToast;

    public $transaction;
    public $isOpen = false;
    public $notes = '';

    protected $listeners = ['openApprovalModal' => 'open'];

    public function open($transactionId)
    {
        $this->transaction = Transaction::with('player')->findOrFail($transactionId);
        $this->isOpen = true;
    }

    public function close()
    {
        $this->reset(['notes']);
        $this->isOpen = false;
    }

    public function approve()
    {
        DB::transaction(function () {
            $player = $this->transaction->player;

            if ($this->transaction->type === 'deposit') {
                // Incrementar saldo
                $player->increment('balance', $this->transaction->amount);
            } elseif ($this->transaction->type === 'withdrawal') {
                // Decrementar saldo (verificar que tenga suficiente)
                if ($player->balance < $this->transaction->amount) {
                    $this->showToast('El jugador no tiene saldo suficiente', 'error');
                    return;
                }
                $player->decrement('balance', $this->transaction->amount);
            }

            // Actualizar transacción
            $this->transaction->update([
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'balance_after' => $player->balance,
                'notes' => $this->notes ?: $this->transaction->notes,
            ]);

            // Activity log
            activity()
                ->performedOn($this->transaction)
                ->causedBy(auth()->user())
                ->log('transaction_approved');
        });

        $this->showToast('Transacción aprobada correctamente', 'success');

        $this->dispatch('transactionProcessed');
        $this->close();
    }

    public function render()
    {
        return view('livewire.agent.transactions.approve-transaction');
    }
}