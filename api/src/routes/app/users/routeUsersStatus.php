<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\StatusUserDao;

$usersStatusDao = new StatusUserDao();
$autenticationDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Inactivar/Activar Usuario */

$app->get('/inactivateActivateUser/{id_user}', function (Request $request, Response $response, $args) use (
    $usersStatusDao,
    $autenticationDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $users = $usersStatusDao->inactivateActivateUser($args['id_user']);
    if ($users == 0)
        $resp = array('info' => true, 'message' => 'Usuario inactivado correctamente');

    if ($users == 1)
        $resp = array('success' => true, 'message' => 'Usuario activado correctamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
