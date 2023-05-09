<?php

use tezlikv3\dao\EconomyScaleDao;
use tezlikv3\dao\PricesDao;

$economyScaleDao = new EconomyScaleDao();
$priceDao = new PricesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/calcEconomyScale/{id_product}', function (Request $request, Response $response, $args) use ($priceDao, $economyScaleDao) {
    // session_start();
    // $id_company = $_SESSION['id_company'];

    $price = $priceDao->findPriceByProduct($args['id_product'], 5);
    $fixedCosts = $economyScaleDao->findFixedCostByProduct($args['id_product'], 5);
    $variableCosts = $economyScaleDao->findVariableCostByProduct($args['id_product'], 5);

    $data['price'] = $price['price'];
    $data['fixedCost'] = $fixedCosts['costFixed'];
    $data['variableCost'] = $variableCosts['variableCost'];
    $data['commission'] = $variableCosts['commission'];

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
