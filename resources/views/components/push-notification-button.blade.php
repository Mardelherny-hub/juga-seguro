<div x-data="{ 
    pushSupported: false,
    isSubscribed: false,
    canInstall: false,
    deferredPrompt: null,
    async init() {
        this.pushSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        if (this.pushSupported) {
            await PushNotifications.init();
            this.isSubscribed = await PushNotifications.isSubscribed();
        }
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.canInstall = true;
        });
    },
    async subscribe() {
        const result = await PushNotifications.subscribe();
        if (result.success) {
            this.isSubscribed = true;
        }
        alert(result.message);
    },
    async installApp() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                this.canInstall = false;
            }
            this.deferredPrompt = null;
        }
    }
}">
    <!-- Activar Notificaciones -->
    <template x-if="pushSupported && !isSubscribed">
        <button @click="subscribe()" 
                class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition w-full text-left">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span>Activar Notificaciones</span>
        </button>
    </template>

    <!-- Instalar App -->
    <template x-if="canInstall">
        <button @click="installApp()" 
                class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 transition w-full text-left">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Instalar App</span>
        </button>
    </template>
</div>