<!-- PWA Prompt Modal -->
<div x-data="{
    show: false,
    isSubscribed: false,
    isInstalled: false,
    device: 'unknown',
    async init() {
        // Detectar si ya descartó el prompt
        const dismissed = localStorage.getItem('pwa_prompt_dismissed');
        if (dismissed && Date.now() < parseInt(dismissed)) {
            return;
        }
        
        // Detectar dispositivo
        const ua = navigator.userAgent;
        if (/iPhone|iPad|iPod/.test(ua)) {
            this.device = 'ios';
        } else if (/Android/.test(ua)) {
            this.device = 'android';
        } else {
            this.device = 'desktop';
        }
        
        // Verificar si ya está instalado (standalone mode)
        this.isInstalled = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        
        // Verificar suscripción push
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            try {
                await PushNotifications.init();
                this.isSubscribed = await PushNotifications.isSubscribed();
            } catch (e) {
                console.log('Push no disponible');
            }
        }
        
        // Mostrar si no tiene notificaciones O no tiene app instalada
        if (!this.isSubscribed || !this.isInstalled) {
            setTimeout(() => { this.show = true; }, 2000);
        }
    },
    async activateNotifications() {
        const result = await PushNotifications.subscribe();
        if (result.success) {
            this.isSubscribed = true;
            if (this.isInstalled) {
                this.close();
            }
        } else {
            alert(result.message);
        }
    },
    dismiss() {
        // No mostrar por 7 días
        localStorage.setItem('pwa_prompt_dismissed', Date.now() + (7 * 24 * 60 * 60 * 1000));
        this.show = false;
    },
    close() {
        this.show = false;
    }
}"
x-show="show"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50"
style="display: none;"
@keydown.escape.window="close()">
    
    <div @click.outside="close()" 
         class="bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-gray-700">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">📱 Mejora tu experiencia</h3>
                <button @click="close()" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-4">
            
            <!-- Activar Notificaciones -->
            <div x-show="!isSubscribed" class="bg-gray-700/50 rounded-xl p-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-indigo-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-white mb-1">Activar Notificaciones</h4>
                        <p class="text-gray-400 text-sm mb-3">Recibe alertas de tus transacciones al instante</p>
                        <button @click="activateNotifications()" 
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                            🔔 Activar ahora
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Instalar App -->
            <div x-show="!isInstalled" class="bg-gray-700/50 rounded-xl p-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-white mb-1">Instalar App</h4>
                        <p class="text-gray-400 text-sm mb-3">Acceso directo desde tu pantalla de inicio</p>
                        
                        <!-- Instrucciones Android -->
                        <div x-show="device === 'android'" class="bg-gray-800 rounded-lg p-3 text-sm">
                            <p class="text-gray-300 mb-2">En Chrome:</p>
                            <ol class="text-gray-400 space-y-1 list-decimal list-inside">
                                <li>Tocá el menú <span class="text-white font-mono">⋮</span> (arriba)</li>
                                <li>Seleccioná <span class="text-green-400">"Instalar app"</span></li>
                            </ol>
                        </div>
                        
                        <!-- Instrucciones iOS -->
                        <div x-show="device === 'ios'" class="bg-gray-800 rounded-lg p-3 text-sm">
                            <p class="text-gray-300 mb-2">En Safari:</p>
                            <ol class="text-gray-400 space-y-1 list-decimal list-inside">
                                <li>Tocá <span class="text-white text-lg">⬆️</span> (compartir)</li>
                                <li>Seleccioná <span class="text-green-400">"Añadir a inicio"</span></li>
                            </ol>
                        </div>
                        
                        <!-- Instrucciones Desktop -->
                        <div x-show="device === 'desktop'" class="bg-gray-800 rounded-lg p-3 text-sm">
                            <p class="text-gray-300 mb-2">En tu navegador:</p>
                            <ol class="text-gray-400 space-y-1 list-decimal list-inside">
                                <li>Buscá el ícono <span class="text-white">⊕</span> en la barra de direcciones</li>
                                <li>O menú → <span class="text-green-400">"Instalar"</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ya todo OK -->
            <div x-show="isSubscribed && isInstalled" class="text-center py-4">
                <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-white font-medium">¡Todo listo!</p>
                <p class="text-gray-400 text-sm">Ya tenés notificaciones y la app instalada</p>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-900/50 border-t border-gray-700">
            <button @click="dismiss()" class="w-full text-gray-400 hover:text-gray-300 text-sm py-2 transition">
                No mostrar de nuevo por 7 días
            </button>
        </div>
        
    </div>
</div>