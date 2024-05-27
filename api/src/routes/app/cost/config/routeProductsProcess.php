<?php

use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\GeneralPayrollDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralMachinesDao;
use tezlikv3\dao\GeneralProcessDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralProductsProcessDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\Dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\WebTokenDao;

$productsProcessDao = new ProductsProcessDao();
$generalProductsProcessDao = new GeneralProductsProcessDao();
$webTokenDao = new WebTokenDao();
$convertDataDao = new ConvertDataDao();
$lastDataDao = new LastDataDao();
$productsDao = new GeneralProductsDao();
$generalProcessDao = new GeneralProcessDao();
$generalPayrollDao = new GeneralPayrollDao();
$machinesDao = new GeneralMachinesDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Productos procesos
$app->get('/productsProcess/{idProduct}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsProcessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $productsProcessDao->findAllProductsprocessByIdProduct($args['idProduct'], $id_company);
    $response->getBody()->write(json_encode($productProcess));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allProductsProcess', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalProductsProcessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $generalProductsProcessDao->findAllProductsprocess($id_company);
    $response->getBody()->write(json_encode($productProcess));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/employees/{id_product_process}', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalProductsProcessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $employees = $generalProductsProcessDao->findAllEmloyeesByProcess($args['id_product_process']);
    $response->getBody()->write(json_encode($employees));
    return $response->withHeader('Content-Type', 'application/json');
});

