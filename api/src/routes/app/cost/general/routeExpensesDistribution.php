<?php

use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\TotalExpenseDao;
use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\GeneralCostProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\PriceProductDao;

$expensesDistributionDao = new ExpensesDistributionDao();
$productsDao = new GeneralProductsDao();
$totalExpenseDao = new TotalExpenseDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();
$generalCostProductsDao = new GeneralCostProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/expensesDistribution', function (Request $request, Response $response, $args) use ($expensesDistributionDao) {
    $expensesDistribution = $expensesDistributionDao->findAllExpensesDistributionByCompany();
    $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expenseTotal', function (Request $request, Response $response, $args) use ($totalExpenseDao) {
    $totalExpense = $totalExpenseDao->findTotalExpenseByCompany();
    $response->getBody()->write(json_encode($totalExpense, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseDistributionDataValidation', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $productsDao
) {
    $dataExpensesDistribution = $request->getParsedBody();

    if (isset($dataExpensesDistribution)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $expensesDistribution = $dataExpensesDistribution['importExpense'];

        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
            if (empty($expensesDistribution[$i]['unitsSold']) || empty($expensesDistribution[$i]['turnover'])) {
                $i = $i + 1;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($expensesDistribution[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila{$i}");
                break;
            } else $expensesDistribution[$i]['selectNameProduct'] = $findProduct['id_product'];

            $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($expensesDistribution[$i], $id_company);
            if (!$findExpenseDistribution) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportExpenseDistribution['insert'] = $insert;
            $dataImportExpenseDistribution['update'] = $update;
        }
    } else
        $dataImportExpenseDistribution = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExpenseDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExpensesDistribution', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $productsDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    $dataExpensesDistributions = sizeof($dataExpensesDistribution);

    if ($dataExpensesDistributions > 1) {
        $expensesDistribution = $expensesDistributionDao->insertExpensesDistributionByCompany($dataExpensesDistribution, $id_company);
        /* Calcular gasto asignable */
        if ($expensesDistribution == null)
            $expensesDistribution = $assignableExpenseDao->calcAssignableExpense($id_company);

        // Calcular Precio del producto
        if ($expensesDistribution == null)
            $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

        if (isset($expensesDistribution['totalPrice']))
            $expensesDistribution = $generalCostProductsDao->updatePrice($dataExpensesDistribution['refProduct'], $expensesDistribution['totalPrice']);

        if ($expensesDistribution == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto asignado correctamente');
        else if (isset($expensesDistribution['info']))
            $resp = array('info' => true, 'message' => $expensesDistribution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    } else {
        $expensesDistribution = $dataExpensesDistribution['importExpense'];

        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($expensesDistribution[$i], $id_company);
            $expensesDistribution[$i]['selectNameProduct'] = $findProduct['id_product'];

            $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($expensesDistribution[$i], $id_company);
            if (!$findExpenseDistribution)
                $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($expensesDistribution[$i], $id_company);
            else {
                $expensesDistribution[$i]['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                $resolution = $expensesDistributionDao->updateExpensesDistribution($expensesDistribution[$i]);
            }
            /* Calcular gasto asignable */
            if ($resolution != null) break;
            $resolution = $assignableExpenseDao->calcAssignableExpense($id_company);

            // Calcular Precio del producto
            if ($resolution != null) break;
            $resolution = $priceProductDao->calcPrice($expensesDistribution[$i]['selectNameProduct']);

            if ($resolution != null) break;

            $resolution = $generalCostProductsDao->updatePrice($expensesDistribution[$i]['selectNameProduct'], $resolution['totalPrice']);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpensesDistribution', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    $expensesDistribution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution);
    // Calcular gasto asignable
    if ($expensesDistribution == null)
        $expensesDistribution = $assignableExpenseDao->calcAssignableExpense($id_company);

    // Calcular Precio del producto
    if ($expensesDistribution == null)
        $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

    if (isset($expensesDistribution['totalPrice']))
        $expensesDistribution = $generalCostProductsDao->updatePrice($dataExpensesDistribution['refProduct'], $expensesDistribution['totalPrice']);

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribución de gasto actualizada correctamente');
    else if (isset($expensesDistribution['info']))
        $resp = array('info' => true, 'message' => $expensesDistribution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpensesDistribution', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    $expensesDistribution = $expensesDistributionDao->deleteExpensesDistribution($dataExpensesDistribution);
    // Calcular gasto asignable
    if ($expensesDistribution == null)
        $expensesDistribution = $assignableExpenseDao->calcAssignableExpense($id_company);

    // Calcular Precio del producto
    if ($expensesDistribution == null)
        $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['selectNameProduct']);

    if (isset($expensesDistribution['totalPrice']))
        $expensesDistribution = $generalCostProductsDao->updatePrice($dataExpensesDistribution['selectNameProduct'], $expensesDistribution['totalPrice']);

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribucion de gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
