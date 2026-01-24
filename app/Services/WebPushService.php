<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Player;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);
    }

    public function sendToSubscription(PushSubscription $subscription, string $title, string $body, string $url = '/'): bool
    {
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'tag' => 'notification-' . time(),
        ]);

        $sub = Subscription::create([
            'endpoint' => $subscription->endpoint,
            'keys' => [
                'p256dh' => $subscription->p256dh,
                'auth' => $subscription->auth,
            ],
        ]);

        $result = $this->webPush->sendOneNotification($sub, $payload);

        if (!$result->isSuccess()) {
            // Eliminar suscripción inválida
            $subscription->delete();
            return false;
        }

        return true;
    }

    public function sendToPlayer(Player $player, string $title, string $body, string $url = '/'): int
    {
        $sent = 0;
        $subscriptions = PushSubscription::where('player_id', $player->id)->get();

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $title, $body, $url)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendToUser(User $user, string $title, string $body, string $url = '/'): int
    {
        $sent = 0;
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $title, $body, $url)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendToTenantUsers(Tenant $tenant, string $title, string $body, string $url = '/'): int
    {
        $sent = 0;
        $subscriptions = PushSubscription::where('tenant_id', $tenant->id)
            ->whereNotNull('user_id')
            ->get();

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $title, $body, $url)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendToTenantPlayers(Tenant $tenant, string $title, string $body, string $url = '/'): int
    {
        $sent = 0;
        $subscriptions = PushSubscription::where('tenant_id', $tenant->id)
            ->whereNotNull('player_id')
            ->get();

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $title, $body, $url)) {
                $sent++;
            }
        }

        return $sent;
    }
}