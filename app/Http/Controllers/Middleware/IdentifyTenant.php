<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener tenant desde subdomain o dominio
        $host = $request->getHost();
        
        // Estrategia 1: Subdomain (casino1.tuapp.com)
        if (str_contains($host, '.')) {
            $parts = explode('.', $host);
            $subdomain = $parts[0];
            
            // Si no es localhost, buscar por slug
            if ($subdomain !== 'localhost' && $subdomain !== '127') {
                $tenant = Tenant::where('slug', $subdomain)
                               ->where('is_active', true)
                               ->first();
            }
        }
        
        // Estrategia 2: Dominio personalizado (casinoroyal.com)
        if (!isset($tenant)) {
            $tenant = Tenant::where('domain', $host)
                           ->where('is_active', true)
                           ->first();
        }
        
        // Estrategia 3: Para desarrollo local, usar tenant por defecto
        if (!isset($tenant) && app()->environment('local')) {
            $tenant = Tenant::where('is_active', true)->first();
        }
        
        // Si no hay tenant, error 404
        if (!$tenant) {
            abort(404, 'Casino no encontrado');
        }
        
        // Cargar tenant en el contenedor
        app()->instance('tenant', $tenant);
        
        // Configurar marca blanca
        config([
            'app.name' => $tenant->name,
            'app.logo' => $tenant->logo_url,
            'app.colors' => [
                'primary' => $tenant->primary_color,
                'secondary' => $tenant->secondary_color,
            ],
        ]);
        
        // Compartir tenant con todas las vistas
        view()->share('currentTenant', $tenant);
        
        return $next($request);
    }
}