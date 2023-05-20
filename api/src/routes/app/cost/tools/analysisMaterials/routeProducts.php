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
        if (sizeof($productsRawmaterials) > 0)
            $productsRawmaterials = $AMProductsDao->orderDataMaterial($productsRawmaterials);

        $data80MP = [];
        $arr80MP = [];

        $participation = 0;

        $first = false;

        for ($i = 0; $i < sizeof($productsRawmaterials); $i++) {
            $participation += $productsRawmaterials[$i]['participation'];
            if ($participation <= 80) {
                $arr80MP[$i] = $productsRawmaterials[$i];
            } else if ($i + 1 > sizeof($productsRawmaterials) && $first == false) {
                $first = true;
                $participation = 0;
            }
        }

        $data80MP = array_merge($data80MP, $arr80MP);
        $data['allRawMaterials'] = $productsRawmaterials;
        $data['80RawMaterials'] = $data80MP;
    }

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
