<div class="col-span-2 grid grid-cols-2 gap-3">
    <!-- Desbloquear Usuario -->
    <button 
        wire:click="openUnlockModal"
        class="flex flex-col items-center justify-center gap-2 p-5 bg-gray-700/50 rounded-xl transition-all hover:bg-gray-700 text-white"
    >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
        </svg>
        <span class="text-xs font-medium text-center">Desbloquear Usuario</span>
    </button>

    <!-- Cambiar Contraseña -->
    <button 
        wire:click="openPasswordResetModal"
        class="flex flex-col items-center justify-center gap-2 p-5 bg-gray-700/50 rounded-xl transition-all hover:bg-gray-700 text-white"
    >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        <span class="text-xs font-medium text-center">Cambiar Contraseña</span>
    </button>

    {{-- Modal Crear Usuario --}}
    @if($showCreateUserModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        🎮 Crear Usuario en Plataforma
                    </h3>
                    
                    {{-- Opciones --}}
                    <div class="mb-4 space-y-3">
                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                            :class="$wire.accountCreationType === 'new' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="radio" 
                                wire:model.live="accountCreationType" 
                                value="new" 
                                class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    ✨ Crear usuario nuevo
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    El administrador creará un usuario para ti
                                </div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                            :class="$wire.accountCreationType === 'existing' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20' : 'border-gray-300 dark:border-gray-600'">
                            <input type="radio" 
                                wire:model.live="accountCreationType" 
                                value="existing" 
                                class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    👤 Ya tengo usuario
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Ingresa tu usuario existente para verificación
                                </div>
                            </div>
                        </label>
                    </div>
                    
                    {{-- Campo para usuario existente --}}
                    @if($accountCreationType === 'existing')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tu nombre de usuario
                            </label>
                            <input type="text" 
                                wire:model="existingUsername"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Ej: jugador123"
                                required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Ingresa el usuario que ya tienes en la plataforma
                            </p>
                        </div>
                    @else
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                📝 El administrador revisará tu solicitud y te enviará tus credenciales.
                            </p>
                        </div>
                    @endif
                    
                    <div class="flex space-x-3">
                        <button wire:click="$set('showCreateUserModal', false)"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </button>
                        <button wire:click="requestAccountCreation"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Enviar Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showUnlockModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showUnlockModal', false)"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                    <div class="flex items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="mt-3 ml-4 text-left flex-1">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white">Desbloquear Usuario</h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Se generará una solicitud para desbloquear al usuario <strong>{{ $player->display_name }}</strong> en la plataforma de juego.
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    En breve te avisaremos cuando el usuario haya sido desbloqueado.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-3 justify-end">
                    <button wire:click="$set('showUnlockModal', false)" class="px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">Cancelar</button>
                    <button wire:click="requestAccountUnlock" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">Generar Solicitud</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showPasswordResetModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showPasswordResetModal', false)"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                    <div class="flex items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div class="mt-3 ml-4 text-left flex-1">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white">Cambiar Contraseña</h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Se generará una solicitud para cambiar la contraseña para <strong>{{ $player->display_name }}</strong>.
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    En breve te avisaremos con la nueva contraseña generada.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-3 justify-end">
                    <button wire:click="$set('showPasswordResetModal', false)" class="px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">Cancelar</button>
                    <button wire:click="requestPasswordReset" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">Generar Solicitud</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>