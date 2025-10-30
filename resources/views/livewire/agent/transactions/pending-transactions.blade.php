<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg" wire:poll.10s>
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                Transacciones Pendientes
            </h3>
            @if($pendingCount > 0)
                <span class="px-3 py-1 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
                    {{ $pendingCount }}
                </span>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            @if($pendingCount > 0)
                <a href="{{ route('dashboard.transactions.monitor') }}" 
                   class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span>Modo Monitor</span>
                </a>
            @endif
            <a href="{{ route('dashboard.transactions.pending') }}" 
               class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                Ver todas →
            </a>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="p-6">
        @if($transactions->isEmpty())
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 font-medium">¡Todo al día!</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">No hay transacciones pendientes</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($transactions as $transaction)
                    <div class="border rounded-lg p-4 hover:shadow-md transition {{ $this->getUrgencyClass($transaction->created_at) }}">
                        <div class="flex items-center justify-between">
                            {{-- Info Izquierda --}}
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    {{-- Tipo con badge --}}
                                    <div>
                                        @if($transaction->type === 'deposit')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                                </svg>
                                                Depósito
                                            </span>
                                        @elseif($transaction->type === 'withdrawal')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                                </svg>
                                                Retiro
                                            </span>
                                        @elseif($transaction->type === 'account_creation')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                                </svg>
                                                Crear Usuario
                                            </span>
                                        @elseif($transaction->type === 'account_unlock')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                </svg>
                                                Desbloquear Usuario
                                            </span>
                                        @elseif($transaction->type === 'password_reset')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                                Cambiar Contraseña
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Monto --}}
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        @if($transaction->isAccountRequest())
                                            <span class="text-gray-400">-</span>
                                        @else
                                            ${{ number_format($transaction->amount, 2) }}
                                        @endif
                                    </span>

                                    {{-- ID --}}
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        #{{ $transaction->id }}
                                    </span>
                                </div>

                                {{-- Jugador --}}
                                <div class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-medium">{{ $transaction->player->name }}</span>
                                    <span class="text-gray-400">|</span>
                                    <span>Saldo: ${{ number_format($transaction->player->balance, 2) }}</span>
                                </div>

                                {{-- Tiempo de espera --}}
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $this->getTimeWaiting($transaction->created_at) }}
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="flex flex-col space-y-2">
                                <button 
                                    wire:click="$dispatch('openApprovalModal', { transactionId: {{ $transaction->id }} })"
                                    class="px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition flex items-center justify-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aprobar
                                </button>
                                <button 
                                    wire:click="$dispatch('openRejectionModal', { transactionId: {{ $transaction->id }} })"
                                    class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center justify-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Rechazar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Ver más si hay más de 10 --}}
            @if($pendingCount > 10)
                <div class="mt-4 text-center">
                    <a href="{{ route('dashboard.transactions.pending') }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Ver todas las {{ $pendingCount }} transacciones pendientes →
                    </a>
                </div>
            @endif
        @endif
    </div>

     <!-- Modales -->
    @livewire('agent.transactions.approve-transaction')
    @livewire('agent.transactions.reject-transaction')

</div>