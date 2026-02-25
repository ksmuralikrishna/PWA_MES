const CACHE_NAME = 'mes-cache-v2';

const APP_SHELL = [
    '/',
    '/index.html',
    '/offline.html',
    '/js/receiving.js',
    '/js/acid_testing.js',
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
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match('/index.html'))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then(r => r || fetch(event.request))
    );
});