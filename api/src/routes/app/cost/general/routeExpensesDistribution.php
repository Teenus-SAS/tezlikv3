<?php

use tezlikv2\dao\ExpensesDistributionDao;
use tezlikv2\dao\ProductsDao;
use tezlikv2\dao\TotalExpenseDao;
use tezlikv2\dao\AssignableExpenseDao;
use tezlikv2\dao\PriceProductDao;

$expensesDistributionDao = new ExpensesDistributionDao();
$productsDao = new ProductsDao();
$totalExpenseDao = new TotalExpenseDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();

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

$app->post('/expenseDistributionDataValidation', function (Request $request, Response $response, $args) use ($expensesDistributionDao, $productsDao) {
    $dataExpensesDistribution = $request->getParsedBody();

    if (isset($dataExpensesDistribution)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $expensesDistribution = $dataExpensesDistribution['importExpenseDistribution'];

        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($expensesDistribution[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila{$i}");
                break;
            } else $expensesDistribution[$i]['selectNameProduct'] = $findProduct['id_product'];

            $unitsSold = $expensesDistribution[$i]['unitsSold'];
            $turnover = $expensesDistribution[$i]['turnover'];
            if (empty($unitsSold) || empty($turnover)) {
                $i = $i + 1;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($expensesDistribution[$i], $id_company);
                if (!$findExpenseDistribution) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportExpenseDistribution['insert'] = $insert;
                $dataImportExpenseDistribution['update'] = $update;
            }
        }
    } else
        $dataImportExpenseDistribution = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExpenseDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExpensesDistribution', function (Request $request, Response $response, $args) use ($expensesDistributionDao, $productsDao, $assignableExpenseDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    $dataExpensesDistributions = sizeof($dataExpensesDistribution);

    if ($dataExpensesDistributions > 1) {
        $expensesDistribution = $expensesDistributionDao->insertExpensesDistributionByCompany($dataExpensesDistribution, $id_company);
        /* Calcular gasto asignable
        $assignableExpense = $assignableExpenseDao->calcAssignableExpense($id_company);*/
        // Calcular Precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

        if ($expensesDistribution == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto asignado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    } else {
        $expensesDistribution = $dataExpensesDistribution['importExpenseDistribution'];

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
            // Calcular Precio del producto
            $priceProduct = $priceProductDao->calcPrice($expensesDistribution[$i]['selectNameProduct']);
        }
        /* Calcular gasto asignable
        $assignableExpense = $assignableExpenseDao->calcAssignableExpense($id_company);*/

        if ($resolution == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    // Calcular gasto asignable
    $assignableExpenseDao->calcAssignableExpense($id_company);

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpensesDistribution', function (Request $request, Response $response, $args) use ($expensesDistributionDao, $assignableExpenseDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    if (empty($dataExpensesDistribution['selectNameProduct']) || empty($dataExpensesDistribution['unitsSold']) || empty($dataExpensesDistribution['turnover']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $expensesDistribution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution);
        // Calcular gasto asignable
        $assignableExpense = $assignableExpenseDao->calcAssignableExpense($id_company);

        // Calcular Precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

        if ($expensesDistribution == null && $assignableExpense == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto actualizada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpensesDistribution', function (Request $request, Response $response, $args) use ($expensesDistributionDao, $assignableExpenseDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    $expensesDistribution = $expensesDistributionDao->deleteExpensesDistribution($dataExpensesDistribution);
    // Calcular gasto asignable
    $assignableExpense = $assignableExpenseDao->calcAssignableExpense($id_company);

    // Calcular Precio del producto
    $priceProduct = $priceProductDao->calcPrice($dataExpensesDistribution['selectNameProduct']);

    if ($expensesDistribution == null && $assignableExpense == null && $priceProduct == null)
        $resp = array('success' => true, 'message' => 'Distribucion de gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
