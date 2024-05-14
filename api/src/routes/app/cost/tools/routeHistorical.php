<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\DataCostDao;
use tezlikv3\dao\ExpensesDao;
use tezlikv3\dao\HistoricalExpenseDistributionDao;
use tezlikv3\dao\HistoricalExpensesDao;
use tezlikv3\dao\HistoricalProductsDao;
use tezlikv3\dao\PricesDao;
use tezlikv3\dao\WebTokenDao;

$historicalProductsDao = new HistoricalProductsDao();
$webTokenDao = new WebTokenDao();
$historicalExpensesDao = new HistoricalExpensesDao();
$expensesDao = new ExpensesDao();
$historicalEDDao = new HistoricalExpenseDistributionDao();
$assignableExpenseDao = new AssignableExpenseDao();
$pricesDao = new PricesDao();
$dataCostDao = new DataCostDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/historical', function (Request $request, Response $response, $args) use (
    $historicalProductsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $data = $historicalProductsDao->findAllHistoricalByCompany($id_company);

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/historical/{id_historic}', function (Request $request, Response $response, $args) use (
    $historicalProductsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $data = $historicalProductsDao->findHistorical($args['id_historic']);
    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/lastHistorical', function (Request $request, Response $response, $args) use (
    $historicalProductsDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $data = $historicalProductsDao->findLastHistorical($id_company);

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveHistorical', function (Request $request, Response $response, $args) use (
    $pricesDao,
    $webTokenDao,
    $dataCostDao,
    $historicalProductsDao,
    $historicalExpensesDao,
    $historicalEDDao,
    $assignableExpenseDao,
    $expensesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $flag_expense = $_SESSION['flag_expense'];
    $dataHistorical = $request->getParsedBody();

    $products = $pricesDao->findAllPricesByCompany($id_company);

    $resolution = null;
    if (isset($dataHistorical['type'])) {
        $month = date('m');
        $year = date('Y');

        foreach ($products as $arr) {
            if (isset($resolution['info'])) break;

            $data = [];
            $data['idProduct'] = $arr['id_product'];
            $data['price'] = $arr['price'];
            $data['salePrice'] = $arr['sale_price'];
            $data['profitability'] = $arr['profitability'];
            $data['commisionSale'] = $arr['commission_sale'];
            $data['costMaterials'] = $arr['cost_materials'];
            $data['costWorkforce'] = $arr['cost_workforce'];
            $data['costIndirect'] = $arr['cost_indirect_cost'];
            $data['externalServices'] = $arr['services'];
            $data['unitsSold'] = $arr['units_sold'];
            $data['turnover'] = $arr['turnover'];
            $data['assignableExpense'] = $arr['assignable_expense'];
            $data['expenseRecover'] = $arr['expense_recover'];
            $data['month'] = $month;
            $data['year'] = $year;

            $k = $dataCostDao->calcMinProfitability($data, $flag_expense);

            $data['minProfitability'] = $k;

            $resolution = $historicalProductsDao->insertHistoricalByCompany($data, $id_company);
        }
    } else {
        $historical = $dataHistorical['data'];
        $year = $historical['year'];
        $month = $historical['month'];

        if (isset($historical['products']))
            $historicalProducts = $historical['products'];

        // Productos
        foreach ($products as $arr) {
            if (isset($resolution['info'])) break;

            $data = [];
            $data['idProduct'] = $arr['id_product'];
            $data['price'] = $arr['price'];
            $data['salePrice'] = $arr['sale_price'];
            $data['profitability'] = $arr['profitability'];
            $data['commisionSale'] = $arr['commission_sale'];
            $data['costMaterials'] = $arr['cost_materials'];
            $data['costWorkforce'] = $arr['cost_workforce'];
            $data['costIndirect'] = $arr['cost_indirect_cost'];
            $data['externalServices'] = $arr['services'];
            $data['unitsSold'] = $arr['units_sold'];
            $data['turnover'] = $arr['turnover'];
            $data['assignableExpense'] = $arr['assignable_expense'];
            $data['expenseRecover'] = $arr['expense_recover'];
            $data['month'] = $month;
            $data['year'] = $year;

            $k = $dataCostDao->calcMinProfitability($data, $flag_expense);

            $data['minProfitability'] = $k;

            $insert = true;

            if (isset($historical['products'])) {
                for ($i = 0; $i < sizeof($historicalProducts); $i++) {
                    if ($data['idProduct'] == $historicalProducts[$i]['id_product']) {
                        $insert = false;
                        break;
                    }
                }
            }

            if ($insert == true)
                $resolution = $historicalProductsDao->insertHistoricalByCompany($data, $id_company);
            else {
                $resolution = $historicalProductsDao->updateHistoricalByCompany($data);
            }
        }
    }

    // Gastos
    if ($resolution == null) {
        $expenses = $expensesDao->findAllExpensesByCompany($id_company);

        foreach ($expenses as $arr) {
            $arr['year'] = $year;
            $arr['month'] = $month;

            $historical = $historicalExpensesDao->findHistorical($arr, $id_company);

            if (!$historical)
                $resolution = $historicalExpensesDao->insertHistoricalExpense($arr, $id_company);
            else {
                $arr['id_historical_expense'] = $historical['id_historical_expense'];

                $resolution = $historicalExpensesDao->updateHistoricalExpense($arr);
            }

            if (isset($resolution['info'])) break;
        }
    }

    // Distribucion
    if ($resolution == null) {
        $expenses = $assignableExpenseDao->findAllExpensesDistribution($id_company);

        foreach ($expenses as $arr) {
            $arr['year'] = $year;
            $arr['month'] = $month;
            $arr['assignable_expense'] = $arr['assignable_expense'];

            // Guardar ED Historico (mes)
            $historical = $historicalEDDao->findHistorical($arr, $id_company);

            if (!$historical)
                $resolution = $historicalEDDao->insertHistoricalExpense($arr, $id_company);
            else {
                $arr['id_historical_distribution'] = $historical['id_historical_distribution'];

                $resolution = $historicalEDDao->updateHistoricalExpense($arr);
            }

            if (isset($resolution['info'])) break;
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Historico guardado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
