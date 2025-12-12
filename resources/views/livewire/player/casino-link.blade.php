<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    {{-- Header --}}
                    <div class="px-6 py-4" style="background: linear-gradient(135deg, {{ $tenant->primary_color }} 0%, {{ $tenant->secondary_color }} 100%);">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-white">
                                    🎰 Acceder al Casino
                                </h3>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-6">
                        
                        {{-- Avatar y Usuario --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white text-2xl font-bold">
                                {{ strtoupper(substr($player->username, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Usuario</p>
                                <p class="text-lg font-bold text-white">{{ $player->username }}</p>
                            </div>
                        </div>

                        {{-- Aviso de Contraseña --}}
                        <div class="bg-yellow-900/30 border border-yellow-600/50 rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-200 mb-1">⚠️ Importante</p>
                                    <p class="text-sm text-yellow-300">
                                        No olvides cambiar la contraseña predeterminada desde tu perfil una vez dentro.
                                    </p>
                                    <p class="text-xs text-yellow-400 mt-2">
                                        Tu seguridad es importante.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="bg-blue-900/30 border border-blue-600/50 rounded-lg p-4">
                            <p class="text-sm text-blue-200">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Serás redirigido al casino en una nueva pestaña
                            </p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-700 px-6 py-4 flex justify-end space-x-3">
                        <button 
                            type="button"
                            wire:click="closeModal"
                            class="px-6 py-2 border border-gray-500 rounded-lg text-gray-300 hover:bg-gray-600 transition"
                        >
                            Cancelar
                        </button>
                        <a 
                            href="{{ $tenant->casino_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            onclick="@this.logCasinoAccess()"
                            class="px-6 py-2 text-white rounded-lg hover:opacity-90 transition font-semibold inline-block text-center"
                            style="background: linear-gradient(135deg, {{ $tenant->primary_color }} 0%, {{ $tenant->secondary_color }} 100%);"
                        >
                            Ir al Casino 🎰
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

   
</div>