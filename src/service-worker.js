self.addEventListener('install', (event) => {
    event.waitUntil(
      caches.open('v1.01').then((cache) => {
        return cache.addAll([
          '/',
          '/index.php?p=home',
          '/vendor/style.css',
          '/include/js/app.js',
          '/icon-168x168.png',
          '/icon-192x192.png',
          '/icon-512x512.png',
        ]).catch((error) => {
          console.error('Cache addAll failed:', error);
        });
      })
    );
  });
  