<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;
use Illuminate\Support\Facades\DB;

class TransactionApproval extends Component
{
    use WithToast;

    public $showModal = false;
    public $transaction = null;
    public $player = null;
    public $isProcessing = false;
    
    
    // Cálculos para retiros
    public $currentBalance = 0;
    public $newBalance = 0;
    public $hasSufficientBalance = true;
    // Campos para credenciales de cuenta
    public $username = '';
    public $password = '';

    #[On('openTransactionApproval')]
    public function openModal($transactionId)
    {
        $this->transaction = Transaction::with(['player', 'processor'])
            ->findOrFail($transactionId);
        
        $this->player = $this->transaction->player;
        
        // Validar que esté pendiente
        if ($this->transaction->status !== 'pending') {
            $this->showToast('Esta transacción ya fue procesada', 'error');
            return;
        }
        
        // Cálculos para retiros
        if ($this->transaction->type === 'withdrawal') {
            $this->currentBalance = $this->player->balance;
            $this->newBalance = $this->currentBalance - $this->transaction->amount;
            $this->hasSufficientBalance = $this->currentBalance >= $this->transaction->amount;
            // Resetear campos de credenciales
            $this->username = '';
            $this->password = '';
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['transaction', 'player', 'isProcessing', 'currentBalance', 'newBalance', 'hasSufficientBalance', 'username', 'password']);
    }

    public function approve()
    {
        if (!$this->transaction) {
            return;
        }

        // Validar credenciales si son requeridas
        if ($this->transaction->requiresCredentials()) {
            if ($this->transaction->type === 'account_creation') {
                if (empty($this->username) || empty($this->password)) {
                    $this->showToast('Debes ingresar usuario y contraseña para crear la cuenta', 'error');
                    return;
                }
                
                // Validar formato de username
                if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]{3,14}$/', $this->username)) {
                    $this->showToast('El usuario debe empezar con letra y tener entre 4-15 caracteres alfanuméricos', 'error');
                    return;
                }
                
                // Validar que username no exista ya
                $existingPlayer = \App\Models\Player::where('tenant_id', $this->player->tenant_id)
                    ->where('username', strtolower($this->username))
                    ->where('id', '!=', $this->player->id)
                    ->exists();
                    
                if ($existingPlayer) {
                    $this->showToast('Este nombre de usuario ya existe', 'error');
                    return;
                }
                
                if (strlen($this->password) < 6) {
                    $this->showToast('La contraseña debe tener al menos 6 caracteres', 'error');
                    return;
                }
            }
            
            if ($this->transaction->type === 'password_reset') {
                if (empty($this->password)) {
                    $this->showToast('Debes ingresar la nueva contraseña', 'error');
                    return;
                }
                
                if (strlen($this->password) < 6) {
                    $this->showToast('La contraseña debe tener al menos 6 caracteres', 'error');
                    return;
                }
            }
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                $user = auth()->user();
                
                // Para solicitudes de cuenta, guardar credenciales en notes
                $notes = $this->transaction->notes;
                
                if ($this->transaction->type === 'account_creation' && $this->username && $this->password) {
                    $notes = "Usuario: {$this->username} | Contraseña: {$this->password}";
                } elseif ($this->transaction->type === 'password_reset' && $this->password) {
                    $notes = "Nueva contraseña: {$this->password}";
                }
                
                // Actualizar la transacción
                $this->transaction->update([
                    'status' => 'completed',
                    'processed_by' => $user->id,
                    'processed_at' => now(),
                    'notes' => $notes,
                ]);
                
                // Si es depósito o retiro, usar el TransactionService
                if ($this->transaction->type === 'deposit' || $this->transaction->type === 'withdrawal') {
                    $transactionService = app(\App\Services\TransactionService::class);
                    
                    if ($this->transaction->type === 'deposit') {
                        $this->player->increment('balance', $this->transaction->amount);
                        $this->transaction->update(['balance_after' => $this->player->fresh()->balance]);
                    } elseif ($this->transaction->type === 'withdrawal') {
                        if ($this->player->balance < $this->transaction->amount) {
                            throw new \Exception('Saldo insuficiente');
                        }
                        $this->player->decrement('balance', $this->transaction->amount);
                        $this->transaction->update(['balance_after' => $this->player->fresh()->balance]);
                    }
                }
                
                // Activity log
                activity()
                    ->performedOn($this->transaction)
                    ->causedBy($user)
                    ->log('transaction_approved');
            });
            
            $this->showToast('Solicitud aprobada correctamente', 'success');
            $this->dispatch('transactionUpdated');
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->showToast($e->getMessage(), 'error');
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.transaction-approval');
    }
}