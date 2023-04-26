<?php

use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralServicesDao;
use tezlikv3\dao\PriceProductDao;

$externalServicesDao = new ExternalServicesDao();
$generalServicesDao = new GeneralServicesDao();
$productsDao = new GeneralProductsDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/externalservices/{id_product}', function (Request $request, Response $response, $args) use ($externalServicesDao) {
    $externalServices = $externalServicesDao->findAllExternalServices($args['id_product']);
    $response->getBody()->write(json_encode($externalServices, JSON_NUMERIC_CHECK));
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
                $i = $i + 1;
                $dataImportExternalService = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $externalService[$i]['idProduct'] = $findProduct['id_product'];

            if (empty($externalService[$i]['service']) || empty($externalService[$i]['costService'])) {
                $i = $i + 1;
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
    $GeneralProductsDao
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
                $externalServices = $GeneralProductsDao->updatePrice($dataExternalService['idProduct'], $externalServices['totalPrice']);

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

            $resolution = $GeneralProductsDao->updatePrice($externalService[$i]['idProduct'], $resolution['totalPrice']);
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
    $GeneralProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataExternalService = $request->getParsedBody();

    $externalService = $externalServicesDao->findExternalService($dataExternalService, $id_company);

    if ($externalService['id_service'] == $dataExternalService['idService'] || $externalService['id_service'] == 0) {
        $externalServices = $externalServicesDao->updateExternalServices($dataExternalService);

        // Calcular precio del producto
        if ($externalServices == null)
            $externalServices = $priceProductDao->calcPrice($dataExternalService['idProduct']);

        if (isset($externalServices['totalPrice']))
            $externalServices = $GeneralProductsDao->updatePrice($dataExternalService['idProduct'], $externalServices['totalPrice']);

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
    $GeneralProductsDao
) {
    $dataExternalService = $request->getParsedBody();

    $externalServices = $externalServicesDao->deleteExternalService($dataExternalService['idService']);

    // Calcular precio del producto
    if ($externalServices == null)
        $externalServices = $priceProductDao->calcPrice($dataExternalService['idProduct']);

    if (isset($externalServices['totalPrice']))
        $externalServices = $GeneralProductsDao->updatePrice($dataExternalService['idProduct'], $externalServices['totalPrice']);

    if ($externalServices == null)
        $resp = array('success' => true, 'message' => 'Servicio externo eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el servicio externo, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
