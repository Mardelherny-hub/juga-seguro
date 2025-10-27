<div>
    <div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header con info del cliente --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            {{-- Logo del cliente --}}
                            @if($tenant->logo_url)
                                <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->name }}" class="h-16 w-16 rounded-lg object-cover">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-white">{{ substr($tenant->name, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenant->domain }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 text-xs rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->is_active ? '✓ Activo' : '✕ Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('super-admin.clients.edit', $tenant->id) }}" 
                               wire:navigate
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Editar Cliente
                            </a>
                            <a href="{{ route('super-admin.clients.index') }}" 
                               wire:navigate
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Volver
                            </a>
                        </div>
                    </div>

                    {{-- Métricas del Cliente --}}
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-600 dark:text-blue-300">Total Jugadores</p>
                                    <p class="text-2xl font-bold text-blue-900 dark:text-white">{{ number_format($totalPlayers) }}</p>
                                </div>
                                <svg class="w-10 h-10 text-blue-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-600 dark:text-green-300">Activos</p>
                                    <p class="text-2xl font-bold text-green-900 dark:text-white">{{ number_format($activePlayers) }}</p>
                                </div>
                                <svg class="w-10 h-10 text-green-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-purple-600 dark:text-purple-300">Saldo Total</p>
                                    <p class="text-2xl font-bold text-purple-900 dark:text-white">${{ number_format($totalBalance, 2) }}</p>
                                </div>
                                <svg class="w-10 h-10 text-purple-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-300">Pendientes</p>
                                    <p class="text-2xl font-bold text-yellow-900 dark:text-white">{{ number_format($pendingTransactions) }}</p>
                                </div>
                                <svg class="w-10 h-10 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900 dark:to-indigo-800 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-indigo-600 dark:text-indigo-300">Trans. Hoy</p>
                                    <p class="text-2xl font-bold text-indigo-900 dark:text-white">{{ number_format($completedTransactionsToday) }}</p>
                                </div>
                                <svg class="w-10 h-10 text-indigo-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Info del Admin --}}
                    @php
                        $admin = $tenant->users->first();
                    @endphp
                    
                    @if($admin)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">👤 Administrador del Cliente</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Nombre:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Email:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Último acceso:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Nunca' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabla de Jugadores --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            👥 Jugadores del Cliente
                        </h3>
                    </div>

                    {{-- Filtros y Búsqueda --}}
                    <div class="mb-6 flex flex-col md:flex-row gap-4">
                        {{-- Búsqueda --}}
                        <div class="flex-1">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Buscar por nombre, email, teléfono, usuario o código..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        {{-- Filtro Estado --}}
                        <div>
                            <select 
                                wire:model.live="statusFilter"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="all">Todos los estados</option>
                                <option value="active">Activos</option>
                                <option value="suspended">Suspendidos</option>
                                <option value="blocked">Bloqueados</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jugador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contacto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Saldo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registro</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($players as $player)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            #{{ $player->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $player->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">@{{ $player->username }}</div>
                                                    @if($player->referrals_count > 0)
                                                        <div class="text-xs text-blue-600">
                                                            👥 {{ $player->referrals_count }} referido{{ $player->referrals_count > 1 ? 's' : '' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $player->email ?? '-' }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $player->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold {{ $player->balance > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                                ${{ number_format($player->balance, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $player->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $player->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $player->status === 'blocked' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                @if($player->status === 'active') ✓ Activo
                                                @elseif($player->status === 'suspended') ⏸ Suspendido
                                                @else ✕ Bloqueado
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $player->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                {{-- Editar --}}
                                                <button 
                                                    wire:click="$dispatch('openEditPlayer', { playerId: {{ $player->id }} })"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400"
                                                    title="Editar"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>

                                                {{-- Activar/Suspender/Bloquear --}}
                                                @if($player->status === 'active')
                                                    <button 
                                                        wire:click="togglePlayerStatus({{ $player->id }}, 'suspend')"
                                                        wire:confirm="¿Suspender a {{ $player->name }}?"
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                        title="Suspender"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button 
                                                        wire:click="togglePlayerStatus({{ $player->id }}, 'activate')"
                                                        wire:confirm="¿Activar a {{ $player->name }}?"
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Activar"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </button>
                                                @endif

                                                @if($player->status !== 'blocked')
                                                    <button 
                                                        wire:click="togglePlayerStatus({{ $player->id }}, 'block')"
                                                        wire:confirm="¿Bloquear a {{ $player->name }}?"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Bloquear"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                <p class="text-lg font-medium">No se encontraron jugadores</p>
                                                <p class="text-sm">Este cliente aún no tiene jugadores registrados</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if($players->hasPages())
                        <div class="mt-6">
                            {{ $players->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Transacciones Recientes --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        💸 Transacciones Recientes
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Jugador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            #{{ $transaction->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transaction->player->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}
                                            ">
                                                {{ $transaction->type === 'deposit' ? '↓ Depósito' : '↑ Retiro' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $transaction->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                @if($transaction->status === 'pending') ⏳ Pendiente
                                                @elseif($transaction->status === 'completed') ✓ Completada
                                                @else ✕ Rechazada
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            No hay transacciones registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Editar Player (se carga dinámicamente) --}}
    @livewire('super-admin.players.edit-player')
</div>
</div>
