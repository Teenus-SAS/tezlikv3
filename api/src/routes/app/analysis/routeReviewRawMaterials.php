<?php

use tezlikv2\dao\ReviewRawMaterialsDao;

$reviewRawMaterialsDao = new ReviewRawMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/rawMaterials/{idProduct}', function (Request $request, Response $response, $args) use ($reviewRawMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $reviewRawMaterials = $reviewRawMaterialsDao->productsRawMaterials($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($reviewRawMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
