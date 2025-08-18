<?php

use tezlikv3\dao\{
    GeneralCustomPricesDao,
    GeneralPricesListDao,
    PricesDao,
    PricesListDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

// Agrupar todas las rutas de priceList bajo el prefijo '/priceList'
$app->group('/priceList', function (RouteCollectorProxy $group) {

    $group->get('', function (Request $request, Response $response, $args) {
        $priceListDao = new PricesListDao();

        // session_start();
        $id_company = $_SESSION['id_company'];

        $priceList = $priceListDao->findAllPricesListByCompany($id_company);
        $response->getBody()->write(json_encode($priceList, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/priceListByProduct/{id_product}', function (Request $request, Response $response, $args) {
        $priceListDao = new PricesListDao();

        $priceList = $priceListDao->findAllPricesListByProduct($args['id_product']);
        $response->getBody()->write(json_encode($priceList, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addPriceList', function (Request $request, Response $response, $args) {
        $priceListDao = new PricesListDao();
        $generalPriceListDao = new GeneralPricesListDao();

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

    $group->post('/updatePriceList', function (Request $request, Response $response, $args) {
        $priceListDao = new PricesListDao();
        $generalPriceListDao = new GeneralPricesListDao();

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

    $group->get('/deletePriceList/{id_price_list}', function (Request $request, Response $response, $args) {
        $priceListDao = new PricesListDao();
        $customPriceDao = new GeneralCustomPricesDao();

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
})->add(new SessionMiddleware());
