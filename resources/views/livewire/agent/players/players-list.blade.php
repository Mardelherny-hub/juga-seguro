<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Gestión de Jugadores</h2>
        <div class="flex items-center space-x-4">
            <div class="mr-8 text-sm text-gray-600 dark:text-gray-400">
                Total: {{ $players->total() }} jugadores
            </div>
            <button 
                wire:click="$dispatch('openCreatePlayer')"
                class="px-4 py-2 ml-8 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Agregar Jugador</span>
            </button>
        </div>
    </div>

    {{-- Búsqueda y Filtros --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Búsqueda --}}
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Nombre, email, teléfono, código..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            {{-- Filtro Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select 
                    wire:model.live="statusFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="suspended">Suspendidos</option>
                    <option value="blocked">Bloqueados</option>
                </select>
            </div>

            {{-- Filtro Referidor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tiene Referidor</label>
                <select 
                    wire:model.live="hasReferrerFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Todos</option>
                    <option value="yes">Sí</option>
                    <option value="no">No</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Filtro Saldo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Con Saldo</label>
                <select 
                    wire:model.live="hasBalanceFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="all">Todos</option>
                    <option value="yes">Sí</option>
                    <option value="no">No</option>
                </select>
            </div>

            {{-- Botón Limpiar Filtros --}}
            <div class="flex items-end">
                <button 
                    wire:click="$set('search', ''); $set('statusFilter', 'all'); $set('hasReferrerFilter', 'all'); $set('hasBalanceFilter', 'all')"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                >
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table wire:key="players-table-{{ now()->timestamp }}" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jugador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th> --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código Referido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($players as $player)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $player->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $player->display_name }}</div>
                                        @if($player->referrals_count > 0)
                                            <div class="text-xs text-blue-600">
                                                👥 {{ $player->referrals_count }} referido{{ $player->referrals_count > 1 ? 's' : '' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $player->email ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $player->phone }}</div>
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $player->balance > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    ${{ number_format($player->balance, 2) }}
                                </div>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($player->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @elseif($player->status === 'suspended')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Suspendido
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Bloqueado
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $player->referral_code }}</div>
                                @if($player->referrer)
                                    <div class="text-xs text-gray-500">
                                        Por: {{ $player->referrer->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $player->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button 
                                    wire:click="$dispatch('openPlayerDetail', { playerId: {{ $player->id }} })"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                    title="Ver Detalle"
                                >
                                    Ver
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <button 
                                        wire:click="$dispatch('openEditPlayer', { playerId: {{ $player->id }} })"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="Editar"
                                    >
                                        Editar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron jugadores</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($players->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $players->links() }}
            </div>
        @endif
    </div>
</div>