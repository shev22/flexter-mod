const CACHE = 'flexter-static-v2';

self.addEventListener('install', (event) => {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))),
        ).then(() => self.clients.claim()),
    );
});

function isStaticAsset(url) {
    return (
        url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/images/')
        || url.pathname.startsWith('/video/')
        || url.pathname === '/manifest.json'
        || url.pathname === '/favicon.ico'
        || /\.(js|css|woff2?|png|jpe?g|gif|webp|svg|ico|mp4)$/i.test(url.pathname)
    );
}

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    if (url.origin !== self.location.origin) {
        return;
    }

    // Never intercept Inertia visits, navigations, or other dynamic requests.
    if (
        event.request.headers.get('X-Inertia')
        || event.request.headers.get('X-Requested-With') === 'XMLHttpRequest'
        || event.request.mode === 'navigate'
        || ! isStaticAsset(url)
    ) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) {
                return cached;
            }

            return fetch(event.request).then((response) => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE).then((cache) => cache.put(event.request, clone));
                }

                return response;
            });
        }),
    );
});
