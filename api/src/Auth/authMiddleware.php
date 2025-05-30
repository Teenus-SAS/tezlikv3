<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    // Asegúrate de que la sesión está iniciada de todas formas
    if (!isset($_SESSION)) session_start();
}

// Verifica sesión o token (esto va fuera del if de arriba)
if (!isset($_SESSION['user']) && empty($_COOKIE['auth_token'])) {
    header('Location: /');
    exit();
}
