<div>
    <div class="flex flex-wrap gap-3">
    <!-- Botón Crear Usuario -->
    <button 
        wire:click="$set('showCreateUserModal', true)"
        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Crear Usuario
    </button>

    <!-- Botón Desbloquear Usuario -->
    <button 
        wire:click="$set('showUnlockModal', true)"
        class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
        </svg>
        Desbloquear Usuario
    </button>

    <!-- Botón Cambiar Contraseña -->
    <button 
        wire:click="$set('showPasswordResetModal', true)"
        class="px-4 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        Cambiar Contraseña
    </button>
</div>

<!-- Modal: Crear Usuario -->
@if($showCreateUserModal)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showCreateUserModal', false)"></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                <div class="flex items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div class="mt-3 ml-4 text-left flex-1">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white" id="modal-title">
                            Crear Usuario en Plataforma
                        </h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Se generará una solicitud para crear el usuario <strong>{{ $player->name }}</strong> en la plataforma de juego.
                                Una vez aprobada, deberás ingresar el nickname y contraseña en el panel externo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-3 justify-end">
                <button 
                    wire:click="$set('showCreateUserModal', false)"
                    class="px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    wire:click="requestAccountCreation"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Generar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal: Desbloquear Usuario -->
@if($showUnlockModal)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showUnlockModal', false)"></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                <div class="flex items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="mt-3 ml-4 text-left flex-1">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white" id="modal-title">
                            Desbloquear Usuario
                        </h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Se generará una solicitud para desbloquear al usuario <strong>{{ $player->name }}</strong> en la plataforma de juego.
                                Una vez aprobada, deberás desbloquearlo en el panel externo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-3 justify-end">
                <button 
                    wire:click="$set('showUnlockModal', false)"
                    class="px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    wire:click="requestAccountUnlock"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                >
                    Generar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal: Cambiar Contraseña -->
@if($showPasswordResetModal)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showPasswordResetModal', false)"></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4">
                <div class="flex items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div class="mt-3 ml-4 text-left flex-1">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white" id="modal-title">
                            Cambiar Contraseña
                        </h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Se generará una solicitud para cambiar la contraseña de <strong>{{ $player->name }}</strong>.
                                Una vez aprobada, deberás cambiar la contraseña a <strong class="text-red-600 dark:text-red-400">bet123</strong> en el panel externo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-3 justify-end">
                <button 
                    wire:click="$set('showPasswordResetModal', false)"
                    class="px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    wire:click="requestPasswordReset"
                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors"
                >
                    Generar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>
@endif
</div>
