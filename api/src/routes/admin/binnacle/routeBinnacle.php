<?php

use tezlikv3\Dao\BinnacleDao;

$binnacleDao = new BinnacleDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/binnacle', function (Request $request, Response $response, $args) use ($binnacleDao) {
    $binnacle = $binnacleDao->findAllBinnacle();
    $response->getBody()->write(json_encode($binnacle));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
