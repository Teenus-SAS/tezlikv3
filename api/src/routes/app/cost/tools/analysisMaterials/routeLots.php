<?php

use tezlikv3\dao\AMLotsDao;
use tezlikv3\dao\AMProductsDao;

$AMLotsDao = new AMLotsDao();
$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/rawMaterialsLots', function (Request $request, Response $response, $args) use (
    $AMLotsDao,
    $AMProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $lots = $request->getParsedBody();
    // $id_company = 2;

    // $lots = array('data' => array(array('id_product' => 109, 'unit' => 200), array('id_product' => 25, 'unit' => 100)));

    $id_products = array();
    foreach ($lots['data'] as $product) {
        $id_products[] = $product['id_product'];
    }

    $products = implode(',', $id_products);

    $reviewRawMaterials = $AMLotsDao->findConsolidatedRawMaterialsByProduct($products, $id_company);

    if (isset($reviewRawMaterials['info']))
        $data = $reviewRawMaterials;
    else {
        $reviewRawMaterials = $AMLotsDao->groupDataLots($reviewRawMaterials, $lots['data']);

        $totalUnits = $AMLotsDao->calcTotalUnityCost($reviewRawMaterials);

        $reviewRawMaterials = $AMLotsDao->calcAndSetParticipation($reviewRawMaterials, $totalUnits);

        if (sizeof($reviewRawMaterials) > 0)
            $reviewRawMaterials = $AMProductsDao->orderDataMaterial($reviewRawMaterials);

        $data80MP = [];
        $arr80MP = [];

        $participation = 0;

        for ($i = 0; $i < sizeof($reviewRawMaterials); $i++) {
            $participation += $reviewRawMaterials[$i]['participation'];
            if ($participation <= 80) {
                $arr80MP[$i] = $reviewRawMaterials[$i];
            } else if ($i == 0) $participation = 0;
        }
        $data80MP = array_merge($data80MP, $arr80MP);
        $data['allRawMaterials'] = $reviewRawMaterials;
        $data['80RawMaterials'] = $data80MP;
    }

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
