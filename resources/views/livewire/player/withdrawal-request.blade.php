<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center">
            
            <!-- Modal Compacto -->
            <div class="bg-gray-800 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-md border-t sm:border border-gray-700"
                 @click.away="$wire.close()">
                
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">💸 Retirar Fondos</h3>
                    <button wire:click="close" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4">
                    
                    @if($savedAccounts->isEmpty())
                        {{-- NO TIENE CUENTAS --}}
                        <div class="text-center py-6">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-900/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white mb-2">No tienes cuentas guardadas</h4>
                            <p class="text-sm text-gray-400 mb-6">Primero debes agregar una cuenta bancaria para realizar retiros</p>
                            <a 
                                href="{{ route('player.withdrawal-accounts') }}"
                                class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Agregar Cuenta
                            </a>
                        </div>

                    @else
                        {{-- TIENE CUENTAS --}}
                        <form wire:submit.prevent="submit" class="space-y-4">
                            
                            <!-- Monto -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Monto a retirar</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">$</span>
                                    <input 
                                        type="number" 
                                        wire:model="amount"
                                        class="w-full pl-8 pr-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                                        placeholder="500"
                                        step="0.01"
                                    >
                                </div>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-400">Mínimo: $500 | Tu saldo: ${{ number_format($player->balance, 2) }}</p>
                            </div>

                            <!-- Selector de Cuenta -->
                            @if($savedAccounts->count() === 1)
                                {{-- UNA SOLA CUENTA --}}
                                @php $account = $savedAccounts->first(); @endphp
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Cuenta de destino</label>
                                    <div class="bg-gray-700 rounded-lg p-3 border border-gray-600">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                                                @if($account->account_type === 'alias')
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-white">{{ strtoupper($account->account_type) }} - {{ $account->holder_name }}</p>
                                                <p class="text-xs text-gray-400">
                                                    @if($account->account_type === 'alias')
                                                        {{ $account->alias }}
                                                    @else
                                                        {{ $account->formatted_account }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @else
                                {{-- VARIAS CUENTAS --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Selecciona la cuenta</label>
                                    <div class="space-y-2">
                                        @foreach($savedAccounts as $account)
                                            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition
                                                {{ $selectedAccountId == $account->id ? 'border-green-500 bg-gray-700' : 'border-gray-600 hover:border-gray-500' }}">
                                                <input 
                                                    type="radio" 
                                                    wire:model="selectedAccountId" 
                                                    value="{{ $account->id }}"
                                                    class="text-green-600 focus:ring-green-500"
                                                >
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                                                    @if($account->account_type === 'alias')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-sm font-medium text-white">{{ strtoupper($account->account_type) }}</p>
                                                        @if($account->is_default)
                                                            <span class="px-1.5 py-0.5 text-xs bg-green-600 text-white rounded">Predeterminada</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-gray-400 truncate">
                                                        {{ $account->holder_name }} · 
                                                        @if($account->account_type === 'alias')
                                                            {{ $account->alias }}
                                                        @else
                                                            {{ $account->formatted_account }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('selectedAccountId')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Link a gestionar cuentas -->
                            <a 
                                href="{{ route('player.withdrawal-accounts') }}"
                                class="block text-center text-sm text-blue-400 hover:text-blue-300"
                            >
                                Gestionar mis cuentas
                            </a>

                            <!-- Info -->
                            <div class="bg-blue-900/30 border border-blue-600/50 rounded-lg p-3">
                                <p class="text-xs text-blue-200">
                                    ℹ️ Los retiros serán realizados lo antes posible.<br>
                                    Asegúrate de que la información de la cuenta sea correcta para evitar demoras.
                                </p>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3">
                                <button 
                                    type="button"
                                    wire:click="close"
                                    class="flex-1 py-3 border border-gray-600 text-gray-300 font-medium rounded-lg hover:bg-gray-700 transition"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit"
                                    class="flex-1 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition"
                                >
                                    Retirar
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>