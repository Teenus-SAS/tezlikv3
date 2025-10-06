<?php

use tezlikv3\dao\{UserInactiveTimeDao, LastLoginDao, StatusActiveUserDao};

$statusActiveUserDao = new StatusActiveUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;

/**
 * Endpoint para logout por timeout/inactividad
 * MEJORADO: Ahora con mejor manejo de sesiones y prevención de múltiples llamadas
 */
$app->post('/logoutByInactivity', function (Request $request, Response $response) use ($statusActiveUserDao) {

    // Headers para evitar cache
    $response = $response
        ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withHeader('Pragma', 'no-cache');

    $sessionWasActive = false;
    $userId = null;
    $companyId = null;

    // Verificar si hay sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Capturar datos antes de destruir
    if (isset($_SESSION['idUser']) && isset($_SESSION['id_company'])) {
        $sessionWasActive = true;
        $userId = $_SESSION['idUser'];
        $companyId = $_SESSION['id_company'];

        try {
            // Cambiar estado en BD ANTES de destruir la sesión
            $statusActiveUserDao->deactivateSession($companyId, $userId);
            error_log("Sesión desactivada para usuario: $userId, empresa: $companyId");
        } catch (Exception $e) {
            error_log("Error al desactivar sesión en BD: " . $e->getMessage());
            // Continuamos con el logout aunque falle la BD
        }

        // Destruir sesión PHP
        session_unset();
        session_destroy();
    }

    // Limpiar cookie de autenticación
    $cookieParams = [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ];

    setcookie('auth_token', '', $cookieParams);

    // Respuesta
    return ResponseHelper::withJson($response, [
        'success' => true,
        'message' => 'Sesión cerrada por inactividad',
        'location' => '/',
        'reload' => true,
        'session_was_active' => $sessionWasActive
    ], 200);
});
