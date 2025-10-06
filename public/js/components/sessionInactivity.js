$(document).ready(function () {
    // Configuración (en milisegundos)
    const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 minutos
    const WARNING_TIMEOUT = 28 * 60 * 1000;    // Mostrar aviso a los 28 minutos
    const CHECK_INTERVAL = 30 * 1000;          // Verificar cada 30 segundos

    let inactivityTimer;
    let warningTimer;
    let lastActivityTime = Date.now();
    let isLoggingOut = false;

    // ============================================
    // NUEVA FUNCIONALIDAD: Sincronización entre pestañas
    // ============================================
    const SESSION_CHANNEL = 'session_sync_channel';
    const SESSION_KEY = 'app_session_state';

    // Crear canal de comunicación entre pestañas
    let broadcastChannel;
    try {
        broadcastChannel = new BroadcastChannel(SESSION_CHANNEL);
    } catch (e) {
        console.warn('BroadcastChannel no disponible, usando localStorage fallback');
    }

    // Escuchar mensajes de otras pestañas
    if (broadcastChannel) {
        broadcastChannel.onmessage = (event) => {
            handleCrossTabMessage(event.data);
        };
    }

    // Fallback: Escuchar cambios en localStorage (para navegadores antiguos)
    window.addEventListener('storage', (event) => {
        if (event.key === SESSION_KEY && event.newValue) {
            try {
                const data = JSON.parse(event.newValue);
                handleCrossTabMessage(data);
            } catch (e) {
                console.error('Error parsing storage event:', e);
            }
        }
    });

    // Manejar mensajes de otras pestañas
    function handleCrossTabMessage(data) {
        if (data.action === 'LOGOUT') {
            console.log('Otra pestaña cerró la sesión, sincronizando...');
            // Otra pestaña cerró sesión, esta también debe cerrar
            forceLogout();
        } else if (data.action === 'ACTIVITY') {
            // Otra pestaña tiene actividad, sincronizar tiempo
            lastActivityTime = data.timestamp;
            resetTimers();
        }
    }

    // Notificar a otras pestañas sobre actividad
    function notifyActivity() {
        const message = {
            action: 'ACTIVITY',
            timestamp: Date.now()
        };

        // Enviar por BroadcastChannel
        if (broadcastChannel) {
            broadcastChannel.postMessage(message);
        }

        // Fallback: localStorage
        try {
            localStorage.setItem(SESSION_KEY, JSON.stringify(message));
        } catch (e) {
            console.warn('No se pudo guardar en localStorage:', e);
        }
    }

    // Notificar a otras pestañas sobre logout
    function notifyLogout() {
        const message = {
            action: 'LOGOUT',
            timestamp: Date.now()
        };

        if (broadcastChannel) {
            broadcastChannel.postMessage(message);
        }

        try {
            localStorage.setItem(SESSION_KEY, JSON.stringify(message));
        } catch (e) {
            console.warn('No se pudo guardar en localStorage:', e);
        }
    }

    // Forzar logout en esta pestaña (llamado desde otra pestaña)
    function forceLogout() {
        if (isLoggingOut) return;

        clearTimeout(inactivityTimer);
        clearTimeout(warningTimer);
        $('#inactivity-warning').hide();

        // Redirigir directamente sin llamar al endpoint
        window.location.href = '/';
    }

    // ============================================
    // FIN NUEVA FUNCIONALIDAD
    // ============================================

    // Crear modal de advertencia
    const warningModal = `
        <div id="inactivity-warning" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:25px; border-radius:8px; border:1px solid #e0e0e0; z-index:9999; box-shadow:0 4px 20px rgba(0,0,0,0.15); max-width:400px; text-align:center;">
            <h3 style="margin-top:0; color:#d32f2f;">¡Atención!</h3>
            <p style="margin-bottom:20px;">Tu sesión se cerrará automáticamente por inactividad en <span id="countdown" style="font-weight:bold;">2:00</span> minutos.</p>
            <button id="continue-session" style="background-color:#4CAF50; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:16px;">Continuar sesión</button>
        </div>
    `;

    $('body').append(warningModal);

    // Eventos que indican actividad
    const activityEvents = [
        'mousemove', 'keydown', 'scroll', 'click',
        'touchstart', 'mousedown', 'input', 'touchmove'
    ];

    // Función para reiniciar los temporizadores
    function resetTimers() {
        lastActivityTime = Date.now();

        clearTimeout(inactivityTimer);
        clearTimeout(warningTimer);

        $('#inactivity-warning').hide();

        warningTimer = setTimeout(showWarning, WARNING_TIMEOUT);
        inactivityTimer = setTimeout(logoutDueToInactivity, INACTIVITY_TIMEOUT);

        // NUEVO: Notificar actividad a otras pestañas (throttled)
        throttledNotifyActivity();
    }

    // Throttle para no saturar la comunicación entre pestañas
    let lastNotification = 0;
    const NOTIFICATION_THROTTLE = 5000; // Notificar máximo cada 5 segundos

    function throttledNotifyActivity() {
        const now = Date.now();
        if (now - lastNotification > NOTIFICATION_THROTTLE) {
            lastNotification = now;
            notifyActivity();
        }
    }

    // Mostrar advertencia de inactividad
    function showWarning() {
        $('#inactivity-warning').show();

        let secondsLeft = (INACTIVITY_TIMEOUT - WARNING_TIMEOUT) / 1000;

        const countdownInterval = setInterval(() => {
            secondsLeft--;
            const mins = Math.floor(secondsLeft / 60);
            const secs = secondsLeft % 60;
            $('#countdown').text(`${mins}:${secs < 10 ? '0' + secs : secs}`);

            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    }

    // Cerrar sesión por inactividad
    function logoutDueToInactivity() {
        if (isLoggingOut) return;

        clearTimeout(inactivityTimer);
        clearTimeout(warningTimer);

        isLoggingOut = true;
        $('#inactivity-warning').html('<p>Cerrando sesión...</p>').show();

        // NUEVO: Notificar a otras pestañas ANTES de hacer logout
        notifyLogout();

        // Desloguear Usuario
        $.ajax({
            type: 'POST',
            url: '/api/logoutByInactivity',
            success: function (response) {
                window.location.href = response.location || '/';
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Error durante logout';
                console.error(errorMessage);
                // Forzar redirección incluso si hay error
                window.location.href = '/';
            },
            complete: function () {
                isLoggingOut = false;
            }
        });
    }

    // Evento para continuar la sesión
    $('#continue-session').click(function () {
        lastActivityTime = Date.now();
        $('#inactivity-warning').hide();
        resetTimers();

        // Notificar a otras pestañas que la sesión continúa
        notifyActivity();

        // Ping al servidor para mantener sesión activa
        $.get('/api/ping').fail(() => {
            logoutDueToInactivity();
        });
    });

    // Registrar eventos de actividad
    activityEvents.forEach(event => {
        $(document).on(event, resetTimers);
    });

    // Verificación periódica adicional
    setInterval(() => {
        const currentTime = Date.now();
        const elapsed = currentTime - lastActivityTime;

        if (elapsed > INACTIVITY_TIMEOUT) {
            logoutDueToInactivity();
        }
    }, CHECK_INTERVAL);

    // NUEVO: Limpiar al cerrar la pestaña
    window.addEventListener('beforeunload', () => {
        if (broadcastChannel) {
            broadcastChannel.close();
        }
    });

    // Iniciar temporizadores
    resetTimers();
});