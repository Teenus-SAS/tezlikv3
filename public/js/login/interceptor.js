// Interceptor global para fetch
const originalFetch = window.fetch;
window.fetch = function (...args) {
    return originalFetch.apply(this, args)
        .then(response => {
            // Verificar si la respuesta es 401
            if (response.status === 401) {
                handleSessionExpired(response);
            }
            return response;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            throw error;
        });
};

// Función para manejar sesión expirada
async function handleSessionExpired(response) {
    try {
        const data = await response.json();

        if (data.reload) {
            // Mostrar mensaje antes de cerrar/recargar
            if (data.message) {
                alert(data.message); // O usar toastr/bootbox
            }

            // Cerrar la pestaña o recargar
            closeOrReloadTab();
        }
    } catch (e) {
        console.error('Error processing 401 response:', e);
        closeOrReloadTab();
    }
}

function closeOrReloadTab() {
    // Intentar cerrar la pestaña
    if (window.history.length > 1) {
        window.close(); // Solo funciona si la pestaña fue abierta por JS
    }

    // Si no se puede cerrar, redirigir al login
    if (!window.closed) {
        window.location.href = '/'; // Ajusta la ruta según tu aplicación
    }
}