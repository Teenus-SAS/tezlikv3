<?php

use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralServicesDao;
use tezlikv3\dao\PriceProductDao;

$externalServicesDao = new ExternalServicesDao();
$generalServicesDao = new GeneralServicesDao();
$productsDao = new GeneralProductsDao();
$priceProductDao = new PriceProductDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/externalservices/{id_product}', function (Request $request, Response $response, $args) use ($externalServicesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $externalServices = $externalServicesDao->findAllExternalServicesByIdProduct($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($externalServices));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allExternalservices', function (Request $request, Response $response, $args) use ($generalServicesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $externalServices = $generalServicesDao->findAllExternalServices($id_company);
    $response->getBody()->write(json_encode($externalServices));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/externalServiceDataValidation', function (Request $request, Response $response, $args) use (
    $externalServicesDao,
    $productsDao
) {
    $dataExternalService = $request->getParsedBody();

    if (isset($dataExternalService)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $externalService = $dataExternalService['importExternalService'];

        for ($i = 0; $i < sizeof($externalService); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($externalService[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportExternalService = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $externalService[$i]['idProduct'] = $findProduct['id_product'];

            if (empty($externalService[$i]['service']) || empty($externalService[$i]['costService'])) {
                $i = $i + 2;
                $dataImportExternalService = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }
            if (empty(trim($externalService[$i]['service'])) || empty(trim($externalService[$i]['costService']))) {
                $i = $i + 2;
                $dataImportExternalService = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                $findExternalService = $externalServicesDao->findExternalService($externalService[$i], $id_company);
                if (!$findExternalService) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportExternalService['insert'] = $insert;
                $dataImportExternalService['update'] = $update;
            }
        }
    } else
        $dataImportExternalService = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExternalService, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addExternalService', function (Request $request, Response $response, $args) use (
    $externalServicesDao,
    $productsDao,
    $priceProductDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExternalService = $request->getParsedBody();

    $dataExternalServices = sizeof($dataExternalService);

    if ($dataExternalServices > 1) {

        $externalService = $externalServicesDao->findExternalService($dataExternalService, $id_company);

        if (!$externalService) {
            $externalServices = $externalServicesDao->insertExternalServicesByCompany($dataExternalService, $id_company);
            // Calcular precio del producto
            if ($externalServices == null)
                $externalServices = $priceProductDao->calcPrice($dataExternalService['idProduct']);

            if (isset($externalServices['totalPrice']))
                $externalServices = $productsDao->updatePrice($dataExternalService['idProduct'], $externalServices['totalPrice']);

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
                    $externalServices = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($productProcess['info'])) break;

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
                        $externalServices = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
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
            // Obtener id_producto
            $findProduct = $productsDao->findProduct($externalService[$i], $id_company);
            $externalService[$i]['idProduct'] = $findProduct['id_product'];

            $findExternalService = $externalServicesDao->findExternalService($externalService[$i], $id_company);

            if (!$findExternalService)
                $resolution = $externalServicesDao->insertExternalServicesByCompany($externalService[$i], $id_company);
            else {
                $externalService[$i]['idService'] = $findExternalService['id_service'];
                $resolution = $externalServicesDao->updateExternalServices($externalService[$i]);
            }

            // Calcular precio del producto
            $resolution = $priceProductDao->calcPrice($externalService[$i]['idProduct']);

            if (isset($resolution['info']))
                break;

            $resolution = $productsDao->updatePrice($externalService[$i]['idProduct'], $resolution['totalPrice']);

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
                    $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

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
                        $resolution = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
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
    $externalServicesDao,
    $priceProductDao,
    $productsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataExternalService = $request->getParsedBody();

    $data = [];

    $externalService = $externalServicesDao->findExternalService($dataExternalService, $id_company);

    !is_array($externalService) ? $data['id_service'] = 0 : $data = $externalService;

    if ($data['id_service'] == $dataExternalService['idService'] || $data['id_service'] == 0) {
        $externalServices = $externalServicesDao->updateExternalServices($dataExternalService);

        // Calcular precio del producto
        if ($externalServices == null)
            $externalServices = $priceProductDao->calcPrice($dataExternalService['idProduct']);

        if (isset($externalServices['totalPrice']))
            $externalServices = $productsDao->updatePrice($dataExternalService['idProduct'], $externalServices['totalPrice']);

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
    $externalServicesDao,
    $priceProductDao,
    $productsDao
) {
    $dataExternalService = $request->getParsedBody();

    $externalServices = $externalServicesDao->deleteExternalService($dataExternalService['idService']);

    // Calcular precio del producto
    if ($externalServices == null)
        $externalServices = $priceProductDao->calcPrice($dataExternalService['idProduct']);

    if (isset($externalServices['totalPrice']))
        $externalServices = $productsDao->updatePrice($dataExternalService['idProduct'], $externalServices['totalPrice']);

    if ($externalServices == null)
        $resp = array('success' => true, 'message' => 'Servicio externo eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el servicio externo, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
