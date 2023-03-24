<?php

use tezlikv3\dao\AMProductsDao;

$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/rawMaterialsLots', function (Request $request, Response $response, $args) use ($AMProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $lots = $request->getParsedBody();
    $data80MP = [];

    $reviewRawMaterials = $AMProductsDao->findConsolidatedRawMaterialsByProduct($lots['data'], $id_company);

    $participation = 0;

    for ($i = 0; $i < sizeof($reviewRawMaterials); $i++) {
        if ($participation <= 80) {
            $arr80MP[$i] = $reviewRawMaterials[$i];
            $participation += $reviewRawMaterials[$i]['participation'];
        } else break;
    }
    $data80MP = array_merge($data80MP, $arr80MP);
    $data['allRawMaterials'] = $reviewRawMaterials;
    $data['80RawMaterials'] = $data80MP;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
