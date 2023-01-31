<?php

use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\ProductsDao;

$expenseRecoverDao = new ExpenseRecoverDao();
$productsDao = new ProductsDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/expenseRecoverProducts', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $expenseRecoverDao->findAllProducts($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $expensesRecover = $expenseRecoverDao->findAllExpenseRecoverByCompany($id_company);
    $response->getBody()->write(json_encode($expensesRecover, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseRecoverDataValidation', function (Request $request, Response $response, $args) use ($expenseRecoverDao, $productsDao) {
    $dataExpense = $request->getParsedBody();

    if (isset($dataExpense)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $expensesRecover = $dataExpense['importExpense'];

        for ($i = 0; $i < sizeof($expensesRecover); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($expensesRecover[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportExpenseRecover = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila{$i}");
                break;
            } else $expensesRecover[$i]['idProduct'] = $findProduct['id_product'];

            if (empty($expensesRecover[$i]['percentage'])) {
                $i = $i + 1;
                $dataImportExpenseRecover = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                $expenseRecover = $expenseRecoverDao->findExpenseRecover($expensesRecover[$i], $id_company);
                if (!$expenseRecover) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportExpenseRecover['insert'] = $insert;
                $dataImportExpenseRecover['update'] = $update;
            }
        }
    } else
        $dataImportExpenseRecover = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExpenseRecover, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExpenseRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao, $productsDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expensesRecover = $expenseRecoverDao->insertRecoverExpenseByCompany($dataExpense, $id_company);

        $priceProduct = $priceProductDao->calcPrice($dataExpense['idProduct']);

        if ($expensesRecover == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Gasto guardado correctamente');
        else if (isset($expensesRecover['info']))
            $resp = array('info' => true, 'message' => $expensesRecover['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else {
        $expensesRecover = $dataExpense['importExpense'];

        for ($i = 0; $i < sizeof($expensesRecover); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($expensesRecover[$i], $id_company);
            $expensesRecover[$i]['idProduct'] = $findProduct['id_product'];

            $expenseRecover = $expenseRecoverDao->findExpenseRecover($expensesRecover[$i], $id_company);
            if (!$expenseRecover)
                $resolution = $expenseRecoverDao->insertRecoverExpenseByCompany($expensesRecover[$i], $id_company);
            else {
                $expensesRecover[$i]['idExpenseRecover'] = $expenseRecover['id_expense_recover'];
                $resolution = $expenseRecoverDao->updateRecoverExpense($expensesRecover[$i]);
            }

            $priceProduct = $priceProductDao->calcPrice($expenseRecover[$i]['idProduct']);
        }

        if ($resolution == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Recuperación de gasto importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpenseRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao, $priceProductDao) {
    $dataExpense = $request->getParsedBody();

    if (empty($dataExpense['idExpenseRecover']) || $dataExpense['percentage'] < 0)
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $expensesRecover = $expenseRecoverDao->updateRecoverExpense($dataExpense);

        $priceProduct = $priceProductDao->calcPrice($dataExpense['idProduct']);

        if ($expensesRecover == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Gasto modificado correctamente');
        else if (isset($expensesRecover['info']))
            $resp = array('info' => true, 'message' => $expensesRecover['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpenseRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao, $priceProductDao) {
    $dataExpense = $request->getParsedBody();

    $expensesRecover = $expenseRecoverDao->deleteRecoverExpense($dataExpense['idExpenseRecover']);

    $priceProduct = $priceProductDao->calcPrice($dataExpense['idProduct']);

    if ($expensesRecover == null && $priceProduct == null)
        $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
    else if (isset($expensesRecover['info']))
        $resp = array('info' => true, 'message' => $expensesRecover['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
