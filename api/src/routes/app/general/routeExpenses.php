<?php

use tezlikv3\dao\{
    AssignableExpenseDao,
    CalcRecoveryExpensesDao,
    ExpensesDao,
    ExpensesProductionCenterDao,
    FamiliesDao,
    GeneralCompanyLicenseDao,
    GeneralPCenterDao,
    GeneralProductsDao,
    LastDataDao,
    ParticipationExpenseDao,
    PriceProductDao,
    PriceUSDDao,
    ProductionCenterDao,
    PucDao,
    TotalExpenseDao
};

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Middleware\SessionMiddleware;
use App\Helpers\ResponseHelper;
use Slim\Routing\RouteCollectorProxy;

$app->group('/expenses', function (RouteCollectorProxy $group) {

    /* Consulta todos */
    $group->get('', function (Request $request, Response $response, $args) {

        $expensesDao = new ExpensesDao();
        $expensesProductionCenterDao = new ExpensesProductionCenterDao();

        $id_company = $_SESSION['id_company'];

        if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1)
            $expenses = $expensesProductionCenterDao->findAllExpensesByCompany($id_company);
        else
            $expenses = $expensesDao->findAllExpensesByCompany($id_company);

        return ResponseHelper::withJson($response, $expenses, 200);
    });

    /* $group->get('/checkTypeExpense', function (Request $request, Response $response, $args) {

        $licenseCompanyDao = new LicenseCompanyDao();

        $id_company = $_SESSION['id_company'];
        $typeExpense = $licenseCompanyDao->findLicenseCompany($id_company);
        return ResponseHelper::withJson($response, $typeExpense, 200);
    }); */

    $group->get('/changeTypeExpense/{flag}', function (Request $request, Response $response, $args) {

        $generalCompanyLicenseDao = new GeneralCompanyLicenseDao();

        $id_company = $_SESSION['id_company'];
        $typeExpense = $generalCompanyLicenseDao->updateFlagExpense($args['flag'], $id_company);

        if ($args['flag'] == 2)
            $_SESSION['expense'] = 0;

        $_SESSION['flag_expense'] = $args['flag'];

        if ($typeExpense == null)
            $resp = array('success' => true, 'message' => 'Se selecciono el tipo gasto correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');

        return ResponseHelper::withJson($response, $resp, 200);
    });

    /* Consulta todos */
    $group->get('/totalExpense', function (Request $request, Response $response, $args) {

        $totalExpenseDao = new TotalExpenseDao();

        $id_company = $_SESSION['id_company'];

        // Calcular total del gasto
        if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1)
            $expense = $totalExpenseDao->calcTotalCPExpenseByCompany($id_company);
        else
            $expense = $totalExpenseDao->calcTotalExpenseByCompany($id_company);

        $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);

        if (!$findExpense)
            $totalExpenseDao->insertTotalExpense($expense, $id_company);
        else
            $totalExpenseDao->updateTotalExpense($expense, $id_company);

        $response->getBody()->write(json_encode(1, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/expenseDataValidation', function (Request $request, Response $response, $args) {
        $expensesDao = new ExpensesDao();
        $pucDao = new PucDao();
        $generalPCenterDao = new GeneralPCenterDao();
        $expensesProductionCenterDao = new ExpensesProductionCenterDao();

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

                // Obtener centro de produccion
                if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                    if (
                        $expense[$i]['production'] == '' || trim($expense[$i]['production']) == ''
                    ) {
                        $i = $i + 2;
                        $dataImportExpense = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                        break;
                    }

                    $findProduction = $generalPCenterDao->findPCenter($expense[$i], $id_company);
                    if (!$findProduction) {
                        $i = $i + 2;
                        $dataImportExpense = array('error' => true, 'message' => "Centro de produccion no existe en la base de datos<br>Fila: {$i}");
                        break;
                    } else $expense[$i]['production'] = $findProduction['id_production_center'];
                    //RETORNA id_expense CON idPuc Y id_company
                    $findExpense = $expensesDao->findExpense($expense[$i], $id_company);
                    //SI NO RETORNA id_expense $insert + 1
                    if (!$findExpense) $insert = $insert + 1;
                    else {
                        $expense[$i]['idExpense'] = $findExpense['id_expense'];

                        $findExpense = $expensesProductionCenterDao->findExpense($expense[$i], $id_company);

                        if (!$findExpense) $insert = $insert + 1;
                        //SI RETORNA id_expense $update + 1
                        $update = $update + 1;
                    }
                    $dataImportExpense['insert'] = $insert;
                    $dataImportExpense['update'] = $update;
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

    $group->post('/addExpenses', function (Request $request, Response $response, $args) {

        $expensesDao = new ExpensesDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $pucDao = new PucDao();
        $totalExpenseDao = new TotalExpenseDao();
        $participationExpenseDao = new ParticipationExpenseDao();
        $familiesDao = new FamiliesDao();
        $generalProductsDao = new GeneralProductsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $productionCenterDao = new ProductionCenterDao();
        $generalPCenterDao = new GeneralPCenterDao();
        $expensesProductionCenterDao = new ExpensesProductionCenterDao();
        $lastDataDao = new LastDataDao();
        $calcRecoveryExpenses = new CalcRecoveryExpensesDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $flag = $_SESSION['flag_expense_distribution'];

        $dataExpense = $request->getParsedBody();

        $dataExpenses = sizeof($dataExpense);

        $resolution = null;

        if ($dataExpenses > 1) {
            $expense = $expensesDao->findExpense($dataExpense, $id_company);

            if (!$expense) {
                $resolution = $expensesDao->insertExpensesByCompany($dataExpense, $id_company);

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

                $findExpense = $expensesDao->findExpense($expense[$i], $id_company);

                if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                    $findProduction = $generalPCenterDao->findPCenter($expense[$i], $id_company);
                    $expense[$i]['production'] = $findProduction['id_production_center'];
                } else
                    $expense[$i]['production'] = 0;

                if (!$findExpense) {
                    $resolution = $expensesDao->insertExpensesByCompany($expense[$i], $id_company);
                    $findExpense = $lastDataDao->findLastInsertedExpense($id_company);

                    $dataExpense['idExpense'] = $findExpense['id_expense'];
                } else {
                    $expense[$i]['idExpense'] = $findExpense['id_expense'];
                    $resolution = $expensesDao->updateExpenses($expense[$i]);
                }

                if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                    $findExpense = $expensesProductionCenterDao->findExpense($expense[$i]);

                    if (!$findExpense)
                        $resolution = $expensesProductionCenterDao->insertExpensesByCompany($expense[$i], $id_company);
                    else {
                        $expense[$i]['idExpenseProductionCenter'] = $findExpense['id_expense_product_center'];
                        $resolution = $expensesProductionCenterDao->updateExpenses($expense[$i], $id_company);
                    }
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
            $expense = $totalExpenseDao->calcTotalExpenseByCompany($id_company);

            $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);

            if (!$findExpense)
                $resolution = $totalExpenseDao->insertTotalExpense($expense, $id_company);
            else
                $resolution = $totalExpenseDao->updateTotalExpense($expense, $id_company);
        }

        /* Calcular gasto asignable */
        if ($resolution == null) {
            if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCountCP($id_company);
                $expenseCount = $participationExpenseDao->findAllExpensesByCompanyCP($id_company);

                //$resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 1);

                $productions = $productionCenterDao->findAllPCenterByCompany($id_company);

                for ($i = 0; $i < sizeof($productions); $i++) {
                    // Consulta unidades vendidades y volumenes de venta por producto
                    $unitVol = $assignableExpenseDao->findAllExpensesDistributionByProduction($productions[$i]['id_production_center']);

                    // Calcular el total de unidades vendidas y volumen de ventas
                    $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByProduction($productions[$i]['id_production_center']);

                    // Obtener el total de gastos
                    $expense = $assignableExpenseDao->findTotalExpenseByProduction($productions[$i]['id_production_center']);
                    $data['total_expense'] = $expense['expenses_value'];

                    foreach ($unitVol as $arr) {
                        if (isset($resolution['info'])) break;
                        // Calcular gasto asignable
                        $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                        // Actualizar gasto asignable
                        $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                    }
                }
            } else {
                $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCount($id_company);
                $expenseCount = $participationExpenseDao->findAllExpensesByCompany($id_company);

                //$resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 1);
                // Consulta unidades vendidades y volumenes de venta por producto
                $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

                // Obtener el total de gastos
                $data['total_expense'] = $expense['expenses_value'];

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                }
            }
        }

        /* x Familia */
        if ($resolution == null && $flag == 2) {
            // Consulta unidades vendidades y volumenes de venta por familia
            $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
            }
        }

        if ($resolution == null) {
            $products = $generalProductsDao->findAllProducts($id_company);

            for ($i = 0; $i < sizeof($products); $i++) {
                if ($resolution == null)
                    $product = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

                if (isset($product['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $product['totalPrice']);
                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $product['totalPrice'];
                    $k['sale_price'] = $product['sale_price'];
                    $k['id_product'] = $products[$i]['selectNameProduct'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }
            }
        }

        //Calcular el porcentaje de recuperacion
        if ($flag === 1 && $id_company === 1) { // Distribucion por recuperacion
            $sales = $totalExpenseDao->findTotalRevenuesByCompany($id_company);
            $products = $generalProductsDao->findAllProductsToRecovery($id_company);
            $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);
            $calcRecoveryExpenses->calculateAndStore($products, $sales['expenses_value'], $findExpense['total_expense'], $id_company);
        }

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/updateExpenses', function (Request $request, Response $response, $args) {

        $expensesDao = new ExpensesDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $totalExpenseDao = new TotalExpenseDao();
        $participationExpenseDao = new ParticipationExpenseDao();
        $familiesDao = new FamiliesDao();
        $generalProductsDao = new GeneralProductsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $productionCenterDao = new ProductionCenterDao();
        $expensesProductionCenterDao = new ExpensesProductionCenterDao();
        $calcRecoveryExpenses = new CalcRecoveryExpensesDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $flag = $_SESSION['flag_expense_distribution'];

        $dataExpense = $request->getParsedBody();

        $data = [];

        try {
            $expense = $expensesDao->findExpense($dataExpense, $id_company);

            if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1)
                $expense = $expensesProductionCenterDao->findExpense($dataExpense);

            !is_array($expense) ? $data['id_expense'] = 0 : $data = $expense;

            $resolution = $expensesDao->updateExpenses($dataExpense);

            if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                $findExpense = $expensesProductionCenterDao->findExpense($dataExpense);

                if (!$findExpense)
                    $resolution = $expensesProductionCenterDao->insertExpensesByCompany($dataExpense, $id_company);
                else {
                    $resolution = $expensesProductionCenterDao->updateExpenses($dataExpense, $id_company);
                }
            }
            // Calcular total del gasto
            if ($resolution == null) {
                $expense = $totalExpenseDao->calcTotalExpenseByCompany($id_company);

                $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);

                if (!$findExpense)
                    $resolution = $totalExpenseDao->insertTotalExpense($expense, $id_company);
                else
                    $resolution = $totalExpenseDao->updateTotalExpense($expense, $id_company);
            }

            // Calcular procentaje de participacion 

            /* Calcular gasto asignable */
            if ($resolution == null) {
                if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                    // Calcular procentaje de participacion 
                    $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCountCP($id_company);
                    $expenseCount = $participationExpenseDao->findAllExpensesByCompanyCP($id_company);

                    //$resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 1);

                    $productions = $productionCenterDao->findAllPCenterByCompany($id_company);

                    for ($i = 0; $i < sizeof($productions); $i++) {
                        // Consulta unidades vendidades y volumenes de venta por producto
                        $unitVol = $assignableExpenseDao->findAllExpensesDistributionByProduction($productions[$i]['id_production_center']);

                        // Calcular el total de unidades vendidas y volumen de ventas
                        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByProduction($productions[$i]['id_production_center']);

                        // Obtener el total de gastos
                        $expense = $assignableExpenseDao->findTotalExpenseByProduction($productions[$i]['id_production_center']);
                        $data['total_expense'] = $expense['expenses_value'];

                        foreach ($unitVol as $arr) {
                            if (isset($resolution['info'])) break;
                            // Calcular gasto asignable
                            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                            // Actualizar gasto asignable
                            $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                        }
                    }
                } else {
                    $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCount($id_company);
                    $expenseCount = $participationExpenseDao->findAllExpensesByCompany($id_company);

                    //$resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 1);

                    // Consulta unidades vendidades y volumenes de venta por producto
                    $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

                    // Calcular el total de unidades vendidas y volumen de ventas
                    $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

                    // Obtener el total de gastos
                    $data['total_expense'] = $expense['expenses_value'];
                    // $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

                    foreach ($unitVol as $arr) {
                        if (isset($resolution['info'])) break;
                        // Calcular gasto asignable
                        $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                        // Actualizar gasto asignable
                        $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                    }
                }
            }

            /* x Familia */
            if ($resolution == null && $flag == 2) {
                // Consulta unidades vendidades y volumenes de venta por familia
                $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
                }
            }

            if ($resolution == null) {
                $products = $generalProductsDao->findAllProducts($id_company);

                for ($i = 0; $i < sizeof($products); $i++) {
                    if (isset($resolution['info'])) break;
                    $product = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

                    if (isset($product['totalPrice']))
                        $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $product['totalPrice']);
                    if (isset($resolution['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $product['totalPrice'];
                        $k['sale_price'] = $product['sale_price'];
                        $k['id_product'] = $products[$i]['selectNameProduct'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
                }
            }

            //Calcular el porcentaje 
            if ($flag === 1 && $id_company === 1) { // Distribucion por recuperacion
                $sales = $totalExpenseDao->findTotalRevenuesByCompany($id_company);
                $products = $generalProductsDao->findAllProductsToRecovery($id_company);
                $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);
                $calcRecoveryExpenses->calculateAndStore($products, $sales['expenses_value'], $findExpense['total_expense'], $id_company);
                $priceProductDao->calcPriceByCompany($id_company);
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Gasto actualizado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');


            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            /* if ($connection->inTransaction()) {
            $connection->rollBack();
        } */
            error_log("Error en /updateExpenses: " . $e->getMessage());
            return ResponseHelper::withJson($response, ['error' => 'Internal Server Error'], 500);
        }
    });

    $group->get('/deleteExpenses/{id_expense}/{op}', function (Request $request, Response $response, $args) {

        $expensesDao = new ExpensesDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $totalExpenseDao = new TotalExpenseDao();
        $participationExpenseDao = new ParticipationExpenseDao();
        $familiesDao = new FamiliesDao();
        $generalProductsDao = new GeneralProductsDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $productionCenterDao = new ProductionCenterDao();
        $expensesProductionCenterDao = new ExpensesProductionCenterDao();
        $calcRecoveryExpenses = new CalcRecoveryExpensesDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $flag = $_SESSION['flag_expense_distribution'];

        if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1 && $args['op'] == 2) {
            $resolution = $expensesProductionCenterDao->deleteExpenses($args['id_expense']);
        } else
            $resolution = $expensesDao->deleteExpenses($args['id_expense']);

        if ($resolution == null) {
            if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCountCP($id_company);
                $expenseCount = $participationExpenseDao->findAllExpensesByCompanyCP($id_company);
            } else {
                $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCount($id_company);
                $expenseCount = $participationExpenseDao->findAllExpensesByCompany($id_company);
            }
            //$resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 1);
        }

        // Calcular total del gasto
        if ($resolution == null) {
            $expense = $totalExpenseDao->calcTotalExpenseByCompany($id_company);

            $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);

            if (!$findExpense)
                $resolution = $totalExpenseDao->insertTotalExpense($expense, $id_company);
            else
                $resolution = $totalExpenseDao->updateTotalExpense($expense, $id_company);
        }

        /* Calcular gasto asignable */
        if ($resolution == null) {
            if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
                $productions = $productionCenterDao->findAllPCenterByCompany($id_company);

                for ($i = 0; $i < sizeof($productions); $i++) {
                    // Consulta unidades vendidades y volumenes de venta por producto
                    $unitVol = $assignableExpenseDao->findAllExpensesDistributionByProduction($productions[$i]['id_production_center']);

                    // Calcular el total de unidades vendidas y volumen de ventas
                    $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByProduction($productions[$i]['id_production_center']);

                    // Obtener el total de gastos
                    $expense = $assignableExpenseDao->findTotalExpenseByProduction($productions[$i]['id_production_center']);
                    $data['total_expense'] = $expense['expenses_value'];

                    foreach ($unitVol as $arr) {
                        if (isset($resolution['info'])) break;
                        // Calcular gasto asignable
                        $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                        // Actualizar gasto asignable
                        $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                    }
                }
            } else {
                // Consulta unidades vendidades y volumenes de venta por producto
                $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

                // Obtener el total de gastos
                $data['total_expense'] = $expense['expenses_value'];
                // $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                }
            }

            /* x Familia */
            if ($resolution == null && $flag == 2) {
                // Consulta unidades vendidades y volumenes de venta por familia
                $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

                // Calcular el total de unidades vendidas y volumen de ventas
                $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

                foreach ($unitVol as $arr) {
                    if (isset($resolution['info'])) break;
                    // Calcular gasto asignable
                    $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
                    // Actualizar gasto asignable
                    $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
                }
            }
        }

        if ($resolution == null) {
            $products = $generalProductsDao->findAllProducts($id_company);

            for ($i = 0; $i < sizeof($products); $i++) {
                if ($resolution == null)
                    $product = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

                if (isset($product['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $product['totalPrice']);
                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $product['totalPrice'];
                    $k['sale_price'] = $product['sale_price'];
                    $k['id_product'] = $products[$i]['selectNameProduct'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }
            }
        }

        //Calcular el porcentaje de recuperacion
        if ($flag === 1 && $id_company === 1) { // Distribucion por recuperacion
            $sales = $totalExpenseDao->findTotalRevenuesByCompany($id_company);
            $products = $generalProductsDao->findAllProductsToRecovery($id_company);
            $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);
            $calcRecoveryExpenses->calculateAndStore($products, $sales['expenses_value'], $findExpense['total_expense'], $id_company);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');
        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
