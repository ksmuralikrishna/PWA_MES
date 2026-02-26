const CACHE_NAME = 'mes-cache-v2';

const APP_SHELL = [
    '/',
    '/index.html',
    '/receiving',
    '/acid-testing',
    '/js/db.js',
    '/js/receiving.js',
    '/js/acid_testing.js',
    '/manifest.json',
    '/icons/5024802-200.png',
    '/icons/8056098-200.png',
    '/favicon.ico'
];

// Install → cache app shell
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('[SW] Caching files');
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