<?php

use tezlikv3\dao\Planning_machinesDao;

$planningMachinesDao = new Planning_machinesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/planningMachines', function (Request $request, Response $response, $args) use ($planningMachinesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $planningMachines = $planningMachinesDao->findPlanMachines($id_company);
    $response->getBody()->write(json_encode($planningMachines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlannningMachines', function (Request $request, Response $response, $args) use ($planningMachinesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPMachines = $request->getParsedBody();

    if (
        empty($dataPMachines['idMachine']) || empty($dataPMachines['numberWorkers']) || empty($dataPMachines['hoursDay']) ||
        empty($dataPMachines['hourStart']) || empty($dataPMachines['hourEnd'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {
        $planningMachines = $planningMachinesDao->insertPlanMachinesByCompany($dataPMachines, $id_company);

        if ($planningMachines == null) $resp = array('success' => true, 'message' => 'Planeación de maquina creada correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un problema al crear la planeación, intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanningMachines', function (Request $request, Response $response, $args) use ($planningMachinesDao) {
    $dataPMachines = $request->getParsedBody();

    if (
        empty($dataPMachines['idProgramMachines']) || empty($dataPMachines['idMachine']) || empty($dataPMachines['numberWorkers']) ||
        empty($dataPMachines['hoursDay']) || empty($dataPMachines['hourStart']) || empty($dataPMachines['hourEnd'])
    )
        $resp = array('error' => true, 'message' => 'No hubo ningún cambio');
    else {
        $planningMachines = $planningMachinesDao->updatePlanMachines($dataPMachines);

        if ($planningMachines == null) $resp = array('success' => true, 'message' => 'Planeación de maquina actualizada correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un problema al actualizar la planeación, intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanningMachines/{id_program_machines}', function (Request $request, Response $response, $args) use ($planningMachinesDao) {
    $planningMachines = $planningMachinesDao->deletePlanMachines($args['id_program_machines']);

    if ($planningMachines == null) $resp = array('success' => true, 'message' => 'Planeación de maquina eliminada correctamente');
    else $resp = array('error' => true, 'message' => 'No se pudo eliminar la planeación, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
