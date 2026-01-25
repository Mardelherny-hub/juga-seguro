<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Livewire\Traits\WithTenantContext;

class TransactionHistory extends Component
{
    use WithPagination;
    use WithTenantContext;

    // Búsqueda
    public $search = '';
    
    // Filtros
    public $typeFilter = 'all';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    
    // Estadísticas calculadas
    public $totalDeposits = 0;
    public $totalWithdrawals = 0;
    public $totalTransactions = 0;
    public $netBalance = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => 'all'],
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount()
    {
        // Inicializar fechas por defecto (últimos 30 días)
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function setDateRange($range)
    {
        $this->dateTo = now()->format('Y-m-d');
        
        switch($range) {
            case 'today':
                $this->dateFrom = now()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->dateFrom = now()->subDay()->format('Y-m-d');
                $this->dateTo = now()->subDay()->format('Y-m-d');
                break;
            case 'last7':
                $this->dateFrom = now()->subDays(7)->format('Y-m-d');
                break;
            case 'last30':
                $this->dateFrom = now()->subDays(30)->format('Y-m-d');
                break;
            case 'thisMonth':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                break;
            case 'lastMonth':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
        }
        
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'typeFilter', 'statusFilter', 'dateFrom', 'dateTo']);
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function exportCSV()
    {
        $transactions = $this->getFilteredQuery()->get();
        
        $filename = 'transacciones_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Fecha',
                'Jugador',
                'Tipo',
                'Monto',
                'Estado',
                'Procesada Por',
                'Fecha Procesamiento'
            ]);

            // Datos
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->created_at->format('d/m/Y H:i:s'),
                    $transaction->player->display_name,
                    $transaction->type === 'deposit' ? 'Depósito' : 'Retiro',
                    number_format($transaction->amount, 2),
                    match($transaction->status) {
                        'pending' => 'Pendiente',
                        'completed' => 'Completada',
                        'rejected' => 'Rechazada',
                        default => $transaction->status
                    },
                    $transaction->processor?->name ?? 'N/A',
                    $transaction->processed_at?->format('d/m/Y H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getFilteredQuery()
    {
        $query = Transaction::with(['player:id,name', 'processor:id,name']);

        // Búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('player', function($q) {
                      $q->where('name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('email', 'ilike', '%' . $this->search . '%');
                  });
            });
        }

        // Filtro de tipo
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Filtro de estado
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filtro de fechas
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query;
    }

    private function calculateStats()
    {
        $transactions = $this->getFilteredQuery()->get();
        
        $this->totalTransactions = $transactions->count();
        
        $this->totalDeposits = $transactions
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');
        
        $this->totalWithdrawals = $transactions
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        $this->netBalance = $this->totalDeposits - $this->totalWithdrawals;
    }

    public function render()
    {
        $this->calculateStats();
        
        $transactions = $this->getFilteredQuery()
            ->latest()
            ->paginate(50);

        return view('livewire.agent.transactions.transaction-history', [
            'transactions' => $transactions,
        ]);
    }
}