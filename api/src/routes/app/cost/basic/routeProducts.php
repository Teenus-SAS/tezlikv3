<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\GeneralCostProductsDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\GeneralExpenseDistributionDao;
use tezlikv3\dao\CostProductMaterialsDao;
use tezlikv3\dao\CostProductProcessDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralProductsMaterialsDao;
use tezlikv3\dao\GeneralProductsProcessDao;
use tezlikv3\dao\GeneralServicesDao;
use tezlikv3\dao\ImageDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\dao\ProductsCostDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\ProductsQuantityDao;

$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$generalCostProductsDao = new GeneralCostProductsDao();
$lastDataDao = new LastDataDao();
$imageDao = new ImageDao();
$productsCostDao = new ProductsCostDao();
$priceProductDao = new PriceProductDao();
$productsQuantityDao = new ProductsQuantityDao();
$productsMaterialsDao = new GeneralProductsMaterialsDao();
$generalPMaterialsDao = new CostProductMaterialsDao();
$productsProcessDao = new GeneralProductsProcessDao();
$generalPProcessDao = new CostProductProcessDao();
$externalServicesDao = new ExternalServicesDao();
$generalServicesDao = new GeneralServicesDao();
$expensesDistributionDao = new ExpensesDistributionDao();
$generalExpenseDistributionDao = new GeneralExpenseDistributionDao();
$expensesRecoverDao = new ExpenseRecoverDao();
$generalExpenseRecoverDao = new GeneralExpenseRecoverDao();
$costMaterialsDao = new CostMaterialsDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use (
    $productsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos CRM */
$app->get('/productsCRM', function (Request $request, Response $response, $args) use (
    $generalCostProductsDao
) {
    $products = $generalCostProductsDao->findAllProductsByCRM(1);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/inactivesProducts', function (Request $request, Response $response, $args) use (
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $generalCostProductsDao->findAllInactivesProducts($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productCost/{id_product}', function (Request $request, Response $response, $args) use (
    $generalCostProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $generalCostProductsDao->findProductCost($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/productsDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsDao
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            $profitability = floatval(str_replace(',', '.', $products[$i]['profitability']));
            $commissionSale = floatval(str_replace(',', '.', $products[$i]['commissionSale']));

            $profitability = 1 * $profitability;

            if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) ||
                $products[$i]['commissionSale'] == '' || is_nan($profitability) || $profitability <= 0
            ) {
                $i = $i + 1;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            } else if ($profitability > 100 || $commissionSale > 100) {
                $i = $i + 1;
                $dataImportProduct = array('error' => true, 'message' => "La rentabilidad y comision debe ser menor al 100%, fila: $i");
                break;
            } else {
                $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);
                if (!$findProduct) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportProduct['insert'] = $insert;
                $dataImportProduct['update'] = $update;
            }
        }
    } else
        $dataImportProduct = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProduct, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProducts', function (Request $request, Response $response, $args) use (
    $productsDao,
    $generalProductsDao,
    $lastDataDao,
    $imageDao,
    $productsCostDao,
    $productsQuantityDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

    if ($product['quantity'] < $product['cant_products']) {
        $dataProducts = sizeof($dataProduct);

        if ($dataProducts > 1) {
            //INGRESA id_company, referencia, producto. BD
            $products = $productsDao->insertProductByCompany($dataProduct, $id_company);

            if ($products == null) {
                //ULTIMO REGISTRO DE ID, EL MÁS ALTO
                $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

                if (sizeof($_FILES) > 0)
                    $imageDao->imageProduct($lastProductId['id_product'], $id_company);

                //AGREGA ULTIMO ID A DATA
                $dataProduct['idProduct'] = $lastProductId['id_product'];
                $productsCost = $productsCostDao->insertProductsCostByCompany($dataProduct, $id_company);
            }

            if ($products == null &&  $productsCost == null)
                $resp = array('success' => true, 'message' => 'Producto creado correctamente');
            else if (isset($products['info']))
                $resp = array('info' => true, 'message' => $products['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $products = $dataProduct['importProducts'];

            for ($i = 0; $i < sizeof($products); $i++) {

                $product = $generalProductsDao->findProduct($products[$i], $id_company);

                if (!$product) {
                    $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);

                    if ($resolution = null) {
                        $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

                        $products[$i]['idProduct'] = $lastProductId['id_product'];

                        $resolution = $productsCostDao->insertProductsCostByCompany($products[$i], $id_company);
                    }
                } else {
                    $products[$i]['idProduct'] = $product['id_product'];
                    $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);

                    if ($resolution == null)
                        $resolution = $productsCostDao->updateProductsCostByCompany($products[$i]);
                }
            }
            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Productos importados correctamente');
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
        }
    } else
        $resp = array('error' => true, 'message' => 'Para crear más productos actualice su Plan');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/copyProduct', function (Request $request, Response $response, $args) use (
    $productsDao,
    $lastDataDao,
    $productsCostDao,
    $productsQuantityDao,
    $productsMaterialsDao,
    $generalPMaterialsDao,
    $productsProcessDao,
    $generalPProcessDao,
    $externalServicesDao,
    $generalServicesDao,
    $expensesDistributionDao,
    $generalExpenseDistributionDao,
    $expensesRecoverDao,
    $generalExpenseRecoverDao,
    $costMaterialsDao,
    $costWorkforceDao,
    $indirectCostDao,
    $assignableExpenseDao,
    $priceProductDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */

    $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

    if ($product['quantity'] < $product['cant_products']) {

        //INGRESA id_company, referencia, producto. BD
        $resolution = $productsDao->insertProductByCompany($dataProduct, $id_company);

        if ($resolution == null)
            //ULTIMO REGISTRO DE ID, EL MÁS ALTO
            $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

        if (isset($lastProductId)) {
            //AGREGA ULTIMO ID A DATA
            $dataProduct['idProduct'] = $lastProductId['id_product'];
            $resolution = $productsCostDao->insertProductsCostByCompany($dataProduct, $id_company);
        }

        if ($resolution == null) {
            // Copiar data products_materials
            $oldProduct = $generalPMaterialsDao->findProductMaterialByIdProduct($dataProduct);

            foreach ($oldProduct as $arr) {
                $arr['idProduct'] = $dataProduct['idProduct'];
                $arr['material'] = $arr['id_material'];
                $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($arr, $id_company);
            }

            if ($resolution == null) {
                // Copiar data products_process
                $oldProduct = $generalPProcessDao->findProductProcessByIdProduct($dataProduct);

                $arr = array();

                foreach ($oldProduct as $arr) {
                    $arr['idProduct'] = $dataProduct['idProduct'];
                    $arr['idProcess'] = $arr['id_process'];
                    $arr['idMachine'] = $arr['id_machine'];
                    $arr['enlistmentTime'] = $arr['enlistment_time'];
                    $arr['operationTime'] = $arr['operation_time'];

                    $resolution = $productsProcessDao->insertProductsProcessByCompany($arr, $id_company);
                }
            }

            if ($resolution == null) {
                // Copiar data external_services
                $oldProduct = $generalServicesDao->findExternalServiceByIdProduct($dataProduct);

                $arr = array();

                foreach ($oldProduct as $arr) {
                    $arr['costService'] = $arr['cost'];
                    $arr['service'] = $arr['name_service'];
                    $arr['idProduct'] = $dataProduct['idProduct'];

                    $resolution = $externalServicesDao->insertExternalServicesByCompany($arr, $id_company);
                }
            }

            if ($resolution == null) {
                // Copiar data expenses_distribution
                $oldProduct = $generalExpenseDistributionDao->findExpenseDistributionByIdProduct($dataProduct, $id_company);
                $arr = array();

                if ($oldProduct != false) {
                    $arr['selectNameProduct'] = $dataProduct['idProduct'];
                    $arr['unitsSold'] = $oldProduct['units_sold'];
                    $arr['turnover'] = $oldProduct['turnover'];
                    $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($arr, $id_company);
                }
            }

            // if ($resolution == null) {
            //     // Copiar data expenses_recover
            //     $oldProduct = $generalExpenseRecoverDao->findExpenseRecoverByIdProduct($dataProduct);
            //     $arr = array();

            //     if ($oldProduct != false) {
            //         $arr['idProduct'] = $dataProduct['idProduct'];
            //         $arr['percentage'] = $oldProduct['expense_recover'];
            //         $resolution = $expensesRecoverDao->insertRecoverExpenseByCompany($arr, $id_company);
            //     }
            // }

            if ($resolution == null)
                //Metodo calcular precio total materias
                $resolution = $costMaterialsDao->calcCostMaterial($dataProduct['idProduct'], $id_company);

            if ($resolution == null)
                // Calcular costo nomina
                $resolution = $costWorkforceDao->calcCostPayroll($dataProduct, $id_company);

            if ($resolution == null)
                // Calcular costo indirecto
                $resolution = $indirectCostDao->calcCostIndirectCost($dataProduct, $id_company);

            if ($resolution == null)
                // Calcular gasto asignable
                $resolution =  $assignableExpenseDao->calcAssignableExpense($id_company);

            if ($resolution == null) {
                // Calcular Precio de los productos
                $productsCost = $productsCostDao->findAllProductsCost($id_company);

                foreach ($productsCost as $arr) {
                    $resolution = $priceProductDao->calcPrice($arr['id_product']);
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Producto copiado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras copiaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Para crear más productos actualice su Plan');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProducts', function (Request $request, Response $response, $args) use (
    $productsDao,
    $imageDao,
    $productsCostDao,
    $priceProductDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    // Actualizar Datos, Imagen y Calcular Precio del producto
    $products = $productsDao->updateProductByCompany($dataProduct, $id_company);

    if (sizeof($_FILES) > 0)
        $imageDao->imageProduct($dataProduct['idProduct'], $id_company);

    $products = $productsCostDao->updateProductsCostByCompany($dataProduct);
    $products = $priceProductDao->calcPrice($dataProduct['idProduct']);

    if ($products == null)
        $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
    else if (isset($products['info']))
        $resp = array('info' => true, 'message' => $products['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProduct', function (Request $request, Response $response, $args) use (
    $generalPMaterialsDao,
    $generalPProcessDao,
    $generalServicesDao,
    $generalExpenseDistributionDao,
    $generalExpenseRecoverDao,
    $productsCostDao,
    $generalProductsDao
) {
    $dataProduct = $request->getParsedBody();

    $productsMaterials = $generalPMaterialsDao->deleteProductMaterialByProduct($dataProduct);
    $productsProcess = $generalPProcessDao->deleteProductProcessByProduct($dataProduct);
    $externalServices = $generalServicesDao->deleteExternalServiceByProduct($dataProduct);
    $expensesDistribution = $generalExpenseDistributionDao->deleteExpensesDistributionByProduct($dataProduct);
    $expensesRecover = $generalExpenseRecoverDao->deleteRecoverExpenseByProduct($dataProduct);
    $productsCost = $productsCostDao->deleteProductsCost($dataProduct);
    $product = $generalProductsDao->deleteProduct($dataProduct['idProduct']);

    if (
        $product == null && $productsCost == null && $productsMaterials == null && $productsProcess == null &&
        $externalServices == null && $expensesDistribution == null && $expensesRecover == null
    )
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else if (isset($product['info']))
        $resp = array('info' => true, 'message' => $product['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Inactivar Producto */
$app->get('/inactiveProducts/{id_product}', function (Request $request, Response $response, $args) use ($generalCostProductsDao) {
    $product = $generalCostProductsDao->activeOrInactiveProducts($args['id_product'], 0);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Producto inactivado correctamente');
    else if (isset($products['info']))
        $resp = array('info' => true, 'message' => $products['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/* Activar Productos */
$app->post('/activeProducts', function (Request $request, Response $response, $args) use ($generalCostProductsDao) {
    $dataProducts = $request->getParsedBody();

    $products = $dataProducts['data'];

    for ($i = 0; $i < sizeof($products); $i++) {
        $resolution = $generalCostProductsDao->activeOrInactiveProducts($products[$i]['idProduct'], 1);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Productos activados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo modificar la información. Intente de nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
