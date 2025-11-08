<div class="min-h-screen bg-gray-50 dark:bg-gray-900" wire:poll.10s="refresh">
    {{-- Header fijo --}}
    <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard.transactions.pending') }}" 
                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Monitor de Transacciones
                    </h1>
                    <span class="px-4 py-2 text-xl font-bold text-white bg-red-600 rounded-full">
                        {{ $pendingCount }}
                    </span>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span>Última actualización:</span>
                        <span class="font-medium">{{ $lastUpdate ? $lastUpdate->diffForHumans() : 'Nunca' }}</span>
                    </div>
                    <button 
                        wire:click="refresh"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel de configuración de notificaciones --}}
    <div class="px-6 py-3 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="enable-notifications"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        checked
                        onchange="window.transactionNotifications?.enable(); if(!this.checked) window.transactionNotifications?.disable();"
                    >
                    <label for="enable-notifications" class="text-sm text-gray-700 dark:text-gray-300">
                        Notificaciones del navegador
                    </label>
                </div>
                
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="enable-sound"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        checked
                        onchange="window.transactionNotifications?.enableSound(); if(!this.checked) window.transactionNotifications?.disableSound();"
                    >
                    <label for="enable-sound" class="text-sm text-gray-700 dark:text-gray-300">
                        Sonido de alerta
                    </label>
                </div>

                <button 
                    onclick="window.transactionNotifications?.requestPermission()"
                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                >
                    Solicitar permisos
                </button>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400">
                Actualización automática cada 10 segundos
            </div>
        </div>
    </div>


    {{-- Contenido principal --}}
    <div class="p-6">
        @if($transactions->isEmpty())
            {{-- Estado vacío --}}
            <div class="flex flex-col items-center justify-center py-20">
                <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">¡Todo al día!</h2>
                <p class="text-gray-500 dark:text-gray-400">No hay transacciones pendientes en este momento</p>
            </div>
        @else
            {{-- Grid de transacciones --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($transactions as $transaction)
                    @php
                        $urgencyClass = $this->getUrgencyClass($transaction->created_at);
                        $urgencyBadge = $this->getUrgencyBadge($transaction->created_at);
                    @endphp

                    <div class="border-2 rounded-xl shadow-lg hover:shadow-xl transition-shadow {{ $urgencyClass }}">
                        <div class="p-6">
                            {{-- Header del card --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    @if($transaction->type === 'deposit')
                                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </div>
                                    @elseif($transaction->type === 'withdrawal')
                                        <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </div>
                                    @elseif($transaction->type === 'account_creation')
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @elseif($transaction->type === 'account_unlock')
                                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @elseif($transaction->type === 'password_reset')
                                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            @if($transaction->type === 'deposit')
                                                DEPÓSITO
                                            @elseif($transaction->type === 'withdrawal')
                                                RETIRO
                                            @elseif($transaction->type === 'account_creation')
                                                CREAR USUARIO
                                            @elseif($transaction->type === 'account_unlock')
                                                DESBLOQUEO
                                            @elseif($transaction->type === 'password_reset')
                                                CAMBIAR CONTRASEÑA
                                            @endif
                                        </h3>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $urgencyBadge['class'] }}">
                                    {{ $urgencyBadge['text'] }}
                                </span>
                            </div>

                            {{-- Información del jugador --}}
                            <div class="mb-4">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $transaction->player->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Saldo actual: ${{ number_format($transaction->player->balance, 2) }}
                                </p>
                            </div>

                            {{-- Monto (solo para deposit/withdrawal) --}}
                            @if(in_array($transaction->type, ['deposit', 'withdrawal']))
                                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Monto</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($transaction->amount, 2) }}
                                    </p>
                                </div>
                            @else
                                {{-- Para solicitudes de cuenta, mostrar descripción --}}
                                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm text-blue-600 dark:text-blue-400 mb-1">📝 Solicitud</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $transaction->notes ?? 'Solicitud de gestión de cuenta' }}
                                    </p>
                                </div>
                            @endif

                            {{-- Tiempo de espera --}}
                            <div class="mb-6">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Esperando {{ $this->getTimeWaiting($transaction->created_at) }}
                                </div>
                            </div>

                            {{-- Botones de acción --}}
                            <div class="space-y-3">
                                <button 
                                    wire:click="$dispatch('openTransactionApproval', { transactionId: {{ $transaction->id }} })"
                                    class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors text-lg"
                                >
                                    ✓ APROBAR
                                </button>
                                <button 
                                    wire:click="$dispatch('openTransactionRejection', { transactionId: {{ $transaction->id }} })"
                                    class="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors text-lg"
                                >
                                    ✗ RECHAZAR
                                </button>
                                <button 
                                    wire:click="$dispatch('openTransactionDetail', { transactionId: {{ $transaction->id }} })"
                                    class="w-full py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors"
                                >
                                    Ver Detalle
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Incluir modales existentes --}}
    <livewire:agent.transactions.transaction-approval />
    <livewire:agent.transactions.transaction-rejection />
    <livewire:agent.transactions.transaction-detail />
</div>