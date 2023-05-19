<?php

use tezlikv3\dao\PricesListDao;

$priceListDao = new PricesListDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/priceList', function (Request $request, Response $response, $args) use ($priceListDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $priceList = $priceListDao->findAllPricesListByCompany($id_company);
    $response->getBody()->write(json_encode($priceList, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPriceList', function (Request $request, Response $response, $args) use ($priceListDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataPriceList = $request->getParsedBody();

    $priceList = $priceListDao->insertPricesListByCompany($dataPriceList, $id_company);

    if ($priceList == null)
        $resp = array('success' => true, 'message' => 'Lista de precio agregada correctamente');
    else if (isset($priceList['info']))
        $resp = array('info' => true, 'message' => $priceList['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePriceList', function (Request $request, Response $response, $args) use ($priceListDao) {
    $dataPriceList = $request->getParsedBody();

    $priceList = $priceListDao->updatePriceList($dataPriceList);

    if ($priceList == null)
        $resp = array('success' => true, 'message' => 'Lista de precio modificada correctamente');
    else if (isset($priceList['info']))
        $resp = array('info' => true, 'message' => $priceList['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePriceList/{id_price_list}', function (Request $request, Response $response, $args) use ($priceListDao) {
    $priceList = $priceListDao->deletePriceList($args['id_price_list']);

    if ($priceList == null)
        $resp = array('success' => true, 'message' => 'Lista de precio eliminada correctamente');
    else if (isset($priceList['info']))
        $resp = array('info' => true, 'message' => $priceList['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
