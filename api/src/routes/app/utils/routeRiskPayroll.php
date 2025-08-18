<?php

use tezlikv3\dao\{RisksDao};

$risksDao = new RisksDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/riskPayroll', function (Request $request, Response $response, $args) use ($risksDao) {
    $risks = $risksDao->findAllRisks();
    $response->getBody()->write(json_encode($risks));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
