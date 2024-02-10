<?php

use tezlikv3\dao\PricesDao;

$pricesDao = new PricesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/prices', function (Request $request, Response $response, $args) use ($pricesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $prices = $pricesDao->findAllPricesByCompany($id_company);
    $response->getBody()->write(json_encode($prices));
    return $response->withHeader('Content-Type', 'application/json');
});
