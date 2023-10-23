<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\Dao\ConversionUnitsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\GeneralExpenseDistributionDao;
use tezlikv3\dao\GeneralProductMaterialsDao;
use tezlikv3\dao\GeneralProductsProcessDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\GeneralServicesDao;
use tezlikv3\dao\FilesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralMaterialsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\dao\ProductsCostDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\ProductsQuantityDao;

$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$lastDataDao = new LastDataDao();
$FilesDao = new FilesDao();
$productsCostDao = new ProductsCostDao();
$priceProductDao = new PriceProductDao();
$productsQuantityDao = new ProductsQuantityDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$materialsDao = new GeneralMaterialsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$generalPMaterialsDao = new GeneralProductMaterialsDao();
$productsProcessDao = new ProductsProcessDao();
$generalPProcessDao = new GeneralProductsProcessDao();
$externalServicesDao = new ExternalServicesDao();
$generalServicesDao = new GeneralServicesDao();
$expensesDistributionDao = new ExpensesDistributionDao();
$generalExpenseDistributionDao = new GeneralExpenseDistributionDao();
$familiesDao = new FamiliesDao();
$expensesRecoverDao = new ExpenseRecoverDao();
$generalExpenseRecoverDao = new GeneralExpenseRecoverDao();
$costMaterialsDao = new CostMaterialsDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use (
    $productsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos CRM */
$app->get('/productsCRM', function (Request $request, Response $response, $args) use (
    $generalProductsDao
) {
    $products = $generalProductsDao->findAllProductsByCRM(1);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Productos creados */
$app->get('/productsLimit', function (Request $request, Response $response, $args) use ($productsQuantityDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];

    $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

    $response->getBody()->write(json_encode($product, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/inactivesProducts', function (Request $request, Response $response, $args) use (
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $generalProductsDao->findAllInactivesProducts($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productCost/{id_product}', function (Request $request, Response $response, $args) use (
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $generalProductsDao->findProductCost($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($products));
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
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product'])) ||
                $products[$i]['commissionSale'] == '' || is_nan($profitability) || $profitability <= 0
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            } else if ($profitability > 100 || $commissionSale > 100 || is_nan($profitability) || is_nan($commissionSale)) {
                $i = $i + 2;
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
    $FilesDao,
    $productsCostDao,
    $productsQuantityDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

    if ($product['quantity'] < $product['cant_products'] || $product['quantity'] == 0 && $product['cant_products'] == 0) {
        $dataProducts = sizeof($dataProduct);

        if ($dataProducts > 1) {
            $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

            if (!$product) {
                //INGRESA id_company, referencia, producto. BD
                $products = $productsDao->insertProductByCompany($dataProduct, $id_company);

                if ($products == null) {
                    //ULTIMO REGISTRO DE ID, EL MÁS ALTO
                    $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

                    if (sizeof($_FILES) > 0)
                        $FilesDao->imageProduct($lastProductId['id_product'], $id_company);

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
            } else
                $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
        } else {
            $products = $dataProduct['importProducts'];

            for ($i = 0; $i < sizeof($products); $i++) {

                $product = $generalProductsDao->findProduct($products[$i], $id_company);

                if (!$product) {
                    $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);

                    if ($resolution == null) {
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
        $resp = array('error' => true, 'message' => 'Llegaste al limite de tu plan. Comunicate con tu administrador y sube de categoria para obtener más espacio');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/copyProduct', function (Request $request, Response $response, $args) use (
    $productsDao,
    $lastDataDao,
    $productsCostDao,
    $generalProductsDao,
    $productsQuantityDao,
    $productsMaterialsDao,
    $conversionUnitsDao,
    $materialsDao,
    $generalPMaterialsDao,
    $productsProcessDao,
    $generalPProcessDao,
    $externalServicesDao,
    $generalServicesDao,
    $expensesDistributionDao,
    $familiesDao,
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

        $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

        if (!$product) {
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
                    $arr['unit'] = $arr['id_unit'];
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
                    $flag = $_SESSION['flag_expense_distribution'];
                    $oldProduct = $generalExpenseDistributionDao->findExpenseDistributionByIdProduct($dataProduct['idOldProduct'], $id_company);
                    $arr = array();

                    if ($oldProduct != false) {
                        $arr['selectNameProduct'] = $dataProduct['idProduct'];
                        $arr['idFamily'] = $dataProduct['idFamily'];
                        $arr['unitsSold'] = $oldProduct['units_sold'];
                        $arr['turnover'] = $oldProduct['turnover'];


                        if ($flag == 2) {
                            $products = $familiesDao->findAllProductsInFamily($dataProduct['idFamily'], $id_company);

                            $resolution = $familiesDao->updateDistributionFamily($arr);

                            for ($i = 0; $i < sizeof($products); $i++) {
                                if (isset($resolution['info'])) break;

                                $products[$i]['selectNameProduct'] = $products[$i]['id_product'];
                                $products[$i]['unitsSold'] = $arr['unitsSold'];
                                $products[$i]['turnover'] = $arr['turnover'];
                                $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($products[$i], $id_company);

                                if (!$findExpenseDistribution)
                                    $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($products[$i], $id_company);
                                else {
                                    $products[$i]['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                                    $resolution = $expensesDistributionDao->updateExpensesDistribution($products[$i], $id_company);
                                }
                            }
                        } else {
                            $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($arr, $id_company);

                            if (!$findExpenseDistribution)
                                $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($arr, $id_company);
                            else {
                                $dataExpensesDistribution['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                                $resolution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution, $id_company);
                            }
                        }
                    }
                }

                if ($resolution == null) {
                    // Copiar data expenses_recover
                    $oldProduct = $generalExpenseRecoverDao->findExpenseRecoverByIdProduct($dataProduct['idOldProduct']);
                    $arr = array();

                    if ($oldProduct != false) {
                        $arr['idProduct'] = $dataProduct['idProduct'];
                        $arr['percentage'] = $oldProduct['expense_recover'];
                        $resolution = $expensesRecoverDao->insertRecoverExpenseByCompany($arr, $id_company);

                        isset($resolution['info']) ? $resolution = null : $resolution;
                    }
                }

                if ($resolution == null) {
                    $productsMaterials = $productsMaterialsDao->findAllProductsmaterialsByIdProduct($dataProduct['idProduct'], $id_company);

                    // $totalQuantity = 0;

                    foreach ($productsMaterials as $arr) {
                        if ($resolution != null) break;
                        // Obtener materia prima
                        $material = $materialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                        // Convertir unidades
                        $quantities = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                        // Modificar costo
                        $materialsDao->updateCostProductMaterial($arr, $quantities);
                    }
                    // Metodo calcular precio total materias
                    $dataMaterial = $costMaterialsDao->calcCostMaterial($dataProduct, $id_company);

                    $resolution = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
                }

                if ($resolution == null) {
                    // Calcular costo nomina
                    $resolution = $costWorkforceDao->calcCostPayroll($dataProduct['idProduct'], $id_company);
                    // Calcular costo nomina total
                    if ($resolution == null) {
                        $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($dataProduct['idProduct'], $id_company);

                        $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $dataProduct['idProduct'], $id_company);
                    }
                }

                if ($resolution == null) {
                    // Buscar la maquina asociada al producto
                    $dataProductMachine = $indirectCostDao->findMachineByProduct($dataProduct['idProduct'], $id_company);
                    // Calcular costo indirecto
                    $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // Actualizar campo
                    $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $dataProduct['idProduct'], $id_company);
                }

                if ($resolution == null) {
                    // Obtener el total de gastos
                    $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

                    if ($flag == 1) {
                        // Consulta unidades vendidades y volumenes de venta por producto
                        $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

                        // Calcular el total de unidades vendidas y volumen de ventas
                        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

                        foreach ($unitVol as $arr) {
                            if (isset($resolution['info'])) break;
                            // Calcular gasto asignable
                            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                            // Actualizar gasto asignable
                            $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
                        }

                        /* x Familia */
                    } else {
                        // Consulta unidades vendidades y volumenes de venta por familia
                        $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

                        // Calcular el total de unidades vendidas y volumen de ventas
                        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

                        foreach ($unitVol as $arr) {
                            if (isset($resolution['info'])) break;
                            // Calcular gasto asignable
                            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                            // Actualizar gasto asignable
                            $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
                        }
                    }
                }

                if ($resolution == null) {
                    // Calcular Precio de los productos
                    $productsCost = $productsCostDao->findAllProductsCost($id_company);

                    foreach ($productsCost as $arr) {
                        $resolution = $priceProductDao->calcPrice($arr['id_product']);

                        if (isset($resolution['info']))
                            break;

                        $resolution = $generalProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);
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
            $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
    } else
        $resp = array('error' => true, 'message' => 'Llegaste al limite de tu plan. Comunicate con tu administrador y sube de categoria para obtener más espacio');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProducts', function (Request $request, Response $response, $args) use (
    $productsDao,
    $FilesDao,
    $productsCostDao,
    $generalProductsDao,
    $priceProductDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    $data = [];

    $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    !is_array($product) ? $data['id_product'] = 0 : $data = $product;

    if ($data['id_product'] == $dataProduct['idProduct'] || $data['id_product'] == 0) {
        // Actualizar Datos, Imagen y Calcular Precio del producto
        $products = $productsDao->updateProductByCompany($dataProduct, $id_company);

        if (sizeof($_FILES) > 0)
            $FilesDao->imageProduct($dataProduct['idProduct'], $id_company);

        $products = $productsCostDao->updateProductsCostByCompany($dataProduct);

        if ($products == null)
            $products = $priceProductDao->calcPrice($dataProduct['idProduct']);
        if (isset($products['totalPrice']))
            $products = $generalProductsDao->updatePrice($dataProduct['idProduct'], $products['totalPrice']);

        if ($products == null) {
            // Calcular costo material porq
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProduct['idProduct']);

            foreach ($productsCompositer as $arr) {
                if (isset($products['info'])) break;

                $data = [];
                $data['compositeProduct'] = $arr['id_child_product'];
                $data['idProduct'] = $arr['id_product'];
                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($resolution['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $products = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($products['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);
                $products = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
            }
        }

        if ($products == null)
            $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
        else if (isset($products['info']))
            $resp = array('info' => true, 'message' => $products['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
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
    $productsDao,
    $generalCompositeProductsDao
) {
    $dataProduct = $request->getParsedBody();

    $productsMaterials = $generalPMaterialsDao->deleteProductMaterialByProduct($dataProduct);
    $productsProcess = $generalPProcessDao->deleteProductProcessByProduct($dataProduct);
    $externalServices = $generalServicesDao->deleteExternalServiceByProduct($dataProduct);
    $expensesDistribution = $generalExpenseDistributionDao->deleteExpensesDistributionByProduct($dataProduct);
    $expensesRecover = $generalExpenseRecoverDao->deleteRecoverExpenseByProduct($dataProduct);
    $productsCost = $productsCostDao->deleteProductsCost($dataProduct);
    $productsCompositer = $generalCompositeProductsDao->deleteCompositeProductByProduct($dataProduct['idProduct']);
    $product = $productsDao->deleteProduct($dataProduct['idProduct']);

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
$app->get('/inactiveProducts/{id_product}', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $assignableExpenseDao,
    $familiesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];

    $product = $generalProductsDao->activeOrInactiveProducts($args['id_product'], 0);

    if ($product == null) {
        // Obtener el total de gastos
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        if ($flag == 1) {
            // Consulta unidades vendidades y volumenes de venta por producto
            $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
            }

            /* x Familia */
        } else {
            // Consulta unidades vendidades y volumenes de venta por familia
            $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
            }
        }
    }

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
$app->post('/activeProducts', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $assignableExpenseDao,
    $familiesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];
    $dataProducts = $request->getParsedBody();

    $products = $dataProducts['data'];

    for ($i = 0; $i < sizeof($products); $i++) {
        $resolution = $generalProductsDao->activeOrInactiveProducts($products[$i]['idProduct'], 1);
    }

    if ($resolution == null) {
        // Obtener el total de gastos
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        if ($flag == 1) {
            // Consulta unidades vendidades y volumenes de venta por producto
            $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
            }

            /* x Familia */
        } else {
            // Consulta unidades vendidades y volumenes de venta por familia
            $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
            }
        }
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

$app->get('/changeComposite/{id_product}/{op}', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    $status = true;

    if ($args['op'] == 0) {
        $product = $generalCompositeProductsDao->findCompositeProductByChild($args['id_product']);
        if (sizeof($product) == 0)
            $status = false;
    }

    if ($status == true) {
        $product = $generalProductsDao->changeCompositeProduct($args['id_product'], $args['op']);

        if ($product == null)
            $resp = array('success' => true, 'message' => 'Producto modificado correctamente');
        else if (isset($product['info']))
            $resp = array('info' => true, 'message' => $product['message']);
        else
            $resp = array('error' => true, 'message' => 'No se pudo modificar la información. Intente de nuevo');
    } else
        $resp = array('error' => true, 'message' => 'No se pudo desactivar el producto. Tiene datos relacionados a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
