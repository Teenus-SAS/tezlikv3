<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\GeneralProductsProcessDao;
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
$generalProductsDao = new GeneralProductsDao();
$generalProductProcessDao = new GeneralProductsProcessDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();

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
                empty(trim($machines[$i]['machine'])) || empty(trim($machines[$i]['depreciationYears'])) || $data <= 0 || is_nan($data)
            ) {
                $dataImportMachine = array('error' => true, 'message' => 'Ingrese todos los datos');
                break;
            }

            if ($machines[$i]['hoursMachine'] > 24) {
                $i = $i + 2;
                $dataImportMachine = array('error' => true, 'message' => "Las horas de trabajo no pueden ser mayor a 24, fila: $i");
                break;
            }
            if ($machines[$i]['daysMachine'] > 31) {
                $i = $i + 2;
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
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $dataMachines = sizeof($dataMachine);

    if ($dataMachines > 1) {
        $dataMachine = $convertDataDao->strReplaceMachines($dataMachine);

        $findMachine = $generalMachinesDao->findMachine($dataMachine, $id_company);

        if (!$findMachine) {
            $machines = $machinesDao->insertMachinesByCompany($dataMachine, $id_company);
            if ($machines == null) {
                $lastMachine = $lastDataDao->lastInsertedMachineId($id_company);

                // Calcular depreciacion por minuto
                $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($lastMachine['id_machine']);

                // Modificar depreciacion x minuto
                $dataMachine['idMachine'] = $lastMachine['id_machine'];
                $dataMachine['minuteDepreciation'] = $minuteDepreciation;
                $machine = $minuteDepreciationDao->updateMinuteDepreciation($dataMachine, $id_company);

                if ($machine == null)
                    $resp = array('success' => true, 'message' => 'Maquina creada correctamente');
            } else if (isset($machines['info']))
                $resp = array('info' => true, 'message' => $machines['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Nombre de maquina ya existe. Ingrese una nuevo nombre');
    } else {
        $machines = $dataMachine['importMachines'];

        for ($i = 0; $i < sizeof($machines); $i++) {

            $machine = $generalMachinesDao->findMachine($machines[$i], $id_company);

            $machines[$i] = $convertDataDao->strReplaceMachines($machines[$i]);

            if (!$machine) {
                $resolution = $machinesDao->insertMachinesByCompany($machines[$i], $id_company);

                if ($resolution != null) break;

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
                    $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                    if (isset($resolution['info'])) break;

                    /* Precio Producto */
                    // Calcular precio products_costs
                    $resolution = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($resolution['info'])) break;

                    $resolution = $generalProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($resolution['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer as $j) {
                            if (isset($resolution['info'])) break;

                            $data = [];
                            $data['idProduct'] = $j['id_product'];
                            $data['compositeProduct'] = $j['id_child_product'];

                            /* Calcular costo indirecto */
                            // Buscar la maquina asociada al producto
                            $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                            // Calcular costo indirecto
                            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                            // Actualizar campo
                            $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                            if (isset($resolution['info'])) break;

                            $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                            $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                            if (isset($resolution['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($resolution['info'])) break;

                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($resolution['info'])) break;

                            $data = $priceProductDao->calcPrice($j['id_product']);
                            $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                            if (isset($resolution['info'])) break;

                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($resolution['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                /* Calcular costo indirecto */
                                // Buscar la maquina asociada al producto
                                $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                                // Calcular costo indirecto
                                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                                // Actualizar campo
                                $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                                if (isset($resolution['info'])) break;

                                $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                                $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                                if (isset($resolution['info'])) break;

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($resolution['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($resolution['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);
                                $resolution = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                            }
                        }
                    }
                }
            }
            // Calcular depreciacion por minuto
            $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($machines[$i]['idMachine']);
            // Modificar depreciacion x minuto
            $machines[$i]['minuteDepreciation'] = $minuteDepreciation;
            $resolution = $minuteDepreciationDao->updateMinuteDepreciation($machines[$i], $id_company);
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
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $dataMachine = $convertDataDao->strReplaceMachines($dataMachine);

    $data = [];

    $machine = $generalMachinesDao->findMachine($dataMachine, $id_company);

    !is_array($machine) ? $data['id_machine'] = 0 : $data = $machine;

    if ($data['id_machine'] == $dataMachine['idMachine'] || $data['id_machine'] == 0) {
        $machines = $machinesDao->updateMachine($dataMachine);

        // Calcular depreciacion por minuto
        if ($machines == null) {
            $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($dataMachine['idMachine']);
            // Modificar depreciacion x minuto
            $dataMachine['minuteDepreciation'] = $minuteDepreciation;
            $machines = $minuteDepreciationDao->updateMinuteDepreciation($dataMachine, $id_company);
        }

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
                $machines = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                if (isset($machines['info'])) break;

                /* Precio Producto */
                $data = [];
                // Calcular precio products_costs
                $machines = $priceProductDao->calcPrice($arr['id_product']);

                !is_array($machines) ? $data['totalPrice'] = 0 : $data = $machines;

                if (isset($machines['info'])) break;

                $machines = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if ($_SESSION['flag_composite_product'] == '1') {
                    if (isset($machines['info'])) break;
                    // Calcular costo material porq
                    $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                    foreach ($productsCompositer as $j) {
                        if (isset($machines['info'])) break;
                        $data = [];
                        $data['idProduct'] = $j['id_product'];
                        $data['compositeProduct'] = $j['id_child_product'];

                        /* Calcular costo indirecto */
                        // Buscar la maquina asociada al producto
                        $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                        // Calcular costo indirecto
                        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                        // Actualizar campo
                        $machines = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                        if (isset($machines['info'])) break;

                        $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                        $machines = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                        if (isset($machines['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $machines = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($machines['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $machines = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($machines['info'])) break;
                        $data = $priceProductDao->calcPrice($j['id_product']);
                        $machines = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                        if (isset($machines['info'])) break;

                        $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                        foreach ($productsCompositer2 as $k) {
                            if (isset($machines['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $k['id_child_product'];
                            $data['idProduct'] = $k['id_product'];

                            /* Calcular costo indirecto */
                            // Buscar la maquina asociada al producto
                            $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                            // Calcular costo indirecto
                            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                            // Actualizar campo
                            $machines = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                            if (isset($machines['info'])) break;

                            $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                            $machines = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                            if (isset($machines['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $machines = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($machines['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $machines = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($machines['info'])) break;

                            $data = $priceProductDao->calcPrice($k['id_product']);
                            $machines = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        }
                    }
                }
            }
        }
        if ($machines == null)
            $resp = array('success' => true, 'message' => 'Maquina actualizada correctamente');
        else if (isset($machines['info']))
            $resp = array('info' => true, 'message' => $machines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Nombre de maquina ya existe. Ingrese una nuevo nombre');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Eliminar Maquina */
$app->post('/deleteMachine', function (Request $request, Response $response, $args) use (
    $machinesDao,
    $generalProductProcessDao,
    $indirectCostDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    $machine = $generalProductProcessDao->findProductProcessByIdMachine($dataMachine['idMachine']);

    if ($machine == false) {
        $machines = $machinesDao->deleteMachine($dataMachine['idMachine']);

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
                $machines = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                if (isset($machines['info'])) break;

                /* Precio Producto */
                // Calcular precio products_costs
                $machines = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($machines['info'])) break;

                $machines = $generalProductsDao->updatePrice($arr['id_product'], $machines['totalPrice']);

                if ($_SESSION['flag_composite_product'] == '1') {
                    if (isset($machines['info'])) break;
                    // Calcular costo material porq
                    $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                    foreach ($productsCompositer as $j) {
                        if (isset($machines['info'])) break;

                        $data = [];
                        $data['idProduct'] = $j['id_product'];
                        $data['compositeProduct'] = $j['id_child_product'];

                        /* Calcular costo indirecto */
                        // Buscar la maquina asociada al producto
                        $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                        // Calcular costo indirecto
                        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                        // Actualizar campo
                        $machines = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                        if (isset($machines['info'])) break;

                        $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                        $machines = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                        if (isset($machines['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $machines = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($machines['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $machines = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($machines['info'])) break;

                        $data = $priceProductDao->calcPrice($j['id_product']);
                        $machines = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                        if (isset($machines['info'])) break;

                        $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                        foreach ($productsCompositer2 as $k) {
                            if (isset($machines['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $k['id_child_product'];
                            $data['idProduct'] = $k['id_product'];

                            /* Calcular costo indirecto */
                            // Buscar la maquina asociada al producto
                            $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                            // Calcular costo indirecto
                            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                            // Actualizar campo
                            $machines = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                            if (isset($machines['info'])) break;

                            $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                            $machines = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                            if (isset($machines['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $machines = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($machines['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $machines = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($machines['info'])) break;

                            $data = $priceProductDao->calcPrice($k['id_product']);
                            $machines = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        }
                    }
                }
            }
        }

        if ($machines == null)
            $resp = array('success' => true, 'message' => 'Maquina eliminada correctamente');
        else if (isset($machines['info']))
            $resp = array('info' => true, 'message' => $machines['message']);
    } else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la maquina, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
