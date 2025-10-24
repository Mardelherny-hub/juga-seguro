<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Carbon\Carbon;

class PendingTransactions extends Component
{
    public $pendingCount = 0;
    
    // Para actualización automática cada 30 segundos
    protected $listeners = ['refreshPending' => '$refresh'];

    public function mount()
    {
        $this->updatePendingCount();
    }

    public function updatePendingCount()
    {
        $this->pendingCount = Transaction::pending()->count();
    }

    public function getUrgencyClass($createdAt)
    {
        $hoursAgo = Carbon::parse($createdAt)->diffInHours(now());
        
        if ($hoursAgo > 24) {
            return 'bg-red-100 border-red-300'; // Urgente
        } elseif ($hoursAgo >= 6) {
            return 'bg-yellow-100 border-yellow-300'; // Advertencia
        }
        
        return 'bg-white border-gray-200'; // Normal
    }

    public function getTimeWaiting($createdAt)
    {
        return Carbon::parse($createdAt)->diffForHumans();
    }

    public function render()
    {
        $transactions = Transaction::with(['player:id,name,balance'])
            ->pending()
            ->oldest() // Más antiguas primero
            ->limit(10)
            ->get();

        return view('livewire.agent.transactions.pending-transactions', [
            'transactions' => $transactions,
        ]);
    }
}