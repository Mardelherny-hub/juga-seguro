<div>
    @if($isOpen)
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-data="{ show: @entangle('isOpen') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Modal -->
            <div class="bg-gray-800 rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-gray-700"
                 @click.away="$wire.close()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <!-- Header -->
                <div class="sticky top-0 bg-gray-800 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Cargar Saldo</h3>
                    <button wire:click="close" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <form wire:submit.prevent="submit" class="p-6 space-y-5">
                    
                    <!-- Instrucciones -->
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-200">
                                <p class="font-semibold mb-1">Realiza la transferencia a:</p>
                                @if($tenant->activeBankAccount)
                                <p class="text-blue-100">🏦 <strong>TITULAR:</strong> {{ $tenant->activeBankAccount->account_holder }}</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-blue-100">🔗 <strong>ALIAS:</strong> {{ $tenant->activeBankAccount->alias ?? $tenant->activeBankAccount->cbu ?? $tenant->activeBankAccount->cvu }}</p>
                                    <button type="button" 
                                            x-data="{ copied: false }"
                                            @click="navigator.clipboard.writeText('{{ $tenant->activeBankAccount->alias ?? $tenant->activeBankAccount->cbu ?? $tenant->activeBankAccount->cvu }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="text-blue-300 hover:text-white transition"
                                            :title="copied ? '¡Copiado!' : 'Copiar'">
                                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <svg x-show="copied" x-cloak class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                                @if($tenant->activeBankAccount->bank_name)
                                    <p class="text-blue-100">🏛️ <strong>BANCO:</strong> {{ $tenant->activeBankAccount->bank_name }}</p>
                                @endif
                                @if($tenant->activeBankAccount->cbu)
                                    <div class="flex items-center gap-2">
                                        <p class="text-blue-100">🏛️ CBU: {{ $tenant->activeBankAccount->cbu }}</p>
                                        <button type="button"
                                                x-data="{ copied: false }"
                                                @click="navigator.clipboard.writeText('{{ $tenant->activeBankAccount->cbu }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="text-blue-300 hover:text-white transition"
                                                :title="copied ? '¡Copiado!' : 'Copiar'">
                                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                @if($tenant->activeBankAccount->cvu)
                                    <div class="flex items-center gap-2">
                                        <p class="text-blue-100">🏛️ CVU: {{ $tenant->activeBankAccount->cvu }}</p>
                                        <button type="button"
                                                x-data="{ copied: false }"
                                                @click="navigator.clipboard.writeText('{{ $tenant->activeBankAccount->cvu }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="text-blue-300 hover:text-white transition"
                                                :title="copied ? '¡Copiado!' : 'Copiar'">
                                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                @if($tenant->activeBankAccount->notes)
                                    <p class="text-xs text-blue-300 mt-2">⚠️ {{ $tenant->activeBankAccount->notes }}</p>
                                @endif
                                
                            @else
                                <p class="text-red-200">⚠️ No hay cuenta configurada. Contacta a soporte.</p>
                            @endif
                                <p class="text-xs text-blue-300 mt-2">⚠️ El ALIAS puede cambiar con el tiempo</p>
                            </div>
                        </div>
                    </div>

                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Monto a cargar <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg font-bold">$</span>
                            <input 
                                type="number" 
                                wire:model.blur="amount"
                                placeholder="0.00"
                                step="0.01"
                                class="w-full pl-10 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 text-lg"
                            >
                        </div>
                        @error('amount') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Titular de cuenta -->
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Titular de la cuenta <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.blur="accountHolder"
                            placeholder="Nombre completo del titular"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"
                        >
                        @error('accountHolder') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div> --}}

                    <!-- Número de cuenta / Alias -->
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Número de cuenta o Alias <span class="text-red-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.blur="accountNumber"
                            placeholder="CBU, CVU o Alias"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"
                        >
                        @error('accountNumber') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div> --}}

                    <!-- Requisitos del Comprobante -->
                    <div class="bg-yellow-500/10 border-l-4 border-yellow-500 p-4 mb-4 rounded">
                        <div class="flex gap-2">
                            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-yellow-200 mb-2">⚠️ El comprobante debe incluir claramente:</p>
                                <ul class="text-sm text-yellow-100 space-y-1 ml-4">
                                    <li class="flex items-start gap-2">
                                        <span class="text-yellow-400">•</span>
                                        <span><strong>Fecha y hora</strong> de la transferencia</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-yellow-400">•</span>
                                        <span><strong>Monto exacto</strong> transferido</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-yellow-400">•</span>
                                        <span><strong>Nombre del destinatario</strong> (titular de la cuenta)</span>
                                    </li>
                                </ul>
                                <p class="text-xs text-yellow-200 mt-2 italic">Estos datos son necesarios para verificar y aprobar tu carga rápidamente.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Comprobante -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Comprobante <span class="text-red-400">*</span>
                        </label>
                        
                        <div class="mt-2">
                            @if ($receipt)
                                <!-- Preview -->
                                <div class="relative">
                                    <img src="{{ $receipt->temporaryUrl() }}" 
                                         class="w-full h-48 object-cover rounded-lg border-2 border-gray-600">
                                    <button 
                                        type="button"
                                        wire:click="$set('receipt', null)"
                                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Click en la X para cambiar la imagen</p>
                            @else
                                <!-- Upload Button -->
                                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-600 rounded-lg cursor-pointer hover:border-gray-500 transition bg-gray-700/50">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-400"><span class="font-semibold">Click para subir</span> o arrastra</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, WEBP (max. 5MB)</p>
                                    </div>
                                    <input type="file" wire:model="receipt" accept="image/*" class="hidden">
                                </label>
                            @endif

                            <div wire:loading wire:target="receipt" class="text-sm text-blue-400 mt-2 flex items-center gap-2" style="display: none;" x-show="false" x-cloak>
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Subiendo imagen...
                            </div>
                        </div>

                        @error('receipt') 
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3 pt-4">
                        <button 
                            type="button"
                            wire:click="close"
                            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            class="flex-1 px-4 py-3 font-semibold text-white rounded-lg transition disabled:opacity-50"
                            style="background-color: {{ $tenant->primary_color }}">
                            <span wire:loading.remove wire:target="submit">Enviar Solicitud</span>
                            <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2" style="display: none;">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enviando...
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>