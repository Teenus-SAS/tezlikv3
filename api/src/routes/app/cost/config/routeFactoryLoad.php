<?php

use tezlikv3\dao\FactoryLoadDao;
use tezlikv3\dao\MachinesDao;
use tezlikv3\dao\CostMinuteDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\PriceProductDao;

$factoryloadDao = new FactoryLoadDao();
$machinesDao = new MachinesDao();
$costMinuteDao = new CostMinuteDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/factoryLoad', function (Request $request, Response $response, $args) use ($factoryloadDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $machines = $factoryloadDao->findAllFactoryLoadByCompany($id_company);
    $response->getBody()->write(json_encode($machines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar carga fabril*/
$app->post('/factoryLoadDataValidation', function (Request $request, Response $response, $args) use ($factoryloadDao, $machinesDao) {
    $dataFactoryLoad = $request->getParsedBody();

    if (isset($dataFactoryLoad)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $factoryLoad = $dataFactoryLoad['importFactoryLoad'];

        for ($i = 0; $i < sizeof($factoryLoad); $i++) {
            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($factoryLoad[$i], $id_company);
            if (!$findMachine) {
                $i = $i + 1;
                $dataImportFactoryLoad = array('error' => true, 'message' => "Maquina no existe en la base de datos <br>Fila: {$i}");
                break;
            } else $productProcess[$i]['idMachine'] = $findMachine['id_machine'];

            if (empty($factoryLoad[$i]['descriptionFactoryLoad']) || empty($factoryLoad[$i]['costFactory'])) {
                $i = $i + 1;
                $dataImportFactoryLoad = array('error' => true, 'message' => "Campos vacios en fila {$i}");
                break;
            } else {
                // Falta verificar datos para actualizar
                $insert = $insert + 1;

                $dataImportFactoryLoad['insert'] = $insert;
                $dataImportFactoryLoad['update'] = $update;
            }
        }
    } else
        $dataImportFactoryLoad = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportFactoryLoad, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addFactoryLoad', function (Request $request, Response $response, $args) use ($factoryloadDao, $machinesDao, $costMinuteDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $dataFactoryLoads = sizeof($dataFactoryLoad);

    if ($dataFactoryLoads > 1) {
        $factoryLoad = $factoryloadDao->insertFactoryLoadByCompany($dataFactoryLoad, $id_company);

        $lastFactoryLoad = $factoryloadDao->findLastInsertedFactoryLoad($id_company);
        $factoryLoad['idManufacturingLoad'] = $lastFactoryLoad['id_manufacturing_load'];

        // Calcular costo por minuto
        $costMinute = $costMinuteDao->calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company);

        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company);

        // Calcular Precio products_costs
        $priceProduct = $priceProductDao->calcPriceByMachine($dataFactoryLoad['idMachine'], $id_company);

        if (
            $factoryLoad == null && $costMinute == null &&
            $indirectCost == null && $priceProduct == null
        )
            $resp = array('success' => true, 'message' => 'Carga fabril creada correctamente');
        else if (isset($factoryLoad['info']))
            $resp = array('info' => true, 'message' => $factoryLoad['message']);
        else if ($costMinute == 1)
            $resp = array('error' => true, 'message' => 'Los campos (dias, horas) de la maquina tienen que ser mayor a cero');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $factoryLoad = $dataFactoryLoad['importFactoryLoad'];

        for ($i = 0; $i < sizeof($factoryLoad); $i++) {
            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($factoryLoad[$i], $id_company);
            $factoryLoad[$i]['idMachine'] = $findMachine['id_machine'];

            // Falta verificar datos para actualizar
            $resolution = $factoryloadDao->insertFactoryLoadByCompany($factoryLoad[$i], $id_company);
            $lastFactoryLoad = $factoryloadDao->findLastInsertedFactoryLoad($id_company);
            $factoryLoad[$i]['idManufacturingLoad'] = $lastFactoryLoad['id_manufacturing_load'];

            // Calcular costo por minuto
            $costMinute = $costMinuteDao->calcCostMinuteByFactoryLoad($factoryLoad[$i], $id_company);

            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcCostIndirectCostByFactoryLoad($factoryLoad[$i], $id_company);

            // Calcular Precio products_costs
            $priceProduct = $priceProductDao->calcPriceByMachine($factoryLoad[$i]['idMachine'], $id_company);
        }
        if (
            $resolution == null && $costMinute == null &&
            $indirectCost == null && $priceProduct == null
        )
            $resp = array('success' => true, 'message' => 'Carga fabril importada correctamente');
        else if ($costMinute == 1)
            $resp = array('error' => true, 'message' => 'Los campos (dias, horas) de la maquina tienen que ser mayor a cero');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateFactoryLoad', function (Request $request, Response $response, $args) use ($factoryloadDao, $costMinuteDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $factoryLoad = $factoryloadDao->updateFactoryLoad($dataFactoryLoad);

    // Calcular costo por minuto
    $costMinute = $costMinuteDao->calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company);

    // Calcular costo indirecto
    $indirectCost = $indirectCostDao->calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company);

    // Calcular Precio products_costs
    $priceProduct = $priceProductDao->calcPriceByMachine($dataFactoryLoad['idMachine'], $id_company);

    if (
        $factoryLoad == null && $costMinute == null &&
        $indirectCost == null && $priceProduct == null
    )
        $resp = array('success' => true, 'message' => 'Carga fabril actualizada correctamente');
    else if (isset($factoryLoad['info']))
        $resp = array('info' => true, 'message' => $factoryLoad['message']);
    else if ($costMinute == 1)
        $resp = array('error' => true, 'message' => 'Los campos (dias, horas) de la maquina tienen que ser mayor a cero');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteFactoryLoad', function (Request $request, Response $response, $args) use ($factoryloadDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $factoryLoad = $factoryloadDao->deleteFactoryLoad($dataFactoryLoad);

    // Calcular costo indirecto
    $indirectCost = $indirectCostDao->calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company);

    // Calcular Precio products_costs
    $priceProduct = $priceProductDao->calcPriceByMachine($dataFactoryLoad['idMachine'], $id_company);

    if ($factoryLoad == null && $indirectCost == null && $priceProduct == null)
        $resp = array('success' => true, 'message' => 'Carga fabril eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar la carga fabril, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
