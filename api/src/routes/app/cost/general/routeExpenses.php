<?php

use tezlikv3\dao\ExpensesDao;
use tezlikv3\dao\PucDao;
use tezlikv3\dao\TotalExpenseDao;

$expensesDao = new ExpensesDao();
$pucDao = new PucDao();
$totalExpenseDao = new TotalExpenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/expenses', function (Request $request, Response $response, $args) use ($expensesDao) {
    $expenses = $expensesDao->findAllExpensesByCompany();
    $response->getBody()->write(json_encode($expenses, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseDataValidation', function (Request $request, Response $response, $args) use ($expensesDao, $pucDao) {
    $dataExpense = $request->getParsedBody();

    if (isset($dataExpense)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $expense = $dataExpense['importExpense'];

        for ($i = 0; $i < sizeof($expense); $i++) {
            // Obtener id cuenta
            $findPuc = $pucDao->findPuc($expense[$i]);
            if (!$findPuc) {
                $i = $i + 1;
                $dataImportExpense = array('error' => true, 'message' => "Cuenta no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $expense[$i]['idPuc'] = $findPuc['id_puc'];

            $numberCount = $expense[$i]['numberCount'];
            $count = $expense[$i]['count'];
            $expenseValue = $expense[$i]['expenseValue'];
            if (empty($numberCount) || empty($count) || empty($expenseValue)) {
                $i = $i + 1;
                $dataImportExpense = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                //RETORNA id_expense CON idPuc Y id_company
                $findExpense = $expensesDao->findExpense($expense[$i], $id_company);
                //SI NO RETORNA id_expense $insert + 1
                if (!$findExpense) $insert = $insert + 1;
                //SI RETORNA id_expense $update + 1
                else $update = $update + 1;
                $dataImportExpense['insert'] = $insert;
                $dataImportExpense['update'] = $update;
            }
        }
    } else
        $dataImportExpense = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExpense, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExpenses', function (Request $request, Response $response, $args) use ($expensesDao, $pucDao, $totalExpenseDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expenses = $expensesDao->insertExpensesByCompany($dataExpense, $id_company);

        // Calcular total del gasto
        //$totalExpense = $totalExpenseDao->insertUpdateTotalExpense($id_company);

        if ($expenses == null)
            $resp = array('success' => true, 'message' => 'Gasto creado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    } else {
        $expense = $dataExpense['importExpense'];

        for ($i = 0; $i < sizeof($expense); $i++) {
            // Obtener id cuenta
            $findPuc = $pucDao->findPuc($expense[$i]);
            $expense[$i]['idPuc'] = $findPuc['id_puc'];

            $findExpense = $expensesDao->findExpense($expense[$i], $id_company);
            if (!$findExpense)
                $resolution = $expensesDao->insertExpensesByCompany($expense[$i], $id_company);
            else {
                $expense[$i]['idExpense'] = $findExpense['id_expense'];
                $resolution = $expensesDao->updateExpenses($expense[$i]);
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Gasto importado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    // Calcular total del gasto
    $totalExpenseDao->insertUpdateTotalExpense($id_company);

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpenses', function (Request $request, Response $response, $args) use ($expensesDao, $totalExpenseDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpense = $request->getParsedBody();

    if (empty($dataExpense['idPuc']) || empty($dataExpense['expenseValue']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $expenses = $expensesDao->updateExpenses($dataExpense);

        // Calcular total del gasto
        $totalExpense = $totalExpenseDao->insertUpdateTotalExpense($id_company);

        if ($expenses == null && $totalExpense == null)
            $resp = array('success' => true, 'message' => 'Gasto actualizado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteExpenses/{id_expense}', function (Request $request, Response $response, $args) use ($expensesDao, $totalExpenseDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $expenses = $expensesDao->deleteExpenses($args['id_expense']);

    // Calcular total del gasto
    $totalExpense = $totalExpenseDao->insertUpdateTotalExpense($id_company);

    if ($expenses == null && $totalExpense == null)
        $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
