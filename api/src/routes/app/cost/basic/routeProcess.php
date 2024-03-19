<?php

use tezlikv3\dao\GeneralProcessDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\ProcessDao;

$processDao = new ProcessDao();
$generalProcessDao = new GeneralProcessDao();
$lastDataDao = new LastDataDao();

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

$app->post('/processDataValidation', function (Request $request, Response $response, $args) use ($generalProcessDao) {
    $dataProcess = $request->getParsedBody();

    if (isset($dataProcess)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $process = $dataProcess['importProcess'];

        for ($i = 0; $i < sizeof($process); $i++) {
            if (empty($process[$i]['process'])) {
                $i = $i + 2;
                $dataImportProcess = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }
            if (empty(trim($process[$i]['process']))) {
                $i = $i + 2;
                $dataImportProcess = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findProcess = $generalProcessDao->findProcess($process[$i], $id_company);
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

$app->post('/addProcess', function (Request $request, Response $response, $args) use (
    $processDao,
    $generalProcessDao,
    $lastDataDao
) {
    session_start();
    $dataProcess = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (empty($dataProcess['importProcess'])) {

        $process = $generalProcessDao->findProcess($dataProcess, $id_company);

        if (!$process) {
            $process = $processDao->insertProcessByCompany($dataProcess, $id_company);

            if ($process == null) {
                $lastInserted = $lastDataDao->lastInsertedProcessId($id_company);

                $lastRoute = $generalProcessDao->findNextRoute($id_company);

                $process = $generalProcessDao->changeRouteById($lastInserted['id_process'], $lastRoute['route']);
            }

            if ($process == null)
                $resp = array('success' => true, 'message' => 'Proceso creado correctamente');
            else if (isset($process['info']))
                $resp = array('info' => true, 'message' => $process['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Proceso duplicado. Ingrese una nuevo proceso');
    } else {
        $process = $dataProcess['importProcess'];

        for ($i = 0; $i < sizeof($process); $i++) {
            if (isset($resolution['info'])) break;

            $findProcess = $generalProcessDao->findProcess($process[$i], $id_company);
            if (!$findProcess) {
                $resolution = $processDao->insertProcessByCompany($process[$i], $id_company);

                if (isset($resolution['info'])) break;

                $lastInserted = $lastDataDao->lastInsertedProcessId($id_company);

                $lastRoute = $generalProcessDao->findNextRoute($id_company);

                $resolution = $generalProcessDao->changeRouteById($lastInserted['id_process'], $lastRoute['route']);
            } else {
                $process[$i]['idProcess'] = $findProcess['id_process'];
                $resolution = $processDao->updateProcess($process[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Proceso importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProcess', function (Request $request, Response $response, $args) use (
    $processDao,
    $generalProcessDao
) {
    session_start();
    $dataProcess = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $data = [];

    $process = $generalProcessDao->findProcess($dataProcess, $id_company);

    !is_array($process) ? $data['id_process'] = 0 : $data = $process;

    if ($data['id_process'] == $dataProcess['idProcess'] || $data['id_process'] == 0) {
        $process = $processDao->updateProcess($dataProcess);

        if ($process == null)
            $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
        else if (isset($process['info']))
            $resp = array('info' => true, 'message' => $process['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Proceso duplicado. Ingrese una nuevo proceso');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveRouteProcess', function (Request $request, Response $response, $args) use ($generalProcessDao) {
    session_start();
    $dataProcess = $request->getParsedBody();

    $process = $dataProcess['data'];

    $resolution = null;

    for ($i = 0; $i < sizeof($process); $i++) {
        $resolution = $generalProcessDao->changeRouteById($process[$i]['id_process'], $process[$i]['route']);

        if (isset($resolution['info'])) break;
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Procesos modificados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
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
