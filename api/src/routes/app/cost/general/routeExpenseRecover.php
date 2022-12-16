<?php

use tezlikv3\dao\ExpenseRecoverDao;

$expenseRecoverDao = new ExpenseRecoverDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/expensesRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $expensesRecover = $expenseRecoverDao->findAllExpenseRecoverByCompany($id_company);
    $response->getBody()->write(json_encode($expensesRecover, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExpenseRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataExpenses = $request->getParsedBody();

    $expensesRecover = $expenseRecoverDao->insertRecoverExpenseByCompany($dataExpenses, $id_company);

    if ($expensesRecover == null)
        $resp = array('success' => true, 'message' => 'Gasto guardado correctamente');
    else if (isset($expensesRecover['info']))
        $resp = array('info' => true, 'message' => $expensesRecover['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpenseRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    $dataExpenses = $request->getParsedBody();

    if (empty($dataExpenses['idExpenseRecover']) || $dataExpenses['percentage'] < 0)
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $expensesRecover = $expenseRecoverDao->updateRecoverExpense($dataExpenses);

        if ($expensesRecover == null)
            $resp = array('success' => true, 'message' => 'Gasto modificado correctamente');
        else if (isset($expensesRecover['info']))
            $resp = array('info' => true, 'message' => $expensesRecover['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpenseRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    $dataExpenses = $request->getParsedBody();

    $expensesRecover = $expenseRecoverDao->deleteRecoverExpense($dataExpenses['idExpenseRecover']);

    if ($expensesRecover == null)
        $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
    else if (isset($expensesRecover['info']))
        $resp = array('info' => true, 'message' => $expensesRecover['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
