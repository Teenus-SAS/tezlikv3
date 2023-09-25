<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\ExpensesDao;
use tezlikv3\dao\FlagCompanyDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\ParticipationExpenseDao;
use tezlikv3\dao\PucDao;
use tezlikv3\dao\TotalExpenseDao;

$expensesDao = new ExpensesDao();
$assignableExpenseDao = new AssignableExpenseDao();
$pucDao = new PucDao();
$totalExpenseDao = new TotalExpenseDao();
$licenseCompanyDao = new LicenseCompanyDao();
$flagCompanyDao = new FlagCompanyDao();
$participationExpenseDao = new ParticipationExpenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/checkTypeExpense', function (Request $request, Response $response, $args) use ($licenseCompanyDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $typeExpense = $licenseCompanyDao->findLicenseCompany($id_company);
    $response->getBody()->write(json_encode($typeExpense, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeTypeExpense/{flag}', function (Request $request, Response $response, $args) use ($licenseCompanyDao, $flagCompanyDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $typeExpense = $flagCompanyDao->updateFlagExpense($args['flag'], $id_company);

    if ($args['flag'] == 2) {
        $_SESSION['expense'] = 0;
        $_SESSION['flag_expense'] = 2;
    }

    if ($typeExpense == null)
        $resp = array('success' => true, 'message' => 'Se selecciono el tipo gasto correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});


/* Consulta todos */
$app->get('/expenses', function (Request $request, Response $response, $args) use ($expensesDao) {
    $expenses = $expensesDao->findAllExpensesByCompany();
    $response->getBody()->write(json_encode($expenses, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consulta todos */
$app->get('/totalExpense', function (Request $request, Response $response, $args) use ($totalExpenseDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Calcular total del gasto
    $totalExpenseDao->insertUpdateTotalExpense($id_company);
    $response->getBody()->write(json_encode(1, JSON_NUMERIC_CHECK));
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
            if (empty(trim($expense[$i]['numberCount'])) || empty(trim($expense[$i]['count'])) || empty(trim($expense[$i]['expenseValue']))) {
                $i = $i + 2;
                $dataImportExpense = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }
            // Obtener id cuenta
            $findPuc = $pucDao->findPuc($expense[$i]);
            if (!$findPuc) {
                $i = $i + 2;
                $dataImportExpense = array('error' => true, 'message' => "Cuenta no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $expense[$i]['idPuc'] = $findPuc['id_puc'];

            //RETORNA id_expense CON idPuc Y id_company
            $findExpense = $expensesDao->findExpense($expense[$i], $id_company);
            //SI NO RETORNA id_expense $insert + 1
            if (!$findExpense) $insert = $insert + 1;
            //SI RETORNA id_expense $update + 1
            else $update = $update + 1;
            $dataImportExpense['insert'] = $insert;
            $dataImportExpense['update'] = $update;
        }
    } else
        $dataImportExpense = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExpense, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExpenses', function (Request $request, Response $response, $args) use (
    $expensesDao,
    $assignableExpenseDao,
    $pucDao,
    $totalExpenseDao,
    $participationExpenseDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expense = $expensesDao->findExpense($dataExpense, $id_company);

        if (!$expense) {
            $expenses = $expensesDao->insertExpensesByCompany($dataExpense, $id_company);

            /* Calcular gasto asignable */
            if ($expenses == null) {
                // Consulta unidades vendidades y volumenes de venta por producto
                $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

                // Obtener el total de gastos
                $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

                foreach ($unitVol as $arr) {
                    if (isset($expenses['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                    // Actualizar gasto asignable
                    $expenses = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                }
            }

            if ($expenses == null)
                $resp = array('success' => true, 'message' => 'Gasto creado correctamente');
            else if (isset($expenses['info']))
                $resp = array('info' => true, 'message' => $expenses['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'No. Cuenta duplicada. Ingrese un nuevo No. Cuenta');
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

            /* Calcular gasto asignable */
            if ($resolution == null) {
                // Consulta unidades vendidades y volumenes de venta por producto
                $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

                // Obtener el total de gastos
                $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Gasto importado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    // Calcular total del gasto
    $totalExpenseDao->insertUpdateTotalExpense($id_company);

    // Calcular procentaje de participacion
    $participationExpenseDao->calcParticipationExpense($id_company);

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpenses', function (Request $request, Response $response, $args) use (
    $expensesDao,
    $assignableExpenseDao,
    $totalExpenseDao,
    $participationExpenseDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpense = $request->getParsedBody();

    $data = [];

    $expense = $expensesDao->findExpense($dataExpense, $id_company);

    !is_array($expense) ? $data['id_expense'] = 0 : $data = $expense;

    if ($data['id_expense'] == $dataExpense['idExpense'] || $data['id_expense'] == 0) {
        $expenses = $expensesDao->updateExpenses($dataExpense);

        // Calcular total del gasto
        $totalExpense = $totalExpenseDao->insertUpdateTotalExpense($id_company);

        /* Calcular gasto asignable */
        if ($expenses == null && $totalExpense == null) {
            // Consulta unidades vendidades y volumenes de venta por producto
            $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

            // Obtener el total de gastos
            $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

            foreach ($unitVol as $arr) {
                if (isset($expenses['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                // Actualizar gasto asignable
                $expenses = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
            }
        }

        // Calcular procentaje de participacion
        $participationExpenseDao->calcParticipationExpense($id_company);

        if ($expenses == null)
            $resp = array('success' => true, 'message' => 'Gasto actualizado correctamente');
        else if (isset($expenses['info']))
            $resp = array('info' => true, 'message' => $expenses['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'No. Cuenta duplicada. Ingrese un nuevo No. Cuenta');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteExpenses/{id_expense}', function (Request $request, Response $response, $args) use ($expensesDao, $totalExpenseDao, $participationExpenseDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $expenses = $expensesDao->deleteExpenses($args['id_expense']);

    $participationExpenseDao->calcParticipationExpense($id_company);


    // Calcular total del gasto
    $totalExpense = $totalExpenseDao->insertUpdateTotalExpense($id_company);

    if ($expenses == null && $totalExpense == null)
        $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
