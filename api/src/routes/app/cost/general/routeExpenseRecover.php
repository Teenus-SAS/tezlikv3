<?php

use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\PriceProductDao;

$expenseRecoverDao = new ExpenseRecoverDao();
$generalExpenseRecoverDao = new GeneralExpenseRecoverDao();
$generalProductsDao = new GeneralProductsDao();
$priceProductDao = new PriceProductDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/expensesRecover', function (Request $request, Response $response, $args) use ($expenseRecoverDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $expensesRecover = $expenseRecoverDao->findAllExpenseRecoverByCompany($id_company);
    $response->getBody()->write(json_encode($expensesRecover));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expenseRecoverProducts', function (Request $request, Response $response, $args) use ($generalExpenseRecoverDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $generalExpenseRecoverDao->findAllProductsNotInERecover($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseRecoverDataValidation', function (Request $request, Response $response, $args) use (
    $expenseRecoverDao,
    $generalProductsDao
) {
    $dataExpense = $request->getParsedBody();

    if (isset($dataExpense)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $expensesRecover = $dataExpense['importExpense'];

        for ($i = 0; $i < sizeof($expensesRecover); $i++) {
            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($expensesRecover[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportExpenseRecover = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila{$i}");
                break;
            } else $expensesRecover[$i]['idProduct'] = $findProduct['id_product'];

            if (empty($expensesRecover[$i]['percentage'])) {
                $i = $i + 2;
                $dataImportExpenseRecover = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            if (empty(trim($expensesRecover[$i]['percentage']))) {
                $i = $i + 2;
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

$app->post('/addExpenseRecover', function (Request $request, Response $response, $args) use (
    $expenseRecoverDao,
    $generalProductsDao,
    $priceProductDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expensesRecover = $expenseRecoverDao->insertRecoverExpenseByCompany($dataExpense, $id_company);

        if ($expensesRecover == null)
            $expenseRecover = $priceProductDao->calcPrice($dataExpense['idProduct']);

        if (isset($expenseRecover['totalPrice']))
            $expensesRecover = $generalProductsDao->updatePrice($dataExpense['idProduct'], $expenseRecover['totalPrice']);

        if ($expensesRecover == null && $_SESSION['flag_composite_product'] == '1') {
            // Calcular costo material porq
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExpense['idProduct']);

            foreach ($productsCompositer as $arr) {
                if (isset($expensesRecover['info'])) break;

                $data = [];
                $data['idProduct'] = $arr['id_product'];
                $data['compositeProduct'] = $arr['id_child_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $expensesRecover = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($expensesRecover['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $expensesRecover = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($expensesRecover['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($data['totalPrice']))
                    $expensesRecover = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if (isset($expensesRecover['info'])) break;

                $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer2 as $j) {
                    if (isset($expensesRecover['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $expensesRecover = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($expensesRecover['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $expensesRecover = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($expensesRecover['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $expensesRecover = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);
                }
            }
        }

        if ($expensesRecover == null)
            $resp = array('success' => true, 'message' => 'Gasto guardado correctamente');
        else if (isset($expensesRecover['info']))
            $resp = array('info' => true, 'message' => $expensesRecover['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else {
        $expensesRecover = $dataExpense['importExpense'];

        for ($i = 0; $i < sizeof($expensesRecover); $i++) {
            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($expensesRecover[$i], $id_company);
            $expensesRecover[$i]['idProduct'] = $findProduct['id_product'];

            $expenseRecover = $expenseRecoverDao->findExpenseRecover($expensesRecover[$i], $id_company);
            if (!$expenseRecover)
                $resolution = $expenseRecoverDao->insertRecoverExpenseByCompany($expensesRecover[$i], $id_company);
            else {
                $expensesRecover[$i]['idExpenseRecover'] = $expenseRecover['id_expense_recover'];
                $resolution = $expenseRecoverDao->updateRecoverExpense($expensesRecover[$i]);
            }

            $resolution = $priceProductDao->calcPrice($expenseRecover[$i]['idProduct']);

            if (isset($resolution['info']))
                break;
            if (isset($resolution['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($expenseRecover[$i]['idProduct'], $resolution['totalPrice']);

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($resolution['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($expenseRecover[$i]['idProduct']);

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
                    }
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Recuperación de gasto importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExpenseRecover', function (Request $request, Response $response, $args) use (
    $expenseRecoverDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expensesRecover = $expenseRecoverDao->updateRecoverExpense($dataExpense);

        if ($expensesRecover == null)
            $expenseRecover = $priceProductDao->calcPrice($dataExpense['idProduct']);

        if (isset($expenseRecover['totalPrice']))
            $expensesRecover = $generalProductsDao->updatePrice($dataExpense['idProduct'], $expenseRecover['totalPrice']);

        if ($expensesRecover == null && $_SESSION['flag_composite_product'] == '1') {
            // Calcular costo material porq
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExpense['idProduct']);

            foreach ($productsCompositer as $arr) {
                if (isset($expensesRecover['info'])) break;

                $data = [];
                $data['idProduct'] = $arr['id_product'];
                $data['compositeProduct'] = $arr['id_child_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $expensesRecover = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($expensesRecover['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $expensesRecover = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($expensesRecover['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($data['totalPrice']))
                    $expensesRecover = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if (isset($expensesRecover['info'])) break;

                $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer2 as $j) {
                    if (isset($expensesRecover['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $expensesRecover = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($expensesRecover['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $expensesRecover = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($expensesRecover['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $expensesRecover = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);
                }
            }
        }

        if ($expensesRecover == null)
            $resp = array('success' => true, 'message' => 'Gasto modificado correctamente');
        else if (isset($expensesRecover['info']))
            $resp = array('info' => true, 'message' => $expensesRecover['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    } else {
        $expensesRecover = $dataExpense['data'];

        $percentage = $expensesRecover[sizeof($expensesRecover) - 1];

        unset($expensesRecover[sizeof($expensesRecover) - 1]);

        for ($i = 0; $i < sizeof($expensesRecover); $i++) {
            $expensesRecover[$i]['percentage'] = $percentage;
            $resolution = $expenseRecoverDao->updateRecoverExpense($expensesRecover[$i]);

            if ($resolution == null)
                $resolution = $priceProductDao->calcPrice($expensesRecover[$i]['idProduct']);
            else break;
            if (isset($resolution['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($expensesRecover[$i]['idProduct'], $resolution['totalPrice']);

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($resolution['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($expensesRecover[$i]['idProduct']);

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
                    }
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Gastos modificados correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExpenseRecover', function (Request $request, Response $response, $args) use (
    $expenseRecoverDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExpense = $request->getParsedBody();

    $expensesRecover = $expenseRecoverDao->deleteRecoverExpense($dataExpense['idExpenseRecover']);

    if ($expensesRecover == null)
        $expenseRecover = $priceProductDao->calcPrice($dataExpense['idProduct']);

    if (isset($expenseRecover['totalPrice']))
        $expensesRecover = $generalProductsDao->updatePrice($dataExpense['idProduct'], $expenseRecover['totalPrice']);

    if ($expensesRecover == null && $_SESSION['flag_composite_product'] == '1') {
        // Calcular costo material porq
        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExpense['idProduct']);

        foreach ($productsCompositer as $arr) {
            if (isset($expensesRecover['info'])) break;

            $data = [];
            $data['idProduct'] = $arr['id_product'];
            $data['compositeProduct'] = $arr['id_child_product'];

            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
            $expensesRecover = $generalCompositeProductsDao->updateCostCompositeProduct($data);

            if (isset($expensesRecover['info'])) break;
            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            $expensesRecover = $costMaterialsDao->updateCostMaterials($data, $id_company);

            if (isset($expensesRecover['info'])) break;

            $data = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($data['totalPrice']))
                $expensesRecover = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

            if (isset($expensesRecover['info'])) break;

            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

            foreach ($productsCompositer2 as $j) {
                if (isset($expensesRecover['info'])) break;

                $data = [];
                $data['compositeProduct'] = $j['id_child_product'];
                $data['idProduct'] = $j['id_product'];

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $expensesRecover = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($expensesRecover['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $expensesRecover = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($expensesRecover['info'])) break;

                $data = $priceProductDao->calcPrice($j['id_product']);

                if (isset($data['totalPrice']))
                    $expensesRecover = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);
            }
        }
    }

    if ($expensesRecover == null)
        $resp = array('success' => true, 'message' => 'Gasto eliminado correctamente');
    else if (isset($expensesRecover['info']))
        $resp = array('info' => true, 'message' => $expensesRecover['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
