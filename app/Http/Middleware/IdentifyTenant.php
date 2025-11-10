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

         // DEBUG TEMPORAL
        \Log::info('IdentifyTenant Debug', [
            'host' => $host,
            'subdomain' => $this->extractSubdomain($host),
            'app_domain' => config('app.domain'),
        ]);

        // Si ya hay tenant en sesión, recuperarlo
        if (session('current_tenant_id')) {
            $tenant = Tenant::find(session('current_tenant_id'));
            if ($tenant && $tenant->is_active) {
                $this->setTenant($request, $tenant);
                return $next($request);
            }
        }
        
        // PASO 1: Buscar por dominio personalizado completo
        // Normalizar host removiendo www si existe
        $hostWithoutWww = str_starts_with($host, 'www.') ? substr($host, 4) : $host;

        $tenant = Tenant::where('custom_domain', $hostWithoutWww)
            ->where('is_active', true)
            ->first();
        
        if ($tenant) {
            $this->setTenant($request, $tenant);
            \Log::info('Tenant encontrado por custom_domain', ['tenant_id' => $tenant->id]);
            return $next($request);
        }
        
        // PASO 2: Buscar por subdominio
        $subdomain = $this->extractSubdomain($host);
        
        if ($subdomain) {
            $tenant = Tenant::where('domain', $subdomain)
                ->where('is_active', true)
                ->first();

                \Log::info('Tenant buscado por domain', ['tenant' => $tenant ? $tenant->id : null]);
            
            if ($tenant) {
                $this->setTenant($request, $tenant);
                return $next($request);
            }
        }

            \Log::info('No se encontró tenant, es ruta pública?', ['is_public' => $this->isPublicRoute($request)]);
        
        // PASO 3: Si NO hay tenant y estamos en ruta pública (login), continuar sin tenant
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }
        
        // PASO 4: Si no es ruta pública y no hay tenant, mostrar error
        abort(404, 'Cliente no encontrado. Verifica el dominio.');
    }
    
    /**
     * Verificar si es una ruta pública que no requiere tenant
     */
    private function isPublicRoute(Request $request): bool
    {
        $publicRoutes = [
            'login',
            'logout',
        ];
        
        return in_array($request->path(), $publicRoutes) || 
               $request->is('login') || 
               $request->is('logout');
    }
    
    /**
     * Extraer subdomain del host
     */
    private function extractSubdomain(string $host): ?string
    {
        $appDomain = config('app.domain', 'gestion-redes.test');
        
        // Si el host termina con el dominio base de la app
        if (str_ends_with($host, '.' . $appDomain)) {
            // Extraer la parte del subdomain
            $subdomain = str_replace('.' . $appDomain, '', $host);
            
            // Remover 'www.' del inicio si existe
            if (str_starts_with($subdomain, 'www.')) {
                $subdomain = str_replace('www.', '', $subdomain);
            }
            
            // Si después de remover www queda vacío o es solo 'www', retornar null
            if (empty($subdomain) || $subdomain === 'www') {
                return null;
            }
            
            return $subdomain;
        }
        
        return null;
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
    
    // Guardar en sesión para que persista en peticiones Livewire
    session(['current_tenant_id' => $tenant->id]);
    
    // Guardar el tenant completo en el container de Laravel
    app()->instance('tenant', $tenant);
    
    // Compartir con todas las vistas
    view()->share('currentTenant', $tenant);
    
    \Log::info('Tenant guardado', [
        'tenant_id' => $tenant->id,
        'session' => session('current_tenant_id'),
    ]);
}
}