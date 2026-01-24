<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $data = [
            'endpoint' => $request->endpoint,
            'p256dh' => $request->keys['p256dh'],
            'auth' => $request->keys['auth'],
        ];

        // Detectar si es Player o User
        if (auth('player')->check()) {
            $player = auth('player')->user();
            $data['player_id'] = $player->id;
            $data['tenant_id'] = $player->tenant_id;
            $data['user_id'] = null;
        } else {
            $user = auth()->user();
            $data['user_id'] = $user->id;
            $data['tenant_id'] = $user->tenant_id;
            $data['player_id'] = null;
        }

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            $data
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $query = PushSubscription::where('endpoint', $request->endpoint);

        if (auth('player')->check()) {
            $query->where('player_id', auth('player')->id());
        } else {
            $query->where('user_id', auth()->id());
        }

        $query->delete();

        return response()->json(['success' => true]);
    }

    public function getVapidPublicKey(): JsonResponse
    {
        return response()->json([
            'key' => config('webpush.vapid.public_key'),
        ]);
    }
}