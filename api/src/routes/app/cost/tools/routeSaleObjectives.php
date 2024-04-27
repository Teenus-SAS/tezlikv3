<?php

use tezlikv3\dao\SaleObjectivesDao;

$saleObjectivesDao = new SaleObjectivesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/saleObjectives', function (Request $request, Response $response, $args) use (
    $saleObjectivesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $saleObjectivesDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});
