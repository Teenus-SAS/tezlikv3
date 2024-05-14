<?php

use tezlikv3\dao\GeneralCustomPricesDao;
use tezlikv3\dao\GeneralPricesListDao;
use tezlikv3\dao\PricesDao;
use tezlikv3\dao\PricesListDao;
use tezlikv3\dao\WebTokenDao;

$priceListDao = new PricesListDao();
$webTokenDao = new WebTokenDao();
$generalPriceListDao = new GeneralPricesListDao();
$customPriceDao = new GeneralCustomPricesDao();
$priceDao = new PricesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/priceList', function (Request $request, Response $response, $args) use (
    $priceListDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $priceList = $priceListDao->findAllPricesListByCompany($id_company);
    $response->getBody()->write(json_encode($priceList, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/priceListByProduct/{id_product}', function (Request $request, Response $response, $args) use (
    $priceListDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $priceList = $priceListDao->findAllPricesListByProduct($args['id_product']);
    $response->getBody()->write(json_encode($priceList, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPriceList', function (Request $request, Response $response, $args) use (
    $priceListDao,
    $webTokenDao,
    $generalPriceListDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $dataPriceList = $request->getParsedBody();

    $findPrice = $generalPriceListDao->findPricesList($dataPriceList, $id_company);

    if (!$findPrice) {
        $priceList = $priceListDao->insertPricesListByCompany($dataPriceList, $id_company);

        if ($priceList == null)
            $resp = array('success' => true, 'message' => 'Lista de precio agregada correctamente');
        else if (isset($priceList['info']))
            $resp = array('info' => true, 'message' => $priceList['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Nombre de lista de precio ya existe. Ingrese una nuevo nombre');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePriceList', function (Request $request, Response $response, $args) use (
    $priceListDao,
    $webTokenDao,
    $generalPriceListDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataPriceList = $request->getParsedBody();

    $data = [];

    $findPrice = $generalPriceListDao->findPricesList($dataPriceList, $id_company);

    !is_array($findPrice) ? $data['id_price_list'] = 0 : $data = $findPrice;

    if ($data['id_price_list'] == $dataPriceList['idPriceList'] || $data['id_price_list'] == 0) {
        $priceList = $priceListDao->updatePriceList($dataPriceList);

        if ($priceList == null)
            $resp = array('success' => true, 'message' => 'Lista de precio modificada correctamente');
        else if (isset($priceList['info']))
            $resp = array('info' => true, 'message' => $priceList['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Nombre de lista de precio ya existe. Ingrese una nuevo nombre');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePriceList/{id_price_list}', function (Request $request, Response $response, $args) use (
    $priceListDao,
    $webTokenDao,
    $customPriceDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $customPrice = $customPriceDao->deleteCustomPriceByPriceList($args['id_price_list']);

    if ($customPrice == null)
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
