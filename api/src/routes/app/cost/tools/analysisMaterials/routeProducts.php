<?php

use tezlikv3\dao\AMProductsDao;

$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/rawMaterials/{idProduct}', function (Request $request, Response $response, $args) use ($AMProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productsRawmaterials = $AMProductsDao->findAllProductsRawMaterials($args['idProduct'], $id_company);

    $data80MP = [];
    $participation = 0;

    for ($i = 0; $i < sizeof($productsRawmaterials); $i++) {
        if ($participation <= 80) {
            $data80MP[$i] = $productsRawmaterials[$i];
            $participation += $productsRawmaterials[$i]['participation'];
        } else break;
    }

    $data['allRawMaterials'] = $productsRawmaterials;
    $data['80RawMaterials'] = $data80MP;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
