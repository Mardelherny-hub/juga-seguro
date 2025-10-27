<?php

namespace App\Livewire\Agent\BankAccounts;

use Livewire\Component;
use App\Models\BankAccount;
use App\Livewire\Traits\WithToast;
use Livewire\Attributes\On;

class ManageBankAccounts extends Component
{
    use WithToast;

    public $showModal = false;
    public $editMode = false;
    public $accountId = null;

    // Campos del formulario
    public $account_holder = '';
    public $bank_name = '';
    public $alias = '';
    public $cbu = '';
    public $cvu = '';
    public $notes = '';
    public $status = 'active';

    protected function rules()
    {
        return [
            'account_holder' => 'required|string|min:3|max:255',
            'bank_name' => 'nullable|string|max:100',
            'alias' => 'nullable|string|max:100',
            'cbu' => 'nullable|string|size:22',
            'cvu' => 'nullable|string|size:22',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ];
    }

    protected $messages = [
        'account_holder.required' => 'El titular es obligatorio',
        'account_holder.min' => 'El titular debe tener al menos 3 caracteres',
        'cbu.size' => 'El CBU debe tener exactamente 22 dígitos',
        'cvu.size' => 'El CVU debe tener exactamente 22 dígitos',
    ];

    #[On('openCreateBankAccount')]
    public function openCreate()
    {
        $this->reset(['account_holder', 'bank_name', 'alias', 'cbu', 'cvu', 'notes', 'status']);
        $this->editMode = false;
        $this->showModal = true;
    }

    #[On('openEditBankAccount')]
    public function openEdit($id)
    {
        $account = BankAccount::findOrFail($id);
        
        $this->accountId = $account->id;
        $this->account_holder = $account->account_holder;
        $this->bank_name = $account->bank_name;
        $this->alias = $account->alias;
        $this->cbu = $account->cbu;
        $this->cvu = $account->cvu;
        $this->notes = $account->notes;
        $this->status = $account->status;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;

        if ($this->editMode) {
            // Actualizar
            $account = BankAccount::findOrFail($this->accountId);
            $account->update([
                'account_holder' => $this->account_holder,
                'bank_name' => $this->bank_name,
                'alias' => $this->alias,
                'cbu' => $this->cbu,
                'cvu' => $this->cvu,
                'notes' => $this->notes,
                'status' => $this->status,
            ]);

            activity()
                ->performedOn($account)
                ->causedBy(auth()->user())
                ->log('Cuenta bancaria actualizada');

            $this->showToast('Cuenta actualizada correctamente', 'success');
        } else {
            // Crear nueva
            $account = BankAccount::create([
                'tenant_id' => $tenant->id,
                'account_holder' => $this->account_holder,
                'bank_name' => $this->bank_name,
                'alias' => $this->alias,
                'cbu' => $this->cbu,
                'cvu' => $this->cvu,
                'notes' => $this->notes,
                'status' => $this->status,
                'is_active' => false, // Por defecto no activa
            ]);

            activity()
                ->performedOn($account)
                ->causedBy(auth()->user())
                ->log('Cuenta bancaria creada');

            $this->showToast('Cuenta creada correctamente', 'success');
        }

        $this->closeModal();
        $this->dispatch('bankAccountUpdated');
    }

    public function setActive($id)
    {
        $tenant = auth()->user()->tenant;

        // Desactivar todas las cuentas del tenant
        BankAccount::where('tenant_id', $tenant->id)
            ->update(['is_active' => false]);

        // Activar la seleccionada
        $account = BankAccount::findOrFail($id);
        $account->update(['is_active' => true]);

        activity()
            ->performedOn($account)
            ->causedBy(auth()->user())
            ->log('Cuenta bancaria marcada como activa');

        $this->showToast('Cuenta activa actualizada', 'success');
        $this->dispatch('bankAccountUpdated');
    }

    public function delete($id)
    {
        $account = BankAccount::findOrFail($id);

        if ($account->is_active) {
            $this->showToast('No puedes eliminar la cuenta activa', 'error');
            return;
        }

        activity()
            ->performedOn($account)
            ->causedBy(auth()->user())
            ->log('Cuenta bancaria eliminada');

        $account->delete();

        $this->showToast('Cuenta eliminada correctamente', 'success');
        $this->dispatch('bankAccountUpdated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['account_holder', 'bank_name', 'alias', 'cbu', 'cvu', 'notes', 'status', 'accountId', 'editMode']);
        $this->resetValidation();
    }

    #[On('bankAccountUpdated')]
public function refreshAccounts()
{
    // Simplemente refrescar el componente
}

public function render()
{
    $tenant = auth()->user()->tenant;
    
    $accounts = BankAccount::where('tenant_id', $tenant->id)
        ->orderBy('is_active', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('livewire.agent.bank-accounts.manage-bank-accounts')
        ->with('accounts', $accounts);
}
}