<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg" wire:poll.30s>
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
        <a href="{{ route('dashboard.transactions.pending') }}" 
           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
            Ver todas →
        </a>
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
                                    {{-- Tipo --}}
                                    @if($transaction->type === 'deposit')
                                        <span class="flex items-center text-green-600 font-semibold">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            Depósito
                                        </span>
                                    @else
                                        <span class="flex items-center text-red-600 font-semibold">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                            Retiro
                                        </span>
                                    @endif

                                    {{-- Monto --}}
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($transaction->amount, 2) }}
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
                                    wire:click="$dispatch('openTransactionDetail', { transactionId: {{ $transaction->id }} })"
                                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                >
                                    Ver
                                </button>
                                <button 
                                    wire:click="$dispatch('openTransactionApproval', { transactionId: {{ $transaction->id }} })"
                                    class="px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition"
                                >
                                    Aprobar
                                </button>
                                <button 
                                    wire:click="$dispatch('openTransactionRejection', { transactionId: {{ $transaction->id }} })"
                                    class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition"
                                >
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
</div>