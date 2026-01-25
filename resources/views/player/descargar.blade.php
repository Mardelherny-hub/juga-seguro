<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Descargar App - {{ $currentTenant->name ?? 'Casino' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            @if($currentTenant->logo_url ?? false)
                <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" class="h-16 mx-auto mb-4">
            @endif
            <h1 class="text-3xl font-bold text-white">{{ $currentTenant->name ?? 'Casino' }}</h1>
            <p class="text-gray-400 mt-2">Instalá la app en tu celular</p>
        </div>

        <!-- Botón instalación directa (solo aparece si el navegador lo soporta) -->
        <button id="btn-instalar" onclick="instalarApp()" 
            class="hidden w-full mb-6 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition flex items-center justify-center gap-2 text-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span>Instalar App Ahora</span>
        </button>

        <!-- Mensaje si ya está instalada -->
        <div id="ya-instalada" class="hidden mb-6 p-4 bg-green-500/20 border border-green-500 rounded-xl text-center">
            <p class="text-green-400 font-bold text-lg">✅ ¡App ya instalada!</p>
            <p class="text-gray-400 text-sm mt-1">Buscala en tu pantalla de inicio</p>
        </div>

        <!-- Android -->
        <div id="instrucciones-android" class="bg-gray-800 rounded-xl p-6 mb-4 border border-gray-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.523 15.341l1.615-2.793a.337.337 0 00-.123-.46.338.338 0 00-.46.123l-1.635 2.827c-1.243-.565-2.64-.879-4.106-.879s-2.863.314-4.106.879L7.073 12.21a.337.337 0 00-.46-.123.337.337 0 00-.123.46l1.615 2.793C4.815 16.798 2.554 19.953 2 23.576h20c-.554-3.623-2.815-6.778-6.077-8.235zm-8.127 4.655a.83.83 0 110-1.66.83.83 0 010 1.66zm5.208 0a.83.83 0 110-1.66.83.83 0 010 1.66z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">Android</h2>
            </div>
            <ol class="space-y-3 text-gray-300 text-sm">
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">1</span>
                    <span>Abrí este link en <strong class="text-white">Chrome</strong></span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">2</span>
                    <span>Tocá el menú <strong class="text-white">⋮</strong> (tres puntos arriba a la derecha)</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">3</span>
                    <span>Seleccioná <strong class="text-white">"Instalar app"</strong> o <strong class="text-white">"Agregar a pantalla de inicio"</strong></span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">4</span>
                    <span>¡Listo! La app aparecerá en tu pantalla</span>
                </li>
            </ol>
        </div>

        <!-- iPhone -->
        <div id="instrucciones-iphone" class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gray-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">iPhone</h2>
            </div>
            <ol class="space-y-3 text-gray-300 text-sm">
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">1</span>
                    <span>Abrí este link en <strong class="text-white">Safari</strong></span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">2</span>
                    <span>Tocá el ícono de <strong class="text-white">compartir</strong> (□↑) abajo en el centro</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">3</span>
                    <span>Buscá y tocá <strong class="text-white">"Agregar a pantalla de inicio"</strong></span>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">4</span>
                    <span>Tocá <strong class="text-white">"Agregar"</strong> arriba a la derecha</span>
                </li>
            </ol>
        </div>

        <!-- Botón Crear Cuenta -->
        <a href="{{ route('player.register') }}" 
           class="block w-full py-4 text-center font-bold rounded-xl transition"
           style="background: linear-gradient(135deg, {{ $currentTenant->primary_color ?? '#6366f1' }} 0%, {{ $currentTenant->secondary_color ?? '#8b5cf6' }} 100%); color: white;">
            Crear cuenta
        </a>
        
        <p class="text-center text-gray-500 text-sm mt-4">
            ¿Ya tenés cuenta? <a href="{{ route('player.login') }}" class="text-blue-400 hover:underline">Iniciar sesión</a>
        </p>
    </div>

    <script>
    let deferredPrompt;

    // Detectar si es iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    
    // Detectar si ya está en modo standalone (instalada)
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

    if (isStandalone) {
        document.getElementById('ya-instalada').classList.remove('hidden');
        document.getElementById('instrucciones-android').classList.add('hidden');
        document.getElementById('instrucciones-iphone').classList.add('hidden');
    } else if (isIOS) {
        // En iOS mostrar solo instrucciones de iPhone
        document.getElementById('instrucciones-android').classList.add('hidden');
    }

    // Capturar evento de instalación (solo Chrome/Android)
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        // Mostrar botón y ocultar instrucciones Android
        document.getElementById('btn-instalar').classList.remove('hidden');
        document.getElementById('instrucciones-android').classList.add('hidden');
    });

    function instalarApp() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    document.getElementById('btn-instalar').innerHTML = '✅ ¡App instalada!';
                    document.getElementById('btn-instalar').disabled = true;
                    document.getElementById('btn-instalar').classList.remove('bg-green-600', 'hover:bg-green-700');
                    document.getElementById('btn-instalar').classList.add('bg-gray-600');
                }
                deferredPrompt = null;
            });
        }
    }

    // Detectar cuando se instala
    window.addEventListener('appinstalled', () => {
        document.getElementById('btn-instalar').innerHTML = '✅ ¡App instalada!';
        document.getElementById('btn-instalar').disabled = true;
        deferredPrompt = null;
    });
    </script>

</body>
</html>