<div>
    @if($showModal && $player)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    {{-- Header --}}
                    <div class="bg-blue-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">
                                Editar Información del Jugador
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <form wire:submit.prevent="updatePlayer">
                        <div class="px-6 py-6 space-y-4">
                            {{-- Nombre --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Completo <span class="text-red-600">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    placeholder="Nombre completo del jugador"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    wire:model="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                    placeholder="correo@ejemplo.com"
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Debe ser único dentro del tenant</p>
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono <span class="text-red-600">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="phone"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                    placeholder="+5492234123456"
                                >
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Debe ser único dentro del tenant</p>
                            </div>

                            {{-- Información no editable --}}
                            <div class="border-t pt-4 mt-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Información no editable</h4>
                                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                                    <div>
                                        <p class="text-xs text-gray-600">Código de Referido</p>
                                        <p class="text-sm font-mono font-semibold text-gray-900">{{ $player->referral_code }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Saldo Actual</p>
                                        <p class="text-sm font-semibold text-gray-900">${{ number_format($player->balance, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Fecha de Registro</p>
                                        <p class="text-sm text-gray-900">{{ $player->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    @if($player->referrer)
                                        <div>
                                            <p class="text-xs text-gray-600">Referido Por</p>
                                            <p class="text-sm text-gray-900">{{ $player->referrer->name }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>