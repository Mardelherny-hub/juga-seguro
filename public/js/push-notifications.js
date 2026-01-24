const PushNotifications = {
    async init() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.log('Push notifications no soportadas');
            return false;
        }
        return true;
    },

    async registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registrado');
            return registration;
        } catch (error) {
            console.error('Error registrando SW:', error);
            return null;
        }
    },

    async getVapidKey() {
        const response = await fetch(window.pushRoutes.vapidKey);
        const data = await response.json();
        return data.key;
    },

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    },

    async subscribe() {
        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                return { success: false, message: 'Permiso denegado' };
            }

            const registration = await this.registerServiceWorker();
            if (!registration) {
                return { success: false, message: 'Error en Service Worker' };
            }

            const vapidKey = await this.getVapidKey();
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(vapidKey)
            });

            const response = await fetch(window.pushRoutes.subscribe, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subscription.toJSON())
            });

            if (response.ok) {
                return { success: true, message: 'Notificaciones activadas' };
            }
            return { success: false, message: 'Error al guardar suscripción' };
        } catch (error) {
            console.error('Error:', error);
            return { success: false, message: error.message };
        }
    },

    async unsubscribe() {
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            
            if (subscription) {
                await fetch(window.pushRoutes.unsubscribe, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ endpoint: subscription.endpoint })
                });
                await subscription.unsubscribe();
            }
            return { success: true, message: 'Notificaciones desactivadas' };
        } catch (error) {
            return { success: false, message: error.message };
        }
    },

    async isSubscribed() {
        if (!('serviceWorker' in navigator)) return false;
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        return !!subscription;
    }
};