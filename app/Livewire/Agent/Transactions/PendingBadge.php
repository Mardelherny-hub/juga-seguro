<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;

class PendingBadge extends Component
{
    public $count = 0;

    protected $listeners = [
        'refreshPending' => 'updateCount',
        'transactionProcessed' => 'updateCount',
        'monitorRefreshed' => 'handleMonitorUpdate'
    ];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->count = Transaction::pending()->count();
    }

    public function handleMonitorUpdate($count = null)
    {
        if ($count !== null) {
            $this->count = $count;
        } else {
            $this->updateCount();
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.pending-badge');
    }
}