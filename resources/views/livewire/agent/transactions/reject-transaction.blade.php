<div>
    @if($isOpen && $transaction)
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-data="{ show: @entangle('isOpen') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            
            <!-- Modal -->
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full shadow-2xl"
                 @click.away="$wire.close()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rechazar Transacción</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">ID: #{{ $transaction->id }}</p>
                        </div>
                        <button wire:click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <form wire:submit.prevent="reject" class="p-6 space-y-5">
                    
                    <!-- Info básica -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Jugador</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->$player->display_name }}</p>
                            </div>
                            <div class="text-right">
                                @if($transaction->type === 'deposit')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        DEPÓSITO
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        RETIRO
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Monto</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Motivo del rechazo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo del rechazo 
                        </label>
                        <textarea 
                            wire:model="rejectionReason"
                            rows="2"
                            placeholder="Explica claramente por qué se rechaza esta transacción..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            ></textarea>
                        @error('rejectionReason') 
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Mínimo 10 caracteres. El jugador verá este mensaje.</p>
                    </div>

                    <!-- Advertencia -->
                    <div class="bg-red-50 dark:bg-red-900 dark:bg-opacity-20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-sm text-red-800 dark:text-red-200">
                                <p class="font-semibold mb-1">Al rechazar esta transacción:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>NO se modificará el saldo del jugador</li>
                                    <li>El jugador recibirá una notificación con el motivo</li>
                                    <li>Esta acción no se puede deshacer</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="close"
                            class="flex-1 px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition disabled:opacity-50 flex items-center justify-center gap-2">
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <svg wire:loading class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Rechazar Transacción</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>