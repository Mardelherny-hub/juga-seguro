<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

/* archivo actual */
class Login extends Component
{
    public ?Tenant $tenant = null;

    #[Rule('required|string')]  // Ya no valida como email
    public string $credential = ''; 

    #[Rule('required|string')]
    public string $password = '';

    #[Rule('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the user (Agent or Player) for the current tenant or super admin.
     */
    public function login(): mixed
    {    
        $this->validate();
        $this->ensureIsNotRateLimited();

        // Detectar si es email o username
        $isEmail = filter_var($this->credential, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // PASO 1: Intentar como User (Agente/Admin) con email
            $user = User::where('email', $this->credential)->first();
            
            if ($user) {
                return $this->loginAsUser($user);
            }
            
            // PASO 2: Si no es User, intentar como Player con email (para compatibilidad)
            $player = Player::where('email', $this->credential)->first();
            
            if ($player) {
                return $this->loginAsPlayer($player);
            }
        } else {
            // Es username -> Solo intentar como Player
            if (!$this->tenant) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'credential' => __('No se pudo identificar el cliente.'),
                ]);
            }
            
            $player = Player::where('tenant_id', $this->tenant->id)
                ->whereRaw('LOWER(username) = ?', [strtolower($this->credential)])
                ->first();
            
            if ($player) {
                return $this->loginAsPlayer($player);
            }
        }

        // No se encontró ni User ni Player
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'credential' => __('Las credenciales no coinciden con nuestros registros.'),
        ]);
    }

    /**
     * Autenticar como User (Agente/Admin)
     */
    private function loginAsUser(User $user): mixed
    {
        // Verificar si es super admin
        if ($user->is_super_admin) {
            if (!Auth::guard('web')->attempt(
                ['email' => $this->credential, 'password' => $this->password],
                $this->remember
            )) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'credential' => __('Las credenciales no coinciden con nuestros registros.'),
                ]);
            }

            if (!$user->is_active) {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'credential' => __('Tu cuenta ha sido desactivada. Contacta al administrador.'),
                ]);
            }

            RateLimiter::clear($this->throttleKey());
            session()->regenerate();
            return $this->redirect(route('super-admin.dashboard'));
        }

        // Cliente Admin/Agente: validar por tenant
        $currentTenant = $this->tenant;

        if (!$currentTenant) {
            throw ValidationException::withMessages([
                'credential' => 'No se pudo identificar el cliente.',
            ]);
        }

        if ($user->tenant_id !== $currentTenant->id) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'credential' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        if (!Auth::guard('web')->attempt(
            ['email' =>$this->credential, 'password' => $this->password, 'tenant_id' => $currentTenant->id],
            $this->remember
        )) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'credential' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        if (!$user->is_active) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'credential' => __('Tu cuenta ha sido desactivada. Contacta al administrador.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();

        return $this->redirect(
            session('url.intended', route('dashboard', absolute: false))
        );
    }

    /**
     * Autenticar como Player (Jugador)
     */
    private function loginAsPlayer(Player $player): mixed
    {
        $currentTenant = $this->tenant;

        if (!$currentTenant) {
            throw ValidationException::withMessages([
                'credential' =>'No se pudo identificar el cliente.',
            ]);
        }

        if (!$player || $player->tenant_id !== $currentTenant->id) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'credential' =>__('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        // Verificar estado de la cuenta
        if ($player->status === 'suspended') {
            throw ValidationException::withMessages([
                'credential' =>__('Tu cuenta está suspendida. Contacta a soporte.'),
            ]);
        }

        if ($player->status === 'blocked') {
            throw ValidationException::withMessages([
                'credential' =>__('Tu cuenta está bloqueada. Contacta a soporte.'),
            ]);
        }

        // Verificar contraseña manualmente
        if (!Hash::check($this->password, $player->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'credential' =>__('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        // Login manual con el guard player
        Auth::guard('player')->login($player, $this->remember);

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();

        // Registrar en activity log
        activity()
            ->performedOn($player)
            ->causedBy($player)
            ->log('Inicio de sesión');

        // Redirigir al dashboard del jugador
        return $this->redirect(route('player.dashboard'));
    }

    public function mount()
    {
        $this->tenant = request()->attributes->get('current_tenant') 
                    ?? config('app.current_tenant')
                    ?? (session('current_tenant_id') ? Tenant::find(session('current_tenant_id')) : null);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'credential' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->credential) . '|' . request()->ip()
        );
    }

    #[Layout('layouts.player-auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}