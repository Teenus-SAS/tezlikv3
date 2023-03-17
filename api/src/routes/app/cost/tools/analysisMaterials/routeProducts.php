<?php

use tezlikv3\dao\AMProductsDao;

$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/rawMaterials/{idProduct}', function (Request $request, Response $response, $args) use ($AMProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $reviewRawMaterials = $AMProductsDao->productsRawMaterials($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($reviewRawMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
