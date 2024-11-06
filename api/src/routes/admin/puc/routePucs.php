<?php

use tezlikv3\dao\PucsDao;
use tezlikv3\dao\WebTokenDao;

$pucsDao = new PucsDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Obtener Cuentas generales
$app->get('/findPUC', function (Request $request, Response $response, $args) use (
    $pucsDao,
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

    $resp = $pucsDao->findAllCounts();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Agregar Cuenta
$app->post('/createPUC', function (Request $request, Response $response, $args) use (
    $pucsDao,
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

    $dataPuc = $request->getParsedBody();
    $respPuc = $pucsDao->insertCountsPUC($dataPuc);

    if ($respPuc == 1) {
        $resp = array('error' => true, 'message' => 'La cuenta ya existe en la base de datos');
    } elseif ($respPuc == null) {
        $resp = array('success' => true, 'message' => 'Datos ingresados correctamente');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Actualizar Cuenta
$app->post('/updatePUC', function (Request $request, Response $response, $args) use (
    $pucsDao,
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

    $dataPuc = $request->getParsedBody();
    $respPuc = $pucsDao->updateCountsPUC($dataPuc);

    if ($respPuc == null) {
        $resp = array('success' => true, 'message' => 'Datos actualizados correctamente');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