// Consultar productos procesos importados
$app->post('/productsProcessDataValidation', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsProcessDao,
    $productsDao,
    $generalProcessDao,
    $generalPayrollDao,
    $machinesDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $dataProductProcess = $request->getParsedBody();

    if (isset($dataProductProcess)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productProcess = $dataProductProcess['importProductsProcess'];

        for ($i = 0; $i < sizeof($productProcess); $i++) {
            if (
                empty($productProcess[$i]['referenceProduct']) || empty($productProcess[$i]['product']) || empty($productProcess[$i]['process']) || empty($productProcess[$i]['machine']) ||
                $productProcess[$i]['enlistmentTime'] == '' || $productProcess[$i]['operationTime'] == '' || $productProcess[$i]['efficiency'] == ''
                || empty($productProcess[$i]['autoMachine'])
            ) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }
            if (
                empty(trim($productProcess[$i]['referenceProduct'])) || empty(trim($productProcess[$i]['product'])) || empty(trim($productProcess[$i]['process'])) || empty(trim($productProcess[$i]['machine'])) ||
                trim($productProcess[$i]['enlistmentTime']) == '' || trim($productProcess[$i]['operationTime']) == '' || trim($productProcess[$i]['efficiency']) == ''
                || empty(trim($productProcess[$i]['autoMachine']))
            ) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productProcess[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $generalProcessDao->findProcess($productProcess[$i], $id_company);
            if (!$findProcess) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila: {$i}");
                break;
            }

            if ($productProcess[$i]['autoMachine'] == 'NO' && strtoupper(trim($productProcess[$i]['machine'])) != 'PROCESO MANUAL') {
                $findProcess = $generalPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
                if (!$findProcess) {
                    $i = $i + 2;
                    $dataImportProductProcess = array('error' => true, 'message' => "No existe nomina asociada a este proceso<br>Fila: {$i}");
                    break;
                }
            }

            if ($productProcess[$i]['autoMachine'] == 'SI' && strtoupper(trim($productProcess[$i]['machine'])) == 'PROCESO MANUAL') {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "No se permite esa maquina autonoma<br>Fila: {$i}");
                break;
            }

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
    $webTokenDao,
    $productsProcessDao,
    $generalProcessDao,
    $generalProductsProcessDao,
    $lastDataDao,
    $productsDao,
    $generalPayrollDao,
    $machinesDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataProductProcess = $request->getParsedBody();

    $dataProductsProcess = sizeof($dataProductProcess);

    if ($dataProductsProcess > 1) {
        $productProcess = $productsProcessDao->findProductProcess($dataProductProcess, $id_company);

        if (!$productProcess) {
            // $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
            $productProcess = $productsProcessDao->insertProductsProcessByCompany($dataProductProcess, $id_company);

            /* Ruta */
            if ($productProcess == null) {
                $lastInserted = $lastDataDao->findLastInsertedProductProcess($id_company);

                $lastRoute = $generalProductsProcessDao->findNextRouteByProduct($dataProductProcess['idProduct']);

                $productProcess = $generalProductsProcessDao->changeRouteById($lastInserted['id_product_process'], $lastRoute['route']);
            }

            /* Calcular costo nomina */
            if ($productProcess == null) {
                if ($dataProductProcess['autoMachine'] == '0') {
                    if ($_SESSION['inyection'] == 1)
                        $resolution = $costWorkforceDao->calcCostPayrollInyection($dataProductProcess['idProduct'], $id_company);
                    else
                        $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
                }

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

            $data = [];
            // Calcular Precio del producto
            if ($productProcess == null)
                $data = $priceProductDao->calcPrice($dataProductProcess['idProduct']);

            if (isset($data['totalPrice']))
                $productProcess = $productsDao->updatePrice($dataProductProcess['idProduct'], $data['totalPrice']);

            if ($productProcess == null && isset($data['totalPrice'])) {
                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $dataProductProcess['idProduct'];

                $productProcess = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

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

                    if (isset($data['totalPrice']))
                        $productProcess = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($productProcess['info'])) break;

                    // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $j['id_product'];

                    $productProcess = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

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

                        if (isset($data['totalPrice']))
                            $productProcess = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                        if (isset($productProcess['info'])) break;
                        // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $productProcess = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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
            if (isset($resolution['info'])) break;

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productProcess[$i], $id_company);
            $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            if ($productProcess[$i]['autoMachine'] == 'NO' && strtoupper(trim($productProcess[$i]['machine'])) != 'PROCESO MANUAL') {
                $findProcess = $generalPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
            } else
                $findProcess = $generalProcessDao->findProcess($productProcess[$i], $id_company);

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
            $productProcess[$i]['autoMachine'] == 'SI' ? $productProcess[$i]['autoMachine'] = 1 : $productProcess[$i]['autoMachine'] = 0;

            if (!$findProductProcess) {
                //si no se encuentra, inserta y retorna null, si se encuentra retorna 1
                $resolution = $productsProcessDao->insertProductsProcessByCompany($productProcess[$i], $id_company);

                if ($resolution == 1) {
                    $i = $i + 2;
                    $resp = array('error' => true, 'message' => "El Proceso ya se encuentra en la Base de Datos<br>Fila: {$i}");
                    break;
                } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];

                $lastInserted = $lastDataDao->findLastInsertedProductProcess($id_company);

                $lastRoute = $generalProductsProcessDao->findNextRouteByProduct($productProcess[$i]['idProduct']);

                $resolution = $generalProductsProcessDao->changeRouteById($lastInserted['id_product_process'], $lastRoute['route']);
            } else {
                $productProcess[$i]['idProductProcess'] = $findProductProcess['id_product_process'];
                $resolution = $productsProcessDao->updateProductsProcess($productProcess[$i]);
            }

            /* Calcular costo nomina */
            if ($resolution == null) {
                if ($productProcess[$i]['autoMachine'] == 0) {
                    if ($_SESSION['inyection'] == 1)
                        $resolution = $costWorkforceDao->calcCostPayrollInyection($productProcess[$i]['idProduct'], $id_company);
                    else
                        $resolution = $costWorkforceDao->calcCostPayroll($productProcess[$i]['idProduct'], $id_company);
                } else {
                    if (isset($productProcess[$i]['idProductProcess'])) {
                        $resolution = $generalProductsProcessDao->updateEmployees($productProcess[$i]['idProductProcess'], '');
                        $resolution = $costWorkforceDao->updateTotalCostWorkforceByProductProcess(0, $dataProductProcess['idProductProcess'], $id_company);
                    }
                }
            }

            // Calcular costo nomina total
            if ($resolution == null) {
                $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($productProcess[$i]['idProduct'], $id_company);

                $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $productProcess[$i]['idProduct'], $id_company);
            }

            /* Calcular costo indirecto */
            if ($resolution == null) {
                // Buscar la maquina asociada al producto
                $dataProductMachine = $indirectCostDao->findMachineByProduct($productProcess[$i]['idProduct'], $id_company);
                // Cambiar a 0
                $indirectCostDao->updateCostIndirectCostByProduct(0, $productProcess[$i]['idProduct']);
                // Calcular costo indirecto
                $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // Actualizar campo
                $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $productProcess[$i]['idProduct'], $id_company);
            }

            // Calcular Precio del producto
            $data = [];
            if ($resolution == null)
                $data = $priceProductDao->calcPrice($productProcess[$i]['idProduct']);

            if (isset($data['totalPrice']))
                $resolution = $productsDao->updatePrice($productProcess[$i]['idProduct'], $data['totalPrice']);

            if (isset($resolution['info'])) break;
            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $productProcess[$i]['idProduct'];

            $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($resolution['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($productProcess[$i]['idProduct']);

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

                    if (isset($data['totalPrice']))
                        $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;

                    // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $j['id_product'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

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

                        if (isset($data['totalPrice']))
                            $resolution = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                        if (isset($resolution['info'])) break;
                        // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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
    $webTokenDao,
    $productsProcessDao,
    $generalProductsProcessDao,
    $convertDataDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataProductProcess = $request->getParsedBody();

    $data = [];

    $productProcess = $productsProcessDao->findProductProcess($dataProductProcess, $id_company);

    !is_array($productProcess) ? $data['id_product_process'] = 0 : $data = $productProcess;

    if ($data['id_product_process'] == $dataProductProcess['idProductProcess'] || $data['id_product_process'] == 0) {
        // $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
        $productProcess = $productsProcessDao->updateProductsProcess($dataProductProcess);

        /* Calcular costo nomina */
        $productProcess = $costWorkforceDao->updateTotalCostWorkforceByProductProcess(0, $dataProductProcess['idProductProcess']);

        if ($productProcess == null) {
            if ($dataProductProcess['autoMachine'] == '0') {
                if ($dataProductProcess['employees'] == '' || $_SESSION['flag_employee'] == 0) {
                    if ($_SESSION['inyection'] == 1)
                        $productProcess = $costWorkforceDao->calcCostPayrollInyection($dataProductProcess['idProduct'], $id_company);
                    else
                        $productProcess = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
                } else {
                    if ($_SESSION['inyection'] == 1)
                        $productProcess = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($dataProductProcess['idProduct'], $dataProductProcess['employees']);
                    else {
                        // if ($_SESSION['flag_employee'] == 1)
                        $productProcess = $costWorkforceDao->calcCostPayrollGroupByEmployee($dataProductProcess['idProduct'], $id_company, $dataProductProcess['employees']);
                        // else
                        //     $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
                    }
                }
            } else {
                $productProcess = $generalProductsProcessDao->updateEmployees($dataProductProcess['idProductProcess'], '');
                // $productProcess = $costWorkforceDao->updateTotalCostWorkforce(0, $dataProductProcess['idProduct'], $id_company);
                // $productProcess = $costWorkforceDao->calcTotalCostPayroll($dataProductProcess['idProduct'], $id_company);
            }

            // Calcular costo nomina total
            if ($productProcess == null) {
                if ($dataProductProcess['employees'] == '' || $_SESSION['flag_employee'] == 0)
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
            // Cambiar a 0
            $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProductProcess['idProduct']);
            // Calcular costo indirecto
            $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
            // Actualizar campo
            $productProcess = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
        }

        // Calcular Precio del producto
        $data = [];
        if ($productProcess == null)
            $data = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
        if (isset($data['totalPrice']))
            $productProcess = $productsDao->updatePrice($dataProductProcess['idProduct'], $data['totalPrice']);

        if ($productProcess == null && isset($data['totalPrice'])) {
            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $dataProductProcess['idProduct'];

            $productProcess = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
        }

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

                if (isset($data['totalPrice']))
                    $productProcess = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

                if (isset($productProcess['info'])) break;

                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $j['id_product'];

                $productProcess = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

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

                    if (isset($data['totalPrice']))
                        $productProcess = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                    if (isset($productProcess['info'])) break;

                    // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $arr['id_product'];

                    $productProcess = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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
    $webTokenDao,
    $generalProductsProcessDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataProductProcess = $request->getParsedBody();

    $employees = implode(',', $dataProductProcess['employees']);
    $resolution = $generalProductsProcessDao->updateEmployees($dataProductProcess['idProductProcess'], $employees);

    /* Calcular costo nomina */
    if ($resolution == null) {
        if ($employees == '' || $_SESSION['flag_employee'] == 0) {
            if ($_SESSION['inyection'] == 1)
                $resolution = $costWorkforceDao->calcCostPayrollInyection($dataProductProcess['idProduct'], $id_company);
            else
                $resolution = $costWorkforceDao->calcCostPayroll($dataProductProcess['idProduct'], $id_company);
        } else {
            if ($_SESSION['inyection'] == 1)
                $resolution = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($dataProductProcess['idProduct'], $employees);
            else
                $resolution = $costWorkforceDao->calcCostPayrollGroupByEmployee($dataProductProcess['idProduct'], $id_company, $employees);
        }
        // Calcular costo nomina total
        if ($resolution == null) {
            if ($employees == '' || $_SESSION['flag_employee'] == 0)
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
        // Cambiar a 0
        $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProductProcess['idProduct']);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
    }

    $data = [];
    // Calcular Precio del producto
    if ($resolution == null)
        $data = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
    if (isset($data['totalPrice']))
        $resolution = $productsDao->updatePrice($dataProductProcess['idProduct'], $data['totalPrice']);

    if ($resolution == null && $data['totalPrice']) {
        // Convertir a Dolares 
        $k = [];
        $k['price'] = $data['totalPrice'];
        $k['sale_price'] = $data['sale_price'];
        $k['id_product'] = $dataProductProcess['idProduct'];

        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    }

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

            if (isset($data['totalPrice']))
                $resolution = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

            if (isset($resolution['info'])) break;

            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $j['id_product'];

            $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

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

                if (isset($data['totalPrice']))
                    $resolution = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if (isset($resolution['info'])) break;

                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $arr['id_product'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

$app->post('/saveRouteProductProcess', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $generalProductsProcessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $dataProcess = $request->getParsedBody();

    $process = $dataProcess['data'];

    $resolution = null;

    for ($i = 0; $i < sizeof($process); $i++) {
        $resolution = $generalProductsProcessDao->changeRouteById($process[$i]['id_product_process'], $process[$i]['route']);

        if (isset($resolution['info'])) break;
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Filas modificadas correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductProcess', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsProcessDao,
    $costWorkforceDao,
    $indirectCostDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataProductProcess = $request->getParsedBody();

    $product = $productsProcessDao->deleteProductProcess($dataProductProcess);

    /* Calcular costo nomina */
    if ($product == null) {
        if ($_SESSION['inyection'] == 1)
            $resolution = $costWorkforceDao->calcCostPayrollInyection($dataProductProcess['idProduct'], $id_company);
        else
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
        // Cambiar a 0
        $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProductProcess['idProduct']);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $product = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProductProcess['idProduct'], $id_company);
    }

    // Calcular Precio del producto
    $data = [];
    if ($product == null)
        $product = $priceProductDao->calcPrice($dataProductProcess['idProduct']);
    if (isset($product['totalPrice']))
        $product = $productsDao->updatePrice($dataProductProcess['idProduct'], $product['totalPrice']);

    if ($product == null && isset($data['totalPrice'])) {
        // Convertir a Dolares 
        $k = [];
        $k['price'] = $data['totalPrice'];
        $k['sale_price'] = $data['sale_price'];
        $k['id_product'] = $dataProductProcess['idProduct'];

        $product = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
    }

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

            if (isset($data['totalPrice']))
                $product = $productsDao->updatePrice($j['id_product'], $data['totalPrice']);

            if (isset($product['info'])) break;

            // Convertir a Dolares 
            $k = [];
            $k['price'] = $data['totalPrice'];
            $k['sale_price'] = $data['sale_price'];
            $k['id_product'] = $j['id_product'];

            $product = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);

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

                if (isset($data['totalPrice']))
                    $product = $productsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if (isset($product['info'])) break;
                // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $arr['id_product'];

                $product = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
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

$app->get('/calcAllIndirectCost', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $productsDao,
    $indirectCostDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $products = $productsDao->findAllProductsByCRM($id_company);

    $resolution = null;

    foreach ($products as $arr) {
        // Buscar la maquina asociada al producto
        $dataProductMachine = $indirectCostDao->findMachineByProduct($arr['id_product'], $id_company);
        // Cambiar a 0
        $indirectCostDao->updateCostIndirectCostByProduct(0, $arr['id_product']);
        // Calcular costo indirecto
        $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
        // Actualizar campo
        $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $arr['id_product'], $id_company);

        if (isset($resolution['info'])) break;
    }

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'costos indirectos calculados correctamente en todos los productos'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la informacion. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
