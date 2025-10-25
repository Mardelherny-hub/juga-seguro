<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ open: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $currentTenant->name }} - Panel de Gestión</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div wire:poll.5s class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Navigation -->
            <nav class="bg-white dark:bg-gray-800 border-b-2" style="border-bottom-color: {{ $currentTenant->primary_color }}">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                @if($currentTenant->logo_url)
                                    <a href="{{ route('dashboard') }}" wire:navigate>
                                        <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" class="h-10">
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" wire:navigate class="text-xl font-bold" style="color: {{ $currentTenant->primary_color }}">
                                        {{ $currentTenant->name }}
                                    </a>
                                @endif
                            </div>

                            <!-- Navigation Links - Desktop -->
                            <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                                <!-- Dashboard -->
                                <a href="{{ route('dashboard') }}" wire:navigate 
                                   class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                   {{ request()->routeIs('dashboard') ? 'text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}"
                                   style="{{ request()->routeIs('dashboard') ? 'border-color: ' . $currentTenant->primary_color . '; color: ' . $currentTenant->primary_color : 'border-color: transparent' }}">
                                    Dashboard
                                </a>

                                <!-- Jugadores -->
                                <a href="{{ route('dashboard.players') }}" wire:navigate 
                                   class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                   {{ request()->routeIs('dashboard.players') ? 'text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}"
                                   style="{{ request()->routeIs('dashboard.players') ? 'border-color: ' . $currentTenant->primary_color . '; color: ' . $currentTenant->primary_color : 'border-color: transparent' }}">
                                    Jugadores
                                </a>

                                <!-- Transacciones con Dropdown -->
                                <div x-data="{ openTrans: false }" @click.away="openTrans = false" class="relative flex items-center">
                                    <button @click="openTrans = !openTrans"
                                            class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-full
                                            {{ request()->routeIs('dashboard.transactions.*') ? 'text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}"
                                            style="{{ request()->routeIs('dashboard.transactions.*') ? 'border-color: ' . $currentTenant->primary_color . '; color: ' . $currentTenant->primary_color : 'border-color: transparent' }}">
                                        <span>Transacciones</span>
                                        <livewire:agent.transactions.pending-badge />
                                        <svg class="ml-2 w-4 h-4 transition-transform" :class="openTrans ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="openTrans"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50"
                                         style="top: 100%;">
                                        <div class="py-2">
                                            <a href="{{ route('dashboard.transactions.pending') }}" wire:navigate
                                               class="flex items-center px-4 py-3 text-sm transition-colors {{ request()->routeIs('dashboard.transactions.pending') ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                               style="{{ request()->routeIs('dashboard.transactions.pending') ? 'background-color: ' . $currentTenant->primary_color . '20; color: ' . $currentTenant->primary_color : '' }}">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Pendientes</span>
                                            </a>
                                            <a href="{{ route('dashboard.transactions.history') }}" wire:navigate
                                               class="flex items-center px-4 py-3 text-sm transition-colors {{ request()->routeIs('dashboard.transactions.history') ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                               style="{{ request()->routeIs('dashboard.transactions.history') ? 'background-color: ' . $currentTenant->primary_color . '20; color: ' . $currentTenant->primary_color : '' }}">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <span>Historial</span>
                                            </a>
                                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                            <a href="{{ route('dashboard.transactions.monitor') }}" wire:navigate
                                               class="flex items-center px-4 py-3 text-sm transition-colors {{ request()->routeIs('dashboard.transactions.monitor') ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                               style="{{ request()->routeIs('dashboard.transactions.monitor') ? 'background-color: ' . $currentTenant->primary_color . '20; color: ' . $currentTenant->primary_color : '' }}">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <span>Modo Monitor</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Dropdown - Desktop -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="relative" x-data="{ openUser: false }">
                                <button @click="openUser = !openUser" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openUser" 
                                     @click.away="openUser = false"
                                     x-transition
                                     class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                                            Perfil
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                                                Cerrar Sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger - Mobile -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu - Mobile -->
                <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('dashboard') }}" wire:navigate 
                           class="block w-full ps-3 pe-4 py-2 border-l-4 text-base font-medium transition
                           {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                           style="{{ request()->routeIs('dashboard') ? 'border-color: ' . $currentTenant->primary_color : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('dashboard.players') }}" wire:navigate 
                           class="block w-full ps-3 pe-4 py-2 border-l-4 text-base font-medium transition
                           {{ request()->routeIs('dashboard.players') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                           style="{{ request()->routeIs('dashboard.players') ? 'border-color: ' . $currentTenant->primary_color : '' }}">
                            Jugadores
                        </a>
                        <a href="{{ route('dashboard.transactions.pending') }}" wire:navigate 
                           class="block w-full ps-3 pe-4 py-2 border-l-4 text-base font-medium transition
                           {{ request()->routeIs('dashboard.transactions.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                           style="{{ request()->routeIs('dashboard.transactions.*') ? 'border-color: ' . $currentTenant->primary_color : '' }}">
                            Transacciones
                        </a>
                    </div>

                    <!-- User Menu - Mobile -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4 mb-3">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="space-y-1">
                            <a href="{{ route('profile') }}" wire:navigate class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

                <!-- Toast Notifications -->
        <x-toast-notifications />

        @livewireScripts
        
    </body>
</html>