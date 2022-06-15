<?php

use tezlikv2\dao\ProcessDao;

$processDao = new ProcessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/process', function (Request $request, Response $response, $args) use ($processDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $process = $processDao->findAllProcessByCompany($id_company);
    $response->getBody()->write(json_encode($process, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/processDataValidation', function (Request $request, Response $response, $args) use ($processDao) {
    $dataProcess = $request->getParsedBody();

    if (isset($dataProcess)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $process = $dataProcess['importProcess'];

        for ($i = 0; $i < sizeof($process); $i++) {
            $nameProcess = $process[$i]['process'];
            if (empty($nameProcess)) {
                $i = $i + 1;
                $dataImportProcess = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findProcess = $processDao->findProcess($process[$i], $id_company);
                if (!$findProcess) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportProcess['insert'] = $insert;
                $dataImportProcess['update'] = $update;
            }
        }
    } else
        $dataImportProcess = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProcess', function (Request $request, Response $response, $args) use ($processDao) {
    session_start();
    $dataProcess = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    //$countProcess = sizeof($dataProcess);

    if (empty($dataProcess['importProcess'])) {
        $process = $processDao->insertProcessByCompany($dataProcess, $id_company);

        if ($process == null)
            $resp = array('success' => true, 'message' => 'Proceso creado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $process = $dataProcess['importProcess'];

        for ($i = 0; $i < sizeof($process); $i++) {
            $findProcess = $processDao->findProcess($process[$i], $id_company);
            if (!$findProcess)
                $resolution = $processDao->insertProcessByCompany($process[$i], $id_company);
            else {
                $process[$i]['idProcess'] = $findProcess['id_process'];
                $resolution = $processDao->updateProcess($process[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Proceso importado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProcess', function (Request $request, Response $response, $args) use ($processDao) {
    $dataProcess = $request->getParsedBody();

    if (empty($dataProcess['process']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $process = $processDao->updateProcess($dataProcess);

        if ($process == null)
            $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteProcess/{id_process}', function (Request $request, Response $response, $args) use ($processDao) {
    $process = $processDao->deleteProcess($args['id_process']);

    if ($process == null)
        $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');

    if ($process != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
