<div>
    <div class="mb-8 text-center">
        @if(isset($currentTenant) && $currentTenant?->logo_url)
            <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name ?? 'Logo' }}" class="h-16 mx-auto mb-4">
        @endif
        <h2 class="text-3xl font-bold" style="color: {{ $currentTenant->primary_color ?? '#3B82F6' }}">
            {{ $currentTenant->name ?? config('app.name') }}
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Ingresa a tu panel de gestión
        </p>
    </div>

    <form wire:submit="login">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input 
                wire:model="email" 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                wire:model="password" 
                id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input 
                    wire:model="remember" 
                    id="remember" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a 
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" 
                    href="{{ route('password.request') }}"
                    wire:navigate
                >
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button 
                class="ms-3" 
                style="background-color: {{ $currentTenant->primary_color ?? '#3B82F6' }}; border-color: {{ $currentTenant->primary_color ?? '#3B82F6' }}"
            >
                {{ __('Ingresar') }}
            </x-primary-button>
        </div>
    </form>
</div>