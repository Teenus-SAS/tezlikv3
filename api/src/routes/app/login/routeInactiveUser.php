<?php

use tezlikv3\dao\UserInactiveTimeDao;
use tezlikv3\dao\LastLoginDao;
use tezlikv3\dao\StatusActiveUserDao;

$userInactiveTimeDao = new UserInactiveTimeDao();
$statusActiveUserDao = new StatusActiveUserDao();
$lastLoginDao = new LastLoginDao();
$statusActiveUserDao = new StatusActiveUserDao();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

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

// Endpoint para ping
$app->get('/ping', function (Request $request, Response $response) {
    // Opcional: Renovar token si es necesario
    return ResponseHelper::withJson($response, ['status' => 'active'], 200);
});


/* Validar session activa */
$app->get('/checkSessionUser', function (Request $request, Response $response, $args) use ($userInactiveTimeDao) {
    // Check user session activity
    $userInactiveTime = $userInactiveTimeDao->findSession();

    // Prepare response data
    $userInactiveTime == 1 ? $userInactiveTime = 0 : $userInactiveTime = $userInactiveTime['session_active'];

    $response->getBody()->write(json_encode($userInactiveTime));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->get('/checkLastLoginUsers', function (Request $request, Response $response, $args) use ($lastLoginDao) {
    $users = $lastLoginDao->FindTimeActiveUsers('users');
    $admins = $lastLoginDao->FindTimeActiveUsers('admins');

    if ($users == null && $admins == null)
        $resp = array('success' => true, 'message' => 'Se verificaron los usuarios correctamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->get('/logoutInactiveUser', function (Request $request, Response $response, $args) use ($statusActiveUserDao) {
    session_start();
    $statusActiveUserDao->changeStatusUserLogin();
    session_destroy();
    $resp = array('inactive' => true, 'message' => 'Tiempo de inactividad cumplido');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->get('/updateToken', function (Request $request, Response $response, $args) {
    session_start();

    $exp = strtotime('+5 minutes');
    $key = $_ENV['jwt_key'];

    $payload = [
        'exp' => $exp,
        'data' => $_SESSION['idUser']
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    $_SESSION['token'] = $jwt;

    $resp = ['success' => true, 'message' => 'Token Actualizado'];

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
