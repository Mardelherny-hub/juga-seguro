<?php

namespace App\Livewire\Agent\Transactions;

use App\Models\Transaction;
use App\Services\MessageService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Traits\WithToast;

class ApproveTransaction extends Component
{
    use WithToast;

    public $transaction;
    public $isOpen = false;
    public $notes = '';
    
    // Credenciales para solicitudes de cuenta
    public $username = '';
    public $password = '';

    protected $listeners = ['openApprovalModal' => 'open'];

    public function open($transactionId)
    {
        $this->transaction = Transaction::with('player')->findOrFail($transactionId);
        $this->isOpen = true;
    }

    public function close()
    {
        $this->reset(['notes', 'username', 'password']);
        $this->isOpen = false;
    }

    protected function rules()
    {
        $rules = ['notes' => 'nullable|string|max:500'];
        
        // Validar credenciales solo para tipos que las requieren
        if ($this->transaction && in_array($this->transaction->type, ['account_creation', 'password_reset'])) {
            if ($this->transaction->type === 'account_creation') {
                $rules['username'] = 'required|string|min:4|max:20';
            }
            $rules['password'] = 'required|string|min:6';
        }
        
        return $rules;
    }

    protected $messages = [
        'username.required' => 'El usuario es obligatorio',
        'username.min' => 'El usuario debe tener al menos 4 caracteres',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres',
    ];

    public function approve()
    {
        // Validar credenciales si es necesario
        if ($this->transaction && in_array($this->transaction->type, ['account_creation', 'password_reset'])) {
            $this->validate();
        }

        DB::transaction(function () {
            $player = $this->transaction->player;

            if ($this->transaction->type === 'deposit') {
                // Incrementar saldo
                $player->increment('balance', $this->transaction->amount);
            } elseif ($this->transaction->type === 'withdrawal') {
                // Decrementar saldo (verificar que tenga suficiente)
                if ($player->balance < $this->transaction->amount) {
                    $this->showToast('El jugador no tiene saldo suficiente', 'error');
                    return;
                }
                $player->decrement('balance', $this->transaction->amount);
            }

            // Actualizar transacción
            $this->transaction->update([
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'balance_after' => $player->balance,
                'notes' => $this->notes ?: $this->transaction->notes,
            ]);

            // Enviar notificación con credenciales si es solicitud de cuenta
            if ($this->transaction->isAccountRequest()) {
                $this->notifyPlayerWithCredentials();
                
                // Si es creación de cuenta, marcar como vinculado al casino
                if ($this->transaction->type === 'account_creation') {
                    $player->linkCasino();
                }
            }

            // Activity log
            activity()
                ->performedOn($this->transaction)
                ->causedBy(auth()->user())
                ->log('transaction_approved');
        });

        $this->showToast('Transacción aprobada correctamente', 'success');

        $this->dispatch('transactionProcessed');
        $this->close();
    }

    private function notifyPlayerWithCredentials()
    {
        $messageService = app(MessageService::class);
        $player = $this->transaction->player;
        
        if ($this->transaction->type === 'account_creation') {
            $message = "✅ Tu usuario fue creado correctamente:\n\n";
            $message .= "👤 Usuario: {$this->username}\n";
            $message .= "🔑 Contraseña: {$this->password}\n\n";
            $message .= "¡Ya puedes acceder a la plataforma de juego!";
            
            $messageService->sendSystemMessage($player, $message, 'account', $this->transaction);
            
        } elseif ($this->transaction->type === 'account_unlock') {
            $message = "✅ Tu cuenta fue desbloqueada correctamente.\n\n";
            $message .= "Ya puedes acceder a la plataforma de juego.";
            
            $messageService->sendSystemMessage($player, $message, 'account', $this->transaction);
            
        } elseif ($this->transaction->type === 'password_reset') {
            $message = "✅ Tu contraseña fue cambiada correctamente:\n\n";
            $message .= "🔑 Nueva Contraseña: {$this->password}\n\n";
            $message .= "Usa esta nueva contraseña para acceder a la plataforma.";
            
            $messageService->sendSystemMessage($player, $message, 'account', $this->transaction);
        }
    }

    public function render()
    {
        return view('livewire.agent.transactions.approve-transaction');
    }
}