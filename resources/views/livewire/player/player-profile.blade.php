<div>
    <div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mi Perfil</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gestiona tu información personal</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información del Perfil -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Datos Personales -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Datos Personales</h2>
                    
                    @if(session()->has('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                            <input 
                                wire:model="name" 
                                type="text"
                                class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input 
                                wire:model="email" 
                                type="email"
                                class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                            <input 
                                wire:model="phone" 
                                type="text"
                                class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Cambiar Contraseña</h2>
                    
                    @if(session()->has('password_success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                        {{ session('password_success') }}
                    </div>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="space-y-4">
                        <!-- Contraseña Actual -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña Actual</label>
                            <input 
                                wire:model="current_password" 
                                type="password"
                                class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nueva Contraseña -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva Contraseña</label>
                            <input 
                                wire:model="new_password" 
                                type="password"
                                class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('new_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Nueva Contraseña</label>
                            <input 
                                wire:model="new_password_confirmation" 
                                type="password"
                                class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                            Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información de Cuenta -->
        <div class="space-y-6">
            
            <!-- Estado de Cuenta -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Estado de Cuenta</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ID de Jugador</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $player->id }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Saldo Actual</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            ${{ number_format($player->balance, 2) }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Estado</p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full
                            {{ $player->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $player->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $player->status === 'blocked' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($player->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Miembro desde</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $player->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Código de Referido -->
            @if($player->referral_code)
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg shadow p-6 text-white">
                <h3 class="font-semibold mb-2">Tu Código de Referido</h3>
                <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-3">
                    <p class="text-2xl font-bold text-center tracking-wider">{{ $player->referral_code }}</p>
                </div>
                <a href="{{ route('player.referrals') }}" 
                   class="block text-center text-sm hover:underline">
                    Ver mis referidos →
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
