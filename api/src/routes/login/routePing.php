<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Middleware\SessionMiddleware;
use App\Helpers\ResponseHelper;
use tezlikv3\dao\LastLoginDao;

/**
 * Endpoint de ping para mantener la sesión activa
 * Este endpoint verifica que la sesión esté activa y actualiza el last_login
 */
$app->get('/ping', function (Request $request, Response $response) {
    // Verificar si hay sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Validar que el usuario esté autenticado
    if (!isset($_SESSION['idUser']) || !isset($_SESSION['active'])) {
        return ResponseHelper::withJson($response, [
            'error' => true,
            'message' => 'No hay sesión activa',
            'reload' => true
        ], 401);
    }

    // Actualizar el timestamp de la sesión
    $_SESSION['time'] = time();

    // Actualizar last_login en la base de datos
    try {
        $lastLoginDao = new LastLoginDao();
        $lastLoginDao->findLastLogin();
    } catch (Exception $e) {
        error_log("Error actualizando last_login en ping: " . $e->getMessage());
        // No es crítico, continuamos
    }

    return ResponseHelper::withJson($response, [
        'success' => true,
        'message' => 'Sesión activa',
        'timestamp' => time(),
        'user_id' => $_SESSION['idUser']
    ], 200);
})->add(new SessionMiddleware());
