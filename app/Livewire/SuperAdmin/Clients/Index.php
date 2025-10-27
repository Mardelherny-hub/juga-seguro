<?php

namespace App\Livewire\SuperAdmin\Clients;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['chipsLoaded' => '$refresh'];

    public $search = '';
    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->is_active = !$tenant->is_active;
        $tenant->save();

        session()->flash('message', "Cliente {$tenant->name} " . ($tenant->is_active ? 'activado' : 'desactivado') . " exitosamente.");
    }

    public function delete($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenantName = $tenant->name;
        
        // Soft delete
        $tenant->delete();

        session()->flash('message', "Cliente {$tenantName} eliminado exitosamente.");
    }

    public function openLoadChipsModal($tenantId)
    {
        $this->dispatch('openLoadChipsModal', tenantId: $tenantId);
    }

    public function render()
    {
        $query = Tenant::query()
            ->withCount('players')
            ->withSum('players', 'balance');

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('domain', 'ilike', '%' . $this->search . '%');
            });
        }

        // Filtro por estado
        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $clients = $query->latest()->paginate(10);

        return view('livewire.super-admin.clients.index', [
            'clients' => $clients
        ])->layout('components.layouts.super-admin');
    }
}