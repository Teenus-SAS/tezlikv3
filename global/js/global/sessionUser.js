$(document).ready(function () {
  // Variable para almacenar la última ruta visitada
  let lastVisitedRoute = window.location.pathname;

  // Configuración del temporizador de inactividad
  (function () {
    const IDLE_TIMEOUT = 5 * 60 * 1000; // 5 minutos en milisegundos
    let idleCounter = 0;

    // Función para actualizar el token en el servidor
    async function updateToken() {
      await $.get('/api/updateToken');
      idleCounter = 0;
    }

    // Reinicia el contador y llama a la función de actualización cada 5 minutos
    function resetIdleCounter() {
      idleCounter = 0;
    }

    // Evento para detectar interacción del usuario
    document.addEventListener('mousemove', resetIdleCounter);
    document.addEventListener('keypress', resetIdleCounter);

    // Verifica el estado de inactividad y actualiza el token cada 5 minutos
    setInterval(function () {
      if (++idleCounter >= IDLE_TIMEOUT / 1000) {
        updateToken();
        idleCounter = 0; // Reinicia el contador después de actualizar el token
      }
    }, 1000); // Revisar cada segundo
  })();

  // Función para realizar solicitudes AJAX
  async function getApi(url) {
    try { 
      const response = await fetch(url);
      if (response.ok) {
        return await response.json();
      } else {
        return 0;
      }
    } catch (error) {
      // console.error('Error fetching data:', error);
      return 0;
    }
  }

  // Función para verificar la sesión del usuario
  async function checkSession() {
    const data = await getApi('/api/checkSessionUser'); 
    if (data.reload) {
      location.reload();
    }
    // debugger
    if (data === 0) {
      location.href = '/';
    }
  }
  checkSession();

  // Función para desloguear al usuario
  async function fetchindata() {
    // Verifica si el usuario sigue en la misma ruta
    if (window.location.pathname === lastVisitedRoute) {
      // Solo ejecuta el logout si sigue en la misma ruta
      const resp = await getApi('/api/logoutInactiveUser');
      
      if (resp.reload) {
        location.reload();
      };
      
      if (resp && resp.inactive) {
        location.href = '/';
        toastr.error(resp.message);
      }
    }
  }

  // Manejar el evento beforeunload para asegurar el logout al cerrar la pestaña
  window.addEventListener('beforeunload', async function (e) {
    // Ejecutar fetchindata antes de cerrar la pestaña
    await fetchindata();
  });

  // Actualiza la última ruta visitada cuando cambia la URL
  window.addEventListener('popstate', function() {
    lastVisitedRoute = window.location.pathname;
  });

  // Actualiza la última ruta visitada cuando se hace clic en un enlace
  $(document).on('click', 'a', function() {
    lastVisitedRoute = $(this).attr('href');
  });
});
