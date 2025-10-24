<div class="min-h-screen flex items-center justify-center px-4 py-8" 
     style="background: linear-gradient(135deg, {{ $tenant->primary_color }}15 0%, {{ $tenant->secondary_color }}15 100%);">
    
    <div class="w-full max-w-md">
        <!-- Logo y Nombre del Tenant -->
        <div class="text-center mb-8">
            @if($tenant->logo_url)
                <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->name }}" class="h-16 mx-auto mb-4">
            @else
                <div class="text-4xl font-bold mb-4" style="color: {{ $tenant->primary_color }}">
                    {{ $tenant->name }}
                </div>
            @endif
            <h2 class="text-2xl font-bold text-gray-800">Registro</h2>
            <p class="text-sm text-gray-600 mt-2">🎁 ¡Bono 30% en tu primera carga!</p>
        </div>

        <!-- Card del Formulario -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form wire:submit.prevent="register" class="space-y-5">
                
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo</label>
                    <input 
                        type="text" 
                        wire:model="name"
                        placeholder="Juan Pérez"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: {{ $tenant->primary_color }};"
                    >
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        wire:model.blur="email"
                        placeholder="tu@email.com"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition"
                    >
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input 
                        type="text" 
                        wire:model.blur="phone"
                        placeholder="+54 9 11 1234-5678"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition"
                    >
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                    <div class="relative">
                        <input 
                            type="{{ $showPassword ? 'text' : 'password' }}" 
                            wire:model.live="password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition pr-12"
                        >
                        <button 
                            type="button"
                            wire:click="$toggle('showPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            @if($showPassword)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            @endif
                        </button>
                    </div>
                    
                    <!-- Indicador de Fortaleza -->
                    @if($password)
                        <div class="mt-2">
                            <div class="flex items-center gap-1 mb-1">
                                <div class="flex-1 h-1.5 rounded-full {{ $passwordStrength === 'weak' ? 'bg-red-500' : 'bg-gray-200' }}"></div>
                                <div class="flex-1 h-1.5 rounded-full {{ in_array($passwordStrength, ['medium', 'strong']) ? ($passwordStrength === 'medium' ? 'bg-yellow-500' : 'bg-green-500') : 'bg-gray-200' }}"></div>
                                <div class="flex-1 h-1.5 rounded-full {{ $passwordStrength === 'strong' ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                            </div>
                            <p class="text-xs {{ $passwordStrength === 'weak' ? 'text-red-500' : ($passwordStrength === 'medium' ? 'text-yellow-600' : 'text-green-600') }}">
                                @if($passwordStrength === 'weak') Débil
                                @elseif($passwordStrength === 'medium') Media
                                @else Fuerte
                                @endif
                            </p>
                        </div>
                    @endif
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
                    <div class="relative">
                        <input 
                            type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" 
                            wire:model.blur="password_confirmation"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition pr-12"
                        >
                        <button 
                            type="button"
                            wire:click="$toggle('showPasswordConfirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            @if($showPasswordConfirmation)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Código de Referido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Código de referido <span class="text-gray-400">(opcional)</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.blur="referral_code"
                            placeholder="ABC12345"
                            maxlength="8"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:border-transparent transition pr-12 uppercase"
                        >
                        @if($referralCodeValid === true)
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-green-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @elseif($referralCodeValid === false)
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-red-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">¿Te refirió alguien? Ingresa su código</p>
                    @if($referralCodeValid === true)
                        <p class="text-xs text-green-600 mt-1">✓ Código válido</p>
                    @elseif($referralCodeValid === false)
                        <p class="text-xs text-red-600 mt-1">Código inválido</p>
                    @endif
                    @error('referral_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Términos y Condiciones -->
                <div class="flex items-start gap-2">
                    <input 
                        type="checkbox" 
                        wire:model="terms"
                        id="terms"
                        class="mt-1 rounded border-gray-300"
                        style="color: {{ $tenant->primary_color }};"
                    >
                    <label for="terms" class="text-sm text-gray-600">
                        Acepto los <a href="#" class="underline" style="color: {{ $tenant->primary_color }};">términos y condiciones</a>
                    </label>
                </div>
                @error('terms') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <!-- Botón de Registro -->
                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 rounded-lg text-white font-semibold text-lg transition hover:opacity-90 disabled:opacity-50"
                    style="background: {{ $tenant->primary_color }};"
                >
                    <span wire:loading.remove>Crear Cuenta</span>
                    <span wire:loading>Procesando...</span>
                </button>

                <!-- Link a Login -->
                <p class="text-center text-sm text-gray-600 mt-4">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('player.login') }}" class="font-semibold underline" style="color: {{ $tenant->primary_color }};">
                        Inicia sesión
                    </a>
                </p>
            </form>
        </div>
    </div>
</div>