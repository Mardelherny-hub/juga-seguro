<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Tenant;
use App\Services\ChipsService;
use Livewire\Component;
use App\Livewire\Traits\WithToast;

class LoadChips extends Component
{
    use WithToast;

    public $showModal = false;
    public $tenant = null;
    public $quantity = '';
    public $amountPaid = '';
    public $isProcessing = false;

    protected $listeners = ['openLoadChipsModal' => 'openModal'];

    protected function rules()
    {
        return [
            'quantity' => 'required|integer|min:1|max:10000',
            'amountPaid' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'quantity.required' => 'Ingresa la cantidad de fichas',
        'quantity.min' => 'Mínimo 1 ficha',
        'quantity.max' => 'Máximo 10,000 fichas por carga',
        'amountPaid.required' => 'Ingresa el monto pagado',
    ];

    public function openModal($tenantId)
    {
        $this->tenant = Tenant::findOrFail($tenantId);
        
        if ($this->tenant->subscription_type !== 'prepaid') {
            $this->showToast('Este cliente es tipo MENSUAL, no usa fichas', 'error');
            return;
        }

        // Calcular monto sugerido
        $this->quantity = 100; // Sugerir 100 fichas
        $this->amountPaid = $this->quantity * $this->tenant->chip_price;
        
        $this->showModal = true;
    }

    public function updatedQuantity($value)
    {
        if (is_numeric($value) && $value > 0) {
            $this->amountPaid = $value * $this->tenant->chip_price;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['tenant', 'quantity', 'amountPaid', 'isProcessing']);
        $this->resetValidation();
    }

    public function loadChips()
    {
        $this->validate();
        $this->isProcessing = true;

        try {
            $chipsService = app(ChipsService::class);
            $result = $chipsService->loadChips(
                $this->tenant,
                (int) $this->quantity,
                (float) $this->amountPaid,
                auth()->user()
            );

            $this->showToast(
                "✅ {$result['quantity_loaded']} fichas cargadas correctamente. Nuevo saldo: {$result['new_balance']} fichas",
                'success'
            );

            $this->dispatch('chipsLoaded');
            $this->dispatch('$refresh');
            
            $this->closeModal();

        } catch (\Exception $e) {
            $this->showToast('Error: ' . $e->getMessage(), 'error');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.super-admin.load-chips');
    }
}