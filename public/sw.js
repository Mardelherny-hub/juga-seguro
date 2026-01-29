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
        Promise.all([
            // Mostrar notificación
            self.registration.showNotification(data.title || 'Notificación', options),
            // Avisar a las páginas abiertas para reproducir sonido
            self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clients => {
                clients.forEach(client => {
                    client.postMessage({ type: 'PUSH_RECEIVED', data: data });
                });
            })
        ])
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});