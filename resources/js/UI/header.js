
    if (typeof window.Alpine === 'undefined' && !document.getElementById('alpine-cdn')) {
        var alpineScript = document.createElement('script');
        alpineScript.id = 'alpine-cdn';
        alpineScript.defer = true;
        alpineScript.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
        document.head.appendChild(alpineScript);
    }
