<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;

class NewTransactionMonitor extends Component
{
    public $lastTransactionId = 0;

    public function mount()
    {
        // Obtener el ID de la última transacción al cargar
        $lastTransaction = Transaction::pending()->latest('id')->first();
        $this->lastTransactionId = $lastTransaction ? $lastTransaction->id : 0;
    }

    public function checkNewTransactions()
    {
        // Buscar transacciones más nuevas que la última conocida
        $newTransactions = Transaction::with('player:id,name')
            ->where('id', '>', $this->lastTransactionId)
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->get();

        if ($newTransactions->isNotEmpty()) {
            // Actualizar el último ID conocido
            $this->lastTransactionId = $newTransactions->last()->id;

            // Disparar notificación persistente para cada nueva transacción
            foreach ($newTransactions as $transaction) {
                $typeText = $transaction->type === 'deposit' ? 'DEPÓSITO' : 'RETIRO';
                $message = "🔔 Nuevo {$typeText} de {$transaction->player->name} por \${$transaction->amount}";
                
                $this->dispatch('notify', [
                    'type' => 'transaction',
                    'message' => $message,
                    'persistent' => true
                ]);
            }

            // Refrescar badge y otros componentes
            $this->dispatch('refreshPending');
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.new-transaction-monitor');
    }
}