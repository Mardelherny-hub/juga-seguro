@php
        $currentTenant = $this->tenant;
@endphp

<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <!-- Título dinámico según tenant -->
    <div class="mb-6 text-center">
        @if(isset($currentTenant))
            <!-- Login con marca blanca -->
            @if($currentTenant->logo_url)
                <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" class="h-16 mx-auto mb-4">
            @endif
            <h2 class="text-2xl font-bold text-gray-900">{{ $currentTenant->name }}</h2>
        @else
            <!-- Login Super Admin -->
            <h2 class="text-2xl font-bold text-gray-900">Next Level - Super Admin</h2>
            <p class="text-sm text-gray-600 mt-1">Panel de Administración</p>
        @endif
    </div>

    <form wire:submit.prevent="login">
        <!-- Email -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            <input 
                wire:model="email" 
                id="email" 
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username"
            >
            @error('email') 
                <span class="text-red-600 text-sm mt-2">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Contraseña</label>
            <input 
                wire:model="password" 
                id="password" 
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                type="password"
                name="password"
                required 
                autocomplete="current-password"
            >
            @error('password') 
                <span class="text-red-600 text-sm mt-2">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input 
                    wire:model="remember" 
                    id="remember" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                @if(isset($currentTenant)) style="background-color: {{ $currentTenant->primary_color }}; border-color: {{ $currentTenant->primary_color }};" @endif
            >
                Ingresar
            </button>
        </div>
    </form>
</div>