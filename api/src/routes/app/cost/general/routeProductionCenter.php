<?php

use tezlikv3\dao\GeneralPCenterDao;
use tezlikv3\dao\ProductionCenterDao;
use tezlikv3\dao\WebTokenDao;

$productionCenterDao = new ProductionCenterDao();
$webTokenDao = new WebTokenDao();
$generalPCenterDao = new GeneralPCenterDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/productionCenter', function (Request $request, Response $response, $args) use (
    $productionCenterDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $productions = $productionCenterDao->findAllPCenterByCompany($id_company);
    $response->getBody()->write(json_encode($productions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productionDataValidation', function (Request $request, Response $response, $args) use (
    $generalPCenterDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $dataPCenter = $request->getParsedBody();

    if (isset($dataPCenter)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $production = $dataPCenter['importProduction'];

        for ($i = 0; $i < sizeof($production); $i++) {
            if (empty($production[$i]['production'])) {
                $i = $i + 2;
                $dataimportProduction = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }
            if (empty(trim($production[$i]['production']))) {
                $i = $i + 2;
                $dataimportProduction = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findProduction = $generalPCenterDao->findPCenter($production[$i], $id_company);
                if (!$findProduction) $insert = $insert + 1;
                else $update = $update + 1;
                $dataimportProduction['insert'] = $insert;
                $dataimportProduction['update'] = $update;
            }
        }
    } else
        $dataimportProduction = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataimportProduction, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPCenter', function (Request $request, Response $response, $args) use (
    $productionCenterDao,
    $webTokenDao,
    $generalPCenterDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $dataPCenter = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (empty($dataPCenter['importProduction'])) {

        $production = $generalPCenterDao->findPCenter($dataPCenter, $id_company);

        if (!$production) {
            $production = $productionCenterDao->insertPCenterByCompany($dataPCenter, $id_company);

            if ($production == null)
                $resp = array('success' => true, 'message' => 'Centro de produccion creado correctamente');
            else if (isset($production['info']))
                $resp = array('info' => true, 'message' => $production['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Centro de produccion duplicado. Ingrese una nuevo centro de produccion');
    } else {
        $production = $dataPCenter['importProduction'];

        for ($i = 0; $i < sizeof($production); $i++) {
            if (isset($resolution['info'])) break;

            $findproduction = $generalPCenterDao->findPCenter($production[$i], $id_company);
            if (!$findproduction) {
                $resolution = $productionCenterDao->insertPCenterByCompany($production[$i], $id_company);
            } else {
                $production[$i]['idProductionCenter'] = $findproduction['id_production_center'];
                $resolution = $productionCenterDao->updatePCenter($production[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Centro de produccion importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePCenter', function (Request $request, Response $response, $args) use (
    $productionCenterDao,
    $webTokenDao,
    $generalPCenterDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $dataPCenter = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $data = [];

    $production = $generalPCenterDao->findPCenter($dataPCenter, $id_company);

    !is_array($production) ? $data['id_production_center'] = 0 : $data = $production;

    if ($data['id_production_center'] == $dataPCenter['idProductionCenter'] || $data['id_production_center'] == 0) {
        $production = $productionCenterDao->updatePCenter($dataPCenter);

        if ($production == null)
            $resp = array('success' => true, 'message' => 'Centro de produccion actualizado correctamente');
        else if (isset($production['info']))
            $resp = array('info' => true, 'message' => $production['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Centro de produccion duplicado. Ingrese una nuevo centro de produccion');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePCenter/{id_production_center}', function (Request $request, Response $response, $args) use (
    $productionCenterDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $production = $productionCenterDao->deletePCenter($args['id_production_center']);

    if ($production == null)
        $resp = array('success' => true, 'message' => 'Centro de produccion eliminado correctamente');

    if ($production != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar el Centro de produccion, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
