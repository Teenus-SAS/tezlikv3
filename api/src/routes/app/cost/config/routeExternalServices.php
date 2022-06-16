<?php

use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\dao\PriceProductDao;

$externalServicesDao = new ExternalServicesDao();
$productsDao = new ProductsDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/externalservices/{id_product}', function (Request $request, Response $response, $args) use ($externalServicesDao) {
    $externalServices = $externalServicesDao->externalServices($args['id_product']);
    $response->getBody()->write(json_encode($externalServices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/externalServiceDataValidation', function (Request $request, Response $response, $args) use ($externalServicesDao, $productsDao) {
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

            $service = $externalService[$i]['service'];
            $cost = $externalService[$i]['costService'];
            if (empty($service) || empty($cost)) {
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

$app->post('/addExternalService', function (Request $request, Response $response, $args) use ($externalServicesDao, $productsDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataExternalService = $request->getParsedBody();

    $dataExternalServices = sizeof($dataExternalService);

    if ($dataExternalServices > 1) {
        $externalServices = $externalServicesDao->insertExternalServicesByCompany($dataExternalService, $id_company);

        // Calcular precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataExternalService['idProduct']);

        if ($externalServices == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Servicio externo ingresado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
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
            $priceProduct = $priceProductDao->calcPrice($externalService[$i]['idProduct']);
        }
        if ($resolution == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Servicio externo importado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateExternalService', function (Request $request, Response $response, $args) use ($externalServicesDao, $priceProductDao) {
    $dataExternalService = $request->getParsedBody();

    if (empty($dataExternalService['service']) || empty($dataExternalService['costService']) || empty($dataExternalService['idProduct']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $externalServices = $externalServicesDao->updateExternalServices($dataExternalService);

        // Calcular precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataExternalService['idProduct']);

        if ($externalServices == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Servicio externo actualizado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteExternalService', function (Request $request, Response $response, $args) use ($externalServicesDao, $priceProductDao) {
    $dataExternalService = $request->getParsedBody();

    $externalServices = $externalServicesDao->deleteExternalService($dataExternalService['idService']);

    // Calcular precio del producto
    $priceProduct = $priceProductDao->calcPrice($dataExternalService['idProduct']);

    if ($externalServices == null && $priceProduct == null)
        $resp = array('success' => true, 'message' => 'Servicio externo eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el servicio externo, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
