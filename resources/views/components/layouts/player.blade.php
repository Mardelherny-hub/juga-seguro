<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="manifest" href="/manifest.json">

        <title>{{ config('app.name', 'Casino') }} - {{ auth()->guard('player')->user()->username }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-900" x-data="{ mobileMenuOpen: false }">
        <div class="min-h-screen">
            
            <!-- Navigation Bar -->
            <nav class="bg-gray-800 border-b border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        
                        <div class="flex items-center gap-4">
                            <!-- Botón hamburguesa (móvil) -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-300 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            <!-- Logo & Tenant Name -->
                            <div class="flex items-center">
                                @php
                                    $tenant = auth()->guard('player')->user()->tenant;
                                @endphp
                                
                                @if($tenant->logo)
                                <img 
                                    src="{{ $currentTenant->logo_url }}" 
                                    alt="{{ $tenant->name }}" 
                                    class="h-10 w-auto object-contain"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="text-xl ml-2 font-bold text-white">{{ $tenant->name }}</span>

                                    <div class="hidden items-center gap-2">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xl" 
                                        style="background: linear-gradient(135deg, {{ $tenant->primary_color ?? '#6366f1' }} 0%, {{ $tenant->secondary_color ?? '#8b5cf6' }} 100%);">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xl font-bold text-white">{{ $tenant->name }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xl" 
                                        style="background: linear-gradient(135deg, {{ $tenant->primary_color ?? '#6366f1' }} 0%, {{ $tenant->secondary_color ?? '#8b5cf6' }} 100%);">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xl font-bold text-white">{{ $tenant->name }}</span>
                                </div>
                            @endif
                            </div>
                        </div>

                        <!-- Saldo & User Menu -->
                        <div class="flex items-center gap-3 sm:gap-6">
                            
                            <!-- Saldo Destacado -->
                            {{-- <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" style="color: {{ $tenant->primary_color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @livewire('player.balance-display')
                            </div> --}}

                            {{-- chat --}}
                            @livewire('player.player-chat', key('chat-widget'))

                            <!-- Botón Mensajes -->
                            <button onclick="Livewire.dispatch('toggle-chat')"
                            class="relative flex items-center gap-2 px-2 sm:px-3 py-2 rounded-lg hover:bg-gray-700 transition">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="hidden md:inline text-gray-300">Mensajes</span>
                                
                                @php
                                    $unreadCount = \App\Models\PlayerMessage::where('player_id', auth()->guard('player')->id())
                                        ->whereNull('read_by_player_at')
                                        ->count();
                                @endphp
                                
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </button>

                            <!-- Botón WhatsApp -->
                            @if($tenant->whatsapp_number)
                            <a href="{{ $tenant->whatsapp_link }}?text=Hola, soy {{ auth()->guard('player')->user()->name }} (Usuario: {{ auth()->guard('player')->user()->username }}). Necesito ayuda con:"
                            target="_blank"
                            class="flex items-center gap-2 px-2 sm:px-3 py-2 rounded-lg hover:bg-gray-700 transition">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                <span class="hidden md:inline text-gray-300">WhatsApp</span>
                            </a>
                            @endif

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 px-2 sm:px-3 py-2 rounded-lg hover:bg-gray-700 transition">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-bold text-sm sm:text-lg"
                                         style="background-color: {{ $tenant->primary_color }}">
                                        {{ substr(auth()->guard('player')->user()->username, 0, 1) }}
                                    </div>
                                    <span class="hidden sm:block text-white font-medium">{{ auth()->guard('player')->user()->username }}</span>
                                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                        {{-- <div class="sm:hidden px-4 py-3 border-b border-gray-700">
                                            <p class="text-xs text-gray-400">Saldo actual</p>
                                            <p class="text-lg font-bold text-white">${{ number_format(auth()->guard('player')->user()->balance, 2) }}</p>
                                        </div> --}}

                                        <!-- Mi Perfil -->
                                        <a href="{{ route('player.profile') }}" wire:navigate
                                           class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>Mi Perfil</span>
                                        </a>

                                        <!-- Notificaciones e Instalar App -->
                                        <x-push-notification-button />

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

            <!-- Barra de Navegación Horizontal (Desktop) -->
            <nav class="hidden lg:block bg-gray-700 border-b border-gray-600">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex space-x-1">
                        <!-- Dashboard -->
                        <a href="{{ route('player.dashboard') }}" wire:navigate
                           class="px-4 py-4 text-sm font-medium transition border-b-2 {{ request()->routeIs('player.dashboard') ? 'border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-400' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Inicio</span>
                            </div>
                        </a>

                        <!-- Transacciones -->
                        <a href="{{ route('player.transactions') }}" wire:navigate
                           class="px-4 py-4 text-sm font-medium transition border-b-2 {{ request()->routeIs('player.transactions') ? 'border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-400' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span>Transacciones</span>
                            </div>
                        </a>

                        <!-- Bonos -->
                        <a href="{{ route('player.bonuses') }}" wire:navigate
                           class="px-4 py-4 text-sm font-medium transition border-b-2 {{ request()->routeIs('player.bonuses') ? 'border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-400' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                <span>Bonos</span>
                            </div>
                        </a>

                        <!-- Referidos -->
                        <a href="{{ route('player.referrals') }}" wire:navigate
                        class="px-4 py-4 text-sm font-medium transition border-b-2 {{ request()->routeIs('player.referrals') ? 'border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-400' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Referidos</span>
                            </div>
                        </a>

                        <!-- Ruleta -->
                        {{-- <a href="{{ route('player.wheel') }}" wire:navigate
                           class="px-4 py-4 text-sm font-medium transition border-b-2 {{ request()->routeIs('player.wheel') ? 'border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-400' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Ruleta</span>
                            </div>
                        </a> --}}

                        <!-- Mis Cuentas -->
                        <a href="{{ route('player.withdrawal-accounts') }}" wire:navigate
                        class="px-4 py-4 text-sm font-medium transition border-b-2 {{ request()->routeIs('player.withdrawal-accounts') ?
                            'border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-400' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span>Mis Cuentas</span>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Banner de Mantenimiento --}}
            @if($tenant->maintenance_mode)
            <div class="bg-yellow-500 text-yellow-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">
                            {{ $tenant->maintenance_message ?? 'Plataforma en mantenimiento. Algunas funciones pueden no estar disponibles.' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Menú Lateral Móvil -->
            <div x-show="mobileMenuOpen" 
                 @click="mobileMenuOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
                 style="display: none;">
            </div>

            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 w-64 bg-gray-800 z-50 lg:hidden overflow-y-auto"
                 style="display: none;">
                
                <div class="p-4">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-white">Menú</h2>
                        <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <nav class="space-y-2">
                        <a href="{{ route('player.dashboard') }}" wire:navigate
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('player.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Inicio</span>
                        </a>

                        <a href="{{ route('player.transactions') }}" wire:navigate
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('player.transactions') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Transacciones</span>
                        </a>

                        <a href="{{ route('player.bonuses') }}" wire:navigate
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('player.bonuses') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            <span>Bonos</span>
                        </a>

                        <a href="{{ route('player.referrals') }}" wire:navigate
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('player.referrals') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Referidos</span>
                        </a>

                        {{-- <a href="{{ route('player.wheel') }}" wire:navigate
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('player.wheel') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Ruleta</span>
                        </a> --}}

                        <a href="{{ route('player.withdrawal-accounts') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('player.withdrawal-accounts') ?
                            'bg-gray-700 border-l-4 border-white text-white' : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span>Mis Cuentas</span>
                            </div>
                        </a>

                        <button @click="mobileMenuOpen = false; $nextTick(() => Livewire.dispatch('toggle-chat'))"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition text-gray-300 hover:bg-gray-700 hover:text-white w-full text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Mensajes</span>
                        </button>
                    </nav>
                </div>
            </div>

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

        <!-- PWA Prompt -->
        <x-pwa-prompt />

        {{-- Panel de Actividad en Tiempo Real --}}
        @livewire('player.activity-panel')

        <!-- Push Notifications -->
        <script>
            window.pushRoutes = {
                subscribe: '{{ route("player.push.subscribe") }}',
                unsubscribe: '{{ route("player.push.unsubscribe") }}',
                vapidKey: '{{ route("player.push.vapid") }}'
            };
        </script>
        <script src="/js/push-notifications.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', async function() {
                if (await PushNotifications.init()) {
                    const isSubscribed = await PushNotifications.isSubscribed();
                    if (!isSubscribed && Notification.permission === 'default') {
                        // Mostrar prompt después de 5 segundos
                        setTimeout(() => {
                            if (confirm('¿Deseas recibir notificaciones de tus transacciones?')) {
                                PushNotifications.subscribe();
                            }
                        }, 5000);
                    }
                }
            });
        </script>

    </body>
</html>