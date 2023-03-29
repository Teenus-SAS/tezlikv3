<?php

use tezlikv3\dao\AMProductsDao;

$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/rawMaterials/{idProduct}', function (Request $request, Response $response, $args) use ($AMProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productsRawmaterials = $AMProductsDao->findAllProductsRawMaterials($args['idProduct'], $id_company);


    if (isset($productsRawmaterials['info']))
        $data = $productsRawmaterials;
    else {
        $data80MP = [];
        $arr80MP = [];

        $participation = 0;
        for ($i = 0; $i < sizeof($productsRawmaterials); $i++) {
            $participation += $productsRawmaterials[$i]['participation'];
            if ($participation <= 80) {
                $arr80MP[$i] = $productsRawmaterials[$i];
            }
        }

        $data['allRawMaterials'] = $productsRawmaterials;
        $data['80RawMaterials'] = $data80MP;
    }

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
