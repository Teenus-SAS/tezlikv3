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

    $reviewRawMaterials = $AMLotsDao->findConsolidatedRawMaterialsByProduct($lots['data'], $id_company);

    if (isset($productsRawmaterials['info']))
        $data = $productsRawmaterials;
    else {
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
            }
        }
        $data80MP = array_merge($data80MP, $arr80MP);
        $data['allRawMaterials'] = $reviewRawMaterials;
        $data['80RawMaterials'] = $data80MP;
    }

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});