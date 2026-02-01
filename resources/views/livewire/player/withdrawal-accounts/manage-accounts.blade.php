<div>
    {{-- Header --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mis Cuentas de Retiro</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Gestiona tus cuentas bancarias para retiros</p>
        </div>
        <button 
            wire:click="openModal"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Cuenta
        </button>
    </div>

    {{-- Lista de Cuentas --}}
    <div class="space-y-4">
        @forelse($accounts as $account)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            {{-- Ícono según tipo --}}
<div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
    </svg>
</div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ strtoupper($account->account_type) }}
                                    </h3>
                                    @if($account->is_default)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                            Predeterminada
                                        </span>
                                    @endif
                                    @if($account->is_verified)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                            ✓ Verificada
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $account->holder_name }}</p>
                            </div>
                        </div>

                        <div class="ml-15 space-y-1">
                            @if($account->account_type === 'alias')
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Alias:</span> {{ $account->alias }}
                                </p>
                            @else
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Cuenta:</span> {{ $account->formatted_account }}
                                </p>
                            @endif
                            
                            @if($account->bank_name)
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Banco:</span> {{ $account->bank_name }}
                                </p>
                            @endif
                            
                            @if($account->holder_dni)
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">DNI:</span> {{ $account->holder_dni }}
                                </p>
                            @endif

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Agregada {{ $account->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex flex-col gap-2">
                        @if(!$account->is_default)
                            <button 
                                wire:click="setAsDefault({{ $account->id }})"
                                class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-600 rounded hover:bg-blue-50 transition"
                            >
                                Predeterminar
                            </button>
                        @endif
                        
                        <button 
                            wire:click="openModal({{ $account->id }})"
                            class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded hover:bg-gray-50 transition"
                        >
                            Editar
                        </button>
                        
                        <button 
                            wire:click="delete({{ $account->id }})"
                            wire:confirm="¿Estás seguro de eliminar esta cuenta?"
                            class="px-3 py-1 text-sm text-red-600 hover:text-red-800 border border-red-600 rounded hover:bg-red-50 transition"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No tienes cuentas guardadas
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Agrega una cuenta para realizar retiros más rápido
                </p>
                <button 
                    wire:click="openModal"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Agregar Primera Cuenta
                </button>
            </div>
        @endforelse
    </div>

    {{-- Modal de Crear/Editar --}}
    @if($showModal)
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
                                {{ $editingId ? 'Editar Cuenta' : 'Nueva Cuenta de Retiro' }}
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <form wire:submit.prevent="save">
                        <div class="px-6 py-6 space-y-4">
                            
                            {{-- Tipo de Cuenta --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Cuenta <span class="text-red-600">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="relative flex items-center justify-center px-4 py-3 border rounded-lg cursor-pointer transition
                                        {{ $account_type === 'cbu' ? 'border-blue-600 bg-blue-50' : 'border-gray-300 hover:border-gray-400' }}">
                                        <input type="radio" wire:model.live="account_type" value="cbu" class="sr-only">
                                        <span class="text-sm font-medium {{ $account_type === 'cbu' ? 'text-blue-600' : 'text-gray-700' }}">CBU</span>
                                    </label>
                                    <label class="relative flex items-center justify-center px-4 py-3 border rounded-lg cursor-pointer transition
                                        {{ $account_type === 'cvu' ? 'border-blue-600 bg-blue-50' : 'border-gray-300 hover:border-gray-400' }}">
                                        <input type="radio" wire:model.live="account_type" value="cvu" class="sr-only">
                                        <span class="text-sm font-medium {{ $account_type === 'cvu' ? 'text-blue-600' : 'text-gray-700' }}">CVU</span>
                                    </label>
                                    <label class="relative flex items-center justify-center px-4 py-3 border rounded-lg cursor-pointer transition
                                        {{ $account_type === 'alias' ? 'border-blue-600 bg-blue-50' : 'border-gray-300 hover:border-gray-400' }}">
                                        <input type="radio" wire:model.live="account_type" value="alias" class="sr-only">
                                        <span class="text-sm font-medium {{ $account_type === 'alias' ? 'text-blue-600' : 'text-gray-700' }}">Alias</span>
                                    </label>
                                </div>
                                @error('account_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Número de Cuenta o Alias --}}
                            @if($account_type === 'alias')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Alias <span class="text-red-600">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="alias"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alias') border-red-500 @enderror"
                                        placeholder="Ej: juan.perez.mp"
                                    >
                                    @error('alias')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ strtoupper($account_type) }} <span class="text-red-600">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="account_number"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('account_number') border-red-500 @enderror"
                                        placeholder="22 dígitos"
                                        maxlength="22"
                                    >
                                    @error('account_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Titular --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre del Titular <span class="text-red-600">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="holder_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('holder_name') border-red-500 @enderror"
                                    placeholder="Nombre completo"
                                >
                                @error('holder_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- DNI --}}
                            {{-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    DNI del Titular
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="holder_dni"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('holder_dni') border-red-500 @enderror"
                                    placeholder="Sin puntos ni espacios"
                                    maxlength="8"
                                >
                                @error('holder_dni')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div> --}}

                            {{-- Banco --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Banco
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="bank_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ej: Banco Galicia, Mercado Pago"
                                >
                            </div>

                            {{-- Predeterminada --}}
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="is_default"
                                    id="is_default"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <label for="is_default" class="ml-2 text-sm text-gray-700">
                                    Establecer como cuenta predeterminada
                                </label>
                            </div>

                            {{-- Nota Informativa --}}
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm text-yellow-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Asegúrate de que los datos sean correctos. Las cuentas serán verificadas antes del primer retiro.
                                </p>
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
                                {{ $editingId ? 'Actualizar' : 'Guardar' }} Cuenta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>