<?php

namespace App\Livewire\Player;

use App\Models\PlayerWithdrawalAccount;
use Livewire\Component;
use App\Livewire\Traits\WithToast;

class WithdrawalRequest extends Component
{
    use WithToast;

    public $isOpen = false;
    public $amount = '';
    public $selectedAccountId = null;
    public $savedAccounts = [];
    
    public $tenant;
    public $player;
    
    protected $listeners = ['openWithdrawalModal' => 'open'];

    public function mount()
    {
        $this->player = auth()->guard('player')->user();
        $this->tenant = $this->player->tenant;
    }

    public function loadSavedAccounts()
    {
        $this->savedAccounts = $this->player->withdrawalAccounts()->get();
        
        // Preseleccionar la cuenta predeterminada
        $defaultAccount = $this->savedAccounts->firstWhere('is_default', true);
        if ($defaultAccount) {
            $this->selectedAccountId = $defaultAccount->id;
        } elseif ($this->savedAccounts->isNotEmpty()) {
            $this->selectedAccountId = $this->savedAccounts->first()->id;
        }
    }

    public function open()
    {
        // Verificar que no tenga NINGUNA solicitud pendiente
        $hasPendingRequest = $this->player->transactions()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            $this->showToast('Ya tienes una solicitud pendiente. Espera a que sea procesada antes de solicitar un retiro.', 'error');
            return;
        }

        $this->loadSavedAccounts();
        $this->isOpen = true;
    }

    public function close()
    {
        $this->reset(['amount', 'selectedAccountId']);
        $this->isOpen = false;
    }

    protected function rules()
    {
        $rules = [
            'amount' => ['required', 'numeric', 'min:500'],
        ];

        if ($this->savedAccounts->isNotEmpty()) {
            $rules['selectedAccountId'] = 'required|exists:player_withdrawal_accounts,id';
        }

        return $rules;
    }

    protected $messages = [
        'amount.required' => 'El monto es obligatorio',
        'amount.min' => 'El monto mínimo de retiro es $500',
        'selectedAccountId.required' => 'Selecciona una cuenta',
    ];

    public function submit()
    {
        // Verificar que tenga cuentas
        if ($this->savedAccounts->isEmpty()) {
            $this->showToast('Primero debes agregar una cuenta de retiro', 'error');
            return;
        }

        $this->validate();

        $account = PlayerWithdrawalAccount::findOrFail($this->selectedAccountId);

        // Preparar notas
        $notes = "Método: Transferencia Bancaria\n";
        $notes .= "Tipo: " . strtoupper($account->account_type) . "\n";
        
        if ($account->account_type === 'alias') {
            $notes .= "Alias: {$account->alias}\n";
        } else {
            $notes .= "Cuenta: {$account->account_number}\n";
        }
        
        $notes .= "Titular: {$account->holder_name}\n";
        
        if (!empty($account->holder_dni)) {
            $notes .= "DNI: {$account->holder_dni}\n";
        }
        
        if (!empty($account->bank_name)) {
            $notes .= "Banco: {$account->bank_name}\n";
        }
        
        $notes .= "Cuenta ID: {$account->id}";

        // Crear transacción
        $transaction = $this->player->transactions()->create([
            'tenant_id' => $this->tenant->id,
            'type' => 'withdrawal',
            'amount' => $this->amount,
            'balance_before' => $this->player->balance,
            'balance_after' => $this->player->balance,
            'status' => 'pending',
            'notes' => $notes,
        ]);

        // Activity log
        activity()
            ->performedOn($transaction)
            ->causedBy($this->player)
            ->withProperties([
                'amount' => $this->amount,
                'account_id' => $account->id,
            ])
            ->log('withdrawal_requested');

        $this->showToast('¡Solicitud de retiro enviada! Te avisaremos cuando sea procesada.', 'success');
        $this->dispatch('refreshDashboard');
        $this->close();
    }

    public function render()
    {
        return view('livewire.player.withdrawal-request');
    }
}