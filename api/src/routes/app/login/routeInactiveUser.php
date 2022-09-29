<?php

use tezlikv3\dao\UserInactiveTimeDao;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$userInactiveTimeDao = new UserInactiveTimeDao();

/* Validar session activa */

$app->get('/checkSessionUser', function (Request $request, Response $response, $args) use ($userInactiveTimeDao) {
    $userInactiveTime = $userInactiveTimeDao->findSession();

    if ($userInactiveTime == null)
        $resp = array('active' => true);
    else
        $resp = array('inactive' => true);

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
