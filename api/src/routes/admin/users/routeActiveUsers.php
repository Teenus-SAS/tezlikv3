<?php

use tezlikv3\dao\ActiveUsersDao;
use tezlikv3\dao\LastLoginUsersDao;

$activeUsersDao = new ActiveUsersDao();
$lastLogUsersDao = new LastLoginUsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/lastLoginUsers', function (Request $request, Response $response, $args) use ($lastLogUsersDao) {
    //NÚMERO DE USUARIOS ACTIVOS GENERAL
    $resp = $lastLogUsersDao->loginUsers();

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
