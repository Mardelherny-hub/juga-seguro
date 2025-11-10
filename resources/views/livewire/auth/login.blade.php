@php
    $currentTenant = $this->tenant ?? null;
@endphp
<div>
    <!-- Título -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-white mb-2">Iniciar Sesión</h2>
        <p class="text-gray-400 text-sm">Accede a tu cuenta</p>
    </div>

    <!-- Formulario -->
    <form wire:submit.prevent="login" class="space-y-4">
        
        <!-- Usuario o Email -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Usuario o Email <span class="text-red-400">*</span>
            </label>
            <input 
                type="text" 
                wire:model.blur="credential"
                placeholder="usuario o email@ejemplo.com"
                required
                autofocus
                class="w-full px-4 py-3 bg-white/5 border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition
                    {{ $errors->has('credential') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
            >
            <p class="text-gray-400 text-xs mt-1.5">
                Ingresa tu nombre de usuario de la plataforma o tu email
            </p>
            @error('credential') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Contraseña -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Contraseña <span class="text-red-400">*</span>
            </label>
            <x-password-input 
                id="password"
                name="password"
                model="password"
                placeholder="••••••••"
                required
                class="bg-white/5 border text-white placeholder-gray-500 focus:outline-none focus:bg-white/10 transition {{ $errors->has('password') ? 'border-red-500' : 'border-white/10 focus:border-white/30' }}"
            />
            @error('password') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- Recordarme -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="remember"
                    class="w-4 h-4 rounded border-gray-600 bg-white/5 text-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                >
                <span class="text-sm text-gray-300">Recordarme</span>
            </label>
            
                {{-- TODO: Implementar recuperación de contraseña --}}
            {{-- <a href="#" class="text-sm text-purple-400 hover:text-purple-300 underline">
                ¿Olvidaste tu contraseña?
            </a> --}}
        </div>

        <!-- Botón de Login -->
        <button 
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-3.5 rounded-lg text-white font-semibold text-base transition hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
            style="background: linear-gradient(135deg, {{ $currentTenant->primary_color ?? '#9333ea' }} 0%, {{ $currentTenant->secondary_color ?? '#7e22ce' }} 100%);"
        >
            <span wire:loading.remove wire:target="login">Iniciar Sesión</span>
            <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Iniciando...
            </span>
        </button>

        <!-- Link a Registro (solo para tenants, no para super admin) -->
        @if($currentTenant)
        <p class="text-center text-sm text-gray-400 mt-6">
            ¿No tienes una cuenta? 
            <a href="{{ route('player.register') }}" class="text-purple-400 hover:text-purple-300 font-semibold underline">
                Regístrate aquí
            </a>
        </p>
        @endif
    </form>
</div>