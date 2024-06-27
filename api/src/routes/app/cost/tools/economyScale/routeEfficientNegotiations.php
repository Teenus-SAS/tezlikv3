<?php

use tezlikv3\dao\EfficientNegotiationsDao;
use tezlikv3\dao\GeneralCompanyLicenseDao;
use tezlikv3\dao\PricesDao;
use tezlikv3\dao\WebTokenDao;

$economyScaleDao = new EfficientNegotiationsDao();
$webTokenDao = new WebTokenDao();
$priceDao = new PricesDao();
$generalCompanyLicenseDao = new GeneralCompanyLicenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/calcEconomyScale', function (Request $request, Response $response, $args) use (
    $priceDao,
    $economyScaleDao,
    $webTokenDao
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

    $price = $priceDao->findAllPricesByCompany($id_company);
    $fixedCosts = $economyScaleDao->findAllFixedCostByCompany($id_company);
    $variableCosts = $economyScaleDao->findAllVariableCostByCompany($id_company);

    if (is_array($fixedCosts) && is_array($variableCosts)) {
        $combined = $economyScaleDao->combinedData($price, $fixedCosts, 'id_product');
        $data = $economyScaleDao->combinedData($combined, $variableCosts, 'id_product');
    } else {
        $message = '';

        if (!is_array($fixedCosts) && !is_array($variableCosts)) {
            $message = $fixedCosts . $variableCosts;
        } else if (!is_array($fixedCosts) && is_array($variableCosts)) {
            $message = $fixedCosts;
        } else
            $message = $variableCosts;

        $data = array('info' => true, 'message' => $message);
    }

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeFlagPrice/{type_price}', function (Request $request, Response $response, $args) use (
    $generalCompanyLicenseDao,
    $webTokenDao
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

    $flag = $generalCompanyLicenseDao->updateFlagPrice($args['type_price'], $id_company);

    if ($flag == null) {
        $resp = array('success' => true, 'message' => 'Tipo de precio ingresado correctamente');
        $_SESSION['flag_type_price'] = $args['type_price'];
    } else if (isset($flag['info']))
        $resp = array('info' => true, 'message' => $flag['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
