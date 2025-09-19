<?php

use tezlikv3\dao\{UserInactiveTimeDao, LastLoginDao, StatusActiveUserDao};

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Validar session activa */

$app->group('/userSession', function (RouteCollectorProxy $group) {

    $group->get('/checkSessionUser', function (Request $request, Response $response, $args) {

        $userInactiveTimeDao = new UserInactiveTimeDao();

        // Check user session activity
        $userInactiveTime = $userInactiveTimeDao->findSession();

        // Prepare response data
        $userInactiveTime == 1 ? $userInactiveTime = 0 : $userInactiveTime = $userInactiveTime['session_active'];

        $response->getBody()->write(json_encode($userInactiveTime));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/checkLastLoginUsers', function (Request $request, Response $response, $args) {

        $lastLoginDao = new LastLoginDao();

        $users = $lastLoginDao->FindTimeActiveUsers('users');
        $admins = $lastLoginDao->FindTimeActiveUsers('admins');

        if ($users == null && $admins == null)
            $resp = array('success' => true, 'message' => 'Se verificaron los usuarios correctamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/logoutInactiveUser', function (Request $request, Response $response, $args) {

        $statusActiveUserDao = new StatusActiveUserDao();

        session_start();
        $statusActiveUserDao->changeStatusUserLogin();
        session_destroy();
        $resp = array('inactive' => true, 'message' => 'Tiempo de inactividad cumplido');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/updateToken', function (Request $request, Response $response, $args) {

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
    });
})->add(new SessionMiddleware());
