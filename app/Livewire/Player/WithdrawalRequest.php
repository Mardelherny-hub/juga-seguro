<?php

namespace App\Livewire\Player;

use Livewire\Component;
use App\Livewire\Traits\WithToast;

class WithdrawalRequest extends Component
{
    use WithToast;

    public $isOpen = false;
    public $amount = '';
    public $withdrawalMethod = 'chinchontop'; // Default
    public $accountHolder = '';
    public $accountNumber = '';
    
    public $tenant;
    public $player;
    
    protected $listeners = ['openWithdrawalModal' => 'open'];

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
        $this->reset(['amount', 'withdrawalMethod', 'accountHolder', 'accountNumber']);
        $this->isOpen = false;
    }

    protected function rules()
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:500',
                'max:' . $this->player->balance
            ],
            'withdrawalMethod' => 'required|string',
            'accountHolder' => 'required|string|min:3',
            'accountNumber' => 'required|string|min:8',
        ];
    }

    protected $messages = [
        'amount.required' => 'El monto es obligatorio',
        'amount.min' => 'El monto mínimo de retiro es $500',
        'amount.max' => 'No tienes saldo suficiente',
        'withdrawalMethod.required' => 'Selecciona un método de retiro',
        'accountHolder.required' => 'Ingresa el titular de la cuenta',
        'accountNumber.required' => 'Ingresa el número de cuenta',
    ];

    public function submit()
    {
        // Verificar que no tenga retiros pendientes
        $hasPendingWithdrawal = $this->player->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingWithdrawal) {
            $this->showToast('Ya tienes un retiro pendiente. Espera a que sea procesado.', 'error');
            return;
        }

        $this->validate();

        // Crear transacción
        $transaction = $this->player->transactions()->create([
            'tenant_id' => $this->tenant->id,
            'type' => 'withdrawal',
            'amount' => $this->amount,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance, // No se descuenta hasta aprobar
            'status' => 'pending',
            'notes' => "Método: {$this->withdrawalMethod} | Titular: {$this->accountHolder} | Cuenta: {$this->accountNumber}",
        ]);

        // Activity log
        activity()
            ->performedOn($transaction)
            ->causedBy($this->player)
            ->withProperties([
                'amount' => $this->amount,
                'method' => $this->withdrawalMethod,
                'account_holder' => $this->accountHolder
            ])
            ->log('withdrawal_requested');

        // Notificación
        $this->showToast('¡Solicitud de retiro enviada! Te avisaremos cuando sea procesada.', 'success');

        // Refrescar dashboard
        $this->dispatch('refreshDashboard');

        $this->close();
    }

    public function render()
    {
        return view('livewire.player.withdrawal-request');
    }
}