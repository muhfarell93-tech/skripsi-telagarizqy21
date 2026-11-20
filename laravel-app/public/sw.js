// Service Worker PWA Telaga Rizqy 21
const CACHE_NAME = 'telagarizqy21-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/pos',
  '/manifest.json',
  '/audio/payment-success.wav'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  // Network first, fallback to cache for offline capabilities
  if (event.request.method === 'GET') {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          return response;
        })
        .catch(() => caches.match(event.request))
    );
  }
});
