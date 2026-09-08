// Service Worker - Almoxarifado Central CCB
const CACHE_NAME = 'ccb-almoxarifado-v1';

const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/images/CCB_Logo_fundo_claro.png',
  '/images/CCB_Logo_Reduzido.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
  'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
  'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
  'https://code.jquery.com/jquery-3.7.1.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// Instalação: Pre-cache de arquivos core
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[ServiceWorker] Pre-caching static assets');
      return cache.addAll(STATIC_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// Ativação: Limpeza de caches antigos
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('[ServiceWorker] Removendo cache antigo:', cache);
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Interceptação de Requisições
self.addEventListener('fetch', (event) => {
  // Apenas requisições GET são tratadas com cache
  if (event.request.method !== 'GET') {
    return;
  }

  const url = new URL(event.request.url);

  // Ignora esquemas não suportados pelo Cache API (ex: chrome-extension://)
  if (url.protocol !== 'http:' && url.protocol !== 'https:') {
    return;
  }

  // Ignora serviços de terceiros e telemetria externa (evita erros de CORS/Beacon)
  if (url.hostname.includes('cloudflareinsights.com')) {
    return;
  }

  // Interceptação segura
  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Se a resposta for válida, armazena uma cópia no cache estático
        if (networkResponse && networkResponse.status === 200 && (networkResponse.type === 'basic' || networkResponse.type === 'cors')) {
          if (event.request.url.startsWith('http://') || event.request.url.startsWith('https://')) {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, responseToCache).catch(() => {
                // Ignora falhas pontuais de armazenamento em cache
              });
            });
          }
        }
        return networkResponse;
      })
      .catch(async () => {
        // Tenta buscar no cache se estiver offline
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
          return cachedResponse;
        }

        // Se a requisição for para uma navegação HTML, exibe a página offline
        if (event.request.mode === 'navigate' || (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'))) {
          const offlinePage = await caches.match('/offline.html');
          if (offlinePage) {
            return offlinePage;
          }
        }

        // Retorna Response com status de serviço indisponível em vez de undefined
        return new Response('', { status: 503, statusText: 'Service Unavailable (Offline)' });
      })
  );
});
