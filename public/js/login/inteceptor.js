// ============================================
// INTERCEPTOR CON SINCRONIZACIÓN
// ============================================

(function () {
    'use strict';

    const SESSION_CHANNEL = 'session_sync_channel';
    const SESSION_KEY = 'app_session_state';
    let isHandling401 = false; // Bandera para evitar múltiples llamadas

    // Crear canal de comunicación
    let broadcastChannel;
    try {
        broadcastChannel = new BroadcastChannel(SESSION_CHANNEL);

        // Escuchar mensajes de logout de otras pestañas
        broadcastChannel.onmessage = (event) => {
            if (event.data.action === 'LOGOUT' && !isHandling401) {
                console.log('Sesión cerrada en otra pestaña, redirigiendo...');
                forceLogout();
            }
        };
    } catch (e) {
        console.warn('BroadcastChannel no disponible');
    }

    // Fallback: Escuchar localStorage
    window.addEventListener('storage', (event) => {
        if (event.key === SESSION_KEY && event.newValue && !isHandling401) {
            try {
                const data = JSON.parse(event.newValue);
                if (data.action === 'LOGOUT') {
                    console.log('Logout detectado via localStorage');
                    forceLogout();
                }
            } catch (e) {
                console.error('Error parsing storage event:', e);
            }
        }
    });

    // Interceptor global para fetch
    const originalFetch = window.fetch;
    window.fetch = function (...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                // Verificar si la respuesta es 401
                if (response.status === 401) {
                    handleSessionExpired(response.clone());
                }
                return response;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                throw error;
            });
    };

    // Interceptor global para jQuery AJAX (si usas jQuery)
    if (window.$ && $.ajaxSetup) {
        $.ajaxSetup({
            statusCode: {
                401: function (xhr) {
                    console.log('401 detectado en jQuery AJAX');
                    handleSessionExpired(null);
                }
            }
        });
    }

    // Función para manejar sesión expirada
    async function handleSessionExpired(response) {
        // Evitar múltiples llamadas simultáneas
        if (isHandling401) {
            console.log('Ya se está manejando un 401, ignorando...');
            return;
        }

        isHandling401 = true;

        try {
            let shouldReload = true;

            // Intentar leer respuesta si está disponible
            if (response) {
                try {
                    const data = await response.json();
                    shouldReload = data.reload !== false; // Por defecto true
                } catch (e) {
                    console.warn('No se pudo parsear respuesta 401:', e);
                }
            }

            if (shouldReload) {
                // Notificar a otras pestañas
                notifyLogout();

                // Pequeño delay para que el mensaje se propague
                setTimeout(() => {
                    closeOrReloadTab();
                }, 100);
            }
        } catch (e) {
            console.error('Error procesando 401:', e);
            // En caso de error, cerrar de todas formas
            closeOrReloadTab();
        }
    }

    // Notificar a otras pestañas sobre logout
    function notifyLogout() {
        const message = {
            action: 'LOGOUT',
            timestamp: Date.now(),
            reason: '401_unauthorized'
        };

        // BroadcastChannel
        if (broadcastChannel) {
            try {
                broadcastChannel.postMessage(message);
            } catch (e) {
                console.error('Error enviando por BroadcastChannel:', e);
            }
        }

        // localStorage fallback
        try {
            localStorage.setItem(SESSION_KEY, JSON.stringify(message));
            // Limpiar después de un momento
            setTimeout(() => {
                localStorage.removeItem(SESSION_KEY);
            }, 1000);
        } catch (e) {
            console.warn('Error guardando en localStorage:', e);
        }
    }

    // Cerrar o redireccionar pestaña
    function closeOrReloadTab() {
        // Limpiar datos de sesión
        try {
            sessionStorage.clear();

            // Limpiar solo cookies de auth (no todo localStorage)
            document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        } catch (e) {
            console.error('Error limpiando sesión:', e);
        }

        // Intentar cerrar la pestaña (solo funciona si fue abierta por JS)
        if (window.history.length > 1) {
            window.close();
        }

        // Si no se pudo cerrar, redirigir al login
        if (!window.closed) {
            // Evitar loops infinitos
            const currentPath = window.location.pathname;
            if (!currentPath.includes('login') && currentPath !== '/') {
                window.location.href = '/';
            }
        }
    }

    // Forzar logout (llamado desde otra pestaña)
    function forceLogout() {
        if (isHandling401) return;
        isHandling401 = true;

        console.log('Ejecutando logout forzado por sincronización entre pestañas');

        // No llamar al endpoint nuevamente, solo limpiar y redirigir
        closeOrReloadTab();
    }

    // Limpiar al cerrar la ventana
    window.addEventListener('beforeunload', () => {
        if (broadcastChannel) {
            try {
                broadcastChannel.close();
            } catch (e) {
                console.error('Error cerrando canal:', e);
            }
        }
    });

    console.log('Interceptor de sesión inicializado con sincronización entre pestañas');
})();