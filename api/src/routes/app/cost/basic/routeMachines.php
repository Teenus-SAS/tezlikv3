<?php

use tezlikv3\dao\MachinesDao;
use tezlikv3\dao\MinuteDepreciationDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\PriceProductDao;

$machinesDao = new MachinesDao();
$minuteDepreciationDao = new MinuteDepreciationDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/machines', function (Request $request, Response $response, $args) use ($machinesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $machines = $machinesDao->findAllMachinesByCompany($id_company);
    $response->getBody()->write(json_encode($machines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Maquinas importadas */
$app->post('/machinesDataValidation', function (Request $request, Response $response, $args) use ($machinesDao) {
    $dataMachine = $request->getParsedBody();

    if (isset($dataMachine)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $machines = $dataMachine['importMachines'];

        for ($i = 0; $i < sizeof($machines); $i++) {
            if (
                empty($machines[$i]['machine']) || empty($machines[$i]['cost']) || empty($machines[$i]['yearsDepreciacion']) ||
                $machines[$i]['yearsDepreciacion'] <= 0 || $machines[$i]['hoursMachine'] <= 0 || $machines[$i]['daysMachine'] <= 0
            ) {
                $dataImportMachine = array('error' => true, 'message' => 'Ingrese todos los datos');
                // $dataImportMachine = array('error' => true, 'message' => 'Verifique que los campos dias y horas maquina sean mayor a cero');
                break;
            }

            if ($machines[$i]['hoursMachine'] > 24) {
                $i = $i + 1;
                $dataImportMachine = array('error' => true, 'message' => "Las horas de trabajo no pueden ser mayor a 24, fila: $i");
                break;
            }
            if ($machines[$i]['daysMachine']) {
                $i = $i + 1;
                $dataImportMachine = array('error' => true, 'message' => "Los dias de trabajo no pueden ser mayor a 31, fila: $i");
                break;
            } else {
                $findMachine = $machinesDao->findMachine($machines[$i], $id_company);
                if (!$findMachine) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportMachine['insert'] = $insert;
                $dataImportMachine['update'] = $update;
            }
        }
    } else
        $dataImportMachine = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});


/* Agregar Maquinas */
$app->post('/addMachines', function (Request $request, Response $response, $args) use ($machinesDao, $minuteDepreciationDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $dataMachines = sizeof($dataMachine);

    if ($dataMachines > 1) {
        $machines = $machinesDao->insertMachinesByCompany($dataMachine, $id_company);

        // Calcular depreciacion por minuto
        $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($dataMachine['machine'], $id_company);

        if ($machines == null && $minuteDepreciation == null)
            $resp = array('success' => true, 'message' => 'Maquina creada correctamente');
        else if (isset($machines['info']))
            $resp = array('info' => true, 'message' => $machines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $machines = $dataMachine['importMachines'];

        for ($i = 0; $i < sizeof($machines); $i++) {

            $machine = $machinesDao->findMachine($machines[$i], $id_company);

            if (!$machine) {
                $resolution = $machinesDao->insertMachinesByCompany($machines[$i], $id_company);
                if ($resolution['info'] == true)
                    break;
            } else {
                $machines[$i]['idMachine'] = $machine['id_machine'];
                $resolution = $machinesDao->updateMachine($machines[$i]);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcCostIndirectCostByMachine($dataMachine[$i], $id_company);

                // Calcular precio products_costs
                $priceProduct = $priceProductDao->calcPriceByMachine($dataMachine[$i]['idMachine'], $id_company);
            }
            // Calcular depreciacion por minuto
            $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationImportedByMachine($machines[$i], $id_company);
        }
        if (
            $resolution == null && $minuteDepreciation == null
        )
            $resp = array('success' => true, 'message' => 'Maquina Importada correctamente');
        else if ($resolution['info'] == 'true')
            $resp = $resp = array('info' => true, 'message' => 'No pueden existir máquinas con el mismo nombre. Modifiquelas y vuelva a intentarlo');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Actualizar Maquina */
$app->post('/updateMachines', function (Request $request, Response $response, $args) use ($machinesDao, $minuteDepreciationDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $machines = $machinesDao->updateMachine($dataMachine);

    // Calcular depreciacion por minuto
    $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($dataMachine['machine'], $id_company);

    // Calcular costo indirecto
    $indirectCost = $indirectCostDao->calcCostIndirectCostByMachine($dataMachine, $id_company);

    // Calcular precio products_costs
    $priceProduct = $priceProductDao->calcPriceByMachine($dataMachine['idMachine'], $id_company);

    if (
        $machines == null && $minuteDepreciation == null &&
        $indirectCost == null && $priceProduct == null
    )
        $resp = array('success' => true, 'message' => 'Maquina actualizada correctamente');
    else if (isset($machines['info']))
        $resp = array('info' => true, 'message' => $machines['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Eliminar Maquina */
$app->post('/deleteMachine', function (Request $request, Response $response, $args) use ($machinesDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $machines = $machinesDao->deleteMachine($dataMachine['idMachine']);

    if ($machines == null) {
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcCostIndirectCostByMachine($dataMachine, $id_company);
        // Calcular precio products_costs
        $priceProduct = $priceProductDao->calcPriceByMachine($dataMachine['idMachine'], $id_company);

        if ($indirectCost == null && $priceProduct == null)
            $resp = array('success' => true, 'message' => 'Maquina eliminada correctamente');
    } else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la maquina, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
