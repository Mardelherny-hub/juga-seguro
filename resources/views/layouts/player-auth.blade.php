<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $currentTenant->name ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Fondo con gradiente oscuro -->
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden"
         style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
        
        <!-- Efectos de fondo -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-72 h-72 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" 
                 style="background-color: {{ $currentTenant->primary_color ?? '#3b82f6' }}"></div>
        </div>

        <!-- Container principal -->
        <div class="relative z-10 w-full max-w-sm px-6 py-8 mx-auto">
            
            <!-- Logo del currentTenant -->
            <div class="text-center mb-8">
                @if(isset($currentTenant) && $currentTenant->logo_url)
                    <img src="{{ $currentTenant->logo_url }}" 
                        alt="{{ $currentTenant->name }}" 
                        class="h-16 w-auto mx-auto mb-4">
                @elseif(isset($currentTenant))
                    <h1 class="text-4xl font-bold text-white mb-2">
                        {{ $currentTenant->name }}
                    </h1>
                @else
                    <h1 class="text-4xl font-bold text-white mb-2">
                        Next Level
                    </h1>
                @endif
            </div>

            <!-- Card con glassmorphism -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-8">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="text-center text-sm text-gray-400 mt-6">
                © {{ date('Y') }} {{ $currentTenant->name ?? 'Next Level' }}. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>