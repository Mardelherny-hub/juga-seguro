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
                        @if($transaction->type === 'deposit')
                            Aprobar Depósito
                        @elseif($transaction->type === 'withdrawal')
                            Aprobar Retiro
                        @elseif($transaction->type === 'account_creation')
                            Aprobar Creación de Usuario
                        @elseif($transaction->type === 'account_unlock')
                            Aprobar Desbloqueo
                        @elseif($transaction->type === 'password_reset')
                            Aprobar Cambio de Contraseña
                        @else
                            Aprobar Solicitud
                        @endif
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
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $player->display_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Email</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $player->email ?? 'No especificado' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Teléfono</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $player->phone }}</p>
                                </div>
                                {{-- <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Saldo Actual</p>
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($player->balance, 2) }}</p>
                                </div> --}}
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
                                    @elseif($transaction->type === 'withdrawal')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                            RETIRO
                                        </span>
                                    @elseif($transaction->type === 'account_creation')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            CREAR USUARIO
                                        </span>
                                    @elseif($transaction->type === 'account_unlock')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                            DESBLOQUEAR
                                        </span>
                                    @elseif($transaction->type === 'password_reset')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                            CAMBIAR CONTRASEÑA
                                        </span>
                                    @endif
                                </div>

                                {{-- Monto (solo para deposit/withdrawal) --}}
                                @if(in_array($transaction->type, ['deposit', 'withdrawal']))
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Monto</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</p>
                                    </div>
                                @else
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Solicitud</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Gestión de cuenta
                                        </p>
                                    </div>
                                @endif
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
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Datos de la operación</p>
                                    </div>
                                    
                                    @if($transaction->type === 'withdrawal')
                                        @php
                                        $notesData = [];
                                        $notes = $transaction->notes;
                                        
                                        // Parsear formato sin saltos de línea
                                        if (preg_match('/Tipo:\s*(\w+)/i', $notes, $m)) $notesData['tipo'] = $m[1];
                                        if (preg_match('/Cuenta:\s*([^\s]+)/i', $notes, $m)) $notesData['cuenta'] = $m[1];
                                        if (preg_match('/Alias:\s*([^\s]+)/i', $notes, $m)) $notesData['alias'] = $m[1];
                                        if (preg_match('/Titular:\s*([^A-Z][a-zA-Z0-9\s]+?)(?=\s+[A-Z]|$)/i', $notes, $m)) $notesData['titular'] = trim($m[1]);
                                        if (preg_match('/DNI:\s*(\d+)/i', $notes, $m)) $notesData['dni'] = $m[1];
                                        if (preg_match('/Banco:\s*([a-zA-Z\s]+?)(?=\s+[A-Z][a-z]+:|$)/i', $notes, $m)) $notesData['banco'] = trim($m[1]);
                                    @endphp
                                        
                                        <div class="space-y-2">
                                            {{-- CBU/CVU/Alias --}}
                                            @if(isset($notesData['cuenta']) || isset($notesData['alias']))
                                                <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                                                    <div>
                                                        <span class="text-xs text-gray-500">{{ $notesData['tipo'] ?? 'Cuenta' }}</span>
                                                        <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $notesData['cuenta'] ?? $notesData['alias'] ?? '-' }}</p>
                                                    </div>
                                                    <button type="button"
        onclick="copyToClipboard(this)"
        data-value="{{ $notesData['cuenta'] ?? $notesData['alias'] ?? '' }}"
        class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded">
    <span>Copiar</span>
</button>
                                                </div>
                                            @endif
                                            
                                            {{-- Titular --}}
                                            @if(isset($notesData['titular']))
                                                <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                                                    <div>
                                                        <span class="text-xs text-gray-500">Titular</span>
                                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $notesData['titular'] }}</p>
                                                    </div>
                                                   <button type="button"
        onclick="copyToClipboard(this)"
        data-value="{{ $notesData['titular'] ?? '' }}"
        class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded">
    <span>Copiar</span>
</button>
                                                </div>
                                            @endif
                                            
                                            {{-- Otros datos (DNI, Banco) --}}
                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                @if(isset($notesData['dni']))
                                                    <div>
                                                        <span class="text-xs text-gray-500">DNI</span>
                                                        <p class="text-gray-900 dark:text-white">{{ $notesData['dni'] }}</p>
                                                    </div>
                                                @endif
                                                @if(isset($notesData['banco']))
                                                    <div>
                                                        <span class="text-xs text-gray-500">Banco</span>
                                                        <p class="text-gray-900 dark:text-white">{{ $notesData['banco'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        {{-- Para otros tipos, mostrar notas como texto --}}
                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $transaction->notes }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Credenciales para solicitudes de cuenta --}}
                        @if($transaction && $transaction->requiresCredentials())
                            <div class="bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-800">
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-3">
                                    @if($transaction->type === 'account_creation')
                                        🎮 CREDENCIALES DE USUARIO
                                    @else
                                        🔑 NUEVA CONTRASEÑA
                                    @endif
                                </h4>
                                
                                @if($transaction->type === 'account_creation')
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Usuario *
                                        </label>
                                        <input type="text" 
                                               wire:model="username" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                               placeholder="Ej: jugador123"
                                               required>
                                        @error('username')
                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Contraseña *
                                    </label>
                                    <input type="text" 
                                           wire:model="password" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="Mínimo 6 caracteres"
                                           required>
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <p class="mt-3 text-xs text-blue-700 dark:text-blue-300">
                                    ⚠️ Estas credenciales se enviarán automáticamente al jugador dentro de la plataforma al aprobar.
                                </p>
                            </div>
                        @endif

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
                                    {{-- <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Saldo actual:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($currentBalance, 2) }}</span>
                                    </div> --}}
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Monto a retirar:</span>
                                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($transaction->amount, 2) }}</span>
                                    </div>
                                    {{-- <div class="border-t border-gray-300 dark:border-gray-600 pt-2 flex justify-between">
                                        <span class="font-bold text-gray-900 dark:text-white">Nuevo saldo:</span>
                                        <span class="font-bold text-xl {{ $hasSufficientBalance ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            ${{ number_format($newBalance, 2) }}
                                        </span>
                                    </div> --}}

                                    @if(!$hasSufficientBalance)
                                        <div class="mt-3 p-3 bg-yellow-100 dark:bg-yellow-900 dark:bg-opacity-20 rounded flex items-start">
                                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">⚠️ Advertencia: Saldo en plataforma insuficiente</p>
                                                <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">
                                                    El saldo registrado en la plataforma es menor al monto solicitado. 
                                                    <strong>Verifica manualmente</strong> si el jugador ganó en el casino antes de aprobar.
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
                                type="button"
                                class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>
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
                                </span>
                                <span wire:loading>
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>
                        </div>

                    </div>
                @endif

            </div>
        </div>
    @endif

<script>
function copyToClipboard(btn) {
    var text = btn.dataset.value;
    var textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    btn.querySelector('span').innerText = '¡Copiado!';
    setTimeout(function() {
        btn.querySelector('span').innerText = 'Copiar';
    }, 2000);
}
</script>
</div>