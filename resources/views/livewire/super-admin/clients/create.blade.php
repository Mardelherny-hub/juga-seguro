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
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nuevo Cliente</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Crea un nuevo cliente en la plataforma</p>
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
                                    wire:model.blur="name" 
                                    id="name"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Ej: Cliente Demo"
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
                                    El cliente accederá en: <strong>{{ $domain ?: 'subdominio' }}.{{ config('app.domain') }}</strong>
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
                                <div class="mt-2 text-sm bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3">
                                    <p class="font-medium text-blue-800 dark:text-blue-300">ℹ️ Dominio Personalizado:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-blue-700 dark:text-blue-400">
                                        <li>Permite que el cliente use su propio dominio</li>
                                        <li>El cliente debe configurar sus registros DNS</li>
                                        <li>Se proporcionarán instrucciones después de guardar</li>
                                        <li>El subdominio seguirá funcionando como respaldo</li>
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
                                    placeholder="gestion_redes"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nombre de la base de datos PostgreSQL</p>
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
                                        placeholder="#3B82F6"
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
                                        placeholder="#10B981"
                                    >
                                </div>
                                @error('secondary_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo -->
                            <div class="md:col-span-2">
                                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Logo
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
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Vista previa:</p>
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="h-20 rounded">
                                    </div>
                                @endif
                            </div>

                            <!-- Preview de Colores -->
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vista previa:</p>
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
                                        Cliente compra fichas. Cada depósito aprobado = -1 ficha. Ideal para empezar.
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
                                        Pago fijo mensual. Transacciones ilimitadas. Ideal para alto volumen.
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
                                Precio que pagará el cliente por cada ficha
                            </p>
                        </div>

                        <!-- Fichas Iniciales -->
                        <div class="col-span-3">
                            <label for="chips_balance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fichas Iniciales
                            </label>
                            <input 
                                type="number" 
                                id="chips_balance"
                                wire:model="chips_balance"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="0"
                                min="0"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Cantidad de fichas con las que iniciará el cliente
                            </p>
                        </div>
                    @endif

                    <!-- SECCIÓN: DATOS DEL ADMINISTRADOR DEL CLIENTE -->
                    <div class="border-t pt-6 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            👤 Administrador del Cliente
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Estos serán los datos de acceso del administrador del cliente.
                        </p>

                        <!-- Nombre del Admin -->
                        <div class="mb-4">
                            <label for="admin_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="admin_name" 
                                id="admin_name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Juan Pérez"
                            >
                            @error('admin_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email del Admin -->
                        <div class="mb-4">
                            <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                wire:model="admin_email" 
                                id="admin_email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="admin@gmail.com"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Puede ser Gmail, Hotmail o cualquier email personal del cliente.
                            </p>
                            @error('admin_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Contraseña del Admin -->
                        <div class="mb-4">
                            <label for="admin_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="admin_password" 
                                id="admin_password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono"
                                placeholder="Mínimo 8 caracteres"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Genera una contraseña segura. El cliente podrá cambiarla después del primer acceso.
                            </p>
                            <button 
                                type="button"
                                onclick="document.getElementById('admin_password').value = generatePassword(); @this.set('admin_password', document.getElementById('admin_password').value)"
                                class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                🔄 Generar contraseña automática
                            </button>
                            @error('admin_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <script>
                    function generatePassword() {
                        const length = 12;
                        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
                        let password = "";
                        for (let i = 0; i < length; i++) {
                            password += charset.charAt(Math.floor(Math.random() * charset.length));
                        }
                        return password;
                    }
                    </script>

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
                            Crear Cliente
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
                ✅ Cliente Creado Exitosamente
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