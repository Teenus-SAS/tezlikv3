<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\ExpensesAnualDao;
use tezlikv3\dao\ExpensesDao;
use tezlikv3\dao\ExpensesProductionCenterDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralCompanyLicenseDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralPCenterDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\ParticipationExpenseDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductionCenterDao;
use tezlikv3\dao\PucDao;
use tezlikv3\dao\TotalExpenseDao;
use tezlikv3\dao\WebTokenDao;

$expensesAnualDao = new ExpensesAnualDao();
$expensesDao = new ExpensesDao();
$assignableExpenseDao = new AssignableExpenseDao();
$webTokenDao = new WebTokenDao();
$pucDao = new PucDao();
$totalExpenseDao = new TotalExpenseDao();
$licenseCompanyDao = new LicenseCompanyDao();
$generalCompanyLicenseDao = new GeneralCompanyLicenseDao();
$participationExpenseDao = new ParticipationExpenseDao();
$familiesDao = new FamiliesDao();
$generalProductsDao = new GeneralProductsDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$productionCenterDao = new ProductionCenterDao();
$generalPCenterDao = new GeneralPCenterDao();
$expensesProductionCenterDao = new ExpensesProductionCenterDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/expensesAnual', function (Request $request, Response $response, $args) use (
    $expensesAnualDao,
    $webTokenDao,
    $expensesProductionCenterDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $expenses = $expensesAnualDao->findAllExpensesAnualByCompany($id_company);

    $response->getBody()->write(json_encode($expenses, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consulta todos */
$app->get('/totalExpenseAnual', function (Request $request, Response $response, $args) use (
    $totalExpenseDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    // Calcular total del gasto 
    $expense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);

    // $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);

    // if (!$findExpense)
    //     $totalExpenseDao->insertTotalExpense($expense, $id_company);
    // else
    //     $totalExpenseDao->updateTotalExpense($expense, $id_company);

    $response->getBody()->write(json_encode($expense, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseAnualDataValidation', function (Request $request, Response $response, $args) use (
    $expensesAnualDao,
    $webTokenDao,
    $generalPCenterDao,
    $pucDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

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
});

$app->post('/addExpensesAnual', function (Request $request, Response $response, $args) use (
    $expensesAnualDao,
    $webTokenDao,
    $assignableExpenseDao,
    $pucDao,
    $familiesDao,
    $generalProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $totalExpenseDao,
    $participationExpenseDao,
    $productionCenterDao,
    $generalPCenterDao,
    $expensesProductionCenterDao,
    $lastDataDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    // $flag = $_SESSION['flag_expense_distribution'];

    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    $resolution = null;

    if ($dataExpenses > 1) {
        // if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
        //     $expense = $expensesDao->findExpense($dataExpense, $id_company);

        //     if ($expense) {
        //         $dataExpense['idExpense'] = $expense['id_expense'];
        //         $expense = $expensesProductionCenterDao->findExpense($dataExpense, $id_company);
        //     }
        // } else
        $expense = $expensesAnualDao->findExpense($dataExpense, $id_company);

        if (!$expense) {
            $resolution = $expensesAnualDao->insertExpensesAnualByCompany($dataExpense, $id_company);

            // if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
            //     $findExpense = $lastDataDao->findLastInsertedExpense($id_company);

            //     $dataExpense['idExpense'] = $findExpense['id_expense'];

            //     $resolution = $expensesProductionCenterDao->insertExpensesByCompany($dataExpense, $id_company);
            // }

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

    // if ($resolution == null) {
    //     $products = $generalProductsDao->findAllProducts($id_company);

    //     for ($i = 0; $i < sizeof($products); $i++) {
    //         if ($resolution == null)
    //             $product = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

    //         if (isset($product['totalPrice']))
    //             $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $product['totalPrice']);
    //         if (isset($resolution['info'])) break;
    //         // Convertir a Dolares 
    //         $k = [];
    //         $k['price'] = $product['totalPrice'];
    //         $k['sale_price'] = $product['sale_price'];
    //         $k['id_product'] = $products[$i]['selectNameProduct'];

    //         $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

    //         if ($_SESSION['flag_composite_product'] == '1') {
    //             if (isset($resolution['info'])) break;

    //             // Calcular costo material
    //             $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['selectNameProduct']);

    //             foreach ($productsCompositer as $arr) {
    //                 if (isset($resolution['info'])) break;

    //                 $data = [];
    //                 $data['idProduct'] = $arr['id_product'];
    //                 $data['compositeProduct'] = $arr['id_child_product'];

    //                 $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
    //                 $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

    //                 if (isset($resolution['info'])) break;
    //                 $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
    //                 $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

    //                 if (isset($resolution['info'])) break;

    //                 $data = $priceProductDao->calcPrice($arr['id_product']);

    //                 if (isset($data['totalPrice']))
    //                     $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

    //                 if (isset($resolution['info'])) break;

    //                 // Convertir a Dolares 
    //                 $k = [];
    //                 $k['price'] = $data['totalPrice'];
    //                 $k['sale_price'] = $data['sale_price'];
    //                 $k['id_product'] = $arr['id_product'];

    //                 $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

    //                 if (isset($resolution['info'])) break;

    //                 $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

    //                 foreach ($productsCompositer2 as $j) {
    //                     if (isset($resolution['info'])) break;

    //                     $data = [];
    //                     $data['compositeProduct'] = $j['id_child_product'];
    //                     $data['idProduct'] = $j['id_product'];

    //                     $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
    //                     $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

    //                     if (isset($resolution['info'])) break;
    //                     $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
    //                     $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

    //                     if (isset($resolution['info'])) break;

    //                     $data = $priceProductDao->calcPrice($j['id_product']);

    //                     if (isset($data['totalPrice']))
    //                         $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

    //                     if (isset($resolution['info'])) break;
    //                     // Convertir a Dolares 
    //                     $k = [];
    //                     $k['price'] = $data['totalPrice'];
    //                     $k['sale_price'] = $data['sale_price'];
    //                     $k['id_product'] = $j['id_product'];

    //                     $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    //                 }
    //             }
    //         }
    //     }
    // }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpensesAnual', function (Request $request, Response $response, $args) use (
    $expensesAnualDao,
    $webTokenDao,
    $assignableExpenseDao,
    $totalExpenseDao,
    $participationExpenseDao,
    $familiesDao,
    $generalProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $productionCenterDao,
    $expensesProductionCenterDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
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
            // if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
            //     // Calcular procentaje de participacion 
            //     $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCountCP($id_company);
            //     $expenseCount = $participationExpenseDao->findAllExpensesByCompanyCP($id_company);

            //     $resolution = $participationExpenseDao->calcParticipationExpense($sumExpenseCount, $expenseCount, 2);

            //     $productions = $productionCenterDao->findAllPCenterByCompany($id_company);

            //     for ($i = 0; $i < sizeof($productions); $i++) {
            //         // Consulta unidades vendidades y volumenes de venta por producto
            //         $unitVol = $assignableExpenseDao->findAllExpensesDistributionByProduction($productions[$i]['id_production_center']);

            //         // Calcular el total de unidades vendidas y volumen de ventas
            //         $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByProduction($productions[$i]['id_production_center']);

            //         // Obtener el total de gastos
            //         $expense = $assignableExpenseDao->findTotalExpenseByProduction($productions[$i]['id_production_center']);
            //         $data['total_expense'] = $expense['expenses_value'];

            //         foreach ($unitVol as $arr) {
            //             if (isset($resolution['info'])) break;
            //             // Calcular gasto asignable
            //             $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
            //             // Actualizar gasto asignable
            //             $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
            //         }
            //     }
            // } else {
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
            // }
        }

        /* x Familia 
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
                // Convertir a Dolares 
                $k = [];
                $k['price'] = $product['totalPrice'];
                $k['sale_price'] = $product['sale_price'];
                $k['id_product'] = $products[$i]['selectNameProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

                if ($_SESSION['flag_composite_product'] == '1') {
                    if (isset($resolution['info'])) break;

                    // Calcular costo material
                    $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['selectNameProduct']);

                    foreach ($productsCompositer as $arr) {
                        if (isset($resolution['info'])) break;

                        $data = [];
                        $data['idProduct'] = $arr['id_product'];
                        $data['compositeProduct'] = $arr['id_child_product'];

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($resolution['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($resolution['info'])) break;

                        $data = $priceProductDao->calcPrice($arr['id_product']);

                        if (isset($data['totalPrice']))
                            $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                        if (isset($resolution['info'])) break;
                        // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                        if (isset($resolution['info'])) break;

                        $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer2 as $j) {
                            if (isset($resolution['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $j['id_child_product'];
                            $data['idProduct'] = $j['id_product'];

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($resolution['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($resolution['info'])) break;

                            $data = $priceProductDao->calcPrice($j['id_product']);

                            if (isset($data['totalPrice']))
                                $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);
                            if (isset($resolution['info'])) break;
                            // Convertir a Dolares 
                            $k = [];
                            $k['price'] = $data['totalPrice'];
                            $k['sale_price'] = $data['sale_price'];
                            $k['id_product'] = $j['id_product'];

                            $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                        }
                    }
                }
            }
        } */

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
});

$app->get('/deleteExpensesAnual/{id_expense_anual}', function (Request $request, Response $response, $args) use (
    $expensesAnualDao,
    $webTokenDao,
    $totalExpenseDao,
    $participationExpenseDao,
    $familiesDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $costMaterialsDao,
    $assignableExpenseDao,
    $productionCenterDao,
    $expensesProductionCenterDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    // $coverage_usd = $_SESSION['coverage_usd'];
    // $flag = $_SESSION['flag_expense_distribution'];

    // if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1 && $args['op'] == 2) {
    //     $resolution = $expensesProductionCenterDao->deleteExpenses($args['id_expense']);
    // } else
    $resolution = $expensesAnualDao->deleteExpensesAnual($args['id_expense_anual']);

    if ($resolution == null) {
        // if ($_SESSION['production_center'] == 1 && $_SESSION['flag_production_center'] == 1) {
        //     $sumExpenseCount = $participationExpenseDao->sumTotalExpenseByNumberCountCP($id_company);
        //     $expenseCount = $participationExpenseDao->findAllExpensesByCompanyCP($id_company);
        // } else {
        $sumExpenseCount = $participationExpenseDao->sumTotalExpenseAnualByNumberCount($id_company);
        $expenseCount = $participationExpenseDao->findAllExpensesAnualByCompany($id_company);
        // }
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
        // $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $data);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
        }
    }

    // if ($resolution == null) {
    //     $products = $generalProductsDao->findAllProducts($id_company);

    //     for ($i = 0; $i < sizeof($products); $i++) {
    //         if ($resolution == null)
    //             $product = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

    //         if (isset($product['totalPrice']))
    //             $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $product['totalPrice']);
    //         if (isset($resolution['info'])) break;
    //         // Convertir a Dolares 
    //         $k = [];
    //         $k['price'] = $product['totalPrice'];
    //         $k['sale_price'] = $product['sale_price'];
    //         $k['id_product'] = $products[$i]['selectNameProduct'];

    //         $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

    //         if ($_SESSION['flag_composite_product'] == '1') {
    //             if (isset($resolution['info'])) break;

    //             // Calcular costo material
    //             $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['selectNameProduct']);

    //             foreach ($productsCompositer as $arr) {
    //                 if (isset($resolution['info'])) break;

    //                 $data = [];
    //                 $data['idProduct'] = $arr['id_product'];
    //                 $data['compositeProduct'] = $arr['id_child_product'];

    //                 $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
    //                 $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

    //                 if (isset($resolution['info'])) break;
    //                 $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
    //                 $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

    //                 if (isset($resolution['info'])) break;

    //                 $data = $priceProductDao->calcPrice($arr['id_product']);

    //                 if (isset($data['totalPrice']))
    //                     $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

    //                 if (isset($resolution['info'])) break;
    //                 // Convertir a Dolares 
    //                 $k = [];
    //                 $k['price'] = $data['totalPrice'];
    //                 $k['sale_price'] = $data['sale_price'];
    //                 $k['id_product'] = $arr['id_product'];

    //                 $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    //                 if (isset($resolution['info'])) break;

    //                 $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

    //                 foreach ($productsCompositer2 as $j) {
    //                     if (isset($resolution['info'])) break;

    //                     $data = [];
    //                     $data['compositeProduct'] = $j['id_child_product'];
    //                     $data['idProduct'] = $j['id_product'];

    //                     $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
    //                     $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

    //                     if (isset($resolution['info'])) break;
    //                     $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
    //                     $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

    //                     if (isset($resolution['info'])) break;

    //                     $data = $priceProductDao->calcPrice($j['id_product']);

    //                     if (isset($data['totalPrice']))
    //                         $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);
    //                     if (isset($resolution['info'])) break;
    //                     // Convertir a Dolares 
    //                     $k = [];
    //                     $k['price'] = $data['totalPrice'];
    //                     $k['sale_price'] = $data['sale_price'];
    //                     $k['id_product'] = $j['id_product'];

    //                     $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    //                 }
    //             }
    //         }
    //     }
    // }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});