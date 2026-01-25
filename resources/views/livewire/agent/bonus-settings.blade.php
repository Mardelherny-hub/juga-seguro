<div>
    <!-- Botón para abrir modal -->
    <button 
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out shadow-sm"
    >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
        </svg>
        Configurar Bonos
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <!-- Background overlay -->
            <div 
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                aria-hidden="true"
                wire:click="closeModal"
            ></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            <h3 class="text-xl font-bold text-white">
                                Configuración de Bonos
                            </h3>
                        </div>
                        <button 
                            wire:click="closeModal" 
                            class="text-white hover:text-gray-200 transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-6 space-y-6">
                    
                    <!-- Bono de Bienvenida -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-5 border-2 border-gray-200 dark:border-gray-600">
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                </svg>
                                Bono de Bienvenida
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Se otorga automáticamente en el primer depósito del jugador
                            </p>
                        </div>

                        <!-- Checkbox Activar/Desactivar -->
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model="welcome_bonus_enabled"
                                    class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                                    Activar Bono de Bienvenida
                                </span>
                            </label>
                        </div>

                        <!-- Tipo de bono: Fijo o Porcentaje -->
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="welcome_bonus_is_percentage"
                                    class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                                    Bono por porcentaje del primer depósito
                                </span>
                            </label>
                            <p class="mt-1 ml-8 text-xs text-gray-500 dark:text-gray-400">
                                Si está activo, el bono será un porcentaje del monto depositado
                            </p>
                        </div>

                        <!-- Input Monto/Porcentaje -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $welcome_bonus_is_percentage ? 'Porcentaje del Bono' : 'Monto del Bono' }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-lg">
                                        {{ $welcome_bonus_is_percentage ? '%' : '$' }}
                                    </span>
                                    <input 
                                        type="number" 
                                        wire:model="welcome_bonus_amount"
                                        step="{{ $welcome_bonus_is_percentage ? '1' : '0.01' }}"
                                        min="0"
                                        max="{{ $welcome_bonus_is_percentage ? '100' : '999999' }}"
                                        class="pl-8 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                        placeholder="{{ $welcome_bonus_is_percentage ? '20' : '0.00' }}"
                                    >
                                </div>
                                @error('welcome_bonus_amount')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($welcome_bonus_is_percentage)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tope máximo (opcional)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-lg">
                                        $
                                    </span>
                                    <input 
                                        type="number" 
                                        wire:model="welcome_bonus_max"
                                        step="0.01"
                                        min="0"
                                        class="pl-8 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                        placeholder="5000"
                                    >
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Deja vacío para sin límite
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Ejemplo -->
                        @if($welcome_bonus_is_percentage && $welcome_bonus_amount > 0)
                        <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-sm text-green-800 dark:text-green-200">
                                <strong>Ejemplo:</strong> Si un jugador deposita $10,000, recibirá 
                                <strong>${{ number_format(10000 * ($welcome_bonus_amount / 100), 2) }}</strong> de bono
                                @if($welcome_bonus_max)
                                    (máx. ${{ number_format($welcome_bonus_max, 2) }})
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Bono de Referido -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-5 border-2 border-gray-200 dark:border-gray-600">
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Bono de Referido
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Se otorga cuando un jugador usa un código de referido al registrarse
                            </p>
                        </div>

                        <!-- Checkbox Activar/Desactivar -->
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model="referral_bonus_enabled"
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                                    Activar Bono de Referido
                                </span>
                            </label>
                        </div>

                        <!-- Input Monto -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Monto del Bono (para ambos: referidor y referido)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400 text-lg">
                                    $
                                </span>
                                <input 
                                    type="number" 
                                    wire:model="referral_bonus_amount"
                                    step="0.01"
                                    min="0"
                                    class="pl-8 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    placeholder="0.00"
                                >
                            </div>
                            @error('referral_bonus_amount')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                💡 El monto se otorgará según la opción seleccionada abajo
                            </p>
                        </div>

                        <!-- Selector de Destinatario -->
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                ¿Quién recibe el bono?
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model="referral_bonus_target" value="referrer"
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Solo quien refiere</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model="referral_bonus_target" value="referred"
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Solo el referido</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model="referral_bonus_target" value="both"
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Ambos</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Retiros -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Configuración de Retiros</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Monto mínimo para solicitar retiro</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Monto mínimo de retiro
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input 
                                    type="number" 
                                    wire:model="min_withdrawal"
                                    class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white"
                                    placeholder="500"
                                    min="0"
                                    step="100"
                                >
                            </div>
                            @error('min_withdrawal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-end space-x-3">
                    <button 
                        wire:click="closeModal"
                        type="button" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        wire:click="save"
                        type="button" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out shadow-sm"
                    >
                        Guardar Cambios
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    <!-- Toast de éxito -->
    @if (session()->has('success'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
    >
        {{ session('success') }}
    </div>
    @endif
</div>