<?php

use tezlikv3\dao\AMLotsDao;

$AMLotsDao = new AMLotsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/rawMaterialsLots', function (Request $request, Response $response, $args) use ($AMLotsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $lots = $request->getParsedBody();
    $data80MP = [];

    $reviewRawMaterials = $AMLotsDao->findConsolidatedRawMaterialsByProduct($lots['data'], $id_company);

    $totalUnits = $AMLotsDao->calcTotalUnityCost($reviewRawMaterials);

    $reviewRawMaterials = $AMLotsDao->calcAndSetParticipation($reviewRawMaterials, $totalUnits);

    $reviewRawMaterials = $AMLotsDao->orderDataMaterial($reviewRawMaterials);

    $participation = 0;
    for ($i = 0; $i < sizeof($reviewRawMaterials); $i++) {
        $participation += $reviewRawMaterials[$i]['participation'];
        if ($participation <= 80) {
            $arr80MP[$i] = $reviewRawMaterials[$i];
        } else break;
    }
    $data80MP = array_merge($data80MP, $arr80MP);
    $data['allRawMaterials'] = $reviewRawMaterials;
    $data['80RawMaterials'] = $data80MP;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
