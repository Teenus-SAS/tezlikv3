<?php

use tezlikv3\dao\{BenefitsDao};

$benefitsDao = new BenefitsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/benefitsPayroll', function (Request $request, Response $response, $args) use ($benefitsDao) {
    $benefits = $benefitsDao->findAllBenefits();
    $response->getBody()->write(json_encode($benefits));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
