<div>
    {{-- resources/views/livewire/agent/tenant-settings.blade.php --}}
<div class="max-w-5xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div class="flex items-start justify-between">        
        {{-- Mensaje flash --}}
        @if (session()->has('success'))
            <div class="rounded-md bg-green-50 px-4 py-2 border border-green-200 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- CARD: Ajustes generales --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-lg p-6 space-y-6 mb-8">
        <h2 class="text-lg font-medium text-gray-900">Ajustes generales</h2>

        {{-- WhatsApp --}}
        <div>
            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">Número de WhatsApp</label>
            <input type="text" id="whatsapp_number" wire:model.defer="whatsapp_number"
                   placeholder="+5492231234567"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-gray-500">Formato internacional con “+”. Ej: +5492231234567</p>
            @error('whatsapp_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- URL Casino --}}
        <div>
            <label for="casino_url" class="block text-sm font-medium text-gray-700">URL del Casino</label>
            <input type="url" id="casino_url" wire:model.defer="casino_url"
                   placeholder="https://mi-casino.com"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-gray-500">Debe incluir http:// o https://</p>
            @error('casino_url') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                <svg wire:loading.delay wire:target="save" class="mr-2 h-4 w-4 animate-spin"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                Guardar cambios
            </button>
            <span class="text-sm text-gray-500" wire:loading.delay wire:target="save">Guardando…</span>
        </div>
    </div>

    {{-- CARD: Marca blanca (colores + logo) --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-lg p-6 space-y-6 mb-8">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Marca blanca</h2>
            <div class="flex items-center gap-2">
                {{-- Vista previa de colores --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Preview:</span>
                    <span class="inline-flex h-5 w-5 rounded" style="background: {{ $brand_primary ?? '#4f46e5' }}"></span>
                    <span class="inline-flex h-5 w-5 rounded" style="background: {{ $brand_secondary ?? '#06b6d4' }}"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Color primario --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Color primario</label>
                <div class="mt-1 flex items-center gap-3">
                    <input type="color" wire:model.live="brand_primary"
                           class="h-9 w-12 cursor-pointer rounded border border-gray-300">
                    <input type="text" wire:model.defer="brand_primary"
                           placeholder="#4f46e5"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                @error('brand_primary') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Color secundario --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Color secundario</label>
                <div class="mt-1 flex items-center gap-3">
                    <input type="color" wire:model.live="brand_secondary"
                           class="h-9 w-12 cursor-pointer rounded border border-gray-300">
                    <input type="text" wire:model.defer="brand_secondary"
                           placeholder="#06b6d4"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                @error('brand_secondary') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Logo uploader --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Logo (PNG/SVG, fondo transparente recomendado)</label>
                <div class="mt-1 flex items-center gap-4">
                    <input type="file" accept="image/png,image/svg+xml" wire:model="logo"
                           class="block w-full text-sm text-gray-900 file:mr-3 file:py-2 file:px-4
                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                  file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"/>
                </div>
                <p class="mt-1 text-xs text-gray-500">Máx. 1MB. Relación sugerida horizontal.</p>
                @error('logo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                {{-- Progreso de carga --}}
                <div wire:loading wire:target="logo" class="mt-3 text-sm text-gray-500">Subiendo logo…</div>
            </div>

            {{-- Preview --}}
            <div>
                <span class="block text-sm font-medium text-gray-700">Vista previa</span>
                <div class="mt-2 flex items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 h-24">
                    @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" alt="Nuevo logo" class="max-h-16 object-contain">
                    @elseif (!empty($current_logo_url))
                        <img src="{{ $current_logo_url }}" alt="Logo actual" class="max-h-16 object-contain">
                    @else
                        <span class="text-xs text-gray-400">Sin logo</span>
                    @endif
                </div>
                <div class="mt-3 flex gap-2">
                    @if (!empty($current_logo_url))
                        <button type="button" wire:click="removeLogo" wire:loading.attr="disabled"
                                class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200 hover:bg-gray-50">
                            Quitar logo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="saveBrand" wire:loading.attr="disabled"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60">
                <svg wire:loading.delay wire:target="saveBrand" class="mr-2 h-4 w-4 animate-spin"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                Guardar marca blanca
            </button>
            <span class="text-sm text-gray-500" wire:loading.delay wire:target="saveBrand">Guardando…</span>
        </div>
    </div>

    {{-- CARD: Gestión de usuarios del tenant (CRUD inline) --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-lg p-6 space-y-6 mb-4">

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Usuarios</h2>
            <div class="flex items-center gap-2">
                <input type="text" wire:model.debounce.400ms="search" placeholder="Buscar por nombre o email…"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" wire:click="createUser"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Nuevo
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Tabla --}}
            <div class="lg:col-span-2">
                <div class="overflow-x-auto -mx-4 sm:-mx-6 lg:-mx-6">
                    <div class="inline-block min-w-full py-2 align-middle">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($users as $u)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $u->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $u->email }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $roles[$u->role] ?? ucfirst($u->role) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <div class="inline-flex gap-2">
                                            <button type="button" wire:click="editUser({{ $u->id }})"
                                                    class="rounded-md px-2 py-1 text-xs font-medium text-indigo-700 bg-indigo-50 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-100">
                                                Editar
                                            </button>
                                            <button type="button" wire:click="deleteUser({{ $u->id }})"
                                                    class="rounded-md px-2 py-1 text-xs font-medium text-red-700 bg-red-50 ring-1 ring-inset ring-red-200 hover:bg-red-100">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Sin usuarios</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Formulario Crear/Editar --}}
            <div class="bg-white ring-1 ring-gray-200 rounded-lg p-5 space-y-4">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $editingId ? 'Editar usuario' : 'Nuevo usuario' }}
                </h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" wire:model.defer="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model.defer="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <select wire:model.defer="role"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Interruptor opcional: crear por invitación --}}
                <div class="flex items-center gap-2">
                    <input id="useInviteFlow" type="checkbox" wire:model="useInviteFlow"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="useInviteFlow" class="text-sm text-gray-700">
                        Crear por invitación (enviar email para que defina su contraseña)
                    </label>
                </div>

                {{-- Password: requerido si no se usa invitación; opcional en edición --}}
                @if(! $useInviteFlow)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Contraseña {{ $editingId ? '(dejar vacío para no cambiar)' : '' }}
                        </label>
                        <input type="password" wire:model.defer="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                        <input type="password" wire:model.defer="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2">
                    @if($editingId)
                        <button type="button" wire:click="updateUser" wire:loading.attr="disabled"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Actualizar
                        </button>
                    @else
                        <button type="button" wire:click="storeUser" wire:loading.attr="disabled"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Crear
                        </button>
                    @endif
                </div>
            </div>
        </div>

    </div>


    {{-- NOTAS --}}
    <div class="text-xs text-gray-500">
        <p class="font-semibold">Notas:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>El link de WhatsApp se arma como <span class="font-mono">https://wa.me/&lt;número_sin_signos&gt;</span>.</li>
            <li>Para los colores, validá hex: <span class="font-mono">/^#(?:[0-9a-fA-F]{3}){1,2}$/</span>.</li>
            <li>Para el logo, usá `withValidation` de Livewire: máx. 1024 KB, tipos: png/svg.</li>
            <li>En usuarios, el campo <em>Estado</em> muestra “Invitado” si no verificó email.</li>
        </ul>
    </div>

</div>

</div>
