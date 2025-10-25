<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\WithToast;

class DepositRequest extends Component
{
    use WithToast;
    use WithFileUploads;

    public $isOpen = false;
    public $amount = '';
    public $receipt;
    public $accountHolder = '';
    public $accountNumber = '';
    
    public $tenant;
    public $player;
    
    protected $listeners = ['openDepositModal' => 'open'];

    public function mount()
    {
        $this->player = auth()->guard('player')->user();
        $this->tenant = $this->player->tenant;
    }

    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->reset(['amount', 'receipt', 'accountHolder', 'accountNumber']);
        $this->isOpen = false;
    }

    protected function rules()
    {
        return [
            'amount' => 'required|numeric|min:100',
            'receipt' => 'required|image|max:5120', // 5MB max
            'accountHolder' => 'required|string|min:3',
            'accountNumber' => 'required|string|min:8',
        ];
    }

    protected $messages = [
        'amount.required' => 'El monto es obligatorio',
        'amount.min' => 'El monto mínimo es $100',
        'receipt.required' => 'Debes subir el comprobante',
        'receipt.image' => 'El comprobante debe ser una imagen',
        'receipt.max' => 'La imagen no puede pesar más de 5MB',
        'accountHolder.required' => 'Ingresa el titular de la cuenta',
        'accountNumber.required' => 'Ingresa el número de cuenta',
    ];

    public function submit()
    {
        $this->validate();

        // Guardar comprobante
        $path = $this->receipt->store("receipts/{$this->tenant->id}/{$this->player->id}", 'public');
        $receiptUrl = Storage::url($path);

        // Crear transacción
        $transaction = $this->player->transactions()->create([
            'tenant_id' => $this->tenant->id,
            'type' => 'deposit',
            'amount' => $this->amount,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance,
            'status' => 'pending',
            'proof_url' => $receiptUrl,
            'notes' => "Titular: {$this->accountHolder} | Cuenta: {$this->accountNumber}",
        ]);

        // Activity log
        activity()
            ->performedOn($transaction)
            ->causedBy($this->player)
            ->withProperties([
                'amount' => $this->amount,
                'account_holder' => $this->accountHolder
            ])
            ->log('deposit_requested');

        // Notificación
        $this->showToast('¡Solicitud enviada! Te avisaremos cuando sea aprobada.', 'success');

        // Refrescar dashboard
        $this->dispatch('refreshDashboard');

        $this->close();
    }

    public function render()
    {
        return view('livewire.player.deposit-request');
    }
}