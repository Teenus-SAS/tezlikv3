<?php

use tezlikv3\dao\DataCostDao;
use tezlikv3\dao\HistoricalDao;
use tezlikv3\dao\PricesDao;

$historicalDao = new HistoricalDao();
$pricesDao = new PricesDao();
$dataCostDao = new DataCostDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/historical', function (Request $request, Response $response, $args) use ($historicalDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $data = $historicalDao->findAllHistoricalByCompany($id_company);

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/lastHistorical', function (Request $request, Response $response, $args) use ($historicalDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $data = $historicalDao->findLastHistorical($id_company);

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveHistorical', function (Request $request, Response $response, $args) use (
    $pricesDao,
    $dataCostDao,
    $historicalDao
) {
    session_start();
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

            $resolution = $historicalDao->insertHistoricalByCompany($data, $id_company);
        }
    } else {
        $historical = $dataHistorical['data'];

        if (isset($historical['products']))
            $historicalProducts = $historical['products'];

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
            $data['month'] = $historical['month'];
            $data['year'] = $historical['year'];

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
                $resolution = $historicalDao->insertHistoricalByCompany($data, $id_company);
            else {
                $resolution = $historicalDao->updateHistoricalByCompany($data);
            }
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

// $app->get('/historicalYear', function (Request $request, Response $response, $args) use ($historicalDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $data = $historicalDao->findAllHistoricalByYear($id_company);

//     $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });
// $app->get('/calcEconomyScale/{id_product}', function (Request $request, Response $response, $args) use ($priceDao, $economyScaleDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $price = $priceDao->findPriceByProduct($args['id_product'], $id_company);
//     $fixedCosts = $economyScaleDao->findFixedCostByProduct($args['id_product'], $id_company);
//     $variableCosts = $economyScaleDao->findVariableCostByProduct($args['id_product'], $id_company);

//     $data['price'] = $price['price'];
//     $data['sale_price'] = $price['sale_price'];
//     $data['profitability'] = $price['profitability'];
//     $data['fixedCost'] = $fixedCosts['costFixed'];
//     $data['variableCost'] = $variableCosts['variableCost'];
//     $data['commission'] = $variableCosts['commission'];

//     $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });
