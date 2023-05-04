<?php

use tezlikv3\dao\DashboardProductsDao;

$dashboardProductsDao = new DashboardProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardPricesSimulator/{id_product}', function (Request $request, Response $response, $args) use ($dashboardProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Consultar analisis de costos por producto
    $costAnalysisProducts = $dashboardProductsDao->findCostAnalysisByProduct($args['id_product'], $id_company);

    $response->getBody()->write(json_encode($costAnalysisProducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
