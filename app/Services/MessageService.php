<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerMessage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WebPushService;

class MessageService
{
    protected WebPushService $webPushService;

    public function __construct(WebPushService $webPushService)
    {
        $this->webPushService = $webPushService;
    }
    /**
     * Enviar mensaje del sistema (automático)
     */
    public function sendSystemMessage(
        Player $player, 
        string $message, 
        string $category = 'general',
        ?Transaction $transaction = null
    ): PlayerMessage {
        return PlayerMessage::create([
            'tenant_id' => $player->tenant_id,
            'player_id' => $player->id,
            'sender_type' => 'system',
            'sender_id' => null,
            'message' => $message,
            'category' => $category,
            'transaction_id' => $transaction?->id,
        ]);
    }

    /**
     * Enviar mensaje del jugador
     */
    public function sendPlayerMessage(
        Player $player, 
        string $message, 
        string $category = 'support',
        ?string $imagePath = null
    ): PlayerMessage {
        $playerMessage = PlayerMessage::create([
            'tenant_id' => $player->tenant_id,
            'player_id' => $player->id,
            'sender_type' => 'player',
            'sender_id' => $player->id,
            'message' => $message,
            'image_path' => $imagePath,
            'category' => $category,
            'read_by_agent_at' => null, // Forzar que agent lo vea
        ]);

        // Log de actividad
        activity()
            ->performedOn($playerMessage)
            ->causedBy($player)
            ->log('Mensaje enviado por jugador');

            // Push notification a agentes del tenant
            try {
                $this->webPushService->sendToTenantUsers(
                    $player->tenant,
                    '💬 Nuevo mensaje de ' . $player->display_name,
                    \Str::limit($message, 50),
                    '/dashboard/messages/' . $player->id
                );
            } catch (\Exception $e) {
                // Silenciar error de push
            }

        return $playerMessage;
    }

    /**
     * Enviar mensaje del agente
     */
    public function sendAgentMessage(
        Player $player, 
        User $agent,
        string $message, 
        string $category = 'support',
        ?string $imagePath = null
    ): PlayerMessage {
        $playerMessage = PlayerMessage::create([
            'tenant_id' => $player->tenant_id,
            'player_id' => $player->id,
            'sender_type' => 'agent',
            'sender_id' => $agent->id,
            'message' => $message,
            'image_path' => $imagePath,
            'category' => $category,
            'read_by_player_at' => null, // Forzar que player lo vea
        ]);

        // Log de actividad
        activity()
            ->performedOn($playerMessage)
            ->causedBy($agent)
            ->log('Mensaje enviado por agente');

            // Push notification al jugador
            try {
                $this->webPushService->sendToPlayer(
                    $player,
                    '💬 Nuevo mensaje del operador',
                    \Str::limit($message, 50),
                    '/messages'
                );
            } catch (\Exception $e) {
                // Silenciar error de push
            }

        return $playerMessage;
    }

    /**
     * Marcar mensajes como leídos por el jugador
     */
    public function markAsReadByPlayer(Player $player): int
    {
        return PlayerMessage::where('player_id', $player->id)
            ->whereNull('read_by_player_at')
            ->update(['read_by_player_at' => now()]);
    }

    /**
     * Marcar mensajes como leídos por el agente
     */
    public function markAsReadByAgent(Player $player): int
    {
        return PlayerMessage::where('player_id', $player->id)
            ->whereNull('read_by_agent_at')
            ->update(['read_by_agent_at' => now()]);
    }

    /**
     * Contar mensajes no leídos por el jugador
     */
    public function getUnreadCountForPlayer(Player $player): int
    {
        return PlayerMessage::where('player_id', $player->id)
            ->whereNull('read_by_player_at')
            ->count();
    }

    /**
     * Contar mensajes no leídos por el agente (todos los jugadores del tenant)
     */
    public function getUnreadCountForAgent(int $tenantId): int
    {
        return PlayerMessage::where('tenant_id', $tenantId)
            ->where('sender_type', 'player')
            ->whereNull('read_by_agent_at')
            ->count();
    }

