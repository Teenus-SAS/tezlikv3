<?php

use tezlikv3\dao\PlanCiclesMachineDao;

$planCiclesMachineDao = new PlanCiclesMachineDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/planCiclesMachine', function (Request $request, Response $response, $args) use ($planCiclesMachineDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $planCiclesMachine = $planCiclesMachineDao->findPlanCiclesMachine($id_company);
    $response->getBody()->write(json_encode($planCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanCiclesMachine', function (Request $request, Response $response, $args) use ($planCiclesMachineDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPlanCiclesMachine = $request->getParsedBody();

    if (empty($dataPlanCiclesMachine['selectNameProduct']) || empty($dataPlanCiclesMachine['idMachine']) || empty($dataPlanCiclesMachine['ciclesHour'])) {
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    } else {
        $planCiclesMachine = $planCiclesMachineDao->addPlanCiclesMachines($dataPlanCiclesMachine, $id_company);

        if ($planCiclesMachine == null) $resp = array('success' => true, 'message' => 'Ciclo de maquina agregado correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error al agregar el ciclo de maquina. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanCiclesMachine', function (Request $request, Response $response, $args) use ($planCiclesMachineDao) {
    $dataPlanCiclesMachine = $request->getParsedBody();

    if (empty($dataPlanCiclesMachine['idCiclesMachine']) || empty($dataPlanCiclesMachine['selectNameProduct']) || empty($dataPlanCiclesMachine['idMachine']) || empty($dataPlanCiclesMachine['ciclesHour'])) {
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    } else {
        $planCiclesMachine = $planCiclesMachineDao->updatePlanCiclesMachine($dataPlanCiclesMachine);

        if ($planCiclesMachine == null) $resp = array('success' => true, 'message' => 'Ciclo de maquina modificada correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar ciclo de maquina. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanCiclesMachine/{id_cicles_machine}', function (Request $request, Response $response, $args) use ($planCiclesMachineDao) {
    $planCiclesMachine = $planCiclesMachineDao->deletePlanCiclesMachine($args['id_cicles_machine']);

    if ($planCiclesMachine == null) $resp = array('success' => true, 'message' => 'Ciclo de maquina eliminado correctamente');
    else $resp = array('error' => true, 'message' => 'No se pudo eliminar ciclo de maquina, existe informacion asociada a él');
    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
