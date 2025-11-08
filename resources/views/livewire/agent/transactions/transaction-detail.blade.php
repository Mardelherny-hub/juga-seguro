<div>
    @if($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-5xl shadow-lg rounded-lg bg-white dark:bg-gray-800" wire:click.stop>
                
                {{-- Header --}}
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Detalle de Transacción
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @if($transaction)
                    <div class="mt-6 space-y-6 max-h-[70vh] overflow-y-auto">
                        
                        {{-- Información Básica --}}
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 dark:bg-opacity-20 rounded-lg p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">ID Transacción</p>
                                    <p class="font-mono font-bold text-lg text-gray-900 dark:text-white">#{{ $transaction->id }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Estado</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $statusBadge['color'] }}">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusBadge['icon'] }}"/>
                                        </svg>
                                        {{ $statusBadge['text'] }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Tipo</p>
                                    @if($transaction->type === 'deposit')
                                        <span class="inline-flex items-center text-green-600 dark:text-green-400 font-bold">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            DEPÓSITO
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-red-600 dark:text-red-400 font-bold">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                            RETIRO
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Monto</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Hash Único</p>
                                <p class="font-mono text-sm text-gray-700 dark:text-gray-300 break-all">{{ $transaction->transaction_hash }}</p>
                            </div>
                        </div>

                        {{-- Información específica para solicitudes de cuenta --}}
                        @if($transaction->isAccountRequest())
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mt-4">
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    INSTRUCCIONES
                                </h4>
                                
                                @if($transaction->type === 'account_creation')
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        ✓ Crear usuario <strong>{{ $transaction->player->username }} ({{ $transaction->player->name }})</strong> en el panel externo del casino<br>
                                        ✓ Asignar nickname y contraseña<br>
                                        ✓ Una vez creado, aprobar esta solicitud e ingresar las credenciales en las notas
                                    </p>
                                @elseif($transaction->type === 'account_unlock')
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        ✓ Desbloquear usuario <strong>{{ $transaction->player->username }} ({{ $transaction->player->name }})</strong> en el panel externo del casino<br>
                                        ✓ Una vez desbloqueado, aprobar esta solicitud
                                    </p>
                                @elseif($transaction->type === 'password_reset')
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        ✓ Cambiar contraseña de <strong>{{ $transaction->player->username }} ({{ $transaction->player->name }})</strong> a: <strong class="text-red-600">bet123</strong><br>
                                        ✓ Realizar el cambio en el panel externo del casino<br>
                                        ✓ Una vez cambiada, aprobar esta solicitud
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Información del Jugador --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                JUGADOR
                            </h4>
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
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">${{ number_format($player->balance, 2) }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button 
                                    wire:click="$dispatch('openPlayerDetail', { playerId: {{ $player->id }} })"
                                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium"
                                >
                                    Ver perfil completo →
                                </button>
                            </div>
                        </div>

                        {{-- Detalles Financieros --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                DETALLES FINANCIEROS
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Monto</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Saldo Antes</p>
                                    <p class="text-sm text-gray-900 dark:text-white">${{ number_format($transaction->balance_before, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Saldo Después</p>
                                    <p class="text-sm text-gray-900 dark:text-white">${{ number_format($transaction->balance_after, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Diferencia</p>
                                    <p class="text-sm font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->type === 'deposit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Comprobante --}}
                        @if($transaction->proof_url)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    COMPROBANTE
                                </h4>
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $transaction->proof_url }}" 
                                         alt="Comprobante" 
                                         class="w-40 h-40 object-cover rounded border-2 border-gray-300 dark:border-gray-600 cursor-pointer hover:opacity-75 transition"
                                         onclick="window.open('{{ $transaction->proof_url }}', '_blank')">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">Click en la imagen para ampliar</p>
                                        <a href="{{ $transaction->proof_url }}" 
                                           target="_blank" 
                                           download
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Timeline de Estados --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                HISTORIAL DE ESTADOS
                            </h4>
                            <div class="space-y-4">
                                {{-- Creada --}}
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Creada</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">Por: Sistema (solicitud del jugador)</p>
                                    </div>
                                </div>

                                {{-- Procesada --}}
                                @if($transaction->processed_at)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 {{ $transaction->status === 'completed' ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($transaction->status === 'completed')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    @endif
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $transaction->status === 'completed' ? 'Aprobada' : 'Rechazada' }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $transaction->processed_at->format('d/m/Y H:i:s') }}</p>
                                            @if($processor)
                                                <p class="text-xs text-gray-500 dark:text-gray-500">Por: {{ $processor->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Motivo de Rechazo --}}
                        @if($transaction->status === 'rejected' && $transaction->notes)
                            <div class="bg-red-50 dark:bg-red-900 dark:bg-opacity-20 border-l-4 border-red-500 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    MOTIVO DEL RECHAZO
                                </h4>
                                <p class="text-sm text-red-700 dark:text-red-400">{{ $transaction->notes }}</p>
                                @if($processor)
                                    <p class="text-xs text-red-600 dark:text-red-500 mt-2">
                                        Rechazada por: {{ $processor->name }} el {{ $transaction->processed_at->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Activity Log --}}
                        @if($activityLog->isNotEmpty())
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    REGISTRO DE AUDITORÍA
                                </h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($activityLog as $log)
                                        <div class="text-xs p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                                            <div class="flex justify-between items-start">
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ $log->description }}</span>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @if($log->causer)
                                                <p class="text-gray-600 dark:text-gray-400 mt-1">Por: {{ $log->causer->name }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Botones de Acción (solo si está pendiente) --}}
                        @if($transaction->status === 'pending')
                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button 
                                    wire:click="$dispatch('openTransactionApproval', { transactionId: {{ $transaction->id }} })"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar
                                </button>
                                
                                <button 
                                    wire:click="$dispatch('openTransactionRejection', { transactionId: {{ $transaction->id }} })"
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Rechazar
                                </button>
                            </div>
                        @endif

                    </div>
                @endif

                {{-- Footer --}}
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button 
                        wire:click="closeModal"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                    >
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>