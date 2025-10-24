<?php

namespace App\Livewire\Auth;

use App\Models\Player;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Register extends Component
{
    public $tenant;
    
    // Campos del formulario
    public $name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $referral_code = '';
    public $terms = false;
    
    // UI
    public $showPassword = false;
    public $showPasswordConfirmation = false;
    public $passwordStrength = '';
    public $referralCodeValid = null;
    
    public function mount()
    {
        // Obtener tenant desde el middleware o sesión
        $this->tenant = request()->attributes->get('current_tenant') 
                    ?? config('app.current_tenant');
                    
        if (!$this->tenant) {
            abort(404, 'Tenant no encontrado');
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
            'name' => 'required|min:3|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => [
                'required',
                'email',
                Rule::unique('players')->where('tenant_id', $this->tenant->id)
            ],
            'phone' => [
                'required',
                Rule::unique('players')->where('tenant_id', $this->tenant->id)
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
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
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'phone.required' => 'El teléfono es obligatorio',
            'phone.unique' => 'Este teléfono ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números',
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
            'email' => $this->email,
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
        
        // Auto-login
        auth()->guard('player')->login($player);
        
        // Redirigir al dashboard
        session()->flash('success', '¡Bienvenido! Tu cuenta ha sido creada exitosamente.');
        return redirect()->route('player.dashboard');
    }
    
    public function render()
    {
        return view('livewire.player.auth.register')
            ->layout('layouts.player-auth');
    }
}