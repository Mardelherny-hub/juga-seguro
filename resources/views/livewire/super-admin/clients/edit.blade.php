<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <a href="{{ route('super-admin.clients.index') }}" wire:navigate class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Editar Cliente</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $tenant->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Cliente -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm opacity-80">Jugadores</p>
                        <p class="text-2xl font-bold">{{ number_format($tenant->players()->count()) }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Saldo Total</p>
                        <p class="text-2xl font-bold">${{ number_format($tenant->players()->sum('balance'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-80">Transacciones</p>
                        <p class="text-2xl font-bold">{{ number_format($tenant->transactions()->count()) }}</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <form wire:submit="save" class="p-6 space-y-6">
                    
                    <!-- Información Básica -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Básica</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre del Cliente <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name" 
                                    id="name"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Subdominio -->
                            <div>
                                <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Subdominio <span class="text-red-500">*</span> 
                                    <span class="text-gray-400 text-xs">(obligatorio)</span>
                                </label>
                                <div class="flex rounded-md shadow-sm">
                                    <input 
                                        type="text" 
                                        wire:model="domain" 
                                        id="domain"
                                        class="flex-1 min-w-0 block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="cliente1"
                                    >
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 text-sm">
                                        .{{ config('app.domain') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    El cliente accede en: <strong>{{ $domain ?: 'subdominio' }}.{{ config('app.domain') }}</strong>
                                </p>
                                @error('domain') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dominio Personalizado (NUEVO) -->
                            <div class="border-t pt-6 dark:border-gray-700">
                                <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Dominio Personalizado 
                                    <span class="text-gray-400 text-xs">(opcional)</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="custom_domain" 
                                    id="custom_domain"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="www.ejemplo.com"
                                >
                                @if($tenant->custom_domain)
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                        ✓ Actualmente usando: <strong>{{ $tenant->custom_domain }}</strong>
                                    </p>
                                @endif
                                <div class="mt-2 text-sm bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3">
                                    <p class="font-medium text-blue-800 dark:text-blue-300">ℹ️ Dominio Personalizado:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-blue-700 dark:text-blue-400">
                                        <li>Si cambias el dominio, se generarán nuevas instrucciones DNS</li>
                                        <li>El subdominio siempre estará disponible como respaldo</li>
                                    </ul>
                                </div>
                                @error('custom_domain') 
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            <!-- Base de Datos -->
                            <div>
                                <label for="database" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Base de Datos <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="database" 
                                    id="database"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                @error('database') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Marca Blanca -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Marca Blanca</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Color Primario -->
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Color Primario
                                </label>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="color" 
                                        wire:model.live="primary_color" 
                                        id="primary_color"
                                        class="h-10 w-20 rounded border-gray-300 cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model="primary_color" 
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                                @error('primary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Color Secundario -->
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Color Secundario
                                </label>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="color" 
                                        wire:model.live="secondary_color" 
                                        id="secondary_color"
                                        class="h-10 w-20 rounded border-gray-300 cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model="secondary_color" 
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                </div>
                                @error('secondary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo Actual -->
                            @if($current_logo_url)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo Actual</label>
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $current_logo_url }}" alt="Logo actual" class="h-16 rounded border border-gray-300">
                                        <button 
                                            type="button" 
                                            wire:click="removeLogo"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium"
                                        >
                                            Eliminar logo
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Nuevo Logo -->
                            <div class="md:col-span-2">
                                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $current_logo_url ? 'Cambiar Logo' : 'Subir Logo' }}
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="logo" 
                                    id="logo"
                                    accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                >
                                @error('logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                
                                @if ($logo)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Vista previa del nuevo logo:</p>
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="h-20 rounded">
                                    </div>
                                @endif
                            </div>

                            <!-- Preview de Colores -->
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vista previa de colores:</p>
                                <div class="flex items-center gap-4">
                                    <div class="px-6 py-3 rounded-md text-white font-medium" style="background-color: {{ $primary_color }}">
                                        Color Primario
                                    </div>
                                    <div class="px-6 py-3 rounded-md text-white font-medium" style="background-color: {{ $secondary_color }}">
                                        Color Secundario
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Tipo de Suscripción -->
                    <div class="col-span-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            💳 Modelo de Suscripción
                        </h3>
                    </div>

                    <!-- URL del Casino -->
                    <div class="border-t pt-6 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🎰 Configuración del Casino</h3>
                        
                        <div>
                            <label for="casino_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                URL del Casino
                            </label>
                            <input 
                                type="url" 
                                wire:model="casino_url" 
                                id="casino_url"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="https://casino.ejemplo.com"
                            >
                            @error('casino_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                URL de la plataforma de juego externa. Los jugadores verán el botón "Ir al Casino" con este enlace.
                            </p>
                        </div>
                    </div>

                    <!-- Tipo de Suscripción -->
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Tipo de Suscripción *
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Opción Prepago -->
                            <label class="relative flex cursor-pointer rounded-lg border {{ $subscription_type === 'prepaid' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} p-4 hover:border-blue-500 transition">
                                <input 
                                    type="radio" 
                                    wire:model.live="subscription_type" 
                                    value="prepaid"
                                    class="sr-only"
                                >
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">💎 Prepago (Fichas)</span>
                                        @if($subscription_type === 'prepaid')
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Cliente compra fichas. Cada depósito aprobado = -1 ficha.
                                    </p>
                                </div>
                            </label>

                            <!-- Opción Mensual -->
                            <label class="relative flex cursor-pointer rounded-lg border {{ $subscription_type === 'monthly' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} p-4 hover:border-blue-500 transition">
                                <input 
                                    type="radio" 
                                    wire:model.live="subscription_type" 
                                    value="monthly"
                                    class="sr-only"
                                >
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">📅 Mensual (Abono)</span>
                                        @if($subscription_type === 'monthly')
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Pago fijo mensual. Transacciones ilimitadas.
                                    </p>
                                </div>
                            </label>
                        </div>
                        @error('subscription_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campos condicionales según tipo -->
                    @if($subscription_type === 'monthly')
                        <!-- Cuota Mensual -->
                        <div class="col-span-3">
                            <label for="monthly_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cuota Mensual (USD) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                                <input 
                                    type="number" 
                                    id="monthly_fee"
                                    wire:model="monthly_fee"
                                    step="0.01"
                                    class="pl-8 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0.00"
                                >
                            </div>
                            @error('monthly_fee')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <!-- Saldo Actual de Fichas (Solo lectura) -->
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Saldo Actual de Fichas
                            </label>
                            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-md">
                                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($chips_balance) }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">fichas</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                💡 Para cargar fichas, usa el botón 💎 en la lista de clientes
                            </p>
                        </div>

                        <!-- Precio por Ficha -->
                        <div class="col-span-3">
                            <label for="chip_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Precio por Ficha (USD) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                                <input 
                                    type="number" 
                                    id="chip_price"
                                    wire:model="chip_price"
                                    step="0.01"
                                    class="pl-8 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="100.00"
                                >
                            </div>
                            @error('chip_price')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Precio que paga el cliente por cada ficha
                            </p>
                        </div>
                    @endif

                    <!-- SECCIÓN: ADMINISTRADOR DEL CLIENTE -->
                    <div class="border-t pt-6 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            👤 Administrador del Cliente
                        </h3>
                        
                        @php
                            $admin = $tenant->users()->where('role', 'admin')->first();
                        @endphp
                        
                        @if($admin)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Nombre:</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $admin->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Email:</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $admin->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Último acceso:</p>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Nunca' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Estado:</p>
                                        <p class="font-medium">
                                            @if($admin->is_active)
                                                <span class="text-green-600 dark:text-green-400">✓ Activo</span>
                                            @else
                                                <span class="text-red-600 dark:text-red-400">✗ Inactivo</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🔐 Cambiar Contraseña</p>
                                    <div class="flex gap-2">
                                        <input 
                                            type="password" 
                                            wire:model="admin_password" 
                                            placeholder="Nueva contraseña (mín. 8 caracteres)"
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                                        >
                                        <button 
                                            type="button"
                                            wire:click="changeAdminPassword"
                                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm font-medium"
                                        >
                                            Cambiar
                                        </button>
                                    </div>
                                    @error('admin_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded p-4">
                                <p class="text-yellow-800 dark:text-yellow-300">
                                    ⚠️ Este cliente no tiene un administrador asignado.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cliente activo</span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Los clientes inactivos no pueden acceder a la plataforma</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('super-admin.clients.index') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL DE INSTRUCCIONES DNS -->
        @if($showDnsInstructions)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
                <h3 class="text-lg font-bold mb-4 text-green-600 dark:text-green-400">
                    ✅ Dominio Actualizado
                </h3>
                <p class="mb-4 text-gray-700 dark:text-gray-300">El cliente debe configurar estos registros DNS:</p>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded text-sm overflow-x-auto whitespace-pre-wrap text-gray-800 dark:text-gray-200">{{ $dnsInstructions }}</pre>
                <div class="mt-6 flex justify-end">
                    <button wire:click="$set('showDnsInstructions', false)" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
        @endif
</div>