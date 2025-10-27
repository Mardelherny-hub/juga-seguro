<div>
    @if($showModal && $player)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    {{-- Header - Color diferente para Super Admin --}}
                    <div class="bg-red-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    Editar Jugador (Super Admin)
                                </h3>
                                <p class="text-sm text-red-100">
                                    Cliente: {{ $player->tenant->name }}
                                </p>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <form wire:submit.prevent="updatePlayer">
                        <div class="px-6 py-6 space-y-6">
                            
                            {{-- Sección: Información Personal --}}
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-4 border-b pb-2">
                                    Información Personal
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Nombre --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nombre Completo <span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            wire:model="name"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                            placeholder="Ej: Juan Pérez"
                                        >
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Username --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Usuario <span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            wire:model="username"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('username') border-red-500 @enderror"
                                            placeholder="Ej: juanperez"
                                        >
                                        @error('username')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Email
                                        </label>
                                        <input 
                                            type="email" 
                                            wire:model="email"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                            placeholder="ejemplo@correo.com"
                                        >
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Teléfono --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Teléfono <span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            wire:model="phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                            placeholder="Ej: +54 9 11 1234-5678"
                                        >
                                        @error('phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Cambiar Contraseña --}}
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-4 border-b pb-2">
                                    Cambiar Contraseña (Opcional)
                                </h4>
                                
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                                    <p class="text-sm text-yellow-800">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Solo completa estos campos si deseas cambiar la contraseña del jugador
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Nueva Contraseña --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nueva Contraseña
                                        </label>
                                        <input 
                                            type="password" 
                                            wire:model="password"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                            placeholder="Mínimo 8 caracteres"
                                        >
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Confirmar Contraseña --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Confirmar Contraseña
                                        </label>
                                        <input 
                                            type="password" 
                                            wire:model="password_confirmation"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                            placeholder="Repetir contraseña"
                                        >
                                    </div>
                                </div>
                            </div>

                            {{-- Información de Solo Lectura + Tenant --}}
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-4 border-b pb-2">
                                    Información de la Cuenta
                                </h4>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 rounded-lg p-4">
                                    {{-- NUEVO: Info del Tenant --}}
                                    <div class="col-span-2">
                                        <p class="text-xs text-gray-600">Cliente/Tenant</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $player->tenant->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $player->tenant->domain }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-xs text-gray-600">Código de Referido</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $player->referral_code }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Saldo Actual</p>
                                        <p class="text-sm font-semibold text-gray-900">${{ number_format($player->balance, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Fecha de Registro</p>
                                        <p class="text-sm text-gray-900">{{ $player->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @if($player->referrer)
                                        <div>
                                            <p class="text-xs text-gray-600">Referido Por</p>
                                            <p class="text-sm text-gray-900">{{ $player->referrer->name }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                            >
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>