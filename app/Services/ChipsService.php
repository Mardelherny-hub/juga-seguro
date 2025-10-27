<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChipsService
{
    /**
     * Cargar fichas a un tenant (Super Admin)
     */
    public function loadChips(Tenant $tenant, int $quantity, float $amountPaid, $loadedBy): array
    {
        return DB::transaction(function () use ($tenant, $quantity, $amountPaid, $loadedBy) {
            
            // Validar que sea tipo prepaid
            if ($tenant->subscription_type !== 'prepaid') {
                throw new \InvalidArgumentException('Solo se pueden cargar fichas a tenants tipo PREPAID');
            }

            if ($quantity <= 0) {
                throw new \InvalidArgumentException('La cantidad debe ser mayor a cero');
            }

            $previousBalance = $tenant->chips_balance;
            $newBalance = $previousBalance + $quantity;

            // Actualizar saldo de fichas
            $tenant->chips_balance = $newBalance;
            $tenant->save();

            // Registrar en activity log
            activity()
                ->performedOn($tenant)
                ->causedBy($loadedBy)
                ->withProperties([
                    'quantity' => $quantity,
                    'amount_paid' => $amountPaid,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $newBalance,
                    'chip_price' => $tenant->chip_price
                ])
                ->log('chips_loaded');

            return [
                'success' => true,
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance,
                'quantity_loaded' => $quantity,
                'amount_paid' => $amountPaid
            ];
        });
    }

    /**
     * Descontar ficha al aprobar depósito (solo prepaid)
     */
    public function consumeChip(Tenant $tenant, Transaction $transaction): bool
    {
        return DB::transaction(function () use ($tenant, $transaction) {
            
            // Si es monthly, no consume fichas
            if ($tenant->subscription_type === 'monthly') {
                return true; // Puede aprobar sin límite
            }

            // Si es prepaid, validar saldo
            if ($tenant->chips_balance <= 0) {
                throw new \InvalidArgumentException('No tienes fichas disponibles. Contacta al administrador.');
            }

            // Descontar 1 ficha
            $previousBalance = $tenant->chips_balance;
            $tenant->decrement('chips_balance', 1);
            $tenant->refresh();

            // Registrar consumo
            activity()
                ->performedOn($tenant)
                ->causedBy(auth()->user())
                ->withProperties([
                    'transaction_id' => $transaction->id,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $tenant->chips_balance,
                    'player_id' => $transaction->player_id
                ])
                ->log('chip_consumed');

            return true;
        });
    }

    /**
     * Verificar si el tenant puede aprobar transacciones
     */
    public function canApproveTransactions(Tenant $tenant): bool
    {
        // Monthly: siempre puede (ilimitado)
        if ($tenant->subscription_type === 'monthly') {
            return true;
        }

        // Prepaid: solo si tiene fichas
        return $tenant->chips_balance > 0;
    }

    /**
     * Obtener información del saldo de fichas
     */
    public function getChipsInfo(Tenant $tenant): array
    {
        return [
            'subscription_type' => $tenant->subscription_type,
            'chips_balance' => $tenant->chips_balance,
            'chip_price' => $tenant->chip_price,
            'can_approve' => $this->canApproveTransactions($tenant),
            'total_value' => $tenant->chips_balance * $tenant->chip_price
        ];
    }
}