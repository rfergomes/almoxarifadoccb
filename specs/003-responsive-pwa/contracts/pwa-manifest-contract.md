# Interface & Contract Specifications: PWA & Service Worker

**Feature**: [`spec.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/spec.md) | **Branch**: `003-responsive-pwa` | **Date**: 2026-08-13

## 1. Web App Manifest Contract (`public/manifest.json`)

```json
{
  "name": "Sistema de Gestão de Almoxarifado Central CCB",
  "short_name": "Almoxarifado CCB",
  "description": "Sistema de Controle de Estoque, Empréstimos e EPIs da Congregação Cristã no Brasil",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#f4f6f9",
  "theme_color": "#003b57",
  "orientation": "any",
  "icons": [
    {
      "src": "/images/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/images/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ]
}
```

---

## 2. Meta Tags do Layout Blade (`resources/views/layouts/app.blade.php`)

```html
<!-- PWA Meta Tags -->
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#003b57">

<!-- Apple / iOS Specific Meta Tags -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Almoxarifado CCB">
<link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

<!-- Mobile Viewport Tag -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
```

---

## 3. Service Worker Lifecycle Contract (`public/sw.js`)

```javascript
const CACHE_NAME = 'ccb-almoxarifado-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/offline.html',
  '/images/CCB_Logo_fundo_claro.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS_TO_CACHE))
  );
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        const resClone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, resClone));
        return response;
      })
      .catch(() => caches.match(event.request).then((res) => res || caches.match('/offline.html')))
  );
});
```
