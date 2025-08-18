<?php

use tezlikv3\dao\EfficientNegotiationsDao;
use tezlikv3\dao\GeneralCompanyLicenseDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\PricesDao;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/negotiations', function (RouteCollectorProxy $group) {

    $group->get('/ENegotiationsProducts', function (Request $request, Response $response, $args) {

        $generalProductsDao = new GeneralProductsDao();

        $id_company = $_SESSION['id_company'];
        $products = $generalProductsDao->findAllEDAndERProducts($id_company);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/calcEconomyScale', function (Request $request, Response $response, $args) {

        $economyScaleDao = new EfficientNegotiationsDao();
        $priceDao = new PricesDao();

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

    $group->get('/changeFlagPrice/{type_price}', function (Request $request, Response $response, $args) {

        $generalCompanyLicenseDao = new GeneralCompanyLicenseDao();

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
})->add(new SessionMiddleware());