    /**
     * Obtener mensajes de un jugador
     */
    public function getPlayerMessages(Player $player, int $limit = 50)
    {
        return PlayerMessage::where('player_id', $player->id)
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * MENSAJES AUTOMÁTICOS - Transacciones
     */
    
    public function notifyDepositRequest(Transaction $transaction): void
    {
        // Mensaje para el jugador
        $this->sendSystemMessage(
            $transaction->player,
            "✅ Recibimos tu solicitud de depósito de $" . number_format($transaction->amount, 2) . ". Te notificaremos cuando sea procesada.",
            'transaction',
            $transaction
        );

        // Mensaje para el agente (mismo mensaje para ambos)
        $this->sendSystemMessage(
            $transaction->player,
            "🔔 Nueva solicitud de depósito de $" . number_format($transaction->amount, 2) . " del jugador {$transaction->player->display_name}.",
            'transaction',
            $transaction
        );
    }

    public function notifyDepositApproved(Transaction $transaction): void
    {
        $this->sendSystemMessage(
            $transaction->player,
            "✅ Tu depósito de $" . number_format($transaction->amount, 2) . " fue aprobado. El saldo ya está disponible en tu cuenta.",
            'transaction',
            $transaction
        );
    }

    public function notifyDepositRejected(Transaction $transaction, string $reason): void
    {
        $reasonText = empty(trim($reason)) ? 'Contacta a soporte para más información.' : $reason;
        
        $this->sendSystemMessage(
            $transaction->player,
            "❌ Tu depósito de $" . number_format($transaction->amount, 2) . " fue rechazado. Motivo: {$reasonText}",
            'transaction',
            $transaction
        );
    }

    public function notifyWithdrawalRequest(Transaction $transaction): void
    {
        // Mensaje para el jugador
        $this->sendSystemMessage(
            $transaction->player,
            "✅ Recibimos tu solicitud de retiro de $" . number_format($transaction->amount, 2) . ". Será procesada en breve.",
            'transaction',
            $transaction
        );
    }

    public function notifyWithdrawalRejected(Transaction $transaction, string $reason): void
    {
        $reasonText = empty(trim($reason)) ? 'Contacta a soporte para más información.' : $reason;
        
        $this->sendSystemMessage(
            $transaction->player,
            "❌ Tu retiro de $" . number_format($transaction->amount, 2) . " fue rechazado. Motivo: {$reasonText}",
            'transaction',
            $transaction
        );
    }

    public function notifyWithdrawalApproved(Transaction $transaction): void
    {
        $this->sendSystemMessage(
            $transaction->player,
            "✅ Tu retiro de $" . number_format($transaction->amount, 2) . " fue aprobado. El dinero está en camino.",
            'transaction',
            $transaction
        );
    }

    /**
     * MENSAJES AUTOMÁTICOS - Bonos
     */
    
    public function notifyBonusGranted(Player $player, float $amount, string $type): void
    {
        $this->sendSystemMessage(
            $player,
            "🎁 ¡Felicitaciones! Recibiste un bono de $" . number_format($amount, 2) . " (" . ucfirst($type) . ").",
            'bonus'
        );
    }

    /**
     * MENSAJES AUTOMÁTICOS - Cuenta
     */
    
    public function notifyWelcome(Player $player): void
    {
        $this->sendSystemMessage(
            $player,
            "👋 ¡Bienvenido {$player->name}! Tu cuenta fue creada exitosamente. Si necesitas ayuda, estamos aquí para ti.",
            'account'
        );
    }

    public function notifyAccountSuspended(Player $player, string $reason): void
    {
        $this->sendSystemMessage(
            $player,
            "⚠️ Tu cuenta ha sido suspendida. Motivo: {$reason}. Contacta a soporte para más información.",
            'account'
        );
    }

    public function notifyAccountActivated(Player $player): void
    {
        $this->sendSystemMessage(
            $player,
            "✅ Tu cuenta ha sido reactivada. Ya puedes continuar usando nuestros servicios.",
            'account'
        );
    }

    /**
     * MENSAJES AUTOMÁTICOS - Solicitudes de cuenta
     */

    public function notifyAccountCreated(Transaction $transaction, string $username, string $password): void
    {
        $message = "✅ *¡TU USUARIO FUE CREADO!*\n\n";
        $message .= "🎮 *Usuario:* {$username}\n";
        $message .= "🔑 *Contraseña:* {$password}\n\n";
        $message .= "Ya puedes ingresar a la plataforma de juego.\n";
        $message .= "¡Mucha suerte! 🍀";
        
        $this->sendSystemMessage(
            $transaction->player,
            $message,
            'account',
            $transaction
        );
    }

    public function notifyAccountUnlocked(Transaction $transaction): void
    {
        $message = "✅ *¡TU CUENTA FUE DESBLOQUEADA!*\n\n";
        $message .= "Tu usuario en la plataforma de juego ha sido desbloqueado correctamente.\n";
        $message .= "Ya puedes volver a ingresar. ¡Bienvenido de nuevo! 🎮";
        
        $this->sendSystemMessage(
            $transaction->player,
            $message,
            'account',
            $transaction
        );
    }

    public function notifyPasswordChanged(Transaction $transaction, string $newPassword): void
    {
        $message = "✅ *¡TU CONTRASEÑA FUE CAMBIADA!*\n\n";
        $message .= "🔑 *Nueva contraseña:* {$newPassword}\n\n";
        $message .= "Ya puedes ingresar a la plataforma con tu nueva contraseña.\n";
        $message .= "Te recomendamos cambiarla desde tu perfil por una que recuerdes mejor. 🔐";
        
        $this->sendSystemMessage(
            $transaction->player,
            $message,
            'account',
            $transaction
        );
    }

    /**
     * Enviar mensaje masivo a todos los jugadores activos del tenant
     */
    public function broadcastMessage(int $tenantId, User $sender, string $message, ?string $imagePath = null): int
    {
        $players = Player::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        $count = 0;
        foreach ($players as $player) {
            PlayerMessage::create([
                'tenant_id' => $tenantId,
                'player_id' => $player->id,
                'sender_type' => 'agent',
                'sender_id' => $sender->id,
                'message' => $message,
                'image_path' => $imagePath,
                'category' => 'general',
            ]);
            
            try {
                $webPush = new \App\Services\WebPushService();
                $webPush->sendToPlayer(
                    $player,
                    '📢 Nuevo mensaje',
                    \Str::limit($message, 50),
                    '/player/dashboard'
                );
            } catch (\Exception $e) {
                // Silenciar error de push
            }
            
            $count++;
        }

        activity()
            ->causedBy($sender)
            ->withProperties([
                'recipients' => $count,
                'message' => \Str::limit($message, 100),
                'has_image' => !is_null($imagePath)
            ])
            ->log('Mensaje masivo enviado');

        return $count;
    }
}