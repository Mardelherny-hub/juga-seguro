<div>
    @if($isOpen)
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center"
             x-data="{ show: @entangle('isOpen') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Modal - SÚPER COMPACTO -->
            <div class="bg-gray-800 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-md border-t sm:border border-gray-700"
                 @click.away="$wire.close()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <!-- Header -->
                <div class="bg-gray-800 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg sm:text-xl font-bold text-white">💸 Retirar Fondos</h3>
                    <button wire:click="close" class="text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <form wire:submit.prevent="submit" class="p-4 sm:p-6 space-y-4">
                    
                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            ¿Cuánto querés retirar? <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl font-bold">$</span>
                            <input 
                                type="number" 
                                wire:model.blur="amount"
                                placeholder="0.00"
                                step="0.01"
                                autofocus
                                class="w-full pl-10 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-green-500 text-xl font-semibold"
                            >
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Mínimo: $500</p>
                        @error('amount') 
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Titular -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Titular de tu cuenta <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.blur="accountHolder"
                            placeholder="Tu nombre completo"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"
                        >
                        @error('accountHolder') 
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CBU/CVU/Alias -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Tu CBU, CVU o Alias <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.blur="accountNumber"
                            placeholder="Ej: laucha2.claropay"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 font-mono text-sm"
                        >
                        @error('accountNumber') 
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3">
                        <p class="text-xs text-blue-200">
                            ℹ️ Tu solicitud será verificada por un administrador. El procesamiento puede tardar hasta 48hs.
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="close"
                            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 font-semibold text-white rounded-lg transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="submit">Solicitar</span>
                            <span wire:loading wire:target="submit">Procesando...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>