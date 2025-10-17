<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * Procesar depósito
     */
    public function processDeposit(Player $player, float $amount, ?string $proofUrl = null, ?User $processor = null): Transaction
    {
        return DB::transaction(function () use ($player, $amount, $proofUrl, $processor) {
            // Lock del jugador para evitar condiciones de carrera
            $player->lockForUpdate();
            $player->refresh();

            // Validaciones
            if ($amount <= 0) {
                throw new \InvalidArgumentException('El monto debe ser mayor a cero');
            }

            // Crear transacción
            $transaction = Transaction::create([
                'player_id' => $player->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $player->balance,
                'balance_after' => $player->balance + $amount,
                'status' => $processor ? 'completed' : 'pending',
                'proof_url' => $proofUrl,
                'processed_by' => $processor?->id,
                'processed_at' => $processor ? now() : null,
            ]);

            // Si está procesado, actualizar saldo
            if ($processor) {
                $player->increment('balance', $amount);
                
                activity()
                    ->performedOn($transaction)
                    ->causedBy($processor)
                    ->withProperties([
                        'amount' => $amount,
                        'player_id' => $player->id,
                        'new_balance' => $player->fresh()->balance,
                    ])
                    ->log('deposit_completed');
            } else {
                activity()
                    ->performedOn($transaction)
                    ->withProperties(['amount' => $amount, 'player_id' => $player->id])
                    ->log('deposit_pending');
            }

            return $transaction;
        });
    }

    /**
     * Procesar retiro
     */
    public function processWithdrawal(Player $player, float $amount, ?User $processor = null): Transaction
    {
        return DB::transaction(function () use ($player, $amount, $processor) {
            // Lock del jugador
            $player->lockForUpdate();
            $player->refresh();

            // Validaciones
            if ($amount <= 0) {
                throw new \InvalidArgumentException('El monto debe ser mayor a cero');
            }

            if ($player->balance < $amount) {
                throw new \InvalidArgumentException('Saldo insuficiente');
            }

            // Crear transacción
            $transaction = Transaction::create([
                'player_id' => $player->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_before' => $player->balance,
                'balance_after' => $player->balance - $amount,
                'status' => 'pending',
                'processed_by' => $processor?->id,
            ]);

            activity()
                ->performedOn($transaction)
                ->causedBy($processor)
                ->withProperties(['amount' => $amount, 'player_id' => $player->id])
                ->log('withdrawal_requested');

            return $transaction;
        });
    }

    /**
     * Aprobar transacción pendiente
     */
    public function approveTransaction(Transaction $transaction, User $user): void
    {
        DB::transaction(function () use ($transaction, $user) {
            $transaction->lockForUpdate();
            $player = $transaction->player()->lockForUpdate()->first();

            if ($transaction->status !== 'pending') {
                throw new \InvalidArgumentException('La transacción ya fue procesada');
            }

            // Actualizar saldo según tipo
            if ($transaction->type === 'deposit') {
                $player->increment('balance', $transaction->amount);
            } elseif ($transaction->type === 'withdrawal') {
                if ($player->balance < $transaction->amount) {
                    throw new \InvalidArgumentException('Saldo insuficiente');
                }
                $player->decrement('balance', $transaction->amount);
            }

            // Completar transacción
            $transaction->update([
                'status' => 'completed',
                'processed_by' => $user->id,
                'processed_at' => now(),
                'balance_after' => $player->fresh()->balance,
            ]);

            activity()
                ->performedOn($transaction)
                ->causedBy($user)
                ->withProperties([
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'new_balance' => $player->balance,
                ])
                ->log('transaction_approved');
        });
    }

    /**
     * Rechazar transacción
     */
    public function rejectTransaction(Transaction $transaction, User $user, string $reason): void
    {
        DB::transaction(function () use ($transaction, $user, $reason) {
            $transaction->lockForUpdate();

            if ($transaction->status !== 'pending') {
                throw new \InvalidArgumentException('La transacción ya fue procesada');
            }

            $transaction->update([
                'status' => 'rejected',
                'processed_by' => $user->id,
                'processed_at' => now(),
                'notes' => $reason,
            ]);

            activity()
                ->performedOn($transaction)
                ->causedBy($user)
                ->withProperties(['reason' => $reason])
                ->log('transaction_rejected');
        });
    }

    /**
     * Otorgar bono
     */
    public function grantBonus(Player $player, float $amount, string $type, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($player, $amount, $type, $description) {
            $player->lockForUpdate();
            $player->refresh();

            if ($amount <= 0) {
                throw new \InvalidArgumentException('El monto debe ser mayor a cero');
            }

            // Crear transacción de bono
            $transaction = Transaction::create([
                'player_id' => $player->id,
                'type' => 'bonus',
                'amount' => $amount,
                'balance_before' => $player->balance,
                'balance_after' => $player->balance + $amount,
                'status' => 'completed',
                'processed_at' => now(),
                'notes' => $description,
            ]);

            // Actualizar saldo
            $player->increment('balance', $amount);

            // Crear registro de bono
            \App\Models\Bonus::create([
                'player_id' => $player->id,
                'type' => $type,
                'amount' => $amount,
                'status' => 'used',
                'used_at' => now(),
                'related_transaction_id' => $transaction->id,
                'description' => $description,
            ]);

            activity()
                ->performedOn($transaction)
                ->withProperties([
                    'amount' => $amount,
                    'type' => $type,
                    'new_balance' => $player->fresh()->balance,
                ])
                ->log('bonus_granted');

            return $transaction;
        });
    }
}