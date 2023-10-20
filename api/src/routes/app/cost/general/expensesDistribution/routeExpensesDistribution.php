<?php

use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\TotalExpenseDao;
use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
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
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/expensesDistribution', function (Request $request, Response $response, $args) use ($expensesDistributionDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $expensesDistribution = $expensesDistributionDao->findAllExpensesDistributionByCompany($id_company);
    $response->getBody()->write(json_encode($expensesDistribution));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expensesDistributionProducts', function (Request $request, Response $response, $args) use ($generalExpenseDistributionDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $generalExpenseDistributionDao->findAllProductsNotInEDistribution($id_company);
    $response->getBody()->write(json_encode($products));
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
            if (
                empty(trim($expensesDistribution[$i]['referenceProduct'])) || empty(trim($expensesDistribution[$i]['product'])) ||
                empty(trim($expensesDistribution[$i]['unitsSold'])) || empty(trim($expensesDistribution[$i]['turnover']))
            ) {
                $i = $i + 2;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            $expenseTotal = $totalExpenseDao->findTotalExpenseByCompany($id_company);

            if (empty($expenseTotal) || !$expenseTotal) {
                $dataImportExpenseDistribution = array('error' => true, 'message' => 'Asigne un gasto primero antes de distribuir');
                break;
            }


            // Obtener id producto
            $findProduct = $productsDao->findProduct($expensesDistribution[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
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
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];
    $dataExpensesDistribution = $request->getParsedBody();

    $dataExpensesDistributions = sizeof($dataExpensesDistribution);

    if ($dataExpensesDistributions > 1) {

        if ($flag == 2) {
            $products = $familiesDao->findAllProductsInFamily($dataExpensesDistribution['idFamily'], $id_company);

            $expensesDistribution = $familiesDao->updateDistributionFamily($dataExpensesDistribution);

            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($expensesDistribution['info'])) break;

                $products[$i]['selectNameProduct'] = $products[$i]['id_product'];
                $products[$i]['unitsSold'] = $dataExpensesDistribution['unitsSold'];
                $products[$i]['turnover'] = $dataExpensesDistribution['turnover'];
                $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($products[$i], $id_company);

                if (!$findExpenseDistribution)
                    $expensesDistribution = $expensesDistributionDao->insertExpensesDistributionByCompany($products[$i], $id_company);
                else {
                    $products[$i]['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                    $expensesDistribution = $expensesDistributionDao->updateExpensesDistribution($products[$i], $id_company);
                }
            }
        } else {
            $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($dataExpensesDistribution, $id_company);

            if (!$findExpenseDistribution)
                $expensesDistribution = $expensesDistributionDao->insertExpensesDistributionByCompany($dataExpensesDistribution, $id_company);
            else {
                $dataExpensesDistribution['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                $expensesDistribution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution, $id_company);
            }
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
            if ($expensesDistribution == null && $flag == 2) {
                // Consulta unidades vendidades y volumenes de venta por familia
                $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

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

        if ($flag == 2 && $expensesDistribution == null) {
            // Calcular Precio del producto
            for ($i = 0; $i < sizeof($products); $i++) {
                if ($expensesDistribution == null)
                    $expensesDistribution = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

                if (isset($expensesDistribution['totalPrice']))
                    $expensesDistribution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $expensesDistribution['totalPrice']);

                // if (isset($expensesDistribution['info'])) break;

                // // Calcular costo material
                // $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['selectNameProduct']);

                // foreach ($productsCompositer as $arr) {
                //     if (isset($expensesDistribution['info'])) break;

                //     $data = [];
                //     $data['idProduct'] = $arr['id_product'];
                //     $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                //     $expensesDistribution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                //     if (isset($expensesDistribution['info'])) break;

                //     $data = $priceProductDao->calcPrice($arr['id_product']);
                //     $expensesDistribution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                // }
            }
        } else if ($flag == 1 && $expensesDistribution == null) {
            // Calcular Precio del producto
            $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

            if (isset($expensesDistribution['totalPrice']))
                $expensesDistribution = $generalProductsDao->updatePrice($dataExpensesDistribution['refProduct'], $expensesDistribution['totalPrice']);
            if ($expensesDistribution == null) {
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExpensesDistribution['refProduct']);

                foreach ($productsCompositer as $arr) {
                    if (isset($expensesDistribution['info'])) break;

                    $data = [];
                    $data['idProduct'] = $arr['id_product'];
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $expensesDistribution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($expensesDistribution['info'])) break;

                    $data = $priceProductDao->calcPrice($arr['id_product']);
                    $expensesDistribution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                }
            }
        }

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
        }

        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
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

            // if (isset($resolution['info'])) break;
            // // Calcular costo material porq
            // $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($expensesDistribution[$i]['selectNameProduct']);

            // foreach ($productsCompositer as $arr) {
            //     if (isset($resolution['info'])) break;

            //     $data = [];
            //     $data['idProduct'] = $arr['id_product'];
            //     $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            //     $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

            //     if (isset($resolution['info'])) break;

            //     $data = $priceProductDao->calcPrice($arr['id_product']);
            //     $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            // }
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
    $familiesDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];
    $dataExpensesDistribution = $request->getParsedBody();

    if ($flag == 2) {
        $products = $familiesDao->findAllProductsInFamily($dataExpensesDistribution['idFamily'], $id_company);

        $expensesDistribution = $familiesDao->updateDistributionFamily($dataExpensesDistribution);

        for ($i = 0; $i < sizeof($products); $i++) {
            if (isset($expensesDistribution['info'])) break;
            $products[$i]['selectNameProduct'] = $products[$i]['id_product'];
            $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($products[$i], $id_company);

            if ($findExpenseDistribution != false) {
                $products[$i]['unitsSold'] = $dataExpensesDistribution['unitsSold'];
                $products[$i]['turnover'] = $dataExpensesDistribution['turnover'];

                $products[$i]['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                $expensesDistribution = $expensesDistributionDao->updateExpensesDistribution($products[$i], $id_company);
            }
        }
    } else {
        $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($dataExpensesDistribution, $id_company);

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
        if ($expensesDistribution == null && $flag == 2) {
            // Consulta unidades vendidades y volumenes de venta por familia
            $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

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

    if ($flag == 2 && $expensesDistribution == null) {
        // Calcular Precio del producto
        for ($i = 0; $i < sizeof($products); $i++) {
            if ($expensesDistribution == null)
                $expensesDistribution = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

            if (isset($expensesDistribution['totalPrice']))
                $expensesDistribution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $expensesDistribution['totalPrice']);

            // if (isset($expensesDistribution['info'])) break;

            // // Calcular costo material porq
            // $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['selectNameProduct']);

            // foreach ($productsCompositer as $arr) {
            //     if (isset($expensesDistribution['info'])) break;

            //     $data = [];
            //     $data['idProduct'] = $arr['id_product'];
            //     $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            //     $expensesDistribution = $costMaterialsDao->updateCostMaterials($data, $id_company);

            //     if (isset($expensesDistribution['info'])) break;

            //     $data = $priceProductDao->calcPrice($arr['id_product']);
            //     $expensesDistribution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            // }
        }
    } else if ($flag == 1 && $expensesDistribution == null) {
        // Calcular Precio del producto
        $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['refProduct']);

        if (isset($expensesDistribution['totalPrice']))
            $expensesDistribution = $generalProductsDao->updatePrice($dataExpensesDistribution['refProduct'], $expensesDistribution['totalPrice']);

        // if ($expensesDistribution == null) {
        //     // Calcular costo material porq
        //     $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExpensesDistribution['refProduct']);

        //     foreach ($productsCompositer as $arr) {
        //         if (isset($expensesDistribution['info'])) break;

        //         $data = [];
        //         $data['idProduct'] = $arr['id_product'];
        //         $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
        //         $expensesDistribution = $costMaterialsDao->updateCostMaterials($data, $id_company);

        //         if (isset($expensesDistribution['info'])) break;

        //         $data = $priceProductDao->calcPrice($arr['id_product']);
        //         $expensesDistribution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
        //     }
        // }
    }

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribución de gasto asignado correctamente');
    else if (isset($expensesDistribution['info']))
        $resp = array('info' => true, 'message' => $expensesDistribution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpensesDistribution', function (Request $request, Response $response, $args) use (
    $expensesDistributionDao,
    $assignableExpenseDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
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

    // Calcular Precio del producto
    if ($expensesDistribution == null)
        $expensesDistribution = $priceProductDao->calcPrice($dataExpensesDistribution['selectNameProduct']);

    if (isset($expensesDistribution['totalPrice']))
        $expensesDistribution = $generalProductsDao->updatePrice($dataExpensesDistribution['selectNameProduct'], $expensesDistribution['totalPrice']);

    // if ($expensesDistribution == null) {
    //     // Calcular costo material porq
    //     $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExpensesDistribution['selectNameProduct']);

    //     foreach ($productsCompositer as $arr) {
    //         if (isset($expensesDistribution['info'])) break;

    //         $data = [];
    //         $data['idProduct'] = $arr['id_product'];
    //         $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
    //         $expensesDistribution = $costMaterialsDao->updateCostMaterials($data, $id_company);

    //         if (isset($expensesDistribution['info'])) break;

    //         $data = $priceProductDao->calcPrice($arr['id_product']);
    //         $expensesDistribution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
    //     }
    // }


    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribucion de gasto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
