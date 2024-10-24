<?php

use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralExternalServicesDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralServicesDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\WebTokenDao;

$externalServicesDao = new ExternalServicesDao();
$webTokenDao = new WebTokenDao();
$generalServicesDao = new GeneralServicesDao();
$generalExServicesDao = new GeneralExternalServicesDao();
$productsDao = new GeneralProductsDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/allExternalservices', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $externalServicesDao
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
    $externalServices = $externalServicesDao->findAllExternalServices($id_company);
    $response->getBody()->write(json_encode($externalServices));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/externalServices/{id_product}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalServicesDao
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
    $externalServices = $generalServicesDao->findAllExternalServicesByIdProduct($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($externalServices));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/externalServiceDataValidation', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalServicesDao,
    $generalExServicesDao,
    $productsDao
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

    $dataExternalService = $request->getParsedBody();

    if (isset($dataExternalService)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $externalService = $dataExternalService['importExternalService'];

        if (isset($dataExternalService['debugg']))
            $debugg = $dataExternalService['debugg'];
        else $debugg = [];

        $dataImportExternalService = [];

        if (sizeof($debugg) == 0) {
            for ($i = 0; $i < sizeof($externalService); $i++) {
                $findExternalService = $generalServicesDao->findExternalService($externalService[$i], $id_company);

                if (!$findExternalService) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportExternalService['insert'] = $insert;
                $dataImportExternalService['update'] = $update;
            }
        }
    } else
        $dataImportExternalService = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportExternalService;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExternalService', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $externalServicesDao,
    $generalExServicesDao,
    $lastDataDao,
    $generalServicesDao,
    $productsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
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
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataExternalService = $request->getParsedBody();

    $dataExternalServices = sizeof($dataExternalService);

    if ($dataExternalServices > 1) {

        if ($dataExternalService['idGService'] == 0)
            $externalService = $generalServicesDao->findExternalService($dataExternalService, $id_company);
        else
            $externalService = false;

        if (!$externalService) {
            // Guardar servicio en la tabla 'general_external_services'
            $findExternalService = $generalExServicesDao->findExternalService($dataExternalService, $id_company);

            if (!$findExternalService) {
                $externalService = $generalExServicesDao->insertExternalServicesByCompany($dataExternalService, $id_company);

                $lastData = $lastDataDao->findLastInsertedGeneralServices($id_company);
                $dataExternalService['idGService'] = $lastData['id_general_service'];
            } else
                $dataExternalService['idGService'] = $findExternalService['id_general_service'];

            $externalServices = $externalServicesDao->insertExternalServicesByCompany($dataExternalService, $id_company);

            // Calcular precio del producto
            if ($externalServices == null)
                $data = $priceProductDao->calcPrice($dataExternalService['idProduct']);

            if (isset($data['totalPrice']))
                $externalServices = $productsDao->updatePrice($dataExternalService['idProduct'], $data['totalPrice']);

            // Convertir a Dolares 
            if ($externalServices == null && isset($data) && $_SESSION['flag_currency_usd'] == '1') {
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $dataExternalService['idProduct'];

                $externalService = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if ($externalServices == null && $_SESSION['flag_composite_product'] == '1') {
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataExternalService['idProduct']);

                foreach ($productsCompositer as $j) {
                    if (isset($externalServices['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];
                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $externalServices = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($externalServices['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $externalServices = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($externalService['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $externalServices = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($externalService['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
                    if (isset($externalService['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $arr) {
                        if (isset($externalServices['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $arr['id_child_product'];
                        $data['idProduct'] = $arr['id_product'];

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $externalServices = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($externalServices['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $externalServices = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($externalServices['info'])) break;

                        $data = $priceProductDao->calcPrice($arr['id_product']);

                        if (isset($data['totalPrice']))
                            $externalServices = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                        if (isset($externalServices['info'])) break;
                        if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                            $k = [];
                            $k['price'] = $data['totalPrice'];
                            $k['sale_price'] = $data['sale_price'];
                            $k['id_product'] = $arr['id_product'];

                            $externalService = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                        }
                    }
                }
            }

            if ($externalServices == null)
                $resp = array('success' => true, 'message' => 'Servicio externo ingresado correctamente');
            else if (isset($externalServices['info']))
                $resp = array('info' => true, 'message' => $externalServices['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Servicio duplicado. Ingrese una nuevo servicio');
    } else {
        $externalService = $dataExternalService['importExternalService'];

        for ($i = 0; $i < sizeof($externalService); $i++) {
            $data = [];
            $externalService[$i]['idGService'] = 0;
            // Obtener id_producto
            $findProduct = $productsDao->findProduct($externalService[$i], $id_company);
            $externalService[$i]['idProduct'] = $findProduct['id_product'];

            // Guardar servicio en la tabla 'general_external_services'
            $findExternalService = $generalExServicesDao->findExternalService($externalService[$i], $id_company);

            // $findExternalService ? $externalService[$i]['idGService1'] = $findExternalService['id_general_service'] : $externalService[$i]['idGService1'] = '';

            if (!$findExternalService) {
                $resolution = $generalExServicesDao->insertExternalServicesByCompany($externalService[$i], $id_company);

                $lastData = $lastDataDao->findLastInsertedGeneralServices($id_company);
                $externalService[$i]['idGService'] = $lastData['id_general_service'];
            } else
                $externalService[$i]['idGService'] = $findExternalService['id_general_service'];

            $findExternalService = $generalServicesDao->findExternalService($externalService[$i], $id_company);

            if (!$findExternalService)
                $resolution = $externalServicesDao->insertExternalServicesByCompany($externalService[$i], $id_company);
            else {
                $externalService[$i]['idService'] = $findExternalService['id_service'];
                $resolution = $externalServicesDao->updateExternalServices($externalService[$i]);
            }

            // Calcular precio del producto
            $data = $priceProductDao->calcPrice($externalService[$i]['idProduct']);

            if (isset($data['totalPrice']))
                $resolution = $productsDao->updatePrice($externalService[$i]['idProduct'], $data['totalPrice']);

            if (isset($resolution['info'])) break;
            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $externalService[$i]['idProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if (isset($resolution['info'])) break;

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($resolution['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($externalService[$i]['idProduct']);

                foreach ($productsCompositer as $j) {
                    if (isset($resolution['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];
                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($resolution['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($resolution['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }

                    if (isset($resolution['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $arr) {
                        if (isset($resolution['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $arr['id_child_product'];
                        $data['idProduct'] = $arr['id_product'];

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($resolution['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($resolution['info'])) break;

                        $data = $priceProductDao->calcPrice($arr['id_product']);

                        if (isset($data['totalPrice']))
                            $resolution = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                        if (isset($resolution['info'])) break;
                        if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                            $k = [];
                            $k['price'] = $data['totalPrice'];
                            $k['sale_price'] = $data['sale_price'];
                            $k['id_product'] = $arr['id_product'];

                            $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                        }
                    }
                }
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Servicio externo importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExternalService', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $externalServicesDao,
    $generalExServicesDao,
    $lastDataDao,
    $generalServicesDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao
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
    $coverage_usd = $_SESSION['coverage_usd'];

    $dataExternalService = $request->getParsedBody();

    $data = [];

    if ($dataExternalService['idGService'] == 0)
        $externalService = $generalServicesDao->findExternalService($dataExternalService, $id_company);
    else
        $externalService = false;

    !is_array($externalService) ? $data['id_service'] = 0 : $data = $externalService;

    if ($data['id_service'] == $dataExternalService['idService'] || $data['id_service'] == 0) {
        $data = [];
        // Guardar servicio en la tabla 'general_external_services'
        $findExternalService = $generalExServicesDao->findExternalService($dataExternalService, $id_company);

        // $findExternalService ? $dataExternalService['idGService1'] = $findExternalService['id_general_service'] : $externalService[$i];

        if (!$findExternalService) {
            $externalService = $generalExServicesDao->insertExternalServicesByCompany($dataExternalService, $id_company);

            $lastData = $lastDataDao->findLastInsertedGeneralServices($id_company);
            $dataExternalService['idGService'] = $lastData['id_general_service'];
        } else
            $dataExternalService['idGService'] = $findExternalService['id_general_service'];

        $externalServices = $externalServicesDao->updateExternalServices($dataExternalService);

        // Calcular precio del producto
        if ($externalServices == null)
            $data = $priceProductDao->calcPrice($dataExternalService['idProduct']);

        if (isset($data['totalPrice']))
            $externalServices = $productsDao->updatePrice($dataExternalService['idProduct'], $data['totalPrice']);

        if ($externalServices == null && isset($data) && $_SESSION['flag_currency_usd'] == '1') {
            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $dataExternalService['idProduct'];

            $externalService = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
        }
        if ($externalServices == null)
            $resp = array('success' => true, 'message' => 'Servicio externo actualizado correctamente');
        else if (isset($externalServices['info']))
            $resp = array('info' => true, 'message' => $externalServices['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Servicio duplicado. Ingrese una nuevo servicio');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExternalService', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $externalServicesDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao
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
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataExternalService = $request->getParsedBody();
    $data = [];

    $externalServices = $externalServicesDao->deleteExternalService($dataExternalService['idService']);

    // Calcular precio del producto
    if ($externalServices == null)
        $data = $priceProductDao->calcPrice($dataExternalService['idProduct']);

    if (isset($data['totalPrice']))
        $externalServices = $productsDao->updatePrice($dataExternalService['idProduct'], $data['totalPrice']);

    // Convertir a Dolares 
    if ($externalServices == null && $_SESSION['flag_currency_usd'] == '1') {
        $k = [];
        $k['price'] = $data['totalPrice'];
        $k['sale_price'] = $data['sale_price'];
        $k['id_product'] = $dataExternalService['idProduct'];

        $externalServices = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    }

    if ($externalServices == null)
        $resp = array('success' => true, 'message' => 'Servicio externo eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el servicio externo, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
