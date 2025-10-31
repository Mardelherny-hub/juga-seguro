<div wire:poll.5s="loadActivities">
    {{-- Botón flotante --}}
    <button 
        wire:click="togglePanel"
        class="fixed bottom-6 right-6 z-40 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-full p-4 shadow-lg transition-all duration-200 flex items-center space-x-2"
        title="Ver Mis Actividades"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($hasNew)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-bounce">
                •
            </span>
        @endif
    </button>

    {{-- Panel lateral --}}
    <div 
        class="fixed top-0 right-0 h-full w-96 bg-white dark:bg-gray-800 shadow-2xl transform transition-transform duration-300 z-50 {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}"
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <div>
                    <h3 class="font-bold text-lg">Mis Actividades</h3>
                    <p class="text-xs text-blue-100">Últimas 24 horas</p>
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
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $activity['is_new'] ? 'bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900 dark:to-purple-900' : '' }}">
                    @if($activity['type'] === 'transaction')
                        <div class="flex items-start space-x-3">
                            {{-- Icono según estado --}}
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="bg-red-100 dark:bg-red-900 rounded-full p-2">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Contenido --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    @if($activity['status'] === 'pending')
                                        @if($activity['transaction_type'] === 'deposit')
                                            ⏳ Carga en Revisión
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            ⏳ Retiro en Revisión
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            ⏳ Creación de Usuario en Proceso
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            ⏳ Desbloqueo en Proceso
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            ⏳ Cambio de Contraseña en Proceso
                                        @endif
                                    @elseif($activity['status'] === 'completed')
                                        @if($activity['transaction_type'] === 'deposit')
                                            ✅ Carga Aprobada
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            ✅ Retiro Aprobado
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            ✅ Usuario Creado
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            ✅ Usuario Desbloqueado
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            ✅ Contraseña Cambiada
                                        @endif
                                    @else
                                        @if($activity['transaction_type'] === 'deposit')
                                            ❌ Carga Rechazada
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            ❌ Retiro Rechazado
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            ❌ Creación Rechazada
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            ❌ Desbloqueo Rechazado
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            ❌ Cambio Rechazado
                                        @endif
                                    @endif
                                </p>
                                
                                @if(in_array($activity['transaction_type'], ['deposit', 'withdrawal']))
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Monto: <span class="font-bold">${{ number_format($activity['amount'], 2) }}</span>
                                    </p>
                                @else
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Solicitud de gestión de cuenta
                                    </p>
                                @endif
                                
                                @if($activity['status'] === 'pending')
                                    <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                        Estamos procesando tu solicitud...
                                    </p>
                                @elseif($activity['status'] === 'completed')
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                        @if($activity['transaction_type'] === 'deposit')
                                            ¡Tu saldo fue acreditado!
                                        @elseif($activity['transaction_type'] === 'withdrawal')
                                            ¡Tu retiro fue procesado!
                                        @elseif($activity['transaction_type'] === 'account_creation')
                                            Revisa tus mensajes para ver las credenciales
                                        @elseif($activity['transaction_type'] === 'account_unlock')
                                            Ya puedes acceder a tu cuenta
                                        @elseif($activity['transaction_type'] === 'password_reset')
                                            Tu nueva contraseña es: bet123
                                        @endif
                                    </p>
                                @else
                                    @if(!empty($activity['notes']))
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 italic">
                                            Motivo: {{ $activity['notes'] }}
                                        </p>
                                    @else
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                            Contacta con soporte para más detalles
                                        </p>
                                    @endif
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $activity['time'] }}
                                </p>
                            </div>

                            {{-- Badge nuevo --}}
                            @if($activity['is_new'])
                                <span class="flex-shrink-0 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-xs px-2 py-1 rounded-full font-bold animate-pulse">
                                    NUEVO
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Sin actividad reciente</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Tus transacciones aparecerán aquí</p>
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