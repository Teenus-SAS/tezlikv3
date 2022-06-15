<?php

use tezlikv2\dao\ProductsProcessDao;
use tezlikv2\dao\ProductsDao;
use tezlikv2\dao\ProcessPayrollDao;
use tezlikv2\dao\MachinesDao;
use tezlikv2\dao\CostWorkforceDao;
use tezlikv2\dao\IndirectCostDao;
use tezlikv2\Dao\PriceProductDao;

$productsProcessDao = new ProductsProcessDao();
$productsDao = new ProductsDao();
$processPayrollDao = new ProcessPayrollDao();
$machinesDao = new MachinesDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Productos procesos
$app->get('/productsProcess/{idProduct}', function (Request $request, Response $response, $args) use ($productsProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $productsProcessDao->productsprocess($args['idProduct'], $id_company);
    $response->getBody()->write(json_encode($productProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Consultar productos procesos importados
$app->post('/productsProcessDataValidation', function (Request $request, Response $response, $args) use ($productsProcessDao, $productsDao, $processPayrollDao, $machinesDao) {
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

            // $enlistmentTime = $productProcess[$i]['enlistmentTime'];
            // $operationTime = $productProcess[$i]['operationTime'];
            // if (!isset($enlistmentTime) || !isset($operationTime)) {
            //     $i = $i + 1;
            //     $dataImportProductProcess = array('error' => true, 'message' => "Campos vacios en fila {$i}");
            //     break;
            // } else {
            //     $findProductProcess = $productsProcessDao->findProductProcess($productProcess[$i], $id_company);

            //     if (!$findProductProcess) $insert = $insert + 1;
            //     else $update = $update + 1;
            //     $dataImportProductProcess['insert'] = $insert;
            //     $dataImportProductProcess['update'] = $update;
            // }
        }
    } else
        $dataImportProductProcess = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProductProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductsProcess', function (Request $request, Response $response, $args) use ($productsProcessDao, $productsDao, $processPayrollDao, $machinesDao, $costWorkforceDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $dataProductsProcess = sizeof($dataProductProcess);

    if ($dataProductsProcess > 1) {
        $productProcess = $productsProcessDao->insertProductsProcessByCompany($dataProductProcess, $id_company);

        /* Calcular costo nomina */
        $costPayroll = $costWorkforceDao->calcCostPayroll($dataProductProcess, $id_company);

        /* Calcular costo indirecto */
        $indirectCost = $indirectCostDao->calcCostIndirectCost($dataProductProcess, $id_company);

        // Calcular Precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataProductProcess['idProduct']);

        if (
            $productProcess == null && $costPayroll == null &&
            $indirectCost == null && $priceProduct == null
        )
            $resp = array('success' => true, 'message' => 'Proceso asignado correctamente');
        elseif ($productProcess == 1)
            $resp = array('error' => true, 'message' => 'El Proceso ya se encuentra en la Base de Datos');
        else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
        }
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
            $costPayroll = $costWorkforceDao->calcCostPayroll($productProcess[$i], $id_company);

            /* Calcular costo indirecto */
            $indirectCost = $indirectCostDao->calcCostIndirectCost($productProcess[$i], $id_company);

            // Calcular Precio del producto
            $priceProduct = $priceProductDao->calcPrice($productProcess[$i]['idProduct']);
        }

        if (
            $resolution == null && $costPayroll == null &&
            $indirectCost == null && $priceProduct == null
        )
            $resp = array('success' => true, 'message' => 'Proceso importado correctamente');
        else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
        }
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductsProcess', function (Request $request, Response $response, $args) use ($productsProcessDao, $costWorkforceDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    if (empty($dataProductProcess['idProduct'] || empty($dataProductProcess['idProcess']) || empty($dataProductProcess['idMachine']) || empty($dataProductProcess['enlistmentTime']) || empty($dataProductProcess['operationTime'])))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $productProcess = $productsProcessDao->updateProductsProcess($dataProductProcess);

        /* Calcular costo nomina */
        $costPayroll = $costWorkforceDao->calcCostPayroll($dataProductProcess, $id_company);

        /* Calcular costo indirecto */
        $indirectCost = $indirectCostDao->calcCostIndirectCost($dataProductProcess, $id_company);

        // Calcular Precio del producto
        $priceProduct = $priceProductDao->calcPrice($dataProductProcess['idProduct']);

        if (
            $productProcess == null && $costPayroll == null &&
            $indirectCost == null && $priceProduct == null
        )
            $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductProcess', function (Request $request, Response $response, $args) use ($productsProcessDao, $costWorkforceDao, $indirectCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $product = $productsProcessDao->deleteProductProcess($dataProductProcess);

    /* Calcular costo nomina */
    $costPayroll = $costWorkforceDao->calcCostPayroll($dataProductProcess, $id_company);

    /* Calcular costo indirecto */
    $indirectCost = $indirectCostDao->calcCostIndirectCost($dataProductProcess, $id_company);

    // Calcular Precio del producto
    $priceProduct = $priceProductDao->calcPrice($dataProductProcess['idProduct']);

    if (
        $product == null && $costPayroll == null &&
        $indirectCost == null && $priceProduct == null
    )
        $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso asignado, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
