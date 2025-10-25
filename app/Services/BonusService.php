<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\Player;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BonusService
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Otorgar un bono a un jugador
     */
    public function grantBonus(
        Player $player,
        string $type,
        float $amount,
        ?string $description = null,
        ?\DateTime $expiresAt = null
    ): Bonus {
        return DB::transaction(function () use ($player, $type, $amount, $description, $expiresAt) {
            
            // Actualizar saldo del jugador
            $player->increment('balance', $amount);
            
            // Crear transacción de tipo bonus
            // Crear transacción de tipo bonus
            $transaction = Transaction::create([
                'tenant_id' => $player->tenant_id,
                'player_id' => $player->id,
                'type' => 'bonus',
                'amount' => $amount,
                'balance_before' => $player->balance, // ← AGREGAR ESTA LÍNEA
                'status' => 'completed',
                'description' => $description ?? "Bono {$type}",
                'hash' => Str::random(32),
            ]);
            
            // Crear registro del bono
            $bonus = Bonus::create([
                'tenant_id' => $player->tenant_id,
                'player_id' => $player->id,
                'type' => $type,
                'amount' => $amount,
                'status' => 'used', // Ya se aplicó al saldo
                'used_at' => now(),
                'expires_at' => $expiresAt,
                'related_transaction_id' => $transaction->id,
                'description' => $description,
            ]);
            
            // Mensaje automático
            $this->messageService->notifyBonusGranted($player, $amount, $type);
            
            // Activity log
            activity()
                ->performedOn($bonus)
                ->causedBy($player)
                ->withProperties([
                    'amount' => $amount,
                    'type' => $type,
                    'transaction_id' => $transaction->id
                ])
                ->log("Bono otorgado: {$type}");
            
            return $bonus;
        });
    }

    /**
     * Bono de bienvenida
     */
    public function grantWelcomeBonus(Player $player, float $amount): Bonus
    {
        return $this->grantBonus(
            $player,
            'welcome',
            $amount,
            '¡Bienvenido! Bono de registro'
        );
    }

    /**
     * Bono por referido
     */
    public function grantReferralBonus(Player $player, float $amount, string $referredName): Bonus
    {
        return $this->grantBonus(
            $player,
            'referral',
            $amount,
            "Bono por referir a {$referredName}"
        );
    }

    /**
     * Bono personalizado (manual desde admin)
     */
    public function grantCustomBonus(Player $player, float $amount, string $description): Bonus
    {
        return $this->grantBonus(
            $player,
            'custom',
            $amount,
            $description
        );
    }

    /**
     * Verificar si un jugador ya recibió un tipo de bono
     */
    public function hasReceivedBonus(Player $player, string $type): bool
    {
        return Bonus::where('player_id', $player->id)
            ->where('type', $type)
            ->exists();
    }

    /**
     * Obtener bonos de un jugador
     */
    public function getPlayerBonuses(Player $player)
    {
        return Bonus::where('player_id', $player->id)
            ->with('relatedTransaction')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas de bonos por tenant
     */
    public function getTenantBonusStats(int $tenantId)
    {
        return [
            'total_bonuses' => Bonus::where('tenant_id', $tenantId)->count(),
            'total_amount' => Bonus::where('tenant_id', $tenantId)->sum('amount'),
            'by_type' => Bonus::where('tenant_id', $tenantId)
                ->select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->groupBy('type')
                ->get(),
        ];
    }
}