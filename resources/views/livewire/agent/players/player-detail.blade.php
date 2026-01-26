<div>
    {{-- Modal --}}
    @if($showModal && $player)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                {{-- Modal Panel --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="h-12 w-12 rounded-full bg-white flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ $player->display_name }}</h3>
                                    <p class="text-blue-100 text-sm">ID: #{{ $player->id }}</p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-6 max-h-[70vh] overflow-y-auto">
                        
                        {{-- KPIs Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            {{-- Saldo --}}
                            {{-- <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Saldo Actual</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($player->balance, 2) }}</p>
                            </div> --}}

                            {{-- Total Depósitos --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Depósitos</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($totalDeposits, 2) }}</p>
                            </div>

                            {{-- Total Retiros --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Retiros</p>
                                <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($totalWithdrawals, 2) }}</p>
                            </div>

                            {{-- Total Bonos --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Bonos</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($totalBonuses, 2) }}</p>
                            </div>
                        </div>

                        {{-- Información Personal --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Información Personal
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $player->email ?: 'No registrado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Teléfono</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $player->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($player->status === 'active') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                        @elseif($player->status === 'suspended') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                        @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                        @endif">
                                        {{ ucfirst($player->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Fecha de Registro</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $player->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Código de Referido --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Sistema de Referidos
                            </h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Código de Referido</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white font-mono">{{ $player->referral_code }}</p>
                                    </div>
                                    <button 
                                        onclick="navigator.clipboard.writeText('{{ $player->referral_code }}')"
                                        wire:click="copyReferralCode"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                                    >
                                        Copiar
                                    </button>
                                </div>

                                @if($player->referrer)
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Referido por</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $player->referrer->name }}</p>
                                    </div>
                                @endif

                                @if($referrals->count() > 0)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Jugadores Referidos ({{ $referrals->count() }})</p>
                                    <div class="space-y-2 max-h-64 overflow-y-auto">
                                        @foreach($referrals as $referral)
                                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                                        {{ substr($referral->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $referral->name }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Registrado: {{ $referral->created_at->format('d/m/Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="inline-block px-2 py-1 text-xs rounded-full mb-1
                                                        {{ $referral->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                        {{ $referral->status === 'suspended' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                        {{ $referral->status === 'blocked' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                                        {{ ucfirst($referral->status) }}
                                                    </span>
                                                    {{-- <p class="text-xs text-gray-600 dark:text-gray-400">
                                                        Saldo: ${{ number_format($referral->balance, 2) }}
                                                    </p> --}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-lg text-red-500 dark:text-red-400">No ha referido a ningún jugador aún</p>
                            @endif
                            </div>
                        </div>

                        {{-- Últimas Transacciones --}}
                        @if($transactions->count() > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Últimas Transacciones
                                </h4>
                                <div class="space-y-3">
                                    @foreach($transactions as $transaction)
                                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 last:border-0">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    @if($transaction->type === 'deposit')
                                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $transaction->type === 'deposit' ? 'Depósito' : 'Retiro' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold {{ $transaction->type === 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                                </p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($transaction->status === 'completed') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                                    @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                                    @endif">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-100 dark:bg-gray-800 px-6 py-4 flex justify-end border-t border-gray-200 dark:border-gray-700">
                        <button 
                            wire:click="closeModal"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>