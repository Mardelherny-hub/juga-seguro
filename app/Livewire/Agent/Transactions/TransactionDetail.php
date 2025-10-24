<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\On;

class TransactionDetail extends Component
{
    public $showModal = false;
    public $transaction = null;
    public $player = null;
    public $processor = null;
    public $activityLog = [];

    #[On('openTransactionDetail')]
    public function openModal($transactionId)
    {
        $this->transaction = Transaction::with(['player', 'processor'])
            ->findOrFail($transactionId);
        
        $this->player = $this->transaction->player;
        $this->processor = $this->transaction->processor;
        
        // Obtener activity log relacionado
        $this->activityLog = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', Transaction::class)
            ->where('subject_id', $this->transaction->id)
            ->latest()
            ->limit(10)
            ->get();
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['transaction', 'player', 'processor', 'activityLog']);
    }

    public function getStatusBadge()
    {
        return match($this->transaction->status) {
            'pending' => [
                'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'text' => 'PENDIENTE'
            ],
            'completed' => [
                'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'text' => 'COMPLETADA'
            ],
            'rejected' => [
                'color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                'text' => 'RECHAZADA'
            ],
            default => [
                'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'text' => strtoupper($this->transaction->status)
            ],
        };
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-detail', [
            'statusBadge' => $this->transaction ? $this->getStatusBadge() : null,
        ]);
    }
}