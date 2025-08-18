<?php

use tezlikv3\dao\ReportCostDao;

$reportCostDao = new ReportCostDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/reports', function (RouteCollectorProxy $group) use ($reportCostDao) {

    $group->get('/generalCostReport', function (Request $request, Response $response, $args) use ($reportCostDao) {
        $id_company = $_SESSION['id_company'];

        $data = $reportCostDao->findAllCostByCompany($id_company);

        $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/processCostReport', function (Request $request, Response $response, $args) use ($reportCostDao) {

        $id_company = $_SESSION['id_company'];

        $data = $reportCostDao->findAllCostWorkforceByCompany($id_company);

        $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
