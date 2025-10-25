<?php

namespace App\Livewire\Player;

use Livewire\Component;

class TransactionStatusMonitor extends Component
{
    public $lastCheckedTransactions = [];

    public function mount()
    {
        $player = auth()->guard('player')->user();
        
        // Guardar IDs de transacciones actuales
        $this->lastCheckedTransactions = $player->transactions()
            ->whereIn('status', ['pending', 'completed', 'rejected'])
            ->pluck('id', 'status')
            ->toArray();
    }

    public function checkTransactionUpdates()
    {
        $player = auth()->guard('player')->user();
        
        // Buscar transacciones que cambiaron de estado
        $currentTransactions = $player->transactions()
            ->whereIn('status', ['completed', 'rejected'])
            ->where('updated_at', '>', now()->subSeconds(10))
            ->get();

        foreach ($currentTransactions as $transaction) {
            // Si la transacción fue completada recientemente
            if ($transaction->status === 'completed' && $transaction->wasRecentlyUpdated) {
                $typeText = $transaction->type === 'deposit' ? 'depósito' : 'retiro';
                $message = "✅ Tu {$typeText} de \${$transaction->amount} fue APROBADO";
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => $message,
                    'persistent' => true
                ]);
            }
            
            // Si la transacción fue rechazada recientemente
            if ($transaction->status === 'rejected' && $transaction->wasRecentlyUpdated) {
                $typeText = $transaction->type === 'deposit' ? 'depósito' : 'retiro';
                $message = "❌ Tu {$typeText} de \${$transaction->amount} fue RECHAZADO";
                
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => $message,
                    'persistent' => true
                ]);
            }
        }
        
        // Actualizar balance en navbar
        $this->dispatch('refreshBalance');
    }

    public function render()
    {
        return view('livewire.player.transaction-status-monitor');
    }
}