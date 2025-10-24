<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">
                                Detalle del Jugador
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                        @if($player)
                            {{-- Información Personal --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Información Personal
                                </h4>
                                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-600">Nombre Completo</p>
                                        <p class="font-medium text-gray-900">{{ $player->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium text-gray-900">{{ $player->email ?? 'No registrado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Teléfono</p>
                                        <p class="font-medium text-gray-900">{{ $player->phone }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Estado</p>
                                        <p>
                                            @if($player->status === 'active')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                            @elseif($player->status === 'suspended')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Suspendido</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Bloqueado</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Fecha de Registro</p>
                                        <p class="font-medium text-gray-900">{{ $player->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Última Actividad</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $player->last_activity_at ? $player->last_activity_at->diffForHumans() : 'Nunca' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Información Financiera --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Información Financiera
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                        <p class="text-sm text-blue-600 mb-1">Saldo Actual</p>
                                        <p class="text-2xl font-bold text-blue-700">${{ number_format($player->balance, 2) }}</p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                        <p class="text-sm text-green-600 mb-1">Total Depositado</p>
                                        <p class="text-xl font-bold text-green-700">${{ number_format($totalDeposits, 2) }}</p>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                        <p class="text-sm text-red-600 mb-1">Total Retirado</p>
                                        <p class="text-xl font-bold text-red-700">${{ number_format($totalWithdrawals, 2) }}</p>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                        <p class="text-sm text-purple-600 mb-1">Total Bonos</p>
                                        <p class="text-xl font-bold text-purple-700">${{ number_format($totalBonuses, 2) }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Balance Neto (Depósitos - Retiros)</p>
                                    <p class="text-xl font-bold {{ ($totalDeposits - $totalWithdrawals) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($totalDeposits - $totalWithdrawals, 2) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Sistema de Referidos --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Sistema de Referidos
                                </h4>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-2">Código de Referido</p>
                                        <div class="flex items-center space-x-2">
                                            <code class="px-3 py-2 bg-white border border-gray-300 rounded text-lg font-mono font-bold text-blue-600">
                                                {{ $player->referral_code }}
                                            </code>
                                            <button 
                                                onclick="navigator.clipboard.writeText('{{ $player->referral_code }}')"
                                                wire:click="copyReferralCode"
                                                class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm"
                                            >
                                                Copiar
                                            </button>
                                        </div>
                                    </div>

                                    @if($player->referrer)
                                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                            <p class="text-sm text-blue-600 mb-2">Referido Por</p>
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900">{{ $player->referrer->name }}</span>
                                                <button 
                                                    wire:click="$dispatch('openPlayerDetail', { playerId: {{ $player->referrer->id }} })"
                                                    class="text-sm text-blue-600 hover:text-blue-800"
                                                >
                                                    Ver Perfil →
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if($referrals->count() > 0)
                                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                            <p class="text-sm text-green-600 mb-3">
                                                Jugadores Referidos ({{ $referrals->count() }})
                                            </p>
                                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                                @foreach($referrals as $referral)
                                                    <div class="flex items-center justify-between bg-white p-2 rounded">
                                                        <div class="flex-1">
                                                            <p class="font-medium text-gray-900">{{ $referral->name }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                Saldo: ${{ number_format($referral->balance, 2) }} • 
                                                                Registro: {{ $referral->created_at->format('d/m/Y') }}
                                                            </p>
                                                        </div>
                                                        <button 
                                                            wire:click="$dispatch('openPlayerDetail', { playerId: {{ $referral->id }} })"
                                                            class="text-sm text-blue-600 hover:text-blue-800"
                                                        >
                                                            Ver →
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-gray-500 py-4">
                                            No ha referido a ningún jugador aún
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Últimas Transacciones --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Últimas 10 Transacciones
                                </h4>
                                @if($transactions->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($transactions as $transaction)
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm">
                                                            <span class="capitalize">{{ str_replace('_', ' ', $transaction->type) }}</span>
                                                        </td>
                                                        <td class="px-4 py-2 text-sm font-semibold">
                                                            ${{ number_format($transaction->amount, 2) }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm">
                                                            @if($transaction->status === 'completed')
                                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completado</span>
                                                            @elseif($transaction->status === 'pending')
                                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                                            @else
                                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">{{ ucfirst($transaction->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-500">
                                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center text-gray-500 py-4">No hay transacciones registradas</p>
                                @endif
                            </div>

                            {{-- Activity Log --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Últimas Actividades
                                </h4>
                                @if($activityLog->count() > 0)
                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                        @foreach($activityLog as $activity)
                                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded">
                                                <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-blue-500"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-center text-gray-500 py-4">No hay actividad registrada</p>
                                @endif
                            </div>

                            {{-- Acciones Rápidas --}}
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @if($player->status === 'active')
                                        <button 
                                            wire:click="$dispatch('openSuspendPlayer', { playerId: {{ $player->id }} })"
                                            class="px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-sm font-medium"
                                        >
                                            ⚠️ Suspender
                                        </button>
                                        <button 
                                            wire:click="$dispatch('openBlockPlayer', { playerId: {{ $player->id }} })"
                                            class="px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-medium"
                                        >
                                            🚫 Bloquear
                                        </button>
                                    @else
                                        <button 
                                            wire:click="$dispatch('openActivatePlayer', { playerId: {{ $player->id }} })"
                                            class="px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm font-medium"
                                        >
                                            ✅ Activar
                                        </button>
                                    @endif
                                    
                                    <button 
                                        wire:click="$dispatch('openEditPlayer', { playerId: {{ $player->id }} }); closeModal()"
                                        class="px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm font-medium"
                                    >
                                        ✏️ Editar
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button 
                            wire:click="closeModal"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>