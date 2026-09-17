// Aprender a Vivir Contigo — Service Worker
// Cachea lo esencial (estilos, logo, íconos) para que la app cargue rápido y
// funcione mínimamente sin conexión. El contenido dinámico (membresía, citas)
// siempre intenta traerse fresco de la red primero.

var CACHE_NAME = 'avc-cache-v1';
var ARCHIVOS_ESENCIALES = [
  '/assets/css/style.css',
  '/assets/img/logo.png',
  '/assets/img/icon-192.png',
  '/assets/img/icon-512.png',
  '/assets/js/main.js',
  '/index.html',
  '/manifest.json'
];

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(ARCHIVOS_ESENCIALES);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (nombres) {
      return Promise.all(
        nombres.filter(function (nombre) { return nombre !== CACHE_NAME; })
               .map(function (nombre) { return caches.delete(nombre); })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', function (event) {
  var url = new URL(event.request.url);

  // Nunca cachees llamadas a PHP (login, membresía, citas, pagos) — siempre deben ir a la red.
  if (url.pathname.endsWith('.php')) {
    event.respondWith(fetch(event.request).catch(function () {
      return new Response('Sin conexión. Intenta de nuevo cuando tengas internet.', {
        status: 503,
        headers: { 'Content-Type': 'text/plain; charset=utf-8' }
      });
    }));
    return;
  }

  // Para todo lo demás (CSS, JS, imágenes, páginas estáticas): red primero,
  // y si falla (sin internet), usa lo que haya en caché.
  event.respondWith(
    fetch(event.request)
      .then(function (respuesta) {
        var copia = respuesta.clone();
        caches.open(CACHE_NAME).then(function (cache) { cache.put(event.request, copia); });
        return respuesta;
      })
      .catch(function () { return caches.match(event.request); })
  );
});
