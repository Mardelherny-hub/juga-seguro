<?php

namespace App\Livewire\Agent;

use App\Models\Transaction;
use App\Models\Player;
use Livewire\Component;

class ActivityPanel extends Component
{
    public $activities = [];
    public $isOpen = false;
    public $unreadCount = 0;

    protected $listeners = [
        'transactionProcessed' => 'loadActivities',
        'refreshPending' => 'loadActivities',
        'playerBalanceChanged' => 'loadActivities',
        'playerUpdated' => 'loadActivities'
    ];

    public function mount()
    {
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $activities = collect();

        // Transacciones recientes (últimos 30 minutos)
        $transactions = Transaction::with('player:id,name')
            ->where('updated_at', '>', now()->subHours(2))
            ->latest('updated_at')
            ->limit(15)
            ->get();

        foreach ($transactions as $transaction) {
            $activities->push([
                'id' => 'tx-' . $transaction->id,
                'type' => 'transaction',
                'status' => $transaction->status,
                'transaction_type' => $transaction->type,
                'player_name' => $transaction->player->name,
                'amount' => $transaction->amount,
                'time' => $transaction->updated_at->diffForHumans(),
                'is_new' => $transaction->updated_at->isAfter(now()->subSeconds(10))
            ]);
        }

        $this->activities = $activities->sortByDesc(function ($activity) {
            return $activity['is_new'];
        })->values()->all();

        // Contar pendientes
        $this->unreadCount = Transaction::where('status', 'pending')->count();
    }

    public function togglePanel()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.agent.activity-panel');
    }
}