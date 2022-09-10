<?php

use tezlikv3\dao\PlanProductsDao;
use tezlikv3\dao\UnitSalesDao;
use tezlikv3\dao\ClassificationDao;
use tezlikv3\dao\MinimumStockDao;

$unitSalesDao = new UnitSalesDao();
$productsDao = new PlanProductsDao();
$classificationDao = new ClassificationDao();
$minimumStockDao = new MinimumStockDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/unitSales', function (Request $request, Response $response, $args) use ($unitSalesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $unitSales = $unitSalesDao->findAllSalesByCompany($id_company);
    $response->getBody()->write(json_encode($unitSales, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/unitSalesDataValidation', function (Request $request, Response $response, $args) use ($unitSalesDao, $productsDao) {
    $dataSale = $request->getParsedBody();

    if (isset($dataSale)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $unitSales = $dataSale['importUnitSales'];

        for ($i = 0; $i < sizeof($unitSales); $i++) {

            // Obtener id producto
            $findProduct = $productsDao->findProduct($unitSales[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportUnitSales = array('error' => true, 'message' => "Producto no existe en la base de datos.<br>Fila: {$i}");
                break;
            } else $unitSales[$i]['idProduct'] = $findProduct['id_product'];

            if (
                empty($unitSales[$i]['january']) && empty($unitSales[$i]['february']) && empty($unitSales[$i]['march']) && empty($unitSales[$i]['april']) && empty($unitSales[$i]['may']) && empty($unitSales[$i]['june']) &&
                empty($unitSales[$i]['july']) && empty($unitSales[$i]['august']) && empty($unitSales[$i]['september']) && empty($unitSales[$i]['october']) &&  empty($unitSales[$i]['november']) && empty($unitSales[$i]['december'])
            ) {
                $i = $i + 1;
                $dataImportUnitSales = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findUnitSales = $unitSalesDao->findSales($unitSales[$i], $id_company);
                !$findUnitSales ? $insert = $insert + 1 : $update = $update + 1;

                $dataImportUnitSales['insert'] = $insert;
                $dataImportUnitSales['update'] = $update;
            }
        }
    } else
        $dataImportUnitSales = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportUnitSales, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addUnitSales', function (Request $request, Response $response, $args) use ($unitSalesDao, $productsDao, $classificationDao, $minimumStockDao) {
    session_start();
    $dataSale = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataSales = sizeof($dataSale);

    if ($dataSales > 1) {
        $unitSales = $unitSalesDao->insertSalesByCompany($dataSale, $id_company);

        if ($unitSales == null)
            $resp = array('success' => true, 'message' => 'Venta asociada correctamente');
        else if (isset($unitSales['info']))
            $resp = array('info' => true, 'message' => $unitSales['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $unitSales = $dataSale['importUnitSales'];

        for ($i = 0; $i < sizeof($unitSales); $i++) {

            // Obtener id producto
            $findProduct = $productsDao->findProduct($unitSales[$i], $id_company);
            $unitSales[$i]['idProduct'] = $findProduct['id_product'];

            $findUnitSales = $unitSalesDao->findSales($unitSales[$i], $id_company);
            if (!$findUnitSales)
                $resolution = $unitSalesDao->insertSalesByCompany($unitSales[$i], $id_company);
            else {
                $unitSales[$i]['idSale'] = $findUnitSales['id_unit_sales'];
                $resolution = $unitSalesDao->updateSales($unitSales[$i]);
            }

            // Calcular Clasificación producto
            $unitSales[$i]['cantMonths'] = 3;
            $classification = $classificationDao->calcClassificationByProduct($unitSales[$i], $id_company);

            // Calcular Stock minimo
            $minimumStock = $minimumStockDao->calcMinimumStock($unitSales[$i], $id_company);
        }
        if ($resolution == null && $classification == null && $minimumStock == null)
            $resp = array('success' => true, 'message' => 'Venta importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUnitSale', function (Request $request, Response $response, $args) use ($unitSalesDao) {
    $dataSale = $request->getParsedBody();

    if (empty($dataSale['idSale']) || empty($dataSale['refProduct']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $unitSales = $unitSalesDao->updateSales($dataSale);

        if ($unitSales == null)
            $resp = array('success' => true, 'message' => 'Venta actualizada correctamente');
        else if (isset($unitSales['info']))
            $resp = array('info' => true, 'message' => $unitSales['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteUnitSale/{id_unit_sales}', function (Request $request, Response $response, $args) use ($unitSalesDao) {
    $unitSales = $unitSalesDao->deleteSale($args['id_unit_sales']);

    if ($unitSales == null)
        $resp = array('success' => true, 'message' => 'Venta eliminada correctamente');

    if ($unitSales != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar la Venta, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
