<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralExpenseDistributionDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\PriceProductDao;

$familiesDao = new FamiliesDao();
$generalExpenseDistributionDao = new GeneralExpenseDistributionDao();
$priceProductDao = new PriceProductDao();
$assignableExpenseDao = new AssignableExpenseDao();
$generalProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/families', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $families = $familiesDao->findAllFamiliesByCompany($id_company);
    $response->getBody()->write(json_encode($families, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionFamilies', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $expensesDistribution = $familiesDao->findAllExpensesDistributionByCompany($id_company);
    $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionFamilies/{id_family}', function (Request $request, Response $response, $args) use ($generalExpenseDistributionDao) {
    $expensesDistribution = $generalExpenseDistributionDao->findAllExpensesDistributionByFamily($args['id_family']);
    $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productsFamilies', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $familiesDao->findAllProductsFamiliesByCompany($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionFamiliesProducts', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $familiesDao->findAllProductsInFamily(0, $id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeTypeExpenseDistribution/{flag}', function (Request $request, Response $response, $args) use ($familiesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $typeExpense = $familiesDao->updateFlagFamily($args['flag'], $id_company);

    $_SESSION['flag_expense_distribution'] = $args['flag'];

    if ($typeExpense == null)
        $resp = array('success' => true, 'message' => 'Se selecciono el tipo de distribucion correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addFamily', function (Request $request, Response $response, $args) use (
    $familiesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataFamily = $request->getParsedBody();

    $findFamily = $familiesDao->findFamily($dataFamily, $id_company);

    if (!$findFamily) {
        $families = $familiesDao->insertFamilyByCompany($dataFamily, $id_company);

        if ($families == null)
            $resp = array('success' => true, 'message' => 'Familia agregada correctamente');
        else if (isset($families['info']))
            $resp = array('info' => true, 'message' => $families['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Familia ya existente. Ingrese un nueva familia');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveProductFamily', function (Request $request, Response $response, $args) use ($familiesDao) {
    $dataFamily = $request->getParsedBody();

    $resolution = $familiesDao->updateFamilyProduct($dataFamily);

    if ($resolution == null && $dataFamily['idFamily'] != 0)
        $resp = array('success' => true, 'message' => 'Producto asignado a la familia correctamente');
    else if ($resolution == null && $dataFamily['idFamily'] == 0)
        $resp = array('success' => true, 'message' => 'Producto eliminado de la familia correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateFamily', function (Request $request, Response $response, $args) use ($familiesDao) {
    $dataFamily = $request->getParsedBody();

    $resolution = $familiesDao->updateFamily($dataFamily);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Familia modificada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteFamily/{id_family}', function (Request $request, Response $response, $args) use ($familiesDao, $generalExpenseDistributionDao) {
    $expensesDistribution = $generalExpenseDistributionDao->findAllExpensesDistributionByFamily($args['id_family']);

    if (!$expensesDistribution) {
        $resolution = $familiesDao->deleteFamily($args['id_family']);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Familia eliminada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('info' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Familia asociada a productos no se puede eliminar');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteExpensesDistributionFamily/{id_family}', function (Request $request, Response $response, $args) use (
    $familiesDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $data['idFamily'] = $args['id_family'];
    $data['unitsSold'] = 0;
    $data['turnover'] = 0;

    $expensesDistribution = $familiesDao->updateDistributionFamily($data);
    $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($args['id_family'], 0);

    /* Calcular gasto asignable x Familia */
    // Consulta unidades vendidades y volumenes de venta por familia
    $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

    // Calcular el total de unidades vendidas y volumen de ventas
    $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

    // Obtener el total de gastos
    $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

    foreach ($unitVol as $arr) {
        if (isset($resolution['info'])) break;
        // Calcular gasto asignable
        $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
        // Actualizar gasto asignable
        $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
    }

    $products = $familiesDao->findAllProductsInFamily($args['id_family'], $id_company);

    // Calcular Precio del producto
    for ($i = 0; $i < sizeof($products); $i++) {
        $products[$i]['selectNameProduct'] = $products[$i]['id_product'];

        if ($expensesDistribution == null)
            $expensesDistribution = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

        if (isset($expensesDistribution['totalPrice']))
            $expensesDistribution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $expensesDistribution['totalPrice']);

        if (isset($expensesDistribution['info'])) break;
    }

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribucion de gasto x familia eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});