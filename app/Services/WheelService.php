<?php

namespace App\Services;

use App\Models\Player;
use App\Models\WheelSpin;
use Illuminate\Support\Facades\DB;

class WheelService
{
    protected $bonusService;
    protected $messageService;

    public function __construct(BonusService $bonusService, MessageService $messageService)
    {
        $this->bonusService = $bonusService;
        $this->messageService = $messageService;
    }

    /**
     * Verificar si el jugador puede girar hoy
     */
    public function canSpinToday(Player $player): bool
    {
        $todaySpins = WheelSpin::where('player_id', $player->id)
            ->whereDate('created_at', today())
            ->count();
        
        return $todaySpins === 0;
    }

    /**
     * Obtener configuración de premios (hardcoded por ahora, TODO: hacer configurable)
     */
    public function getPrizeConfiguration(): array
    {
        return [
            ['type' => 'cash', 'amount' => 50, 'probability' => 10, 'label' => '$50'],
            ['type' => 'cash', 'amount' => 100, 'probability' => 5, 'label' => '$100'],
            ['type' => 'cash', 'amount' => 500, 'probability' => 2, 'label' => '$500'],
            ['type' => 'bonus', 'amount' => 100, 'probability' => 15, 'label' => '+$100 Bono'],
            ['type' => 'bonus', 'amount' => 200, 'probability' => 8, 'label' => '+$200 Bono'],
            ['type' => 'free_spin', 'amount' => 0, 'probability' => 10, 'label' => 'Giro Extra'],
            ['type' => 'nothing', 'amount' => 0, 'probability' => 50, 'label' => 'Sigue Intentando'],
        ];
    }

    /**
     * Girar la ruleta
     */
    public function spin(Player $player): array
    {
        if (!$this->canSpinToday($player)) {
            throw new \Exception('Ya giraste la ruleta hoy. Vuelve mañana!');
        }

        return DB::transaction(function () use ($player) {
            // Seleccionar premio basado en probabilidades
            $prize = $this->selectPrize();
            
            // Crear registro del giro
            $spin = WheelSpin::create([
                'tenant_id' => $player->tenant_id,
                'player_id' => $player->id,
                'prize_amount' => $prize['amount'],
                'prize_type' => $prize['type'],
                'prize_description' => $prize['label'],
            ]);

            // Procesar el premio
            $this->processPrize($player, $prize, $spin);

            return [
                'prize' => $prize,
                'spin' => $spin,
            ];
        });
    }

    /**
     * Seleccionar premio basado en probabilidades
     */
    protected function selectPrize(): array
    {
        $prizes = $this->getPrizeConfiguration();
        $totalProbability = array_sum(array_column($prizes, 'probability'));
        $random = rand(1, $totalProbability);
        
        $currentProbability = 0;
        foreach ($prizes as $prize) {
            $currentProbability += $prize['probability'];
            if ($random <= $currentProbability) {
                return $prize;
            }
        }
        
        // Fallback (no debería llegar aquí)
        return end($prizes);
    }

    /**
     * Procesar el premio ganado
     */
    protected function processPrize(Player $player, array $prize, WheelSpin $spin): void
    {
        switch ($prize['type']) {
            case 'cash':
                // Agregar dinero directo al saldo
                $player->increment('balance', $prize['amount']);
                
                // Mensaje
                $this->messageService->sendSystemMessage(
                    $player,
                    "🎰 ¡Felicitaciones! Ganaste \${$prize['amount']} en la ruleta. El dinero ya está en tu saldo.",
                    'bonus'
                );
                break;
                
            case 'bonus':
                // Otorgar bono
                $bonus = $this->bonusService->grantBonus(
                    $player,
                    'spin_wheel',
                    $prize['amount'],
                    "Premio de ruleta: {$prize['label']}"
                );
                
                $spin->update(['bonus_id' => $bonus->id]);
                break;
                
            case 'free_spin':
                // TODO: implementar giro extra (por ahora solo mensaje)
                $this->messageService->sendSystemMessage(
                    $player,
                    "🎰 ¡Ganaste un giro extra! Vuelve mañana para otro intento.",
                    'bonus'
                );
                break;
                
            case 'nothing':
                // Mensaje de ánimo
                $this->messageService->sendSystemMessage(
                    $player,
                    "🎰 No ganaste esta vez, pero puedes intentar mañana. ¡Suerte!",
                    'general'
                );
                break;
        }

        // Activity log
        activity()
            ->performedOn($spin)
            ->causedBy($player)
            ->withProperties([
                'prize_type' => $prize['type'],
                'prize_amount' => $prize['amount'],
            ])
            ->log("Giró la ruleta y ganó: {$prize['label']}");
    }

    /**
     * Obtener historial de giros del jugador
     */
    public function getPlayerSpins(Player $player, int $limit = 10)
    {
        return WheelSpin::where('player_id', $player->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}