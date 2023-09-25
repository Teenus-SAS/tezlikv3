<?php

use tezlikv3\dao\CustomPricesDao;
use tezlikv3\dao\GeneralPricesListDao;
use tezlikv3\Dao\PriceCustomDao;
use tezlikv3\dao\ProductsDao;

$customPricesDao = new CustomPricesDao();
$priceCustomDao = new PriceCustomDao();
$productsDao = new ProductsDao();
$generalPricesListDao = new GeneralPricesListDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/addCustomPercentage', function (Request $request, Response $response, $args) use (
    $customPricesDao,
    $productsDao,
    $priceCustomDao,
    $generalPricesListDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataNotData = [];

    $dataPrice = $request->getParsedBody();

    $resolution = $generalPricesListDao->updatePercentage($dataPrice);

    if ($resolution == null) {
        $products = $productsDao->findAllProductsByCompany($id_company);

        foreach ($products as $arr) {
            if ($arr[$dataPrice['name']] == 0)
                array_push($dataNotData, $arr);
            else {
                if (isset($resolution['info'])) break;

                $customPrice = $priceCustomDao->calcPriceCustomByProduct($dataPrice, $arr['id_product']);

                $data['idProduct'] = $arr['id_product'];
                $data['customPricesValue'] = $customPrice;
                $data['idPriceList'] = $dataPrice['idPriceList'];
                $data['typePrice'] = $dataPrice['typePrice'];

                $customPrice = $customPricesDao->findCustomPrice($data, $id_company);

                if (!$customPrice)
                    $resolution = $customPricesDao->insertCustomPricesByCompany($data, $id_company);
                else {
                    $data['idCustomPrice'] = $customPrice['id_custom_price'];
                    $resolution = $customPricesDao->updateCustomPrice($data);
                    $resolution = $customPricesDao->changeflagPrice($data);
                }
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Porcentaje agregado correctamente', 'dataNotData' => $dataNotData);
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
