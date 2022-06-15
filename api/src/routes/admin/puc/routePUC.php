<?php

use tezlikv2\dao\PUCDao;

$pucDao = new PUCDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Obtener Cuentas generales
$app->get('/findPUC', function (Request $request, Response $response, $args) use ($pucDao) {
    $resp = $pucDao->findAllCounts();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Agregar Cuenta
$app->post('/createPUC', function (Request $request, Response $response, $args) use ($pucDao) {
    $dataPuc = $request->getParsedBody();
    $respPuc = $pucDao->insertCountsPUC($dataPuc);
    
    if ($respPuc == 1) {
        $resp = array('error' => true, 'message' => 'La cuenta ya existe en la base de datos');
    } elseif ($respPuc == null){
        $resp = array('success' => true, 'message' => 'Datos ingresados correctamente');
    }else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Actualizar Cuenta
$app->post('/updatePUC', function (Request $request, Response $response, $args) use ($pucDao) {
    $dataPuc = $request->getParsedBody();
    $respPuc = $pucDao->updateCountsPUC($dataPuc);

    if ($respPuc == null){
        $resp = array('success' => true, 'message' => 'Datos actualizados correctamente');
    }else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
