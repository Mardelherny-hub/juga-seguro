<div wire:poll.5s="loadActivities">
    {{-- Botón flotante --}}
    <button 
        wire:click="togglePanel"
        class="fixed bottom-6 right-6 z-40 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-200 flex items-center space-x-2"
        title="Ver Actividad Reciente"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Panel lateral --}}
    <div 
        class="fixed top-0 right-0 h-full w-96 bg-white dark:bg-gray-800 shadow-2xl transform transition-transform duration-300 z-50 {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}"
    >
        {{-- Header --}}
        <div class="bg-blue-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <div>
                    <h3 class="font-bold text-lg">Actividad Reciente</h3>
                    <p class="text-xs text-blue-100">Últimas 2 horas</p>
                </div>
            </div>
            <button 
                wire:click="togglePanel"
                class="text-white hover:text-gray-200"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Lista de actividades --}}
        <div class="overflow-y-auto h-full pb-20">
            @forelse($activities as $activity)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $activity['is_new'] ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                    @if($activity['type'] === 'transaction')
                        <div class="flex items-start space-x-3">
                            {{-- Icono según tipo y estado --}}
                            <div class="flex-shrink-0">
                                @if($activity['status'] === 'pending')
                                    <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-2">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @elseif($activity['status'] === 'completed')
                                    <div class="bg-green-100 dark:bg-green-900 rounded-full p-2">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="bg-red-100 dark:bg-red-900 rounded-full p-2">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Contenido --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $activity['player_name'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    @if($activity['status'] === 'pending')
                                        Solicitó 
                                        @if($activity['transaction_type'] === 'deposit')
                                            <span class="font-bold">depósito</span> de ${{ number_format($activity['amount'], 2) }}
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            <span class="font-bold">retiro</span> de ${{ number_format($activity['amount'], 2) }}
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            <span class="font-bold">creación de usuario</span>
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            <span class="font-bold">desbloqueo de cuenta</span>
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            <span class="font-bold">cambio de contraseña</span>
                                        @endif
                                    @elseif($activity['status'] === 'completed')
                                        @if($activity['transaction_type'] === 'deposit')
                                            Depósito aprobado de ${{ number_format($activity['amount'], 2) }}
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            Retiro aprobado de ${{ number_format($activity['amount'], 2) }}
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            Usuario creado
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            Cuenta desbloqueada
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            Contraseña cambiada
                                        @endif
                                    @else
                                        @if($activity['transaction_type'] === 'deposit')
                                            Depósito rechazado de ${{ number_format($activity['amount'], 2) }}
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            Retiro rechazado de ${{ number_format($activity['amount'], 2) }}
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            Creación rechazada
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            Desbloqueo rechazado
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            Cambio rechazado
                                        @endif
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $activity['time'] }}
                                </p>
                            </div>

                            {{-- Badge nuevo --}}
                            @if($activity['is_new'])
                                <span class="flex-shrink-0 bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-bold animate-pulse">
                                    NUEVO
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Sin actividad reciente</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Las acciones aparecerán aquí</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Overlay --}}
    @if($isOpen)
        <div 
            wire:click="togglePanel"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
        ></div>
    @endif
</div>