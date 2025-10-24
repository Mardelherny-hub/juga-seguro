<div>
    @if($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white dark:bg-gray-800" wire:click.stop>
                
                {{-- Header --}}
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-8 h-8 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Rechazar Transacción
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @if($transaction)
                    <div class="mt-6 space-y-6">
                        
                        {{-- Advertencia --}}
                        <div class="bg-red-50 dark:bg-red-900 dark:bg-opacity-20 border-l-4 border-red-500 p-4">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Advertencia</h4>
                                    <p class="text-sm text-red-700 dark:text-red-400 mt-1">
                                        Esta acción no se puede deshacer. El jugador será notificado del rechazo y el motivo que ingreses.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Información del Jugador --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">INFORMACIÓN DEL JUGADOR</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Nombre</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $player->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Email</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $player->email ?? 'No especificado' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Teléfono</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $player->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Saldo Actual</p>
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($player->balance, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Detalles de la Transacción --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">DETALLES DE LA TRANSACCIÓN</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Tipo --}}
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Tipo</p>
                                    @if($transaction->type === 'deposit')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            DEPÓSITO
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                            RETIRO
                                        </span>
                                    @endif
                                </div>

                                {{-- Monto --}}
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Monto</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">ID Transacción</p>
                                    <p class="font-mono text-sm text-gray-900 dark:text-white">#{{ $transaction->id }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Fecha de Solicitud</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Motivos Comunes --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                MOTIVOS COMUNES (click para seleccionar)
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($commonReasons as $reason)
                                    <button 
                                        type="button"
                                        wire:click="selectCommonReason('{{ $reason }}')"
                                        class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition {{ $rejectionReason === $reason ? 'bg-blue-100 dark:bg-blue-900 border-blue-500 text-blue-800 dark:text-blue-200' : 'text-gray-700 dark:text-gray-300' }}"
                                    >
                                        {{ $reason }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Campo de Motivo --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                MOTIVO DEL RECHAZO <span class="text-red-600">*</span>
                            </label>
                            <textarea 
                                wire:model.live="rejectionReason"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="Explica claramente por qué se rechaza esta solicitud. El jugador verá este mensaje."
                            ></textarea>
                            
                            <div class="flex justify-between items-center mt-2">
                                <div>
                                    @error('rejectionReason')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ strlen($rejectionReason) }}/500 caracteres
                                    @if(strlen($rejectionReason) >= 10)
                                        <span class="text-green-600">✓</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                wire:click="closeModal"
                                type="button"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                {{ $isProcessing ? 'disabled' : '' }}
                            >
                                Cancelar
                            </button>
                            
                            <button 
                                wire:click="reject"
                                wire:confirm="¿Estás seguro de RECHAZAR esta transacción de ${{ number_format($transaction->amount, 2) }} para {{ $player->name }}? Esta acción no se puede deshacer."
                                type="button"
                                class="px-8 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                {{ ($isProcessing || strlen($rejectionReason) < 10) ? 'disabled' : '' }}
                            >
                                @if($isProcessing)
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                @else
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    RECHAZAR TRANSACCIÓN
                                @endif
                            </button>
                        </div>

                    </div>
                @endif

            </div>
        </div>
    @endif
</div>