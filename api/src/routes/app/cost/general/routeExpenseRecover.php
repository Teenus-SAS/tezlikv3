<?php

use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\WebTokenDao;

$expenseRecoverDao = new ExpenseRecoverDao();
$webTokenDao = new WebTokenDao();
$generalExpenseRecoverDao = new GeneralExpenseRecoverDao();
$generalProductsDao = new GeneralProductsDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/expensesRecover', function (Request $request, Response $response, $args) use (
    $expenseRecoverDao,
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

    $expensesRecover = $expenseRecoverDao->findAllExpenseRecoverByCompany($id_company);
    $response->getBody()->write(json_encode($expensesRecover));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/expenseRecoverProducts', function (Request $request, Response $response, $args) use (
    $generalExpenseRecoverDao,
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

    $products = $generalExpenseRecoverDao->findAllProductsNotInERecover($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/expenseRecoverDataValidation', function (Request $request, Response $response, $args) use (
    $expenseRecoverDao,
    $generalProductsDao,
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

    $dataExpense = $request->getParsedBody();

    if (isset($dataExpense)) {
        // session_start();
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
    $webTokenDao,
    $generalProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
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

    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expensesRecover = $expenseRecoverDao->insertRecoverExpenseByCompany($dataExpense, $id_company);

        $data = [];
        if ($expensesRecover == null)
            $data = $priceProductDao->calcPrice($dataExpense['idProduct']);

        if (isset($data['totalPrice']))
            $expensesRecover = $generalProductsDao->updatePrice($dataExpense['idProduct'], $data['totalPrice']);
        if ($expensesRecover == null && isset($data['totalPrice']) && $_SESSION['flag_currency_usd'] == '1') {
            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $dataExpense['idProduct'];

            $expensesRecover = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

            $data = [];
            $data = $priceProductDao->calcPrice($expenseRecover[$i]['idProduct']);

            if (isset($data['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($expenseRecover[$i]['idProduct'], $data['totalPrice']);
            if (isset($resolution['info'])) break;

            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $expenseRecover[$i]['idProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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
    $webTokenDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
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
    $dataExpense = $request->getParsedBody();

    $dataExpenses = sizeof($dataExpense);

    if ($dataExpenses > 1) {
        $expensesRecover = $expenseRecoverDao->updateRecoverExpense($dataExpense);

        $data = [];
        if ($expensesRecover == null)
            $data = $priceProductDao->calcPrice($dataExpense['idProduct']);

        if (isset($data['totalPrice']))
            $expensesRecover = $generalProductsDao->updatePrice($dataExpense['idProduct'], $data['totalPrice']);

        if ($expensesRecover == null && isset($data['totalPrice']) && $_SESSION['flag_currency_usd'] == '1') {
            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $dataExpense['idProduct'];

            $expensesRecover = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

            $data = [];
            if ($resolution == null)
                $data = $priceProductDao->calcPrice($expensesRecover[$i]['idProduct']);
            else break;
            if (isset($data['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($expensesRecover[$i]['idProduct'], $data['totalPrice']);

            if (isset($resolution['info'])) break;
            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $expensesRecover[$i]['idProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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
    $webTokenDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
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
    $dataExpense = $request->getParsedBody();

    $expensesRecover = $expenseRecoverDao->deleteRecoverExpense($dataExpense['idExpenseRecover']);

    if ($expensesRecover == null)
        $data = $priceProductDao->calcPrice($dataExpense['idProduct']);

    if (isset($data['totalPrice']))
        $expensesRecover = $generalProductsDao->updatePrice($dataExpense['idProduct'], $data['totalPrice']);

    if ($expensesRecover == null && isset($data['totalPrice']) && $_SESSION['flag_currency_usd'] == '1') {
        // Convertir a Dolares 
        $k = [];
        $k['price'] = $data['totalPrice'];
        $k['sale_price'] = $data['sale_price'];
        $k['id_product'] = $dataExpense['idProduct'];

        $expensesRecover = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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
