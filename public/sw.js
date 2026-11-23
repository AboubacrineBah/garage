/**
 * Service Worker – Garage Moderne
 * Cache basique pour navigation hors-ligne partielle.
 * Stratégie : Network-first pour les pages, Cache-first pour les assets statiques.
 */

const CACHE_NAME = 'garage-moderne-v1';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
  '/',
  '/offline.html',
  '/manifest.json',
  // Ajouter les CSS/JS compilés Vite en production
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {
        // Ignore les erreurs de cache initial
      });
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Uniquement GET
  if (request.method !== 'GET') return;

  // Navigation (pages HTML) → Network first, fallback offline
  // On ne met PAS en cache les pages authentifiées (évite fuite de données sur appareil partagé)
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Ne cacher que la page offline / assets publics, pas le HTML métier
          return response;
        })
        .catch(() => {
          return caches.match(OFFLINE_URL);
        })
    );
    return;
  }

  // Assets statiques → Cache first
  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) return cached;
      return fetch(request).then((response) => {
        if (response.ok) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
        }
        return response;
      });
    })
  );
});
