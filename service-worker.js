const CACHE_NAME = 'techhelp-v1';
const BASE_PATH = '/sistema';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                `${BASE_PATH}/`,
                `${BASE_PATH}/assets/css/style.css`,
                `${BASE_PATH}/assets/css/cliente.css`,
                `${BASE_PATH}/assets/js/main.js`,
                `${BASE_PATH}/assets/images/icon-192.png`,
                `${BASE_PATH}/assets/images/icon-512.png`,
                `${BASE_PATH}/assets/images/icon.png`
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener('push', event => {
    const options = {
        body: event.data.text(),
        icon: '/sistema/assets/images/icon.png',
        badge: '/sistema/assets/images/badge.png'
    };

    event.waitUntil(
        self.registration.showNotification('TechHelp', options)
    );
});
