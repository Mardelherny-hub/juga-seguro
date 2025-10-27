<div>
    {{-- Mostrar estado si tiene solicitud pendiente --}}
    @if($pendingRequest)
        <div class="bg-yellow-900/30 border border-yellow-600/50 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-200 mb-1">Solicitud en Revisión</h3>
                    <p class="text-sm text-yellow-300 mb-2">
                        Tu solicitud de desbloqueo está siendo revisada por el equipo. Te notificaremos pronto.
                    </p>
                    <p class="text-xs text-yellow-400">
                        Enviada {{ $pendingRequest->created_at->diffForHumans() }}
                    </p>
                    @if($pendingRequest->reason)
                        <div class="mt-3 p-3 bg-yellow-900/20 rounded border border-yellow-600/30">
                            <p class="text-xs text-yellow-200 font-medium mb-1">Tu mensaje:</p>
                            <p class="text-sm text-yellow-100">{{ $pendingRequest->reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Botón para solicitar desbloqueo --}}
        <button 
            wire:click="openModal"
            class="w-full px-6 py-4 bg-blue-600 text-white rounded-xl font-bold text-lg hover:bg-blue-700 transition-all shadow-lg flex items-center justify-center gap-3"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            Solicitar Desbloqueo
        </button>
    @endif

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal --}}
                <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    {{-- Header --}}
                    <div class="bg-blue-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-white">
                                    Solicitar Desbloqueo de Cuenta
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
                    <form wire:submit.prevent="submit">
                        <div class="px-6 py-6">
                            
                            {{-- Info --}}
                            <div class="bg-blue-900/30 border border-blue-600/50 rounded-lg p-4 mb-6">
                                <p class="text-sm text-blue-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Tu cuenta está bloqueada. Explícanos por qué deberíamos desbloquearla y nuestro equipo revisará tu caso.
                                </p>
                            </div>

                            {{-- Campo de razón --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    ¿Por qué deberíamos desbloquear tu cuenta? <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    wire:model="reason"
                                    rows="6"
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('reason') border-red-500 @enderror"
                                    placeholder="Explica tu situación de forma clara y honesta. Esto ayudará al equipo a tomar una decisión..."
                                ></textarea>
                                @error('reason')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-400">
                                    Mínimo 10 caracteres. Sé claro y sincero.
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
                            <button 
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>