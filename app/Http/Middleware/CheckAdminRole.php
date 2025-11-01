<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin siempre puede pasar
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Solo admin del tenant puede acceder
        if ($user->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección. Solo administradores.');
        }

        return $next($request);
    }
}