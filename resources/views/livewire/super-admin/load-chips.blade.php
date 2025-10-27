<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Cargar Fichas
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if($tenant)
                        <!-- Body -->
                        <div class="px-6 py-4 space-y-4">
                            
                            <!-- Info del Cliente -->
                            <div class="bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 rounded-lg p-4">
                                <p class="text-sm font-semibold text-blue-800 dark:text-blue-300">Cliente</p>
                                <p class="text-lg font-bold text-blue-900 dark:text-blue-200">{{ $tenant->name }}</p>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <span class="text-blue-700 dark:text-blue-400">Saldo actual:</span>
                                    <span class="font-bold text-blue-900 dark:text-blue-200">{{ number_format($tenant->chips_balance) }} fichas</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-blue-700 dark:text-blue-400">Precio por ficha:</span>
                                    <span class="font-bold text-blue-900 dark:text-blue-200">${{ number_format($tenant->chip_price, 2) }}</span>
                                </div>
                            </div>

                            <!-- Cantidad de fichas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cantidad de fichas a cargar *
                                </label>
                                <input 
                                    type="number" 
                                    wire:model.live="quantity"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Ej: 100"
                                    min="1"
                                    max="10000"
                                >
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Monto pagado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Monto pagado *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                                    <input 
                                        type="number" 
                                        wire:model="amountPaid"
                                        step="0.01"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0.00"
                                    >
                                </div>
                                @error('amountPaid')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cálculo -->
                            @if($quantity && is_numeric($quantity) && $quantity > 0)
                                <div class="bg-green-50 dark:bg-green-900 dark:bg-opacity-20 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-green-700 dark:text-green-400">Saldo actual:</span>
                                        <span class="font-semibold text-green-900 dark:text-green-200">{{ number_format($tenant->chips_balance) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-green-700 dark:text-green-400">A cargar:</span>
                                        <span class="font-semibold text-green-900 dark:text-green-200">+{{ number_format($quantity) }}</span>
                                    </div>
                                    <div class="border-t border-green-300 dark:border-green-700 mt-2 pt-2 flex items-center justify-between">
                                        <span class="font-bold text-green-800 dark:text-green-300">Nuevo saldo:</span>
                                        <span class="font-bold text-xl text-green-900 dark:text-green-100">{{ number_format($tenant->chips_balance + $quantity) }} fichas</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3">
                            <button 
                                wire:click="closeModal"
                                type="button"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition"
                                {{ $isProcessing ? 'disabled' : '' }}
                            >
                                Cancelar
                            </button>
                            
                            <button 
                                wire:click="loadChips"
                                type="button"
                                class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                wire:loading.attr="disabled"
                                {{ $isProcessing ? 'disabled' : '' }}
                            >
                                <span wire:loading.remove wire:target="loadChips">
                                    💎 Cargar Fichas
                                </span>
                                <span wire:loading wire:target="loadChips">
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>