<?php

use tezlikv3\dao\ActiveUsersDao;
use tezlikv3\dao\LastLoginUsersDao;
use tezlikv3\dao\WebTokenDao;

$activeUsersDao = new ActiveUsersDao();
$lastLogUsersDao = new LastLoginUsersDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/lastLoginUsers', function (Request $request, Response $response, $args) use (
    $lastLogUsersDao,
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

    //NÚMERO DE USUARIOS ACTIVOS GENERAL
    $resp = $lastLogUsersDao->loginUsers();

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
