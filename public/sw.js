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
    
    const urlToOpen = event.notification.data.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Buscar si ya hay una ventana abierta con la app
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(self.registration.scope) && 'focus' in client) {
                    return client.focus().then(function(focusedClient) {
                        if ('navigate' in focusedClient) {
                            return focusedClient.navigate(urlToOpen);
                        }
                    });
                }
            }
            // Si no hay ventana abierta, abrir una nueva
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});