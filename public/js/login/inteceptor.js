// Interceptor global para manejar 401 automáticamente
(function () {
    if (window.fetchInterceptorLoaded) return; // Evitar duplicados
    window.fetchInterceptorLoaded = true;

    const originalFetch = window.fetch;
    window.fetch = function (...args) {
        return originalFetch.apply(this, args).then(response => {
            if (response.status === 401) {
                console.log('🚫 Token expirado, redirigiendo a login...');
                sessionStorage.clear();
                localStorage.clear();
                window.location.href = '/';
            }
            return response;
        });
    };
})();
