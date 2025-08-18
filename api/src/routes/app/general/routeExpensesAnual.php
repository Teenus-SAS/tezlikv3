<?php

use tezlikv3\dao\{
    AssignableExpenseDao,
    CostMaterialsDao,
    ExpensesAnualDao,
    ExpensesDao,
    ExpensesProductionCenterDao,
    FamiliesDao,
    GeneralCompanyLicenseDao,
    GeneralCompositeProductsDao,
    GeneralPCenterDao,
    GeneralProductsDao,
    LastDataDao,
    LicenseCompanyDao,
    ParticipationExpenseDao,
    PriceProductDao,
    PriceUSDDao,
    ProductionCenterDao,
    PucDao,
    TotalExpenseDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->group('/expensesAnual', function (RouteCollectorProxy $group) {

    $group->get('', function (Request $request, Response $response, $args) {

        $expensesAnualDao = new ExpensesAnualDao();

        $id_company = $_SESSION['id_company'];

        $expenses = $expensesAnualDao->findAllExpensesAnualByCompany($id_company);

        $response->getBody()->write(json_encode($expenses, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());

    /* Consulta todos */
    $group->get('/totalExpenseAnual', function (Request $request, Response $response, $args) {

        $totalExpenseDao = new TotalExpenseDao();

        $id_company = $_SESSION['id_company'];

        // Calcular total del gasto 
        $expense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);

        $response->getBody()->write(json_encode($expense, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());

    $group->post('/expenseAnualDataValidation', function (Request $request, Response $response, $args) {

        $expensesAnualDao = new ExpensesAnualDao();
        $pucDao = new PucDao();

        $dataExpense = $request->getParsedBody();

        if (isset($dataExpense)) {
            // session_start();
            $id_company = $_SESSION['id_company'];

            $insert = 0;
            $update = 0;

            $expense = $dataExpense['importExpense'];

            for ($i = 0; $i < sizeof($expense); $i++) {
                if (
                    empty($expense[$i]['numberCount']) || empty($expense[$i]['count'])
                    //|| empty($expense[$i]['expenseValue'])
                ) {
                    $i = $i + 2;
                    $dataImportExpense = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                    break;
                }

                if (
                    empty(trim($expense[$i]['numberCount'])) || empty(trim($expense[$i]['count']))
                    //|| empty(trim($expense[$i]['expenseValue']))
                ) {
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
                $findExpense = $expensesAnualDao->findExpense($expense[$i], $id_company);
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
    })->add(new SessionMiddleware());

    $group->post('/addExpensesAnual', function (Request $request, Response $response, $args) {

        $expensesAnualDao = new ExpensesAnualDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $pucDao = new PucDao();
        $totalExpenseDao = new TotalExpenseDao();
        $participationExpenseDao = new ParticipationExpenseDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        // $flag = $_SESSION['flag_expense_distribution'];

        $dataExpense = $request->getParsedBody();

        $dataExpenses = sizeof($dataExpense);

        $resolution = null;

        if ($dataExpenses > 1) {
            $expense = $expensesAnualDao->findExpense($dataExpense, $id_company);

            if (!$expense) {
                $resolution = $expensesAnualDao->insertExpensesAnualByCompany($dataExpense, $id_company);

                if ($resolution == null)
                    $resp = array('success' => true, 'message' => 'Gasto creado correctamente');
                else if (isset($resolution['info']))
                    $resp = array('info' => true, 'message' => $resolution['message']);
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

                $findExpense = $expensesAnualDao->findExpense($expense[$i], $id_company);

                if (!$findExpense) {
                    $resolution = $expensesAnualDao->insertExpensesAnualByCompany($expense[$i], $id_company);
                } else {
                    $expense[$i]['idExpense'] = $findExpense['id_expense_anual'];
                    $resolution = $expensesAnualDao->updateExpensesAnual($expense[$i]);
                }
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Gasto importado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
        }

        // Calcular total del gasto
        if ($resolution == null) {
            $expense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);
        }

        /* Calcular gasto asignable */
        if ($resolution == null) {
            $sumExpenseCount = $participationExpenseDao->sumTotalExpenseAnualByNumberCount($id_company);
            $expenseCount = $participationExpenseDao->findAllExpensesAnualByCompany($id_company);

            $resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 2);
            // Consulta unidades vendidades y volumenes de venta por producto
            $unitVol = $assignableExpenseDao->findAllExpensesDistributionAnual($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolAnual($id_company);

            // Obtener el total de gastos
            $data['total_expense'] = $expense['expenses_value'];
            // $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
            }
        }

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());

    $group->post('/updateExpensesAnual', function (Request $request, Response $response, $args) {

        $expensesAnualDao = new ExpensesAnualDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $totalExpenseDao = new TotalExpenseDao();
        $participationExpenseDao = new ParticipationExpenseDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $flag = $_SESSION['flag_expense_distribution'];

        $dataExpense = $request->getParsedBody();

        $data = [];

        $expense = $expensesAnualDao->findExpense($dataExpense, $id_company);

        !is_array($expense) ? $data['id_expense_anual'] = 0 : $data = $expense;

        if ($data['id_expense_anual'] == $dataExpense['idExpense'] || $data['id_expense_anual'] == 0) {
            $resolution = $expensesAnualDao->updateExpensesAnual($dataExpense);

            // Calcular total del gasto
            if ($resolution == null) {
                $expense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);
            }

            // Calcular procentaje de participacion 

            /* Calcular gasto asignable */
            if ($resolution == null) {
                $sumExpenseCount = $participationExpenseDao->sumTotalExpenseAnualByNumberCount($id_company);
                $expenseCount = $participationExpenseDao->findAllExpensesAnualByCompany($id_company);

                $resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 2);

                // Consulta unidades vendidades y volumenes de venta por producto
                $unitVol = $assignableExpenseDao->findAllExpensesDistributionAnual($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolAnual($id_company);

                // Obtener el total de gastos
                $data['total_expense'] = $expense['expenses_value'];

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
                }
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Gasto actualizado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'No. Cuenta duplicada. Ingrese un nuevo No. Cuenta');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());

    $group->get('/deleteExpensesAnual/{id_expense_anual}', function (Request $request, Response $response, $args) {

        $expensesAnualDao = new ExpensesAnualDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $totalExpenseDao = new TotalExpenseDao();
        $participationExpenseDao = new ParticipationExpenseDao();

        $id_company = $_SESSION['id_company'];
        $resolution = $expensesAnualDao->deleteExpensesAnual($args['id_expense_anual']);

        if ($resolution == null) {
            $sumExpenseCount = $participationExpenseDao->sumTotalExpenseAnualByNumberCount($id_company);
            $expenseCount = $participationExpenseDao->findAllExpensesAnualByCompany($id_company);
            $resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 2);
        }

        // Calcular total del gasto
        if ($resolution == null) {
            $expense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);
        }

        /* Calcular gasto asignable */
        if ($resolution == null) {
            // Consulta unidades vendidades y volumenes de venta por producto
            $unitVol = $assignableExpenseDao->findAllExpensesDistributionAnual($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolAnual($id_company);

            // Obtener el total de gastos
            $data['total_expense'] = $expense['expenses_value'];

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');
        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new SessionMiddleware());
})->add(new SessionMiddleware());
