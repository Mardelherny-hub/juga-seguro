<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Casino') }} - {{ auth()->guard('player')->user()->name }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-900">
        <div class="min-h-screen">
            
            <!-- Navigation Bar -->
            <nav class="bg-gray-800 border-b border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        
                        <!-- Logo & Tenant Name -->
                        <div class="flex items-center">
                            @php
                                $tenant = auth()->guard('player')->user()->tenant;
                            @endphp
                            
                            @if($tenant->logo)
                                <img src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->name }}" class="h-10 w-auto">
                            @else
                                <span class="text-2xl font-bold text-white">{{ $tenant->name }}</span>
                            @endif
                        </div>

                        <!-- Saldo & User Menu -->
                        <div class="flex items-center gap-6">
                            
                            <!-- Saldo Destacado -->
                            <svg class="w-6 h-6" style="color: {{ $tenant->primary_color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @livewire('player.balance-display')

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-700 transition">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                         style="background-color: {{ $tenant->primary_color }}">
                                        {{ substr(auth()->guard('player')->user()->name, 0, 1) }}
                                    </div>
                                    <span class="hidden sm:block text-white font-medium">{{ auth()->guard('player')->user()->name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     @click.outside="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-gray-800 border border-gray-700 z-50"
                                     style="display: none;">
                                    
                                    <div class="py-2">
                                        <!-- Saldo (móvil) -->
                                        <div class="sm:hidden px-4 py-3 border-b border-gray-700">
                                            <p class="text-xs text-gray-400">Saldo actual</p>
                                            <p class="text-lg font-bold text-white">${{ number_format(auth()->guard('player')->user()->balance, 2) }}</p>
                                        </div>

                                        <!-- Mi Perfil -->
                                        <a href="{{ route('player.profile') }}" wire:navigate
                                           class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>Mi Perfil</span>
                                        </a>

                                        <!-- Mis Transacciones -->
                                        <a href="{{ route('player.transactions') }}" wire:navigate
                                           class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span>Mis Transacciones</span>
                                        </a>

                                        <!-- Cerrar Sesión -->
                                        <form method="POST" action="{{ route('player.logout') }}" class="border-t border-gray-700 mt-2 pt-2">
                                            @csrf
                                            <button type="submit" 
                                                    class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-gray-700 transition w-full text-left">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                <span>Cerrar Sesión</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-6">
                {{ $slot }}
            </main>

        </div>

        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Monitor de transacciones del player -->
        @livewire('player.transaction-status-monitor')

        <!-- Toast Notifications -->
        <x-toast-notifications />

        {{-- Panel de Actividad en Tiempo Real --}}
        @livewire('player.activity-panel')

    </body>
</html>