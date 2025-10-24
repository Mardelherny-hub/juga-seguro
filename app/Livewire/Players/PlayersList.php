<?php

namespace App\Livewire\Players;

use App\Models\Player;
use Livewire\Component;
use Livewire\WithPagination;

class PlayersList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $hasReferrerFilter = 'all';
    public $hasBalanceFilter = 'all';
    public $selectedPlayer = null;

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

    public function updatingHasReferrerFilter()
    {
        $this->resetPage();
    }

    public function updatingHasBalanceFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $players = Player::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('email', 'ilike', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('referral_code', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->hasReferrerFilter !== 'all', function ($query) {
                if ($this->hasReferrerFilter === 'yes') {
                    $query->whereNotNull('referred_by');
                } else {
                    $query->whereNull('referred_by');
                }
            })
            ->when($this->hasBalanceFilter !== 'all', function ($query) {
                if ($this->hasBalanceFilter === 'yes') {
                    $query->where('balance', '>', 0);
                } else {
                    $query->where('balance', '<=', 0);
                }
            })
            ->with(['referrer:id,name', 'referrals:id,referred_by'])
            ->withCount('referrals')
            ->latest()
            ->paginate(20);

        return view('livewire.players.players-list', [
            'players' => $players,
        ]);
    }
}