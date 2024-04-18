<?php

use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\FactoryLoadDao;
use tezlikv3\dao\CostMinuteDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralFactoryLoadDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\PriceProductDao;

$factoryloadDao = new FactoryLoadDao();
$generalFactoryLoadDao = new GeneralFactoryLoadDao();
$lastDataDao = new LastDataDao();
$machinesDao = new GeneralMachinesDao();
$costMinuteDao = new CostMinuteDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();
$generalProductsDao = new GeneralProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();

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
    $machinesDao,
    $generalFactoryLoadDao
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
                $i = $i + 2;
                $dataImportFactoryLoad = array('error' => true, 'message' => "Maquina no existe en la base de datos <br>Fila: {$i}");
                break;
            } else $productProcess[$i]['idMachine'] = $findMachine['id_machine'];

            if (empty($factoryLoad[$i]['descriptionFactoryLoad']) || empty($factoryLoad[$i]['costFactory'])) {
                $i = $i + 2;
                $dataImportFactoryLoad = array('error' => true, 'message' => "Campos vacios en fila {$i}");
                break;
            }

            if (empty(trim($factoryLoad[$i]['descriptionFactoryLoad'])) || empty(trim($factoryLoad[$i]['costFactory']))) {
                $i = $i + 2;
                $dataImportFactoryLoad = array('error' => true, 'message' => "Campos vacios en fila {$i}");
                break;
            } else {
                $findFactoryLoad = $generalFactoryLoadDao->findFactoryLoad($factoryLoad[$i]);

                !$findFactoryLoad ? $insert += 1 : $update += +1;

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
    $generalFactoryLoadDao,
    $lastDataDao,
    $machinesDao,
    $costMinuteDao,
    $indirectCostDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $dataFactoryLoads = sizeof($dataFactoryLoad);

    if ($dataFactoryLoads > 1) {
        $findFactoryLoad = $generalFactoryLoadDao->findFactoryLoad($dataFactoryLoad);

        if (!$findFactoryLoad) {
            $factoryLoad = $factoryloadDao->insertFactoryLoadByCompany($dataFactoryLoad, $id_company);

            $lastFactoryLoad = $lastDataDao->findLastInsertedFactoryLoad($id_company);
            $dataFactoryLoad['idManufacturingLoad'] = $lastFactoryLoad['id_manufacturing_load'];

            // Calcular costo por minuto
            if ($factoryLoad == null) {
                $factoryLoad = $costMinuteDao->calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company);

                $dataFactoryLoad['costMinute'] = $factoryLoad['costMinute'];
                $factoryLoad = $costMinuteDao->updateCostMinuteFactoryLoad($dataFactoryLoad, $id_company);
            }

            if ($factoryLoad == null) {
                // Buscar producto por idMachine
                $dataProducts = $indirectCostDao->findProductByMachine($dataFactoryLoad['idMachine'], $id_company);

                foreach ($dataProducts as $arr) {
                    if (isset($factoryLoad['info'])) break;
                    /* Costo Indirecto */
                    // Buscar la maquina asociada al producto
                    $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
                    // Cambiar a 0
                    $indirectCostDao->updateCostIndirectCostByProduct(0, $arr['id_product']);
                    // Calcular costo indirecto
                    $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // Actualizar campo
                    $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                    if (isset($factoryLoad['info'])) break;
                    /* Precio Producto */
                    // Calcular Precio products_costs
                    $factoryLoad = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($factoryLoad['info'])) break;
                    if (isset($factoryLoad['totalPrice']))
                        $factoryLoad = $generalProductsDao->updatePrice($arr['id_product'], $factoryLoad['totalPrice']);

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($factoryLoad['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer as $j) {
                            if (isset($factoryLoad['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $j['id_child_product'];
                            $data['idProduct'] = $j['id_product'];

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $factoryLoad = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($factoryLoad['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $factoryLoad = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($factoryLoad['info'])) break;

                            $data = $priceProductDao->calcPrice($j['id_product']);

                            if (isset($data['totalPrice']))
                                $factoryLoad = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                            if (isset($factoryLoad['info'])) break;

                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($factoryLoad['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $factoryLoad = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($factoryLoad['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $factoryLoad = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($factoryLoad['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);

                                if (isset($data['totalPrice']))
                                    $factoryLoad = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                            }
                        }
                    }
                }
            }

            if ($factoryLoad == null)
                $resp = array('success' => true, 'message' => 'Carga fabril creada correctamente');
            else if (isset($factoryLoad['info']))
                $resp = array('info' => true, 'message' => $factoryLoad['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Carga fabril existente. Ingrese nueva carga');
    } else {
        $factoryLoad = $dataFactoryLoad['importFactoryLoad'];

        for ($i = 0; $i < sizeof($factoryLoad); $i++) {
            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($factoryLoad[$i], $id_company);
            $factoryLoad[$i]['idMachine'] = $findMachine['id_machine'];

            $findFactoryLoad = $generalFactoryLoadDao->findFactoryLoad($factoryLoad[$i]);

            if (!$findFactoryLoad) {
                $resolution = $factoryloadDao->insertFactoryLoadByCompany($factoryLoad[$i], $id_company);
                $lastFactoryLoad = $lastDataDao->findLastInsertedFactoryLoad($id_company);
                $factoryLoad[$i]['idManufacturingLoad'] = $lastFactoryLoad['id_manufacturing_load'];
            } else {
                $factoryLoad[$i]['idManufacturingLoad'] = $findFactoryLoad['id_manufacturing_load'];
                $resolution = $factoryloadDao->updateFactoryLoad($factoryLoad[$i]);
            }

            // Calcular costo por minuto
            if ($resolution != null) break;

            $resolution = $costMinuteDao->calcCostMinuteByFactoryLoad($factoryLoad[$i], $id_company);

            if (isset($resolution['info'])) break;

            $factoryLoad[$i]['costMinute'] = $resolution['costMinute'];
            $resolution = $costMinuteDao->updateCostMinuteFactoryLoad($factoryLoad[$i], $id_company);

            if ($resolution != null) break;

            $dataProducts = $indirectCostDao->findProductByMachine($factoryLoad[$i]['idMachine'], $id_company);

            foreach ($dataProducts as $arr) {
                if (isset($resolution['info'])) break;
                /* Costo Indirecto */
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
                // Cambiar a 0
                $indirectCostDao->updateCostIndirectCostByProduct(0, $arr['id_product']);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // Actualizar campo
                $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                if (isset($resolution['info'])) break;
                /* Precio Producto */
                // Calcular Precio products_costs
                $resolution = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($resolution['info'])) break;
                if (isset($resolution['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);

                if ($_SESSION['flag_composite_product'] == '1') {
                    if (isset($resolution['info'])) break;
                    // Calcular costo material porq
                    $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                    foreach ($productsCompositer as $j) {
                        if (isset($resolution['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $j['id_child_product'];
                        $data['idProduct'] = $j['id_product'];

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($resolution['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($resolution['info'])) break;

                        $data = $priceProductDao->calcPrice($j['id_product']);

                        if (isset($data['totalPrice']))
                            $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                        if (isset($resolution['info'])) break;

                        $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                        foreach ($productsCompositer2 as $k) {
                            if (isset($resolution['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $k['id_child_product'];
                            $data['idProduct'] = $k['id_product'];

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($resolution['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($resolution['info'])) break;

                            $data = $priceProductDao->calcPrice($k['id_product']);

                            if (isset($data['totalPrice']))
                                $resolution = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        }
                    }
                }
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Carga fabril importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateFactoryLoad', function (Request $request, Response $response, $args) use (
    $factoryloadDao,
    $generalFactoryLoadDao,
    $costMinuteDao,
    $indirectCostDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $findFactoryLoad = $generalFactoryLoadDao->findFactoryLoad($dataFactoryLoad);
    !is_array($findFactoryLoad) ? $data['id_manufacturing_load'] = 0 : $data = $findFactoryLoad;

    if ($data['id_manufacturing_load'] == $dataFactoryLoad['idManufacturingLoad'] || $data['id_manufacturing_load'] == 0) {
        $factoryLoad = $factoryloadDao->updateFactoryLoad($dataFactoryLoad);

        // Calcular costo por minuto
        if ($factoryLoad == null)
            $factoryLoad = $costMinuteDao->calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company);

        $dataFactoryLoad['costMinute'] = $factoryLoad['costMinute'];
        $factoryLoad = $costMinuteDao->updateCostMinuteFactoryLoad($dataFactoryLoad, $id_company);

        if ($factoryLoad == null) {
            $dataProducts = $indirectCostDao->findProductByMachine($dataFactoryLoad['idMachine'], $id_company);

            foreach ($dataProducts as $arr) {
                if (isset($factoryLoad['info'])) break;
                /* Costo Indirecto */
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
                // Cambiar a 0
                $indirectCostDao->updateCostIndirectCostByProduct(0, $arr['id_product']);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // Actualizar campo
                $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

                if (isset($factoryLoad['info'])) break;
                /* Precio Producto */
                // Calcular Precio products_costs
                $factoryLoad = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($factoryLoad['info'])) break;
                if (isset($factoryLoad['totalPrice']))
                    $factoryLoad = $generalProductsDao->updatePrice($arr['id_product'], $factoryLoad['totalPrice']);

                if ($_SESSION['flag_composite_product'] == '1') {
                    if (isset($factoryLoad['info'])) break;
                    // Calcular costo material porq
                    $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                    foreach ($productsCompositer as $j) {
                        if (isset($factoryLoad['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $j['id_child_product'];
                        $data['idProduct'] = $j['id_product'];

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $factoryLoad = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($factoryLoad['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $factoryLoad = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($factoryLoad['info'])) break;

                        $data = $priceProductDao->calcPrice($j['id_product']);

                        if (isset($data['totalPrice']))
                            $factoryLoad = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                        if (isset($factoryLoad['info'])) break;

                        $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                        foreach ($productsCompositer2 as $k) {
                            if (isset($factoryLoad['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $k['id_child_product'];
                            $data['idProduct'] = $k['id_product'];

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $factoryLoad = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($factoryLoad['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $factoryLoad = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($factoryLoad['info'])) break;

                            $data = $priceProductDao->calcPrice($k['id_product']);

                            if (isset($data['totalPrice']))
                                $factoryLoad = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        }
                    }
                }
            }
        }

        if ($factoryLoad == null)
            $resp = array('success' => true, 'message' => 'Carga fabril actualizada correctamente');
        else if (isset($factoryLoad['info']))
            $resp = array('info' => true, 'message' => $factoryLoad['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Carga fabril existente. Ingrese nueva carga');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteFactoryLoad', function (Request $request, Response $response, $args) use (
    $factoryloadDao,
    $indirectCostDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataFactoryLoad = $request->getParsedBody();

    $factoryLoad = $factoryloadDao->deleteFactoryLoad($dataFactoryLoad);

    if ($factoryLoad == null) {
        $dataProducts = $indirectCostDao->findProductByMachine($dataFactoryLoad['idMachine'], $id_company);

        foreach ($dataProducts as $arr) {
            if (isset($factoryLoad['info'])) break;
            /* Costo Indirecto */
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
            // Cambiar a 0
            $indirectCostDao->updateCostIndirectCostByProduct(0, $arr['id_product']);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

            if (isset($factoryLoad['info'])) break;
            /* Precio Producto */
            // Calcular Precio products_costs
            $factoryLoad = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($factoryLoad['info'])) break;
            if (isset($factoryLoad['totalPrice']))
                $factoryLoad = $generalProductsDao->updatePrice($arr['id_product'], $factoryLoad['totalPrice']);

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($factoryLoad['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer as $j) {
                    if (isset($factoryLoad['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];

                    /* Calcular costo indirecto */
                    // Buscar la maquina asociada al producto
                    // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                    // // Calcular costo indirecto
                    // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // // Actualizar campo
                    // $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                    // if (isset($factoryLoad['info'])) break;

                    // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    // $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                    // if (isset($factoryLoad['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $factoryLoad = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($factoryLoad['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $factoryLoad = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($factoryLoad['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $factoryLoad = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($factoryLoad['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $k) {
                        if (isset($factoryLoad['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $k['id_child_product'];
                        $data['idProduct'] = $k['id_product'];

                        /* Calcular costo indirecto */
                        // Buscar la maquina asociada al producto
                        // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                        // // Calcular costo indirecto
                        // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                        // // Actualizar campo
                        // $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                        // if (isset($factoryLoad['info'])) break;

                        // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                        // $factoryLoad = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                        // if (isset($factoryLoad['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $factoryLoad = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($factoryLoad['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $factoryLoad = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($factoryLoad['info'])) break;

                        $data = $priceProductDao->calcPrice($k['id_product']);

                        if (isset($data['totalPrice']))
                            $factoryLoad = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                    }
                }
            }
        }
    }

    if ($factoryLoad == null)
        $resp = array('success' => true, 'message' => 'Carga fabril eliminada correctamente');
    else if (isset($factoryLoad['info']))
        $resp = array('info' => true, 'message' => $factoryLoad['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar la carga fabril, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
