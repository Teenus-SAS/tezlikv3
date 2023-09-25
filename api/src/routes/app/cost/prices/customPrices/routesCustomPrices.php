<?php

use tezlikv3\dao\CustomPricesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\Dao\PriceCustomDao;

$customPricesDao = new CustomPricesDao();
$priceCustomDao = new PriceCustomDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/customPrices', function (Request $request, Response $response, $args) use ($customPricesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $customPrices = $customPricesDao->findAllCustomPricesByCompany($id_company);
    $response->getBody()->write(json_encode($customPrices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->post('/addCustomPrice', function (Request $request, Response $response, $args) use (
//     $customPricesDao,
//     $priceCustomDao,
//     $lastDataDao
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $dataCustomPrice = $request->getParsedBody();

//     $findPrice = $customPricesDao->findCustomPrice($dataCustomPrice, $id_company);

//     if (!$findPrice) {
//         $customPrices = $customPricesDao->insertCustomPricesByCompany($dataCustomPrice, $id_company);

//         if ($customPrices == null) {
//             $lastData = $lastDataDao->findLastInsertedCustomPrice();

//             $price = $priceCustomDao->calcPriceCustomByCustomPrice($lastData['id_custom_price']);

//             $customPrices = $customPricesDao->updatePrice($lastData['id_custom_price'], $price['custom_price']);
//         }

//         if ($customPrices == null)
//             $resp = array('success' => true, 'message' => 'Precio agregado correctamente');
//         else if (isset($customPrices['info']))
//             $resp = array('info' => true, 'message' => $customPrices['message']);
//         else
//             $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
//     } else
//         $resp = array('info' => true, 'message' => 'Producto con lista de precio ya existente. Ingrese un nuevo precio');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

$app->post('/updateCustomPrice', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $priceCustomDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataCustomPrice = $request->getParsedBody();

    $data = [];

    $findPrice = $customPricesDao->findCustomPrice($dataCustomPrice, $id_company);

    !is_array($findPrice) ? $data['id_custom_price'] = 0 : $data = $findPrice;

    if ($data['id_custom_price'] == $dataCustomPrice['idCustomPrice'] || $data['id_custom_price'] == 0) {
        $customPrices = $customPricesDao->updateCustomPrice($dataCustomPrice);

        // if ($customPrices == null) {
        //     $price = $priceCustomDao->calcPriceCustomByCustomPrice($dataCustomPrice['idCustomPrice']);

        //     $customPrices = $customPricesDao->updatePrice($dataCustomPrice['idCustomPrice'], $price['custom_price']);
        // }

        if ($customPrices == null)
            $resp = array('success' => true, 'message' => 'Precio modificado correctamente');
        else if (isset($customPrices['info']))
            $resp = array('info' => true, 'message' => $customPrices['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Producto con lista de precio ya existente. Ingrese un nuevo precio');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteCustomPrice/{id_custom_price}', function (Request $request, Response $response, $args) use ($customPricesDao) {
    $customPrices = $customPricesDao->deleteCustomPrice($args['id_custom_price']);

    if ($customPrices == null)
        $resp = array('success' => true, 'message' => 'Precio eliminado correctamente');
    else if (isset($customPrices['info']))
        $resp = array('info' => true, 'message' => $customPrices['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
