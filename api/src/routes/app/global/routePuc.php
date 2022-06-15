<?php

use tezlikv2\dao\PucDao;

$pucDao = new PucDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/puc', function (Request $request, Response $response, $args) use ($pucDao) {
    $puc = $pucDao->findAllCountsPUC();
    $response->getBody()->write(json_encode($puc, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPuc', function (Request $request, Response $response, $args) use ($pucDao) {
    session_start();
    $dataPuc = $request->getParsedBody();

    if (empty($dataPuc['numberCount']) || empty($dataPuc['count']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $puc = $pucDao->insertPuc($dataPuc);
        if ($puc == null)
            $resp = array('success' => true, 'message' => 'Datos ingresados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePuc', function (Request $request, Response $response, $args) use ($pucDao) {
    session_start();
    $dataPuc = $request->getParsedBody();

    if (empty($dataPuc['numberCount']) || empty($dataPuc['count']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $puc = $pucDao->updatePuc($dataPuc);
        if ($puc == null)
            $resp = array('success' => true, 'message' => 'Datos actualizados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePuc/{id_puc}', function (Request $request, Response $response, $args) use ($pucDao) {
    $puc = $pucDao->deletePuc($args['id_puc']);
    if ($puc == null)
        $resp = array('success' => true, 'message' => 'Datos eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Error al eliminar');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
