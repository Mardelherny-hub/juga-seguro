<?php

namespace App\Livewire\SuperAdmin\Clients;

use App\Models\Tenant;
use App\Models\Player;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Show extends Component
{
    use WithPagination;

    public $tenant;
    public $tenantId;
    
    // Filtros y búsqueda de players
    public $search = '';
    public $statusFilter = 'all';
    
    // Métricas del cliente
    public $totalPlayers;
    public $activePlayers;
    public $totalBalance;
    public $pendingTransactions;
    public $completedTransactionsToday;

    public function mount($tenant)
    {
        $this->tenantId = $tenant;
        $this->tenant = Tenant::with(['users' => function($query) {
            $query->where('role', 'admin');
        }])->findOrFail($tenant);
        
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        // Total de jugadores
        $this->totalPlayers = Player::where('tenant_id', $this->tenantId)->count();
        
        // Jugadores activos
        $this->activePlayers = Player::where('tenant_id', $this->tenantId)
            ->where('status', 'active')
            ->count();
        
        // Saldo total
        $this->totalBalance = Player::where('tenant_id', $this->tenantId)
            ->sum('balance');
        
        // Transacciones pendientes
        $this->pendingTransactions = Transaction::whereHas('player', function($query) {
            $query->where('tenant_id', $this->tenantId);
        })->where('status', 'pending')->count();
        
        // Transacciones completadas hoy
        $this->completedTransactionsToday = Transaction::whereHas('player', function($query) {
            $query->where('tenant_id', $this->tenantId);
        })
        ->where('status', 'completed')
        ->whereDate('created_at', today())
        ->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    #[On('playerUpdated')]
    public function refreshPlayers()
    {
        $this->loadMetrics();
    }

    public function togglePlayerStatus($playerId, $action)
    {
        $player = Player::findOrFail($playerId);
        
        switch($action) {
            case 'activate':
                $player->activate();
                session()->flash('message', "Jugador {$player->name} activado correctamente");
                break;
            case 'suspend':
                $player->suspend('Suspendido por Super Admin');
                session()->flash('message', "Jugador {$player->name} suspendido correctamente");
                break;
            case 'block':
                $player->block('Bloqueado por Super Admin');
                session()->flash('message', "Jugador {$player->name} bloqueado correctamente");
                break;
        }
        
        $this->loadMetrics();
        $this->dispatch('playerUpdated');
    }

    public function render()
    {
        // Query de players
        $query = Player::where('tenant_id', $this->tenantId)
            ->with(['referrer', 'referrals']);

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('email', 'ilike', '%' . $this->search . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->search . '%')
                  ->orWhere('username', 'ilike', '%' . $this->search . '%')
                  ->orWhere('referral_code', 'ilike', '%' . $this->search . '%');
            });
        }

        // Filtro por estado
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $players = $query->latest()->paginate(15);

        // Transacciones recientes del cliente
        $recentTransactions = Transaction::whereHas('player', function($q) {
            $q->where('tenant_id', $this->tenantId);
        })
        ->with('player')
        ->latest()
        ->limit(10)
        ->get();

        return view('livewire.super-admin.clients.show', [
            'players' => $players,
            'recentTransactions' => $recentTransactions,
        ])->layout('components.layouts.super-admin');
    }
}