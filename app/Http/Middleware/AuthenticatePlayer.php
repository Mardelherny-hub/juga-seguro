<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePlayer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar autenticación con guard 'player'
        if (!auth()->guard('player')->check()) {
            return redirect()->route('player.login')
                ->with('error', 'Debes iniciar sesión para continuar');
        }
        
        $player = auth()->guard('player')->user();
        
        // Verificar estado de la cuenta
        if ($player->status === 'suspended') {
            auth()->guard('player')->logout();
            return redirect()->route('player.login')
                ->with('error', 'Tu cuenta está suspendida. Contacta a soporte.');
        }
        
        if ($player->status === 'blocked') {
            auth()->guard('player')->logout();
            return redirect()->route('player.login')
                ->with('error', 'Tu cuenta está bloqueada. Contacta a soporte.');
        }
        
        // Identificar tenant automáticamente
        app()->instance('currentTenant', $player->tenant);
        
        return $next($request);
    }
}