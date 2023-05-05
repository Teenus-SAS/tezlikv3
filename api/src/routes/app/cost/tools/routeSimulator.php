<?php

use tezlikv3\dao\DashboardProductsDao;

$dashboardProductsDao = new DashboardProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardPricesSimulator/{id_product}', function (Request $request, Response $response, $args) use (
    $dashboardProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Consultar analisis de costos por producto
    $costAnalysisProducts = $dashboardProductsDao->findCostAnalysisByProduct($args['id_product'], $id_company);
    // Consultar Costo Materia prima por producto
    $costRawMaterials = $dashboardProductsDao->findCostRawMaterialsByProduct($args['id_product'], $id_company);
    // Consultar Ficha tecnica Proceso del producto
    $totalTimeProcess = $dashboardProductsDao->findProductProcessByProduct($args['id_product'], $id_company);

    $data['products'] = $costAnalysisProducts;
    $data['materials'] = $costRawMaterials;
    $data['productsProcess'] = $totalTimeProcess;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
