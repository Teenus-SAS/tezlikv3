$(document).ready(function () {
    // Configuración (en milisegundos)
    const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 minutos en ms
    const WARNING_TIMEOUT = 28 * 60 * 1000;    // Mostrar aviso
    const CHECK_INTERVAL = 30 * 1000;         // Verificar cada 30 segundos

    let inactivityTimer;
    let warningTimer;
    let lastActivityTime = Date.now();
    let isLoggingOut = false; // Bandera para evitar múltiples llamadas

    // Crear modal de advertencia
    const warningModal = `
        <div id="inactivity-warning" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:25px; border-radius:8px; border:1px solid #e0e0e0; z-index:9999; box-shadow:0 4px 20px rgba(0,0,0,0.15); max-width:400px; text-align:center;">
            <h3 style="margin-top:0; color:#d32f2f;">¡Atención!</h3>
            <p style="margin-bottom:20px;">Tu sesión se cerrará automáticamente por inactividad en <span id="countdown" style="font-weight:bold;">2:00</span> minutos.</p>
            <button id="continue-session" style="background-color:#4CAF50; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:16px;">Continuar sesión</button>
        </div>
    `;

    // Añadir modal al DOM
    $('body').append(warningModal);

    // Eventos que indican actividad
    const activityEvents = [
        'mousemove', 'keydown', 'scroll', 'click',
        'touchstart', 'mousedown', 'input', 'touchmove'
    ];

    // Función para reiniciar los temporizadores
    function resetTimers() {
        lastActivityTime = Date.now();

        // Limpiar temporizadores existentes
        clearTimeout(inactivityTimer);
        clearTimeout(warningTimer);

        // Ocultar advertencia si está visible
        $('#inactivity-warning').hide();

        // Programar nueva advertencia
        warningTimer = setTimeout(showWarning, WARNING_TIMEOUT);

        // Programar cierre de sesión
        inactivityTimer = setTimeout(logoutDueToInactivity, INACTIVITY_TIMEOUT);
    }

    // Mostrar advertencia de inactividad
    function showWarning() {
        $('#inactivity-warning').show();

        let secondsLeft = (INACTIVITY_TIMEOUT - WARNING_TIMEOUT) / 1000;

        // Actualizar cuenta regresiva cada segundo
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

        // Evitar múltiples llamadas
        if (isLoggingOut) return;

        // Limpiar temporizadores
        clearTimeout(inactivityTimer);
        clearTimeout(warningTimer);

        isLoggingOut = true;
        $('#inactivity-warning').html('<p>Cerrando sesión...</p>').show();

        // Desloguear Usuario
        $.ajax({
            type: 'POST',
            url: '/api/logoutByInactivity',
            success: function (response, textStatus, xhr) {
                window.location.href = response.location;
                window.location.href = '../';
            },
            error: function (xhr, textStatus, errorThrown) {
                const errorMessage = xhr.responseJSON?.message || 'Error during logout';
                toastr.error(errorMessage);
            },
            finally() {
                isLoggingOut = false;
            }
        });
    }

    // Evento para continuar la sesión
    $('#continue-session').click(function () {
        // Registrar actividad
        lastActivityTime = Date.now();

        // Ocultar advertencia
        $('#inactivity-warning').hide();

        // Reiniciar temporizadores
        resetTimers();

        // Opcional: Enviar ping al servidor
        $.get('/api/ping').fail(() => {
            // Si falla el ping, forzar logout
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

    // Iniciar temporizadores
    resetTimers();
})