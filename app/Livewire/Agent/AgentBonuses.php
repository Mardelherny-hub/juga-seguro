<?php

namespace App\Livewire\Agent;

use App\Models\Player;
use App\Services\BonusService;
use Livewire\Component;
use App\Livewire\Traits\WithTenantContext;

class AgentBonuses extends Component
{
    use WithTenantContext;
    
    public $showGrantModal = false;
    public $selectedPlayerId = null;
    public $bonusAmount = '';
    public $bonusDescription = '';
    public $search = '';

    protected $bonusService;

    public function boot(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;
    }

    public function openGrantModal($playerId)
    {
        $this->selectedPlayerId = $playerId;
        $this->showGrantModal = true;
        $this->reset(['bonusAmount', 'bonusDescription']);
        $this->resetValidation();
    }

    public function closeGrantModal()
    {
        $this->showGrantModal = false;
        $this->selectedPlayerId = null;
    }

    public function grantBonus()
    {
        $this->validate([
            'bonusAmount' => 'required|numeric|min:1|max:999999',
            'bonusDescription' => 'required|min:5|max:255',
        ], [
            'bonusAmount.required' => 'El monto es obligatorio',
            'bonusAmount.numeric' => 'El monto debe ser un número',
            'bonusAmount.min' => 'El monto mínimo es $1',
            'bonusDescription.required' => 'La descripción es obligatoria',
            'bonusDescription.min' => 'La descripción debe tener al menos 5 caracteres',
        ]);

        $player = Player::find($this->selectedPlayerId);
        
        if (!$player) {
            session()->flash('error', 'Jugador no encontrado');
            return;
        }

        $this->bonusService->grantCustomBonus(
            $player,
            $this->bonusAmount,
            $this->bonusDescription
        );

        session()->flash('success', "Bono de \${$this->bonusAmount} otorgado a {$player->display_name}");
        
        $this->closeGrantModal();
    }

    public function getPlayers()
    {
        $tenantId = auth()->user()->tenant_id;
        
        $query = Player::where('tenant_id', $tenantId);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->withCount('bonuses')
            ->withSum('bonuses', 'amount')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStats()
    {
        $tenantId = auth()->user()->tenant_id;
        return $this->bonusService->getTenantBonusStats($tenantId);
    }

    public function render()
    {
        return view('livewire.agent.agent-bonuses', [
            'players' => $this->getPlayers(),
            'stats' => $this->getStats(),
        ]);
    }
}