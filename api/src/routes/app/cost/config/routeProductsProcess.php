<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralPayrollDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralProductsProcessDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\Dao\PriceProductDao;

$productsProcessDao = new ProductsProcessDao();
$generalProductsProcessDao = new GeneralProductsProcessDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$generalPayrollDao = new GeneralPayrollDao();
$machinesDao = new GeneralMachinesDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Productos procesos
$app->get('/productsProcess/{idProduct}', function (Request $request, Response $response, $args) use ($productsProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $productsProcessDao->findAllProductsprocessByIdProduct($args['idProduct'], $id_company);
    $response->getBody()->write(json_encode($productProcess));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allProductsProcess', function (Request $request, Response $response, $args) use ($generalProductsProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $generalProductsProcessDao->findAllProductsprocess($id_company);
    $response->getBody()->write(json_encode($productProcess));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/employees/{id_product_process}', function (Request $request, Response $response, $args) use ($generalProductsProcessDao) {
    $employees = $generalProductsProcessDao->findAllEmloyeesByProcess($args['id_product_process']);
    $response->getBody()->write(json_encode($employees));
    return $response->withHeader('Content-Type', 'application/json');
});

// Consultar productos procesos importados
$app->post('/productsProcessDataValidation', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $productsDao,
    $generalPayrollDao,
    $machinesDao
) {
    $dataProductProcess = $request->getParsedBody();

    if (isset($dataProductProcess)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productProcess = $dataProductProcess['importProductsProcess'];

        for ($i = 0; $i < sizeof($productProcess); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productProcess[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $generalPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
            if (!$findProcess) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila: {$i}");
                break;
            } else
                $productProcess[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($productProcess[$i]['machine']) || strtoupper(trim($productProcess[$i]['machine'])) == 'PROCESO MANUAL') {
                $productProcess[$i]['idMachine'] = 0;
            } else {
                $findMachine = $machinesDao->findMachine($productProcess[$i], $id_company);
                if (!$findMachine) {
                    $i = $i + 2;
                    $dataImportProductProcess = array('error' => true, 'message' => "Maquina no existe en la base de datos <br>Fila: {$i}");
                    break;
                } else $productProcess[$i]['idMachine'] = $findMachine['id_machine'];
            }

            //tiempo de alistamiento = 0 si no está definido
            if (!isset($productProcess[$i]['enlistmentTime'])) {
                $productProcess[$i]['enlistmentTime'] = 0;
            }

            //Tiempo de operación = 0 si no está definido
            if (!isset($productProcess[$i]['operationTime'])) {
                $productProcess[$i]['operationTime'] = 0;
            }

            $findProductProcess = $productsProcessDao->findProductProcess($productProcess[$i], $id_company);

            if (!$findProductProcess) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportProductProcess['insert'] = $insert;
            $dataImportProductProcess['update'] = $update;
        }
    } else
        $dataImportProductProcess = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProductProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductsProcess', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $convertDataDao,
    $productsDao,
    $generalPayrollDao,
    $machinesDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $dataProductsProcess = sizeof($dataProductProcess);

    if ($dataProductsProcess > 1) {

        $productProcess = $productsProcessDao->findProductProcess($dataProductProcess, $id_company);

        if (!$productProcess) {
            // $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
            $productProcess = $productsProcessDao->insertProductsProcessByCompany($dataProductProcess, $id_company);

            /* Calcular costo nomina */
            if ($productProcess == null) {
                $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
                // Calcular costo nomina total
                if ($resolution == null) {
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProductProcess['idProduct'], $id_company);
                    $productProcess = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
                }
            }

            // Calcular costo indirecto
            if ($productProcess == null) {
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);

                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine, $id_company);

                // Modificar campo
                $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
            }

            // Calcular Precio del producto
            if ($productProcess == null)
                $productProcess = $priceProductDao->calcPrice($dataProductProcess['idProduct']);

            if (isset($productProcess['totalPrice']))
                $productProcess = $productsDao->updatePrice($dataProductProcess['idProduct'], $productProcess['totalPrice']);

            if ($productProcess == null && $_SESSION['flag_composite_product'] == '1') {
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductProcess['idProduct']);

                foreach ($productsCompositer as $j) {
                    if (isset($productProcess['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];

                    /* Calcular costo indirecto */
                    // Buscar la maquina asociada al producto
                    // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                    // // Calcular costo indirecto
                    // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // // Actualizar campo
                    // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                    // if (isset($productProcess['info'])) break;

                    // // Calcular costo nomina total
                    // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                    // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                    // if (isset($productProcess['info'])) break;

                    // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                    // if (isset($productProcess['info'])) break;

                    // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                    // if (isset($productProcess['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $productProcess = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($productProcess['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $productProcess = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($productProcess['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);
                    $productProcess = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($productProcess['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $arr) {
                        if (isset($productProcess['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $arr['id_child_product'];
                        $data['idProduct'] = $arr['id_product'];

                        /* Calcular costo indirecto */
                        // Buscar la maquina asociada al producto
                        // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                        // // Calcular costo indirecto
                        // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                        // // Actualizar campo
                        // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                        // if (isset($productProcess['info'])) break;

                        // // Calcular costo nomina total
                        // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                        // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                        // if (isset($productProcess['info'])) break;

                        // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                        // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                        // if (isset($productProcess['info'])) break;

                        // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                        // if (isset($productProcess['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $productProcess = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($productProcess['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $productProcess = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($productProcess['info'])) break;

                        $data = $priceProductDao->calcPrice($arr['id_product']);
                        $productProcess = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                    }
                }
            }
            if ($productProcess == null)
                $resp = array('success' => true, 'message' => 'Proceso asignado correctamente');
            elseif ($productProcess == 1)
                $resp = array('error' => true, 'message' => 'El Proceso ya se encuentra en la Base de Datos');
            else if (isset($productProcess['info']))
                $resp = array('info' => true, 'message' => $productProcess['message']);
            else {
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
            }
        } else
            $resp = array('info' => true, 'message' => 'Proceso ya existente. Ingrese otro proceso');
    } else {
        $productProcess = $dataProductProcess['importProductsProcess'];

        for ($i = 0; $i < sizeof($productProcess); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productProcess[$i], $id_company);
            $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $generalPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
            $productProcess[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($productProcess[$i]['machine']) || strtoupper(trim($productProcess[$i]['machine'])) == 'PROCESO MANUAL') {
                $productProcess[$i]['idMachine'] = 0;
            } else {
                // Obtener id maquina
                $findMachine = $machinesDao->findMachine($productProcess[$i], $id_company);
                $productProcess[$i]['idMachine'] = $findMachine['id_machine'];
            }

            //consultar si existe producto_proceso en bd
            //false = no, id_product_process = si
            $findProductProcess = $productsProcessDao->findProductProcess($productProcess[$i], $id_company);

            // $productProcess[$i] = $convertDataDao->strReplaceProductsProcess($productProcess[$i]);

            if (!$findProductProcess) {

                //si no se encuentra, inserta y retorna null, si se encuentra retorna 1
                $resolution = $productsProcessDao->insertProductsProcessByCompany($productProcess[$i], $id_company);

                if ($resolution == 1) {
                    $i = $i + 2;
                    $resp = array('error' => true, 'message' => "El Proceso ya se encuentra en la Base de Datos<br>Fila: {$i}");
                    break;
                } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];
            } else {
                $productProcess[$i]['idProductProcess'] = $findProductProcess['id_product_process'];
                $resolution = $productsProcessDao->updateProductsProcess($productProcess[$i]);
            }

            /* Calcular costo nomina */
            if ($resolution == null) {
                $resolution = $costWorkforceDao->calcCostPayroll($productProcess[$i]['idProduct'], $id_company);
                // Calcular costo nomina total
                if ($resolution == null) {
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($productProcess[$i]['idProduct'], $id_company);

                    $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $productProcess[$i]['idProduct'], $id_company);
                }
            }

            /* Calcular costo indirecto */
            if ($resolution == null) {
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($productProcess[$i]['idProduct'], $id_company);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // Actualizar campo
                $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $productProcess[$i]['idProduct'], $id_company);
            }

            // Calcular Precio del producto
            if ($resolution == null)
                $resolution = $priceProductDao->calcPrice($productProcess[$i]['idProduct']);

            if (isset($resolution['info']))
                break;

            $resolution = $productsDao->updatePrice($productProcess[$i]['idProduct'], $resolution['totalPrice']);

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($resolution['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($productProcess[$i]['id_product']);

                foreach ($productsCompositer as $j) {
                    if (isset($resolution['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];

                    /* Calcular costo indirecto */
                    // Buscar la maquina asociada al producto
                    // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                    // // Calcular costo indirecto
                    // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // // Actualizar campo
                    // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                    // if (isset($resolution['info'])) break;

                    // // Calcular costo nomina total
                    // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                    // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                    // if (isset($resolution['info'])) break;

                    // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    // $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                    // if (isset($resolution['info'])) break;

                    // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                    // if (isset($resolution['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($resolution['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($resolution['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);
                    $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $arr) {
                        if (isset($resolution['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $arr['id_child_product'];
                        $data['idProduct'] = $arr['id_product'];

                        /* Calcular costo indirecto */
                        // Buscar la maquina asociada al producto
                        // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                        // // Calcular costo indirecto
                        // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                        // // Actualizar campo
                        // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                        // if (isset($resolution['info'])) break;

                        // // Calcular costo nomina total
                        // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                        // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                        // if (isset($resolution['info'])) break;

                        // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                        // $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                        // if (isset($resolution['info'])) break;

                        // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                        // if (isset($resolution['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($resolution['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($resolution['info'])) break;

                        $data = $priceProductDao->calcPrice($arr['id_product']);
                        $resolution = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                    }
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Proceso importado correctamente');
        else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
        }
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductsProcess', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $convertDataDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $data = [];

    $productProcess = $productsProcessDao->findProductProcess($dataProductProcess, $id_company);

    !is_array($productProcess) ? $data['id_product_process'] = 0 : $data = $productProcess;

    if ($data['id_product_process'] == $dataProductProcess['idProductProcess'] || $data['id_product_process'] == 0) {
        // $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
        $productProcess = $productsProcessDao->updateProductsProcess($dataProductProcess);

        /* Calcular costo nomina */
        if ($productProcess == null) {
            if ($dataProductProcess['employees'] == '')
                $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
            else {
                // $employees = implode(',', $dataProductProcess['employees']);
                $resolution = $costWorkforceDao->calcCostPayrollGroupByEmployee($dataProductProcess['idProduct'], $id_company, $dataProductProcess['employees']);
            }
            // Calcular costo nomina total
            if ($resolution == null) {
                if ($dataProductProcess['employees'] == '')
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProductProcess['idProduct'], $id_company);
                else {
                    // $employees = implode(',', $dataProductProcess['employees']);
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayrollGroupByEmployee($dataProductProcess['idProduct'], $id_company, $dataProductProcess['employees']);
                }

                $productProcess = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
            }
        }

        /* Calcular costo indirecto */
        if ($productProcess == null) {
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
        }

        // Calcular Precio del producto
        if ($productProcess == null)
            $productProcess = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
        if (isset($productProcess['totalPrice']))
            $productProcess = $productsDao->updatePrice($dataProductProcess['idProduct'], $productProcess['totalPrice']);

        if ($productProcess == null && $_SESSION['flag_composite_product'] == '1') {
            // Calcular costo material porq
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductProcess['idProduct']);

            foreach ($productsCompositer as $j) {
                if (isset($productProcess['info'])) break;

                $data = [];
                $data['compositeProduct'] = $j['id_child_product'];
                $data['idProduct'] = $j['id_product'];

                /* Calcular costo indirecto */
                // if (isset($productProcess['info'])) break;
                // // Buscar la maquina asociada al producto
                // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                // // Calcular costo indirecto
                // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // // Actualizar campo
                // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);

                // // Calcular costo nomina total
                // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                // if (isset($productProcess['info'])) break;

                // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                // if (isset($productProcess['info'])) break;

                // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                // if (isset($productProcess['info'])) break;

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $productProcess = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($productProcess['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $productProcess = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($productProcess['info'])) break;

                $data = $priceProductDao->calcPrice($j['id_product']);
                $productProcess = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                if (isset($productProcess['info'])) break;

                $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                foreach ($productsCompositer2 as $arr) {
                    if (isset($productProcess['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $arr['id_child_product'];
                    $data['idProduct'] = $arr['id_product'];

                    /* Calcular costo indirecto */
                    // if (isset($productProcess['info'])) break;
                    // // Buscar la maquina asociada al producto
                    // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                    // // Calcular costo indirecto
                    // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // // Actualizar campo
                    // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);

                    // // Calcular costo nomina total
                    // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                    // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                    // if (isset($productProcess['info'])) break;

                    // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    // $productProcess = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                    // if (isset($productProcess['info'])) break;

                    // $productProcess = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                    // if (isset($productProcess['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $productProcess = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($productProcess['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $productProcess = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($productProcess['info'])) break;

                    $data = $priceProductDao->calcPrice($arr['id_product']);
                    $productProcess = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                }
            }
        }

        if ($productProcess == null)
            $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
        else if (isset($productProcess['info']))
            $resp = array('info' => true, 'message' => $productProcess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Proceso ya existente. Ingrese otro proceso');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveEmployees', function (Request $request, Response $response, $args) use (
    $generalProductsProcessDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $employees = implode(',', $dataProductProcess['employees']);
    $resolution = $generalProductsProcessDao->updateEmployees($dataProductProcess['idProductProcess'], $employees);

    /* Calcular costo nomina */
    if ($resolution == null) {
        if ($employees == '')
            $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
        else {
            $resolution = $costWorkforceDao->calcCostPayrollGroupByEmployee($dataProductProcess['idProduct'], $id_company, $employees);
        }
        // Calcular costo nomina total
        if ($resolution == null) {
            if ($employees == '')
                $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProductProcess['idProduct'], $id_company);
            else {
                $dataPayroll = $costWorkforceDao->calcTotalCostPayrollGroupByEmployee($dataProductProcess['idProduct'], $id_company, $employees);
            }

            $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
        }
    }

    /* Calcular costo indirecto */
    if ($resolution == null) {
        // Buscar la maquina asociada al producto
        $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
    }

    // Calcular Precio del producto
    if ($resolution == null)
        $product = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
    if (isset($product['totalPrice']))
        $resolution = $productsDao->updatePrice($dataProductProcess['idProduct'], $product['totalPrice']);

    if ($resolution == null && $_SESSION['flag_composite_product'] == '1') {
        // Calcular costo material porq
        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductProcess['idProduct']);

        foreach ($productsCompositer as $j) {
            if (isset($resolution['info'])) break;

            $data = [];
            $data['compositeProduct'] = $j['id_child_product'];
            $data['idProduct'] = $j['id_product'];

            /* Calcular costo indirecto */
            // Buscar la maquina asociada al producto
            // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
            // // Calcular costo indirecto
            // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // // Actualizar campo
            // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
            // if (isset($resolution['info'])) break;

            // // Calcular costo nomina total
            // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

            // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

            // if (isset($resolution['info'])) break;

            // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
            // $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
            // if (isset($resolution['info'])) break;

            // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
            // if (isset($resolution['info'])) break;

            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

            if (isset($resolution['info'])) break;
            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

            if (isset($resolution['info'])) break;

            $data = $priceProductDao->calcPrice($j['id_product']);
            $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

            if (isset($resolution['info'])) break;

            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

            foreach ($productsCompositer2 as $arr) {
                if (isset($resolution['info'])) break;

                $data = [];
                $data['compositeProduct'] = $arr['id_child_product'];
                $data['idProduct'] = $arr['id_product'];

                /* Calcular costo indirecto */
                // Buscar la maquina asociada al producto
                // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                // // Calcular costo indirecto
                // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // // Actualizar campo
                // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                // // Calcular costo nomina total
                // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                // if (isset($resolution['info'])) break;

                // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                // $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($resolution['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($resolution['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);
                $resolution = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
    else if (isset($productProcess['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductProcess', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $product = $productsProcessDao->deleteProductProcess($dataProductProcess);

    /* Calcular costo nomina */
    if ($product == null) {
        $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
        // Calcular costo nomina total
        if ($resolution == null) {
            $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProductProcess['idProduct'], $id_company);
            $product = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
        }
    }

    /* Calcular costo indirecto */
    if ($product == null) {
        // Buscar la maquina asociada al producto
        $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $product = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
    }

    // Calcular Precio del producto
    if ($product == null)
        $product = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
    if (isset($product['totalPrice']))
        $product = $productsDao->updatePrice($dataProductProcess['idProduct'], $product['totalPrice']);

    if ($product == null && $_SESSION['flag_composite_product'] == '1') {
        // Calcular costo material porq
        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProductProcess['idProduct']);

        foreach ($productsCompositer as $j) {
            if (isset($product['info'])) break;

            $data = [];
            $data['compositeProduct'] = $j['id_child_product'];
            $data['idProduct'] = $j['id_product'];

            /* Calcular costo indirecto */
            // Buscar la maquina asociada al producto
            // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
            // // Calcular costo indirecto
            // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // // Actualizar campo
            // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
            // if (isset($resolution['info'])) break;

            // // Calcular costo nomina total
            // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

            // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

            // if (isset($resolution['info'])) break;

            // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
            // $product = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
            // if (isset($product['info'])) break;

            // $product = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
            // if (isset($product['info'])) break;

            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
            $product = $generalCompositeProductsDao->updateCostCompositeProduct($data);

            if (isset($product['info'])) break;
            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            $product = $costMaterialsDao->updateCostMaterials($data, $id_company);

            if (isset($product['info'])) break;

            $data = $priceProductDao->calcPrice($j['id_product']);
            $product = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

            if (isset($product['info'])) break;

            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

            foreach ($productsCompositer2 as $arr) {
                if (isset($product['info'])) break;

                $data = [];
                $data['compositeProduct'] = $arr['id_child_product'];
                $data['idProduct'] = $arr['id_product'];

                /* Calcular costo indirecto */
                // Buscar la maquina asociada al producto
                // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                // // Calcular costo indirecto
                // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // // Actualizar campo
                // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                // // Calcular costo nomina total
                // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                // if (isset($resolution['info'])) break;

                // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                // $product = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                // if (isset($product['info'])) break;

                // $product = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                // if (isset($product['info'])) break;

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $product = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($product['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $product = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($product['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);
                $product = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            }
        }
    }
    if ($product == null)
        $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso asignado, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
