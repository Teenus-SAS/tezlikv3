<?php

use tezlikv3\dao\CompositeProductsDao;
use tezlikv3\dao\DashboardProductsDao;



$dashboardProductsDao = new DashboardProductsDao();
$compositeProductsDao = new CompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/dashboardPricesProducts/{id_product}', function (Request $request, Response $response, $args) use ($dashboardProductsDao, $compositeProductsDao) {
    // session_start();
    $id_company = $_SESSION['id_company'];
    $id_product = $args['id_product'];

    $products = $compositeProductsDao->findAllCompositeProductsByIdProduct($args['id_product'], $id_company);

    if (!$products) {
        $op = 1;
    } else {
        $op = 2;
    }

    // Consultar analisis de costos por producto
    $costAnalysisProducts = $dashboardProductsDao->findCostAnalysisByProduct($args['id_product'], $id_company);

    /* Consultar Proceso del producto */
    $totalTimeProcess = $dashboardProductsDao->findProductProcessByProduct($id_product, $id_company, 1);

    // Consultar Costo Mano de obra por producto
    $costWorkforce = $dashboardProductsDao->findCostWorkforceByProduct($id_product, $id_company, 1);

    // Consultar Costo Materia prima por producto
    $costRawMaterials = $dashboardProductsDao->findCostRawMaterialsByProduct($id_product, $id_company, $op);

    // Consultar promedio de tiempos procesos
    $averageTimeProcess = $dashboardProductsDao->findAverageTimeProcessByProduct($id_product, $id_company, 1);

    /* Creacion de arrays */
    $costProduct['cost_product'] = $costAnalysisProducts;
    $costProduct['cost_time_process'] = $totalTimeProcess;
    $costProduct['cost_workforce'] = $costWorkforce;
    $costProduct['cost_materials'] = $costRawMaterials;
    $costProduct['average_time_process'] = $averageTimeProcess;

    $response->getBody()->write(json_encode($costProduct, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
