<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\CustomPricesDao;
use tezlikv3\dao\GeneralCustomPricesDao;
use tezlikv3\dao\GeneralPricesListDao;
use tezlikv3\Dao\PriceCustomDao;
use tezlikv3\dao\ProductsDao;

$customPricesDao = new CustomPricesDao();
$autenticationDao = new AutenticationUserDao();
$priceCustomDao = new PriceCustomDao();
$productsDao = new ProductsDao();
$generalPricesListDao = new GeneralPricesListDao();
$generalCustomPricesDao = new GeneralCustomPricesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/addCustomPercentage', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $autenticationDao,
    $generalCustomPricesDao,
    $productsDao,
    $priceCustomDao,
    $generalPricesListDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $dataNotData = [];

    $dataPrice = $request->getParsedBody();

    $resolution = $generalPricesListDao->updatePercentage($dataPrice);

    if ($resolution == null) {
        $op = $dataPrice['typeProducts'];

        if ($op == '1')
            $products = $productsDao->findAllProductsByCompany($id_company);
        else
            $products = $dataPrice['products'];

        foreach ($products as $arr) {
            if (isset($resolution['info'])) break;

            $data['idProduct'] = $arr['id_product'];
            $data['typePrice'] = $dataPrice['typePrice'];
            $data['idPriceList'] = $dataPrice['idPriceList'];

            $findCustomPrice = $generalCustomPricesDao->findCustomPrice($data, $id_company);

            if ($arr[$dataPrice['name']] == 0) {
                array_push($dataNotData, $arr);

                $data['customPricesValue'] = 0;

                if (!$findCustomPrice)
                    $resolution = $customPricesDao->insertCustomPricesByCompany($data, $id_company);
                else {
                    $data['idCustomPrice'] = $findCustomPrice['id_custom_price'];
                    $resolution = $customPricesDao->updateCustomPrice($data);
                    // $resolution = $customPricesDao->changeflagPrice($data);
                }
            } else {
                $customPrice = $priceCustomDao->calcPriceCustomByProduct($dataPrice, $arr['id_product']);

                $data['customPricesValue'] = $customPrice;

                if (!$findCustomPrice)
                    $resolution = $customPricesDao->insertCustomPricesByCompany($data, $id_company);
                else {
                    $data['idCustomPrice'] = $findCustomPrice['id_custom_price'];
                    $resolution = $customPricesDao->updateCustomPrice($data);
                }
            }
            $resolution = $generalCustomPricesDao->changeflagPrice($data);
        }
    }

    if ($resolution == null && sizeof($products) > sizeof($dataNotData))
        $resp = array('success' => true, 'message' => 'Porcentaje agregado correctamente', 'dataNotData' => $dataNotData);
    else if (sizeof($products) == sizeof($dataNotData))
        $resp = array('error' => true, 'message' => 'Los productos no se pudieron agregar correctamente', 'dataNotData' => $dataNotData);
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
