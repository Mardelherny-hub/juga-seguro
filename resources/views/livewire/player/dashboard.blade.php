<div wire:poll.5s="loadData" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    @php
        $tenant = $player->tenant;
    @endphp

    <!-- Bienvenida + Saldo Destacado -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-6">
            Bienvenido, {{ $player->username }}
        </h1>

        <!-- Card de Saldo Principal -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 shadow-2xl">
    
    <!-- BOTÓN CASINO DESTACADO -->
    @if($tenant->casino_url)
        <button 
            wire:click="$dispatch('openCasinoModal')"                            
            class="w-full mb-4 px-6 py-3.5 rounded-xl font-semibold text-white text-sm flex items-center justify-center gap-2 transition-all hover:opacity-90 shadow-lg"
            style="background: linear-gradient(135deg, {{ $tenant->primary_color }} 0%, {{ $tenant->secondary_color }} 100%);"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Link al casino ¡CLICK AQUÍ!
        </button>
    @endif

    <!-- GRID 2x2 DE ACCIONES PRINCIPALES -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <!-- Cargar Saldo -->
        <button 
            wire:click="$dispatch('openDepositModal')" 
            class="flex flex-col items-center justify-center gap-2 p-5 bg-gray-700/50 rounded-xl transition-all hover:bg-gray-700 text-white"
        >
            <div class="text-3xl">💰</div>
            <span class="text-xs font-medium text-center">Cargar Saldo</span>
        </button>

        <!-- Retirar Fondos -->
        <button 
            wire:click="$dispatch('openWithdrawalModal')" 
            class="flex flex-col items-center justify-center gap-2 p-5 bg-gray-700/50 rounded-xl transition-all hover:bg-gray-700 text-white"
        >
            <div class="text-3xl">🏆</div>
            <span class="text-xs font-medium text-center">Retirar Fondos</span>
        </button>

        <!-- Botones del componente player-account-actions (se integran en el grid) -->
        @livewire('player.player-account-actions', ['player' => $player])
    </div>

    <!-- MENSAJE SI ESTÁ BLOQUEADO -->
    @if($player->isBlocked())
        <div class="mb-4">
            <div class="bg-red-900/30 border-2 border-red-600 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-200 mb-1">Cuenta Bloqueada</h3>
                        <p class="text-red-300 text-sm mb-3">
                            Tu cuenta ha sido bloqueada. Para más información o solicitar el desbloqueo, contacta con soporte.
                        </p>
                        @livewire('player.request-unblock')
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- BOTÓN WHATSAPP -->
    @if($tenant->whatsapp_number)
        <a 
            href="{{ $tenant->whatsapp_link }}?text=Hola, soy {{ $player->name }} (Usuario: {{ $player->username }}). Necesito ayuda con:"
            target="_blank"
            class="w-full px-6 py-3.5 bg-green-600 rounded-xl font-semibold text-white text-sm hover:bg-green-700 transition-all shadow-lg flex items-center justify-center gap-2"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Contactar por WhatsApp
        </a>
    @endif
</div>
    </div>

    <!-- Grid de Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Card: Código de Referido -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                     style="background-color: {{ $tenant->primary_color }}20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Tu código de referido</p>
                    <p class="text-2xl font-bold text-white font-mono">{{ $referralCode }}</p>
                </div>
            </div>
            <button wire:click="copyReferralCode" 
                    onclick="navigator.clipboard.writeText('{{ $referralCode }}')"
                    class="w-full py-2 px-4 bg-gray-700 hover:bg-gray-600 rounded-lg text-white font-medium transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Copiar código
            </button>
            <p class="text-gray-400 text-xs mt-3 text-center">Comparte y gana bonos</p>
        </div>

        <!-- Card: Referidos -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition cursor-pointer"
             wire:click="$dispatch('openReferralsModal')">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-500/20">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-4xl font-bold text-white mb-2">{{ $referralsCount }}</p>
            <p class="text-gray-400 text-sm">Jugadores referidos</p>
            <a href="{{ route('player.referrals') }}" class="text-blue-400 text-xs mt-2 hover:underline">Ver detalles →</a>
        </div>

        <!-- Card: Bonos Activos -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition cursor-pointer"
             wire:click="$dispatch('openBonusesModal')">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-yellow-500/20">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>
            </div>
            <p class="text-4xl font-bold text-white mb-2">{{ $activeBonuses }}</p>
            <p class="text-gray-400 text-sm">Bonos disponibles</p>
            <a href="{{ route('player.bonuses') }}" class="text-yellow-400 text-xs mt-2 hover:underline">Ver bonos →</a>
        </div>

    </div>

    <!-- Últimas Transacciones -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">Últimas Transacciones</h2>
                <a href="{{ route('player.transactions') }}" wire:navigate 
                   class="text-sm font-medium hover:underline"
                   style="color: {{ $tenant->primary_color }}">
                    Ver todas →
                </a>
            </div>
        </div>

        @if($recentTransactions->count() > 0)
            <div class="divide-y divide-gray-700">
                @foreach($recentTransactions as $transaction)
                    <div class="p-6 hover:bg-gray-700/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <!-- Icono según tipo -->
                                <div class="w-12 h-12 rounded-full flex items-center justify-center
                                    {{ $transaction->type == 'deposit' ? 'bg-green-500/20' : 
                                       ($transaction->type == 'withdrawal' ? 'bg-red-500/20' : 'bg-yellow-500/20') }}">
                                    @if($transaction->type == 'deposit')
                                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                        </svg>
                                    @elseif($transaction->type == 'withdrawal')
                                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <p class="font-semibold text-white">
                                        @if($transaction->type == 'deposit') Depósito
                                        @elseif($transaction->type == 'withdrawal') Retiro
                                        @elseif($transaction->type == 'bonus') Bono
                                        @else {{ ucfirst($transaction->type) }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-400">{{ $transaction->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-xl font-bold {{ $transaction->type == 'deposit' || $transaction->type == 'bonus' ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $transaction->type == 'deposit' || $transaction->type == 'bonus' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </p>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium mt-1
                                    {{ $transaction->status == 'completed' ? 'bg-green-500/20 text-green-400' : 
                                       ($transaction->status == 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-400">No tienes transacciones aún</p>
                <p class="text-gray-500 text-sm mt-2">Realiza tu primera carga de saldo</p>
            </div>
        @endif
    </div>


    <!-- Modal de Carga -->
    @livewire('player.deposit-request')


    <!-- Modal de Retiro -->
    @livewire('player.withdrawal-request')


    @livewire('player.casino-link')

</div>