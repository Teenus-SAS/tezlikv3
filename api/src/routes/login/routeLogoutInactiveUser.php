<?php

use tezlikv3\dao\UserInactiveTimeDao;
use tezlikv3\dao\LastLoginDao;
use tezlikv3\dao\StatusActiveUserDao;

$statusActiveUserDao = new StatusActiveUserDao();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;

// Endpoint para logout por timeout
$app->post('/logoutByInactivity', function (Request $request, Response $response) use ($statusActiveUserDao) {
    // Verificar si hay sesión activa

    if (session_status() === PHP_SESSION_NONE) {
        session_start();

        $id_user = $_SESSION['idUser'];
        $id_company = $_SESSION['id_company'];

        // Cambiar estado en BD
        $statusActiveUserDao->deactivateSession($id_company, $id_user);

        // Destruir sesión
        session_unset();
        session_destroy();
    }

    // Limpiar cookie
    setcookie('auth_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true
    ]);

    return ResponseHelper::withJson($response, [
        'status' => 'success',
        'message' => 'Sesión cerrada por inactividad',
        'location' => '/login'
    ], 200);
});
