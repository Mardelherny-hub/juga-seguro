<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Login extends Component
{
    public ?Tenant $tenant = null;

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    #[Rule('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the user for the current tenant or super admin.
     */
    public function login(): void
    {    
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Buscar el usuario primero
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        // Verificar si es super admin
        if ($user->is_super_admin) {
            // Super Admin: login directo sin validar tenant
            if (!Auth::guard('web')->attempt(
                ['email' => $this->email, 'password' => $this->password],
                $this->remember
            )) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => __('Las credenciales no coinciden con nuestros registros.'),
                ]);
            }

            // Verificar si el usuario está activo
            if (!$user->is_active) {
                Auth::guard('web')->logout();

                throw ValidationException::withMessages([
                    'email' => __('Tu cuenta ha sido desactivada. Contacta al administrador.'),
                ]);
            }

            RateLimiter::clear($this->throttleKey());
            session()->regenerate();

            // Redirigir al super admin dashboard
            $this->redirect(route('super-admin.dashboard'), navigate: true);
            return;
        }

        // Cliente Admin: validar por tenant
        $currentTenant = $this->tenant;

        if (!$currentTenant) {
            throw ValidationException::withMessages([
                'email' => 'No se pudo identificar el cliente.',
            ]);
        }

        // Verificar que el usuario pertenece al tenant actual
        if ($user->tenant_id !== $currentTenant->id) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        // Intentar autenticación con tenant_id
        if (!Auth::guard('web')->attempt(
            ['email' => $this->email, 'password' => $this->password, 'tenant_id' => $currentTenant->id],
            $this->remember
        )) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        // Verificar si el usuario está activo
        if (!$user->is_active) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => __('Tu cuenta ha sido desactivada. Contacta al administrador.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();

        // Redirigir al dashboard del cliente
        $this->redirect(
            session('url.intended', route('dashboard', absolute: false)),
            navigate: true
        );
    }

    public function mount()
    {
        // Intentar obtener tenant de múltiples fuentes
        $this->tenant = request()->attributes->get('current_tenant') 
                    ?? config('app.current_tenant')
                    ?? (session('current_tenant_id') ? Tenant::find(session('current_tenant_id')) : null);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->email) . '|' . request()->ip()
        );
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}