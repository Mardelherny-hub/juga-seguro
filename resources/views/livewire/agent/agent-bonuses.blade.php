<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Bonos</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Otorga bonos personalizados a tus jugadores</p>
    </div>

    <!-- Mensajes flash -->
    @if(session()->has('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Bonos Otorgados</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_bonuses'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monto Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($stats['total_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Por Tipo</p>
                    <div class="mt-2 space-y-1">
                        @foreach($stats['by_type'] as $stat)
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400">{{ ucfirst($stat->type) }}:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $stat->count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Búsqueda -->
    <div class="mb-6">
        <input 
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Buscar jugador por nombre, teléfono o email..."
            class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
        />
    </div>

    <!-- Lista de jugadores -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jugador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contacto</th>
                    {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Saldo</th> --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Bonos Recibidos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Bonos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($players as $player)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                {{ substr($player->display_name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $player->display_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $player->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        {{ $player->phone }}
                    </td>
                    {{-- <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($player->balance, 2) }}
                        </span>
                    </td> --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        {{ $player->bonuses_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                            ${{ number_format($player->bonuses_sum_amount ?? 0, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <button 
                            wire:click="openGrantModal({{ $player->id }})"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            Otorgar Bono
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        No se encontraron jugadores
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal: Otorgar Bono -->
    @if($showGrantModal && $selectedPlayerId)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Otorgar Bono Personalizado</h2>
                
                @php
                    $player = \App\Models\Player::find($selectedPlayerId);
                @endphp
                
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                    <p class="text-sm text-blue-900 dark:text-blue-100">
                        <span class="font-semibold">Jugador:</span> {{ $player->display_name }}
                    </p>
                    {{-- <p class="text-sm text-blue-900 dark:text-blue-100">
                        <span class="font-semibold">Saldo actual:</span> ${{ number_format($player->balance, 2) }}
                    </p> --}}
                </div>
                
                <form wire:submit.prevent="grantBonus">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto del Bono</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                <input 
                                    wire:model="bonusAmount" 
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="w-full pl-8 pr-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                            @error('bonusAmount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción / Motivo</label>
                            <textarea 
                                wire:model="bonusDescription"
                                rows="3"
                                placeholder="Ej: Bono por fidelidad, compensación, etc."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('bonusDescription')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button 
                            type="button"
                            wire:click="closeGrantModal"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                            Otorgar Bono
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>