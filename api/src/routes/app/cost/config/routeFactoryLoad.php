<?php

use tezlikv3\dao\FactoryLoadDao;
use tezlikv3\dao\CostMinuteDao;
use tezlikv3\dao\GeneralCostProductsDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\PriceProductDao;

$factoryloadDao = new FactoryLoadDao();
$lastDataDao = new LastDataDao();
$machinesDao = new GeneralMachinesDao();
$costMinuteDao = new CostMinuteDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();
$generalCostProductsDao = new GeneralCostProductsDao();

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
$app->post('/factoryLoadDataValidation', function (Request $request, Response $response, $args) use (
    $machinesDao
) {
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

$app->post('/addFactoryLoad', function (Request $request, Response $response, $args) use (
    $factoryloadDao,
    $lastDataDao,
    $machinesDao,
    $costMinuteDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $dataFactoryLoads = sizeof($dataFactoryLoad);

    if ($dataFactoryLoads > 1) {
        $factoryLoad = $factoryloadDao->insertFactoryLoadByCompany($dataFactoryLoad, $id_company);

        $lastFactoryLoad = $lastDataDao->findLastInsertedFactoryLoad($id_company);
        $dataFactoryLoad['idManufacturingLoad'] = $lastFactoryLoad['id_manufacturing_load'];

        // Calcular costo por minuto
        if ($factoryLoad == null)
            $factoryLoad = $costMinuteDao->calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company);

        // Calcular costo indirecto
        if ($factoryLoad == null)
            $factoryLoad = $indirectCostDao->calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company);

        // Calcular Precio products_costs
        if ($factoryLoad == null) {
            $dataProducts = $indirectCostDao->findProductByMachine($dataFactoryLoad['idMachine'], $id_company);

            foreach ($dataProducts as $arr) {
                $machines = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($machines['info'])) break;

                $machines = $generalCostProductsDao->updatePrice($arr['id_product'], $machines['totalPrice']);
            }
        }

        if ($factoryLoad == null)
            $resp = array('success' => true, 'message' => 'Carga fabril creada correctamente');
        else if (isset($factoryLoad['info']))
            $resp = array('info' => true, 'message' => $factoryLoad['message']);
        else if ($factoryLoad == 1)
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
            $lastFactoryLoad = $lastDataDao->findLastInsertedFactoryLoad($id_company);
            $factoryLoad[$i]['idManufacturingLoad'] = $lastFactoryLoad['id_manufacturing_load'];

            // Calcular costo por minuto
            if ($resolution != null) break;

            $resolution = $costMinuteDao->calcCostMinuteByFactoryLoad($factoryLoad[$i], $id_company);

            // Calcular costo indirecto
            if ($resolution != null) break;

            $resolution = $indirectCostDao->calcCostIndirectCostByFactoryLoad($factoryLoad[$i], $id_company);

            // Calcular Precio products_costs
            if ($resolution != null) break;

            $dataProducts = $indirectCostDao->findProductByMachine($factoryLoad[$i]['idMachine'], $id_company);

            foreach ($dataProducts as $arr) {
                $resolution = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($resolution['info'])) break;

                $resolution = $generalCostProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Carga fabril importada correctamente');
        else if ($resolution == 1)
            $resp = array('error' => true, 'message' => 'Los campos (dias, horas) de la maquina tienen que ser mayor a cero');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateFactoryLoad', function (Request $request, Response $response, $args) use (
    $factoryloadDao,
    $costMinuteDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $factoryLoad = $factoryloadDao->updateFactoryLoad($dataFactoryLoad);

    // Calcular costo por minuto
    if ($factoryLoad == null)
        $factoryLoad = $costMinuteDao->calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company);

    // Calcular costo indirecto
    if ($factoryLoad == null)
        $factoryLoad = $indirectCostDao->calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company);

    // Calcular Precio products_costs
    if ($factoryLoad == null) {
        $dataProducts = $indirectCostDao->findProductByMachine($dataFactoryLoad['idMachine'], $id_company);

        foreach ($dataProducts as $arr) {
            $factoryLoad = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($factoryLoad['info'])) break;

            $factoryLoad = $generalCostProductsDao->updatePrice($arr['id_product'], $factoryLoad['totalPrice']);
        }
    }


    if ($factoryLoad == null)
        $resp = array('success' => true, 'message' => 'Carga fabril actualizada correctamente');
    else if (isset($factoryLoad['info']))
        $resp = array('info' => true, 'message' => $factoryLoad['message']);
    else if ($factoryLoad == 1)
        $resp = array('error' => true, 'message' => 'Los campos (dias, horas) de la maquina tienen que ser mayor a cero');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteFactoryLoad', function (Request $request, Response $response, $args) use (
    $factoryloadDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $factoryLoad = $factoryloadDao->deleteFactoryLoad($dataFactoryLoad);

    // Calcular costo indirecto
    if ($factoryLoad == null)
        $factoryLoad = $indirectCostDao->calcCostIndirectCostByFactoryLoad($dataFactoryLoad, $id_company);

    // Calcular Precio products_costs
    if ($factoryLoad == null) {
        $dataProducts = $indirectCostDao->findProductByMachine($dataFactoryLoad['idMachine'], $id_company);

        foreach ($dataProducts as $arr) {
            $factoryLoad = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($factoryLoad['info'])) break;

            $factoryLoad = $generalCostProductsDao->updatePrice($arr['id_product'], $factoryLoad['totalPrice']);
        }
    }

    if ($factoryLoad == null)
        $resp = array('success' => true, 'message' => 'Carga fabril eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar la carga fabril, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
