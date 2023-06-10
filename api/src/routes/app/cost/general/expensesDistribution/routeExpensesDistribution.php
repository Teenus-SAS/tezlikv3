<?php

use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\TotalExpenseDao;
use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralExpenseDistributionDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\PriceProductDao;

$expensesDistributionDao = new ExpensesDistributionDao();
$generalExpenseDistributionDao = new GeneralExpenseDistributionDao();
$productsDao = new GeneralProductsDao();
$totalExpenseDao = new TotalExpenseDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();
$generalProductsDao = new GeneralProductsDao();
$familiesDao = new FamiliesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/expensesDistribution', function (Request $request, Response $response, $args) use ($expensesDistributionDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $expensesDistribution = $expensesDistributionDao->findAllExpensesDistributionByCompany($id_company);
    $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionProducts', function (Request $request, Response $response, $args) use ($generalExpenseDistributionDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $generalExpenseDistributionDao->findAllProductsNotInEDistribution($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expenseTotal', function (Request $request, Response $response, $args) use ($totalExpenseDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $totalExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);
    $response->getBody()->write(json_encode($totalExpense, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseDistributionDataValidation', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $totalExpenseDao,
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

            $expenseTotal = $totalExpenseDao->findTotalExpenseByCompany($id_company);

            if (empty($expenseTotal) || !$expenseTotal) {
                $dataImportExpenseDistribution = array('error' => true, 'message' => 'Asigne un gasto primero antes de distribuir');
                break;
            }

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
    $familiesDao,
    $productsDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];
    $dataExpensesDistribution = $request->getParsedBody();

    $dataExpensesDistributions = sizeof($dataExpensesDistribution);

    if ($dataExpensesDistributions > 1) {

        if ($flag == 0) $dataExpensesDistribution['idFamily'] = 0;

        $expensesDistribution = $familiesDao->updateFamilyProduct($dataExpensesDistribution);

        $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($dataExpensesDistribution, $id_company);

        if (!$findExpenseDistribution)
            $expensesDistribution = $expensesDistributionDao->insertExpensesDistributionByCompany($dataExpensesDistribution, $id_company);
        else {
            $dataExpensesDistribution['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
            $expensesDistribution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution, $id_company);
        }

        /* Calcular gasto asignable */
        if ($expensesDistribution == null) {
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

            /* x Familia */
            if ($expensesDistribution == null && $flag == 1) {
                // Consulta unidades vendidades y volumenes de venta por familia
                $unitVol = $assignableExpenseDao->findUnitsVolByFamily($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
                }
            }
        }

        // Calcular Precio del producto
        if ($expensesDistribution == null)
            $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

        if (isset($expensesDistribution['totalPrice']))
            $expensesDistribution = $generalProductsDao->updatePrice($dataExpensesDistribution['refProduct'], $expensesDistribution['totalPrice']);

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
            if ($resolution != null) break;

            /* Calcular gasto asignable */
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

            // Calcular Precio del producto
            if ($resolution != null) break;
            $resolution = $priceProductDao->calcPrice($expensesDistribution[$i]['selectNameProduct']);

            if (!isset($resolution['totalPrice'])) break;

            $resolution = $generalProductsDao->updatePrice($expensesDistribution[$i]['selectNameProduct'], $resolution['totalPrice']);
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
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];
    $dataExpensesDistribution = $request->getParsedBody();

    if (sizeof($dataExpensesDistribution) > 1)
        $resolution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution);
    else {
        $expensesDistribution = $dataExpensesDistribution['data'];
        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
            $resolution = $expensesDistributionDao->updateExpensesDistribution($expensesDistribution[$i]);
            if (isset($resolution['info'])) break;
        }
    }
    // Calcular gasto asignable
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

    /* x Familia */
    if ($resolution == null && $flag == 1) {
        // Consulta unidades vendidades y volumenes de venta por familia
        $unitVol = $assignableExpenseDao->findUnitsVolByFamily($id_company);

        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
        }
    }

    // Calcular Precio del producto
    if ($resolution == null) {
        if (sizeof($dataExpensesDistribution) > 1) {
            $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);
            $resolution = $generalProductsDao->updatePrice($dataExpensesDistribution['refProduct'], $expensesDistribution['totalPrice']);
        } else {
            $expensesDistribution = $dataExpensesDistribution['data'];

            for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
                $arr = $priceProductDao->calcPrice($expensesDistribution[$i]['selectNameProduct']);
                $resolution = $generalProductsDao->updatePrice($expensesDistribution[$i]['selectNameProduct'], $arr['totalPrice']);
                if (isset($resolution['info'])) break;
            }
        }
    }
    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Distribución de gasto actualizada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpensesDistribution', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalProductsDao
) {
    session_start();
    $flag = $_SESSION['flag_expense_distribution'];
    $id_company = $_SESSION['id_company'];
    $dataExpensesDistribution = $request->getParsedBody();

    $expensesDistribution = $expensesDistributionDao->deleteExpensesDistribution($dataExpensesDistribution);
    // Calcular gasto asignable
    if ($expensesDistribution == null) {
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
    /* x Familia */
    if ($resolution == null && $flag == 1) {
        // Consulta unidades vendidades y volumenes de venta por familia
        $unitVol = $assignableExpenseDao->findUnitsVolByFamily($id_company);

        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
        }
    }

    // Calcular Precio del producto
    if ($expensesDistribution == null)
        $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['selectNameProduct']);

    if (isset($expensesDistribution['totalPrice']))
        $expensesDistribution = $generalProductsDao->updatePrice($dataExpensesDistribution['selectNameProduct'], $expensesDistribution['totalPrice']);

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribucion de gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
