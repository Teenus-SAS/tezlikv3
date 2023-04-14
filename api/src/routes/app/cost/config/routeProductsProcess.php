<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\ProcessPayrollDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\Dao\PriceProductDao;

$productsProcessDao = new ProductsProcessDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$processPayrollDao = new ProcessPayrollDao();
$machinesDao = new GeneralMachinesDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Productos procesos
$app->get('/productsProcess/{idProduct}', function (Request $request, Response $response, $args) use ($productsProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $productsProcessDao->findAllProductsprocess($args['idProduct'], $id_company);
    $response->getBody()->write(json_encode($productProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Consultar productos procesos importados
$app->post('/productsProcessDataValidation', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $productsDao,
    $processPayrollDao,
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
                $i = $i + 1;
                $dataImportProductProcess = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $processPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
            if (!$findProcess) {
                $i = $i + 1;
                $dataImportProductProcess = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila: {$i}");
                break;
            } else
                $productProcess[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($productProcess[$i]['machine'])) {
                $productProcess[$i]['idMachine'] = 0;
            } else {
                $findMachine = $machinesDao->findMachine($productProcess[$i], $id_company);
                if (!$findMachine) {
                    $i = $i + 1;
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
    $processPayrollDao,
    $machinesDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $GeneralProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $dataProductsProcess = sizeof($dataProductProcess);

    if ($dataProductsProcess > 1) {

        $productProcess = $productsProcessDao->findProductProcess($dataProductProcess, $id_company);

        if (!$productProcess) {
            $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
            $productProcess = $productsProcessDao->insertProductsProcessByCompany($dataProductProcess, $id_company);

            /* Calcular costo nomina */
            if ($productProcess == null) {
                $dataPayroll = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
                $productProcess = $costWorkforceDao->updateCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
            }

            // Calcular costo indirecto
            if ($productProcess == null) {
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);

                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine, $id_company);

                // Modificar campo
                $productProcess = $indirectCostDao->updateCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
            }

            // Calcular Precio del producto
            if ($productProcess == null)
                $productProcess = $priceProductDao->calcPrice($dataProductProcess['idProduct']);

            if (isset($productProcess['totalPrice']))
                $productProcess = $GeneralProductsDao->updatePrice($dataProductProcess['idProduct'], $productProcess['totalPrice']);

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
            $findProcess = $processPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
            $productProcess[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($productProcess[$i]['machine'])) {
                $productProcess[$i]['idMachine'] = 0;
            } else {
                // Obtener id maquina
                $findMachine = $machinesDao->findMachine($productProcess[$i], $id_company);
                $productProcess[$i]['idMachine'] = $findMachine['id_machine'];
            }

            //consultar si existe producto_proceso en bd
            //false = no, id_product_process = si
            $findProductProcess = $productsProcessDao->findProductProcess($productProcess[$i], $id_company);

            $productProcess[$i] = $convertDataDao->strReplaceProductsProcess($productProcess[$i]);

            if (!$findProductProcess) {

                //si no se encuentra, inserta y retorna null, si se encuentra retorna 1
                $resolution = $productsProcessDao->insertProductsProcessByCompany($productProcess[$i], $id_company);

                if ($resolution == 1) {
                    $i = $i + 1;
                    $resp = array('error' => true, 'message' => "El Proceso ya se encuentra en la Base de Datos<br>Fila: {$i}");
                    break;
                } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];
            } else {
                $productProcess[$i]['idProductProcess'] = $findProductProcess['id_product_process'];
                $resolution = $productsProcessDao->updateProductsProcess($productProcess[$i]);
            }

            /* Calcular costo nomina */
            if ($resolution == null) {
                $dataPayroll = $costWorkforceDao->calcCostPayroll($productProcess[$i]['idProduct'], $id_company);

                $resolution = $costWorkforceDao->updateCostWorkforce($dataPayroll['cost'], $productProcess[$i]['idProduct'], $id_company);
            }

            /* Calcular costo indirecto */
            if ($resolution == null) {
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($productProcess[$i]['idProduct'], $id_company);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // Actualizar campo
                $resolution = $indirectCostDao->updateCostIndirectCost($indirectCost, $productProcess[$i]['idProduct'], $id_company);
            }

            // Calcular Precio del producto
            if ($resolution == null)
                $resolution = $priceProductDao->calcPrice($productProcess[$i]['idProduct']);

            if (isset($resolution['info']))
                break;

            $resolution = $GeneralProductsDao->updatePrice($dataProductProcess[$i]['idProduct'], $resolution['totalPrice']);
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
    $GeneralProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $productProcess = $productsProcessDao->findProductProcess($dataProductProcess, $id_company);

    !isset($productProcess['id_product_process']) ? $productProcess['id_product_process'] = 0 : $productProcess;

    if ($productProcess['id_product_process'] == $dataProductProcess['idProductProcess'] || !$productProcess) {
        $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
        $productProcess = $productsProcessDao->updateProductsProcess($dataProductProcess);

        /* Calcular costo nomina */
        if ($productProcess == null) {
            $dataPayroll = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
            $productProcess = $costWorkforceDao->updateCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
        }

        /* Calcular costo indirecto */
        if ($productProcess == null) {
            // Buscar la maquina asociada al producto
            $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $productProcess = $indirectCostDao->updateCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
        }

        // Calcular Precio del producto
        if ($productProcess == null)
            $productProcess = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
        if (isset($productProcess['totalPrice']))
            $productProcess = $GeneralProductsDao->updatePrice($dataProductProcess['idProduct'], $productProcess['totalPrice']);

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

$app->post('/deleteProductProcess', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $GeneralProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $product = $productsProcessDao->deleteProductProcess($dataProductProcess);

    /* Calcular costo nomina */
    if ($product == null) {
        $dataPayroll = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
        $product = $costWorkforceDao->updateCostWorkforce($dataPayroll['cost'], $dataProductProcess['idProduct'], $id_company);
    }

    /* Calcular costo indirecto */
    if ($product == null) {
        // Buscar la maquina asociada al producto
        $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProductProcess['idProduct'], $id_company);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $product = $indirectCostDao->updateCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
    }

    // Calcular Precio del producto
    if ($product == null)
        $product = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
    if (isset($product['totalPrice']))
        $product = $GeneralProductsDao->updatePrice($dataProductProcess['idProduct'], $product['totalPrice']);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso asignado, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
