<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\UnitsDao;

$unitsDao = new UnitsDao();
$autenticationDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/units', function (Request $request, Response $response, $args) use (
    $unitsDao,
    $autenticationDao
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

    $units = $unitsDao->findAllUnits();
    $response->getBody()->write(json_encode($units));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/units/{id_magnitude}', function (Request $request, Response $response, $args) use (
    $unitsDao,
    $autenticationDao
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

    $units = $unitsDao->findUnitsByMagnitude($args['id_magnitude']);
    $response->getBody()->write(json_encode($units));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/addUnit', function (Request $request, Response $response, $args) use (
    $unitsDao,
    $autenticationDao
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
    $autenticationDao
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
    $autenticationDao
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
