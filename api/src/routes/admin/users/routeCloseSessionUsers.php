<?php

use tezlikv3\dao\CloseSessionUsersDao;
use tezlikv3\dao\WebTokenDao;

$closeSessionUser = new CloseSessionUsersDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Cerra Sesión usuarios
$app->post('/closeSessionUser/{id}', function (Request $request, Response $response, $args) use (
    $closeSessionUser,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }
    // $dataUser = $request->getParsedBody();
    $session = $closeSessionUser->closeSessionUsers($args);

    if ($session == null) {
        $resp = array('success' => true, 'message' => 'Sesión terminada');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
