/**
 * Dream Career IT — Progressive Web App Service Worker
 * Secure offline caching policy strictly excluding private data & API credentials
 */

const CACHE_NAME = 'dcit-suite-v1';
const STATIC_ASSETS = [
    '/assets/css/app.css',
    '/assets/css/auth.css',
    '/assets/js/app.js',
    '/assets/images/logo.svg',
    '/assets/images/favicon.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // CRITICAL SECURITY RULE:
    // Never cache dynamic API responses, auth requests, or private dashboard pages
    if (
        event.request.method !== 'GET' ||
        url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/campaigns/') ||
        url.pathname.startsWith('/messages/')
    ) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }
            return fetch(event.request).catch(() => {
                // Return offline fallback if network fails
                if (event.request.headers.get('accept').includes('text/html')) {
                    return new Response(
                        `<!DOCTYPE html><html><body style="background:#070d19;color:#fff;font-family:sans-serif;text-align:center;padding:50px;">
                        <h2>Offline — Dream Career IT</h2>
                        <p>You are currently disconnected from the network. Reconnect to resume live operations.</p>
                        </body></html>`,
                        { headers: { 'Content-Type': 'text/html' } }
                    );
                }
            });
        })
    );
});
