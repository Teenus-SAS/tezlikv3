<?php

use tezlikv3\dao\UserInactiveTimeDao;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use tezlikv3\dao\StatusActiveUserDao;

$userInactiveTimeDao = new UserInactiveTimeDao();
$statusActiveUserDao = new StatusActiveUserDao();

/* Validar session activa */

$app->get('/checkSessionUser', function (Request $request, Response $response, $args) use ($userInactiveTimeDao, $statusActiveUserDao) {
    session_start();
    $userInactiveTime = $userInactiveTimeDao->findSession();

    // if ($userInactiveTime == null)
    //     $resp = array('active' => true);
    // else {
    //     $resp = array('inactive' => true, 'message' => 'El tiempo de logueo se ha cumplido');
    //     $statusActiveUserDao->changeStatusUserLogin();
    // }

    $response->getBody()->write(json_encode($userInactiveTime['session_active']));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/logoutInactiveUser', function (Request $request, Response $response, $args) use ($statusActiveUserDao) {
    session_start();
    $statusActiveUserDao->changeStatusUserLogin();
    $resp = array('inactive' => true, 'message' => 'Tiempo de inactividad cumplido');
    session_destroy();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
