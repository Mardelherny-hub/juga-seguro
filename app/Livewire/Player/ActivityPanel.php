<?php

namespace App\Livewire\Player;

use App\Models\Transaction;
use Livewire\Component;

class ActivityPanel extends Component
{
    public $activities = [];
    public $isOpen = false;
    public $hasNew = false;

    protected $listeners = [
        'refreshBalance' => 'loadActivities',
        'balanceUpdated' => 'loadActivities',
        'transactionProcessed' => 'loadActivities',
        'refreshDashboard' => 'loadActivities'
    ];

    public function mount()
    {
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $player = auth()->guard('player')->user();
        
        if (!$player) return;

        $activities = collect();

        // Transacciones recientes del jugador (últimas 24 horas)
        $transactions = Transaction::where('player_id', $player->id)
            ->where('updated_at', '>', now()->subHours(24))
            ->latest('updated_at')
            ->limit(15)
            ->get();

        foreach ($transactions as $transaction) {
            $activities->push([
                'id' => 'tx-' . $transaction->id,
                'type' => 'transaction',
                'status' => $transaction->status,
                'transaction_type' => $transaction->type,
                'amount' => $transaction->amount,
                'notes' => $transaction->notes,
                'time' => $transaction->updated_at->diffForHumans(),
                'is_new' => $transaction->updated_at->isAfter(now()->subSeconds(10))
            ]);
        }

        $this->activities = $activities->sortByDesc(function ($activity) {
            return $activity['is_new'];
        })->values()->all();

        // Verificar si hay algo nuevo
        $this->hasNew = $activities->contains('is_new', true);
    }

    public function togglePanel()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->hasNew = false;
        }
    }

    public function render()
    {
        return view('livewire.player.activity-panel');
    }
}