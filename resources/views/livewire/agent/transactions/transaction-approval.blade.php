<div>
    @if($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white dark:bg-gray-800" wire:click.stop>
                
                {{-- Header --}}
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aprobar Transacción
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @if($transaction)
                    <div class="mt-6 space-y-6">
                        
                        {{-- Información del Jugador --}}
                        <div class="bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-3">INFORMACIÓN DEL JUGADOR</h4>
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
                            <div class="mt-3">
                                <a href="#" wire:click.prevent="$dispatch('openPlayerDetail', { playerId: {{ $player->id }} })" 
                                   class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    Ver perfil completo →
                                </a>
                            </div>
                        </div>

                        {{-- Detalles de la Transacción --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">DETALLES DE LA TRANSACCIÓN</h4>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
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
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">ID Transacción</p>
                                    <p class="font-mono text-sm text-gray-900 dark:text-white">#{{ $transaction->id }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Fecha de Solicitud</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Tiempo de Espera</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->diffForHumans() }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Hash</p>
                                    <p class="font-mono text-xs text-gray-700 dark:text-gray-300 truncate">{{ $transaction->transaction_hash }}</p>
                                </div>
                            </div>

                            {{-- Notas si existen --}}
                            @if($transaction->notes)
                                <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900 dark:bg-opacity-20 rounded">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Notas</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $transaction->notes }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Comprobante (solo para depósitos) --}}
                        @if($transaction->type === 'deposit')
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">COMPROBANTE</h4>
                                @if($transaction->proof_url)
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $transaction->proof_url }}" 
                                             alt="Comprobante" 
                                             class="w-32 h-32 object-cover rounded border border-gray-300 cursor-pointer hover:opacity-75"
                                             onclick="window.open('{{ $transaction->proof_url }}', '_blank')">
                                        <div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">Click en la imagen para ampliar</p>
                                            <a href="{{ $transaction->proof_url }}" 
                                               target="_blank" 
                                               download
                                               class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                Descargar comprobante →
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-600 dark:text-gray-400">No se adjuntó comprobante</p>
                                @endif
                            </div>
                        @endif

                        {{-- Cálculos para Retiros --}}
                        @if($transaction->type === 'withdrawal')
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">CÁLCULO DEL RETIRO</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Saldo actual:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($currentBalance, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Monto a retirar:</span>
                                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($transaction->amount, 2) }}</span>
                                    </div>
                                    <div class="border-t border-gray-300 dark:border-gray-600 pt-2 flex justify-between">
                                        <span class="font-bold text-gray-900 dark:text-white">Nuevo saldo:</span>
                                        <span class="font-bold text-xl {{ $hasSufficientBalance ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            ${{ number_format($newBalance, 2) }}
                                        </span>
                                    </div>

                                    @if(!$hasSufficientBalance)
                                        <div class="mt-3 p-3 bg-red-100 dark:bg-red-900 dark:bg-opacity-20 rounded flex items-start">
                                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-red-800 dark:text-red-300">Saldo insuficiente</p>
                                                <p class="text-xs text-red-700 dark:text-red-400 mt-1">
                                                    El jugador no tiene saldo suficiente para este retiro. 
                                                    No se puede aprobar. Considera rechazar la transacción.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

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
                                wire:click="approve"
                                wire:confirm="¿Estás seguro de aprobar esta transacción de ${{ number_format($transaction->amount, 2) }} para {{ $player->name }}?"
                                type="button"
                                class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                {{ ($isProcessing || ($transaction->type === 'withdrawal' && !$hasSufficientBalance)) ? 'disabled' : '' }}
                            >
                                @if($isProcessing)
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                @else
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    APROBAR TRANSACCIÓN
                                @endif
                            </button>
                        </div>

                    </div>
                @endif

            </div>
        </div>
    @endif
</div>