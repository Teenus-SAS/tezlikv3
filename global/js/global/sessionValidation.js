// Función robusta para obtener cookies
window.getCookie = function (name) {
    try {
        // Intenta primero con document.cookie
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();

        // Si falla, intenta con localStorage como respaldo
        const storedToken = localStorage.getItem('authToken');
        if (storedToken) return storedToken;

        return null;
    } catch (e) {
        console.error('Error reading cookie:', e);
        return null;
    }
}

// Verificación mejorada al cargar la página
$(document).ready(function () {
    const token = getCookie('auth_token');
    const currentPath = window.location.pathname;
    const isLoginPage = currentPath.includes('login') ||
        currentPath.endsWith('/') ||
        currentPath === '/index.html';

    if (!token && !isLoginPage) {
        // Guardar la página actual para redirigir después del login
        sessionStorage.setItem('redirectUrl', window.location.href);
        window.location.href = '../';
    }
});