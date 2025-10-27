<div>
    @if($isOpen)
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-data="{ show: @entangle('isOpen') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Modal -->
            <div class="bg-gray-800 rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-gray-700"
                 @click.away="$wire.close()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <!-- Header -->
                <div class="sticky top-0 bg-gray-800 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Retirar Fondos</h3>
                    <button wire:click="close" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <form wire:submit.prevent="submit" class="p-6 space-y-5">
                    
                    <!-- Saldo disponible -->
                    <div class="bg-gradient-to-r from-green-500/20 to-blue-500/20 border border-green-500/30 rounded-lg p-4">
                        <p class="text-sm text-gray-300 mb-1">Saldo disponible</p>
                        <p class="text-3xl font-bold text-white">${{ number_format($player->balance, 2) }}</p>
                    </div>

                    <!-- Monto a retirar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Monto a retirar <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg font-bold">$</span>
                            <input 
                                type="number" 
                                wire:model.blur="amount"
                                placeholder="0.00"
                                step="0.01"
                                {{-- max="{{ $player->balance }}" --}}
                                class="w-full pl-10 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 text-lg"
                            >
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Mínimo: $500</p>
                        @error('amount') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Método de retiro -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Método de retiro <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-3">
                            <!-- Chinchontop -->
                            <label class="flex items-center p-4 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition border-2"
                                   :class="$wire.withdrawalMethod === 'chinchontop' ? 'border-green-500' : 'border-transparent'">
                                <input 
                                    type="radio" 
                                    wire:model="withdrawalMethod" 
                                    value="chinchontop"
                                    class="w-5 h-5 text-green-500 focus:ring-0 focus:ring-offset-0">
                                <div class="ml-3">
                                    <p class="font-semibold text-white">Chinchontop</p>
                                    <p class="text-xs text-gray-400">Plataforma de juegos</p>
                                </div>
                            </label>

                            <!-- Transferencia bancaria -->
                            <label class="flex items-center p-4 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition border-2"
                                   :class="$wire.withdrawalMethod === 'bank_transfer' ? 'border-green-500' : 'border-transparent'">
                                <input 
                                    type="radio" 
                                    wire:model="withdrawalMethod" 
                                    value="bank_transfer"
                                    class="w-5 h-5 text-green-500 focus:ring-0 focus:ring-offset-0">
                                <div class="ml-3">
                                    <p class="font-semibold text-white">Transferencia Bancaria</p>
                                    <p class="text-xs text-gray-400">CBU, CVU o Alias</p>
                                </div>
                            </label>
                        </div>
                        @error('withdrawalMethod') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Titular de cuenta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Titular de la cuenta <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.blur="accountHolder"
                            placeholder="Tu nombre completo"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"
                        >
                        @error('accountHolder') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Número de cuenta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            @if($withdrawalMethod === 'chinchontop')
                                Usuario de Chinchontop <span class="text-red-400">*</span>
                            @else
                                CBU, CVU o Alias <span class="text-red-400">*</span>
                            @endif
                        </label>
                        <input 
                            type="text" 
                            wire:model.blur="accountNumber"
                            placeholder="{{ $withdrawalMethod === 'chinchontop' ? 'Tu usuario' : 'Número de cuenta o alias' }}"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"
                        >
                        @error('accountNumber') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Advertencia -->
                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-sm text-yellow-200">
                                <p class="font-semibold mb-1">Importante:</p>
                                <ul class="text-xs space-y-1 list-disc list-inside">
                                    <li>Solo puedes tener 1 retiro pendiente a la vez</li>
                                    <li>El procesamiento puede tardar hasta 48hs</li>
                                    <li>Verifica bien los datos de tu cuenta</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3 pt-4">
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
                            class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 font-semibold text-white rounded-lg transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="submit">Solicitar Retiro</span>
                            <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>