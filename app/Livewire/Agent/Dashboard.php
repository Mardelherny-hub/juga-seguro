<?php

namespace App\Livewire\Agent;

use App\Models\Player;
use App\Models\Transaction;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalPlayers;
    public $activePlayers;
    public $totalBalance;
    public $pendingTransactions;
    public $todayTransactions;
    public $todayDeposits;
    public $todayWithdrawals;
    public $recentTransactions;

    // Listener para actualizar cuando se procesa una transacción
    protected $listeners = [
        'transactionProcessed' => 'loadMetrics',
        'refreshPending' => 'loadMetrics',
        'playerUpdated' => 'loadMetrics',
        'playerBalanceChanged' => 'loadMetrics'
    ];
    public function mount()
    {
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        // Total de jugadores
        $this->totalPlayers = Player::count();

        // Jugadores activos (status = active)
        $this->activePlayers = Player::where('status', 'active')->count();

        // Saldo total en el sistema
        $this->totalBalance = Player::sum('balance');

        // Transacciones pendientes
        $this->pendingTransactions = Transaction::where('status', 'pending')->count();

        // Transacciones de hoy
        $this->todayTransactions = Transaction::whereDate('created_at', today())->count();

        // Depósitos de hoy
        $this->todayDeposits = Transaction::whereDate('created_at', today())
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');

        // Retiros de hoy
        $this->todayWithdrawals = Transaction::whereDate('created_at', today())
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');

        // Últimas 10 transacciones
        $this->recentTransactions = Transaction::with('player')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.agent.dashboard');
    }
}