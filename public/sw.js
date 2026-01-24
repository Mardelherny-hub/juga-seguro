self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'Nueva notificación',
        icon: data.icon || '/favicon.ico',
        badge: '/favicon.ico',
        vibrate: [200, 100, 200],
        tag: data.tag || 'default',
        requireInteraction: true,
        data: {
            url: data.url || '/'
        }
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'Notificación', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});