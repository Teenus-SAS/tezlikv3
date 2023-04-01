<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\GeneralCostProductsDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\MachinesDao;
use tezlikv3\dao\MinuteDepreciationDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\PriceProductDao;

$machinesDao = new MachinesDao();
$generalMachinesDao = new GeneralMachinesDao();
$convertDataDao = new ConvertDataDao();
$lastDataDao = new LastDataDao();
$minuteDepreciationDao = new MinuteDepreciationDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();
$generalCostProductsDao = new GeneralCostProductsDao();

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
$app->post('/machinesDataValidation', function (Request $request, Response $response, $args) use (
    $generalMachinesDao,
    $convertDataDao
) {
    $dataMachine = $request->getParsedBody();

    if (isset($dataMachine)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $machines = $dataMachine['importMachines'];

        for ($i = 0; $i < sizeof($machines); $i++) {

            $machines[$i] = $convertDataDao->strReplaceMachines($machines[$i]);

            $data = floatval($machines[$i]['costMachine']) * floatval($machines[$i]['depreciationYears']) * floatval($machines[$i]['hoursMachine']) * floatval($machines[$i]['daysMachine']);

            if (
                empty($machines[$i]['machine']) || empty($machines[$i]['cost']) || empty($machines[$i]['yearsDepreciacion']) || $data <= 0 || is_nan($data)
            ) {
                $dataImportMachine = array('error' => true, 'message' => 'Ingrese todos los datos');
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
                $findMachine = $generalMachinesDao->findMachine($machines[$i], $id_company);
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
$app->post('/addMachines', function (Request $request, Response $response, $args) use (
    $machinesDao,
    $generalMachinesDao,
    $convertDataDao,
    $lastDataDao,
    $minuteDepreciationDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $dataMachines = sizeof($dataMachine);

    if ($dataMachines > 1) {
        $dataMachine = $convertDataDao->strReplaceMachines($dataMachine);

        $findMachine = $generalMachinesDao->findMachine($dataMachine, $id_company);

        if ($findMachine == false) {
            $machines = $machinesDao->insertMachinesByCompany($dataMachine, $id_company);
            if ($machines == null) {
                $lastMachine = $lastDataDao->lastInsertedMachineId($id_company);

                // Calcular depreciacion por minuto
                $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($lastMachine['id_machine'], $id_company);

                if ($minuteDepreciation == null)
                    $resp = array('success' => true, 'message' => 'Maquina creada correctamente');
            } else if (isset($machines['info']))
                $resp = array('info' => true, 'message' => $machines['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'La referencia ya existe. Ingrese una nueva referencia');
    } else {
        $machines = $dataMachine['importMachines'];

        for ($i = 0; $i < sizeof($machines); $i++) {

            $machine = $generalMachinesDao->findMachine($machines[$i], $id_company);

            $machines[$i] = $convertDataDao->strReplaceMachines($machines[$i]);

            if (!$machine) {
                $resolution = $machinesDao->insertMachinesByCompany($machines[$i], $id_company);

                if ($resolution['info'] == true)
                    break;
                $lastMachine = $lastDataDao->lastInsertedMachineId($id_company);
                $machines[$i]['idMachine'] = $lastMachine['id_machine'];
            } else {
                $machines[$i]['idMachine'] = $machine['id_machine'];
                $resolution = $machinesDao->updateMachine($machines[$i]);

                if ($resolution != null) break;

                // Buscar producto por idMachine
                $dataProducts = $indirectCostDao->findProductByMachine($machines[$i]['idMachine'], $id_company);

                foreach ($dataProducts as $arr) {
                    if (isset($resolution['info'])) break;
                    /* Costo Indirecto */
                    // Buscar la maquina asociada al producto
                    $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
                    // Calcular costo indirecto
                    $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // Actualizar campo
                    $resolution = $indirectCostDao->updateCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                    if (isset($resolution['info'])) break;

                    /* Precio Producto */
                    // Calcular precio products_costs
                    $resolution = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($resolution['info'])) break;

                    $resolution = $generalCostProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);
                }
            }
            // Calcular depreciacion por minuto
            $resolution = $minuteDepreciationDao->calcMinuteDepreciationImportedByMachine($machines[$i]['idMachine'], $id_company);
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Maquina Importada correctamente');
        else if (isset($resolution['info']))
            $resp = $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Actualizar Maquina */
$app->post('/updateMachines', function (Request $request, Response $response, $args) use (
    $machinesDao,
    $generalMachinesDao,
    $convertDataDao,
    $minuteDepreciationDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $dataMachine = $convertDataDao->strReplaceMachines($dataMachine);

    $machine = $generalMachinesDao->findMachine($dataMachine, $id_company);

    if (isset($machine['id_machine']) == $dataMachine['idMachine'] || !$machine) {
        $machines = $machinesDao->updateMachine($dataMachine);

        // Calcular depreciacion por minuto
        if ($machines == null)
            $machines = $minuteDepreciationDao->calcMinuteDepreciationByMachine($dataMachine['idMachine'], $id_company);

        if ($machines == null) {
            // Buscar producto por idMachine
            $dataProducts = $indirectCostDao->findProductByMachine($dataMachine['idMachine'], $id_company);

            foreach ($dataProducts as $arr) {
                if (isset($machines['info'])) break;
                /* Costo Indirecto */
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // Actualizar campo
                $machines = $indirectCostDao->updateCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                if (isset($machines['info'])) break;

                /* Precio Producto */
                // Calcular precio products_costs
                $machines = $priceProductDao->calcPrice($arr['id_product']);
                $machines == false ? $machines['totalPrice'] = 0 : $machines;

                if (isset($machines['info'])) break;

                $machines = $generalCostProductsDao->updatePrice($arr['id_product'], $machines['totalPrice']);
            }
        }
        if ($machines == null)
            $resp = array('success' => true, 'message' => 'Maquina actualizada correctamente');
        else if (isset($machines['info']))
            $resp = array('info' => true, 'message' => $machines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'La referencia ya existe. Ingrese una nueva referencia');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Eliminar Maquina */
$app->post('/deleteMachine', function (Request $request, Response $response, $args) use (
    $generalMachinesDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $machines = $generalMachinesDao->deleteMachine($dataMachine['idMachine']);

    if ($machines == null) {
        // Buscar producto por idMachine
        $dataProducts = $indirectCostDao->findProductByMachine($dataMachine['idMachine'], $id_company);

        foreach ($dataProducts as $arr) {
            if (isset($machines['info'])) break;
            /* Costo Indirecto */
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $machines = $indirectCostDao->updateCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

            if (isset($machines['info'])) break;

            /* Precio Producto */
            // Calcular precio products_costs
            $machines = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($machines['info'])) break;

            $machines = $generalCostProductsDao->updatePrice($arr['id_product'], $machines['totalPrice']);
        }
    }

    if ($machines == null)
        $resp = array('success' => true, 'message' => 'Maquina eliminada correctamente');
    else if (isset($machines['info']))
        $resp = array('info' => true, 'message' => $machines['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la maquina, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
