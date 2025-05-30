<?php

use tezlikv3\dao\LastDataDao;

$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;


//Obtener los ultimos login de todos los usuarios activos en orden desc

$app->get('/lastLogins', function (Request $request, Response $response, $args) use ($lastDataDao) {
    //todas las empresas
    $resp = $lastDataDao->findLastLogins();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
