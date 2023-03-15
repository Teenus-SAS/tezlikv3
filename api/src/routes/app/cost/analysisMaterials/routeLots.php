<?php

use tezlikv3\dao\AMProductsDao;

$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/rawMaterialsLots', function (Request $request, Response $response, $args) use ($AMProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $data = $request->getParsedBody();
    $lots = [];

    foreach ($data['data'] as $arr => $value) {
        $reviewRawMaterials = $AMProductsDao->productsRawMaterials($value, $id_company);

        $lots = array_merge($lots, $reviewRawMaterials);
    }

    $response->getBody()->write(json_encode($lots, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
