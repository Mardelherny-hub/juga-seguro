<div>
<div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Programa de Referidos</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Invita amigos y gana bonos</p>
    </div>

    <!-- Código de Referido -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl font-bold mb-3">Tu Código de Referido</h2>
            <p class="text-blue-100 mb-6">Comparte este código con tus amigos y gana bonos cuando hagan su primer depósito</p>
            
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-6 mb-4">
                <p class="text-5xl font-bold tracking-wider mb-2">{{ $player->referral_code }}</p>
                <button 
                    wire:click="copyReferralCode"
                    class="px-6 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition">
                    📋 Copiar Código
                </button>
                
                @if(session()->has('copied'))
                <p class="text-sm text-green-200 mt-2">✓ Código copiado al portapapeles</p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-white bg-opacity-10 rounded-lg p-4">
                    <p class="text-blue-200 mb-1">Bono para ti</p>
                    <p class="text-2xl font-bold">$200</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-4">
                    <p class="text-blue-200 mb-1">Bono para tu amigo</p>
                    <p class="text-2xl font-bold">$200</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Referidos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_referrals'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Activos</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_referrals'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Depósitos Totales</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($stats['total_deposits'], 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Bonos Ganados</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">${{ number_format($stats['referral_bonuses'], 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Referidos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Mis Referidos</h2>
        </div>
        
        @forelse($referrals as $referral)
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($referral->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $referral->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Registrado: {{ $referral->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                
                <div class="text-right">
                    <span class="inline-block px-3 py-1 text-xs rounded-full mb-2
                        {{ $referral->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                        {{ $referral->status === 'suspended' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                        {{ $referral->status === 'blocked' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                        {{ ucfirst($referral->status) }}
                    </span>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $referral->deposits_count }} depósitos • ${{ number_format($referral->deposits_sum ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-lg font-medium">Aún no tienes referidos</p>
            <p class="text-sm mt-2">Comparte tu código y empieza a ganar bonos</p>
        </div>
        @endforelse
    </div>
</div>

@script
<script>
    $wire.on('code-copied', () => {
        const code = '{{ $player->referral_code }}';
        navigator.clipboard.writeText(code);
    });
</script>
@endscript
</div>
