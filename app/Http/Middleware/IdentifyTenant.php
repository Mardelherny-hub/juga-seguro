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
        $host = $request->getHost();
        
        // PASO 1: Buscar por dominio personalizado completo
        $tenant = Tenant::where('custom_domain', $host)
            ->where('is_active', true)
            ->first();
        
        if ($tenant) {
            $this->setTenant($request, $tenant);
            return $next($request);
        }
        
        // PASO 2: Buscar por subdominio
        $subdomain = $this->extractSubdomain($host);
        
        if ($subdomain) {
            $tenant = Tenant::where('domain', $subdomain)
                ->where('is_active', true)
                ->first();
            
            if ($tenant) {
                $this->setTenant($request, $tenant);
                return $next($request);
            }
        }
        
        // PASO 3: Entorno local (localhost/testing)
        if ($this->isLocalEnvironment($host)) {
            $tenant = Tenant::where('domain', 'demo')
                ->orWhere('slug', 'demo')
                ->first();
            
            if ($tenant) {
                $this->setTenant($request, $tenant);
                return $next($request);
            }
        }
        
        // Si no se encontró tenant, mostrar error
        abort(404, 'Cliente no encontrado. Verifica el dominio.');
    }
    
    /**
     * Extraer subdomain del host
     */
    private function extractSubdomain(string $host): ?string
    {
        $appDomain = config('app.domain', 'plataforma.com');
        
        // Si el host termina con el dominio base de la app
        if (str_ends_with($host, '.' . $appDomain)) {
            // Extraer la parte del subdomain
            $subdomain = str_replace('.' . $appDomain, '', $host);
            return $subdomain;
        }
        
        return null;
    }
    
    /**
     * Verificar si estamos en entorno local
     */
    private function isLocalEnvironment(string $host): bool
    {
        return in_array($host, [
            'localhost',
            '127.0.0.1',
            '::1',
            'localhost:8000'
        ]) || str_ends_with($host, '.test') 
           || str_ends_with($host, '.local');
    }
    
    /**
     * Establecer el tenant en el request y config
     */
    private function setTenant(Request $request, Tenant $tenant): void
    {
        // Guardar en request attributes
        $request->attributes->set('current_tenant', $tenant);
        
        // Guardar en config para acceso global
        config(['app.current_tenant' => $tenant]);
        
        // Compartir con todas las vistas
        view()->share('currentTenant', $tenant);
    }
    
}