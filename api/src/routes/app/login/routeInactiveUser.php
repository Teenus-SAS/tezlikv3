<?php

use tezlikv3\dao\UserInactiveTimeDao;
use tezlikv3\dao\LastLoginDao;
use tezlikv3\dao\StatusActiveUserDao;
use tezlikv3\dao\WebTokenDao;

$userInactiveTimeDao = new UserInactiveTimeDao();
$statusActiveUserDao = new StatusActiveUserDao();
$lastLoginDao = new LastLoginDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Validar session activa */

$app->get('/checkSessionUser', function (Request $request, Response $response, $args) use (
    $userInactiveTimeDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $userInactiveTime = $userInactiveTimeDao->findSession();

    $userInactiveTime == 1 ? $userInactiveTime = 0 : $userInactiveTime = $userInactiveTime['session_active'];

    $response->getBody()->write(json_encode($userInactiveTime));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/checkLastLoginUsers', function (Request $request, Response $response, $args) use (
    $lastLoginDao,
    $webTokenDao
) {
    // $info = $webTokenDao->getToken();

    // if (!is_object($info) && ($info == 1)) {
    //     $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    // }

    // if (is_array($info)) {
    //     $response->getBody()->write(json_encode(['error' => $info['info']]));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    // }

    // $validate = $webTokenDao->validationToken($info);

    // if (!$validate) {
    //     $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    // }

    $users = $lastLoginDao->FindTimeActiveUsers('users');
    $admins = $lastLoginDao->FindTimeActiveUsers('admins');

    if ($users == null && $admins == null)
        $resp = array('success' => true, 'message' => 'Se verificaron los usuarios correctamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/logoutInactiveUser', function (Request $request, Response $response, $args) use (
    $statusActiveUserDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $statusActiveUserDao->changeStatusUserLogin();
    $resp = array('inactive' => true, 'message' => 'Tiempo de inactividad cumplido');
    session_destroy();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
