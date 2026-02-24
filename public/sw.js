const CACHE_NAME = 'mes-cache-v2';

const APP_SHELL = [
    '/offline.html',
    '/js/receiving.js',
    '/manifest.json'
];

// Install → cache app shell
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(APP_SHELL);
        })
    );
    self.skipWaiting();
});

// Activate
self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

// Fetch logic
self.addEventListener('fetch', event => {
    const { request } = event;

    // HTML navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match('/offline.html');
            })
        );
        return;
    }

    // Other assets
    event.respondWith(
        caches.match(request).then(response => {
            return response || fetch(request);
        })
    );
});