<?php

use tezlikv3\dao\CustomPricesDao;
use tezlikv3\dao\GeneralPricesListDao;
use tezlikv3\Dao\PriceCustomDao;

$customPricesDao = new CustomPricesDao();
$priceCustomDao = new PriceCustomDao();
$generalPricesListDao = new GeneralPricesListDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/addCustomPercentage', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $priceCustomDao,
    $generalPricesListDao
) {
    // session_start();
    // $id_company = $_SESSION['id_company'];

    $dataPrice = $request->getParsedBody();

    $resolution = $generalPricesListDao->updatePercentage($dataPrice);

    // if ($resolution == null) {
    // $customPrices = $customPricesDao->findAllCustomPricesByCompany($id_company);

    // foreach ($customPrices as $arr) {
    //     if (isset($resolution['info'])) break;

    //     $customPrice = $priceCustomDao->calcPriceCustom($arr['id_custom_price']);

    //     $resolution = $customPricesDao->updatePrice($arr['id_custom_price'], $customPrice['price']);
    // }
    // }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Porcentaje agregado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
