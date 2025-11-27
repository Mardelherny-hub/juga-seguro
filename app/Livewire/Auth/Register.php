<?php

namespace App\Livewire\Auth;

use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Str;

class Register extends Component
{
    public $tenant;
    
    // Campos del formulario
    public $name = '';
    public $username = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $referral_code = '';
    public $terms = false;

    // Validaciones
    public $usernameValid = null;
    
    // UI
    public $showPassword = false;
    public $showPasswordConfirmation = false;
    public $passwordStrength = '';
    public $referralCodeValid = null;

    public $welcomeBonusAmount = 0;
    
    public function mount()
    {
        // Obtener tenant desde el middleware o sesión
        $this->tenant = request()->attributes->get('current_tenant') 
                    ?? config('app.current_tenant');
                    
        if (!$this->tenant) {
            abort(404, 'Tenant no encontrado');
        }
        
        // Cargar monto del bono de bienvenida
        if ($this->tenant->welcome_bonus_enabled && $this->tenant->welcome_bonus_amount > 0) {
            $this->welcomeBonusAmount = $this->tenant->welcome_bonus_amount;
        }
        
    }
    
    public function updatedPassword($value)
    {
        $this->calculatePasswordStrength($value);
    }
    
    public function updatedReferralCode($value)
    {
        if (strlen($value) === 8) {
            $this->validateReferralCode();
        } else {
            $this->referralCodeValid = null;
        }
    }

    public function updatedUsername($value)
    {
        if (strlen($value) >= 4) {
            $this->validateUsername();
        } else {
            $this->usernameValid = null;
        }
    }

    private function validateUsername()
    {
        // Validar formato
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]{3,14}$/', $this->username)) {
            $this->usernameValid = false;
            return;
        }
        
        // Validar unicidad
        $exists = Player::where('username', $this->username)
            ->where('tenant_id', $this->tenant->id)
            ->exists();
            
        $this->usernameValid = !$exists;
    }
    
    private function calculatePasswordStrength($password)
    {
        $strength = 0;
        
        if (strlen($password) >= 8) $strength++;
        if (preg_match('/[a-z]/', $password)) $strength++;
        if (preg_match('/[A-Z]/', $password)) $strength++;
        if (preg_match('/[0-9]/', $password)) $strength++;
        
        if ($strength <= 2) {
            $this->passwordStrength = 'weak';
        } elseif ($strength === 3) {
            $this->passwordStrength = 'medium';
        } else {
            $this->passwordStrength = 'strong';
        }
    }
    
    private function validateReferralCode()
    {
        $exists = Player::where('referral_code', $this->referral_code)
            ->where('tenant_id', $this->tenant->id)
            ->exists();
            
        $this->referralCodeValid = $exists;
    }
    
    public function register()
    {
        $this->validate([
            'username' => [
                'required',
                'min:4',
                'max:15',
                'regex:/^[a-zA-Z][a-zA-Z0-9]*$/',
                function ($attribute, $value, $fail) {
                    $exists = Player::where('tenant_id', $this->tenant->id)
                        ->whereRaw('LOWER(username) = ?', [strtolower($value)])
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este nombre de usuario ya está registrado.');
                    }
                }
            ],
            'phone' => [
                'required',
                Rule::unique('players')->where('tenant_id', $this->tenant->id)
            ],
            'password' => [
                'required',
                'min:8',
                'confirmed'
            ],
            'referral_code' => [
                'nullable',
                'size:8',
                function ($attribute, $value, $fail) {
                    if ($value && !Player::where('referral_code', $value)
                        ->where('tenant_id', $this->tenant->id)
                        ->exists()) {
                        $fail('El código de referido no es válido.');
                    }
                }
            ],
            'terms' => 'accepted',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.regex' => 'El nombre solo puede contener letras y espacios',
            'username.required' => 'El nombre de usuario es obligatorio',  // NUEVO
            'username.min' => 'El usuario debe tener al menos 4 caracteres',  // NUEVO
            'username.max' => 'El usuario no puede tener más de 15 caracteres',  // NUEVO
            'username.regex' => 'El usuario debe empezar con letra y solo contener letras y números',  // NUEVO
            'username.unique' => 'Este nombre de usuario ya está registrado',  // NUEVO
            'phone.required' => 'El teléfono es obligatorio',
            'phone.unique' => 'Este teléfono ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'terms.accepted' => 'Debes aceptar los términos y condiciones',
        ]);
        
        // Buscar referidor si existe código
        $referrerId = null;
        if ($this->referral_code) {
            $referrer = Player::where('referral_code', $this->referral_code)
                ->where('tenant_id', $this->tenant->id)
                ->first();
            $referrerId = $referrer?->id;
        }
        
        // Crear jugador
        $player = Player::create([
            'tenant_id' => $this->tenant->id,
            'name' => $this->name,
            //'email' => $this->email,  // ELIMINAR
            'username' => $this->username,  // AGREGAR
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'referred_by' => $referrerId,
            'status' => 'active',
            'balance' => 0,
        ]);
        
        // Registrar en activity log
        activity()
            ->performedOn($player)
            ->causedBy($player)
            ->withProperties(['referral_code' => $this->referral_code])
            ->log('Jugador registrado');
        
        // Crear solicitud automática de vinculación con casino
        Transaction::create([
            'tenant_id' => $player->tenant_id,
            'player_id' => $player->id,
            'type' => 'account_creation',
            'amount' => 0,
            'balance_before' => 0,
            'balance_after' => 0,
            'status' => 'pending',
            'notes' => 'Solicitud automática - Verificar/crear usuario en casino',
            'transaction_hash' => Str::uuid(),
        ]);

        activity()
            ->performedOn($player)
            ->causedBy($player)
            ->log('Solicitud de vinculación generada automáticamente');
        
        // Auto-login
        auth()->guard('player')->login($player);
        
        // Redirigir al dashboard
        session()->flash('success', '¡Bienvenido! Tu cuenta ha sido creada exitosamente.');
        return redirect()->route('player.dashboard');
    }
    
    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.player-auth');
    }
}