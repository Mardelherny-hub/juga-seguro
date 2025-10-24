<?php

namespace App\Livewire\Components;

use App\Models\Transaction;
use Livewire\Component;

class PendingTransactionsBadge extends Component
{
    public $count = 0;

    protected $listeners = [
        'refreshPending' => 'updateCount', 
        'transactionProcessed' => 'updateCount'
    ];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->count = Transaction::pending()->count();
    }

    public function render()
    {
        return view('livewire.components.pending-transactions-badge');
    }
}