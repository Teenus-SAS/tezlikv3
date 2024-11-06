<?php

use tezlikv3\dao\UnitsDao;
use tezlikv3\dao\WebTokenDao;

$unitsDao = new UnitsDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/units', function (Request $request, Response $response, $args) use (
    $unitsDao,
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

    $units = $unitsDao->findAllUnits();
    $response->getBody()->write(json_encode($units));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/units/{id_magnitude}', function (Request $request, Response $response, $args) use (
    $unitsDao,
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

    $units = $unitsDao->findUnitsByMagnitude($args['id_magnitude']);
    $response->getBody()->write(json_encode($units));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/addUnit', function (Request $request, Response $response, $args) use (
    $unitsDao,
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

    $dataUnit = $request->getParsedBody();

    $units = $unitsDao->insertUnit($dataUnit);

    if ($units == null)
        $resp = array('success' => true, 'message' => 'Unidad ingresada correctamente');
    else if (isset($units['info']))
        $resp = array('info' => true, 'message' => $units['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUnit', function (Request $request, Response $response, $args) use (
    $unitsDao,
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

    $dataUnit = $request->getParsedBody();

    $units = $unitsDao->updateUnit($dataUnit);

    if ($units == null)
        $resp = array('success' => true, 'message' => 'Unidad modificada correctamente');
    else if (isset($units['info']))
        $resp = array('info' => true, 'message' => $units['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteUnit/{id_unit}', function (Request $request, Response $response, $args) use (
    $unitsDao,
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

    $units = $unitsDao->deleteUnit($args['id_unit']);

    if ($units == null)
        $resp = array('success' => true, 'message' => 'Unidad eliminada correctamente');
    else if (isset($units['info']))
        $resp = array('info' => true, 'message' => $units['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al eliminar la unidad. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
