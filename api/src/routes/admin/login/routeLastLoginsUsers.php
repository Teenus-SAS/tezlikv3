<?php

use tezlikv2\dao\LastLoginsDao;

$lastLoginsDao = new LastLoginsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Obtener los ultimos login de todos los usuarios activos en orden desc

$app->get('/lastLogins', function (Request $request, Response $response, $args) use ($lastLoginsDao) {

    //DATOS TODAS LAS EMPRESAS
    $resp = $lastLoginsDao->findLastLogins();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
