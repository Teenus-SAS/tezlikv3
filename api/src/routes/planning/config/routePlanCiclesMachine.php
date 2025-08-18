<?php

use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\LotsProductsDao;
use tezlikv3\dao\PlanCiclesMachineDao;

$planCiclesMachineDao = new PlanCiclesMachineDao();
$machinesDao = new GeneralMachinesDao();
$productsDao = new GeneralProductsDao();
$economicLotDao = new LotsProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/planCiclesMachine', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $planCiclesMachine = $planCiclesMachineDao->findAllPlanCiclesMachine($id_company);
    $response->getBody()->write(json_encode($planCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/planCiclesMachineDataValidation', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao,
    $machinesDao,
    $productsDao
) {
    $dataPlanCiclesMachine = $request->getParsedBody();

    if (isset($dataPlanCiclesMachine['importPlanCiclesMachine'])) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $planCiclesMachine = $dataPlanCiclesMachine['importPlanCiclesMachine'];

        for ($i = 0; $i < sizeof($planCiclesMachine); $i++) {
            if (empty($planCiclesMachine[$i]['ciclesHour'])) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($planCiclesMachine[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "No existe el producto en la base de datos<br>Fila: {$i}");
                break;
            } else $planCiclesMachine[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planCiclesMachine[$i], $id_company);
            if (!$findMachine) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "No existe la maquina en la base de datos<br>Fila: {$i}");
                break;
            } else $planCiclesMachine[$i]['idMachine'] = $findMachine['id_machine'];

            $findPlanCiclesMachine = $planCiclesMachineDao->findPlanCiclesMachine($planCiclesMachine[$i], $id_company);

            if (!$findPlanCiclesMachine) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportPlanCiclesMachine['insert'] = $insert;
            $dataImportPlanCiclesMachine['update'] = $update;
        }
    } else $dataImportPlanCiclesMachine = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportPlanCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanCiclesMachine', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao,
    $productsDao,
    $machinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPlanCiclesMachine = $request->getParsedBody();

    $dataPlanCiclesMachines = sizeof($dataPlanCiclesMachine);

    if ($dataPlanCiclesMachines > 1) {
        $planCiclesMachine = $planCiclesMachineDao->addPlanCiclesMachines($dataPlanCiclesMachine, $id_company);

        if ($planCiclesMachine == null)
            $resp = array('success' => true, 'message' => 'Ciclo de maquina agregado correctamente');
        else if (isset($planCiclesMachine['info']))
            $resp = array('info' => true, 'message' => $planCiclesMachine['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al agregar el ciclo de maquina. Intente nuevamente');
    } else {
        $planCiclesMachine = $dataPlanCiclesMachine['importPlanCiclesMachine'];

        for ($i = 0; $i < sizeof($planCiclesMachine); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($planCiclesMachine[$i], $id_company);
            $planCiclesMachine[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planCiclesMachine[$i], $id_company);
            $planCiclesMachine[$i]['idMachine'] = $findMachine['id_machine'];

            $findPlanCiclesMachine = $planCiclesMachineDao->findPlanCiclesMachine($planCiclesMachine[$i], $id_company);
            if (!$findPlanCiclesMachine) $resolution = $planCiclesMachineDao->addPlanCiclesMachines($planCiclesMachine[$i], $id_company);
            else {
                $planCiclesMachine[$i]['idCiclesMachine'] = $findPlanCiclesMachine['id_cicles_machine'];
                $resolution = $planCiclesMachineDao->updatePlanCiclesMachine($planCiclesMachine[$i]);
            }
        }

        if ($resolution == null) $resp = array('success' => true, 'message' => 'Plan ciclos de maquina importado correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error al importar los ciclos de maquina. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanCiclesMachine', function (Request $request, Response $response, $args) use ($planCiclesMachineDao) {
    $dataPlanCiclesMachine = $request->getParsedBody();

    if (empty($dataPlanCiclesMachine['idCiclesMachine']) || empty($dataPlanCiclesMachine['idProduct']) || empty($dataPlanCiclesMachine['idMachine']) || empty($dataPlanCiclesMachine['ciclesHour'])) {
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
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
