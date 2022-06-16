<?php

use tezlikv3\dao\DashboardProductsDao;
use tezlikv3\dao\DashboardGeneralDao;

$dashboardProductsDao = new DashboardProductsDao();
$dashboardGeneralDao = new DashboardGeneralDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardPricesProducts/{id_product}', function (Request $request, Response $response, $args) use ($dashboardProductsDao, $dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Consultar analisis de costos por producto
    $costAnalysisProducts = $dashboardProductsDao->findCostAnalysisByProduct($args['id_product'], $id_company);

    /* Consultar Proceso del producto */
    $totalTimeProcess = $dashboardProductsDao->findProductProcessByProduct($args['id_product'], $id_company);

    // Consultar Costo Mano de obra por producto
    $costWorkforce = $dashboardProductsDao->findCostWorkforceByProduct($args['id_product'], $id_company);

    // Consultar Costo Materia prima por producto
    $costRawMaterials = $dashboardProductsDao->findCostRawMaterialsByProduct($args['id_product'], $id_company);

    // Consultar promedio de tiempos procesos
    $averageTimeProcess = $dashboardGeneralDao->findAverageTimeProcessByCompany($id_company);

    /* Creacion de arrays */

    $costProduct['cost_product'] = $costAnalysisProducts;
    $costProduct['cost_time_process'] = $totalTimeProcess;
    $costProduct['cost_workforce'] = $costWorkforce;
    $costProduct['cost_materials'] = $costRawMaterials;
    $costProduct['average_time_process'] = $averageTimeProcess;

    $response->getBody()->write(json_encode($costProduct, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
