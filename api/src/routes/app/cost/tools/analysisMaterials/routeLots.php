<?php

use tezlikv3\dao\AMLotsDao;
use tezlikv3\dao\AMProductsDao;
use tezlikv3\dao\WebTokenDao;

$AMLotsDao = new AMLotsDao();
$webTokenDao = new WebTokenDao();
$AMProductsDao = new AMProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/rawMaterialsLots', function (Request $request, Response $response, $args) use (
    $AMLotsDao,
    $webTokenDao,
    $AMProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $lots = $request->getParsedBody();
    $id_products = array();
    foreach ($lots['data'] as $product) {
        $id_products[] = $product['idProduct'];
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
