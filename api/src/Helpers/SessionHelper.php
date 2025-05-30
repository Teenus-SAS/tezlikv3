<?php

function validateSession()
{
    // 1. Iniciar sesión PHP si no está activa
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // 2. Validación básica
    if (empty($_SESSION['active'])) {
        // Destruye la sesión inválida
        session_unset();
        session_destroy();

        // Redirección con parámetro para mostrar mensaje
        header('Location: /login?error=session_expired');
        exit();
    }

    // 3. Opcional: Verificar tiempo de sesión
    if (isset($_SESSION['time']) && (time() - $_SESSION['time'] > 1800)) { // 30 minutos
        session_unset();
        session_destroy();
        header('Location: /login?error=session_timeout');
        exit();
    }

    // 4. Renovar tiempo de sesión en cada validación
    $_SESSION['time'] = time();
}
