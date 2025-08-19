<?php

use tezlikv3\Dao\{MagnitudesDao, UnitsDao};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

// Agrupar todas las rutas de units bajo el prefijo '/units'
$app->group('/measurements', function (RouteCollectorProxy $group) {

    $group->get('/magnitudes', function (Request $request, Response $response, $args) {
        $magnitudesDao = new MagnitudesDao();

        $magnitudes = $magnitudesDao->findAllMagnitudes();
        $response->getBody()->write(json_encode($magnitudes));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/units', function (Request $request, Response $response, $args) {
        $unitsDao = new UnitsDao();

        $units = $unitsDao->findAllUnits();
        $response->getBody()->write(json_encode($units));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
