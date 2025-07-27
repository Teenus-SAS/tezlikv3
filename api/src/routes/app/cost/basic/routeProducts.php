<?php

use tezlikv3\dao\{
    AssignableExpenseDao,
    CalcRecoveryExpensesDao,
    ConversionUnitsDao,
    CostMaterialsDao,
    CostWorkforceDao,
    ExpenseRecoverDao,
    ExpensesDistributionDao,
    ExternalServicesDao,
    FamiliesDao,
    GeneralExpenseRecoverDao,
    GeneralExpenseDistributionDao,
    GeneralProductMaterialsDao,
    GeneralProductsProcessDao,
    ProductsMaterialsDao,
    ProductsProcessDao,
    GeneralServicesDao,
    FilesDao,
    GeneralCompositeProductsDao,
    GeneralExternalServicesDao,
    GeneralMaterialsDao,
    GeneralProductsDao,
    IndirectCostDao,
    LastDataDao,
    ProductsDao,
    ProductsCostDao,
    PriceProductDao,
    PriceUSDDao,
    ProductsQuantityDao,
    TotalExpenseDao
};


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
$generalExServicesDao = new GeneralExternalServicesDao();
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
$pricesUSDDao = new PriceUSDDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$totalExpenseDao = new TotalExpenseDao();
$calcRecoveryExpenses = new CalcRecoveryExpensesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use ($productsDao) {
    $id_company = $_SESSION['id_company'];

    try {
        $products = $productsDao->findAllProductsByCompany($id_company);
        return ResponseHelper::withJson($response, $products, 200);
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());

$app->get('/selectProducts', function (Request $request, Response $response, $args) use ($generalProductsDao) {

    $id_company = $_SESSION['id_company'];
    try {
        $products = $generalProductsDao->findDataBasicProductsByCompany($id_company);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());

/* Consultar productos CRM */
$app->get('/productsCRM', function (Request $request, Response $response, $args) use ($generalProductsDao) {

    try {
        $products = $generalProductsDao->findAllProductsByCRM(1);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());

/* Consultar Productos creados */
$app->get('/productsLimit', function (Request $request, Response $response, $args) use ($productsQuantityDao) {

    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];

    try {
        $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

        $response->getBody()->write(json_encode($product, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());

$app->get('/inactivesProducts', function (Request $request, Response $response, $args) use ($generalProductsDao) {

    $id_company = $_SESSION['id_company'];
    try {
        $products = $generalProductsDao->findAllInactivesProducts($id_company);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());


$app->get('/productCost/{id_product}', function (Request $request, Response $response, $args) use ($generalProductsDao) {

    $id_company = $_SESSION['id_company'];
    try {
        $products = $generalProductsDao->findProductCost($args['id_product'], $id_company);
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ResponseHelper::withJson($response, ['error' => true, 'message' => 'Error al obtener los productos'], 500);
    }
})->add(new SessionMiddleware());

/* Consultar productos importados */
$app->post('/productsDataValidation', function (Request $request, Response $response, $args) use ($generalProductsDao, $generalCompositeProductsDao) {

    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        $id_company = $_SESSION['id_company'];
        $id_user = $_SESSION['idUser'];
        $products = $dataProduct['importProducts'];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportProduct = [];

        for ($i = 0; $i < count($products); $i++) {
            if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) ||
                empty($products[$i]['active']) || $products[$i]['commissionSale'] == ''
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
            if (
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product'])) ||
                empty(trim($products[$i]['active'])) || trim($products[$i]['commissionSale']) == ''
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }

            $item = $products[$i];
            $refProduct = trim($item['referenceProduct']);
            $nameProduct = trim($item['product']);

            if (isset($duplicateTracker[$refProduct]) || isset($duplicateTracker[$nameProduct])) {
                $i = $i + 2;
                $dataImportProduct =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
                break;
            } else {
                $duplicateTracker[$refProduct] = true;
                $duplicateTracker[$nameProduct] = true;
            }
        }

        // session_start();

        $insert = 0;
        $update = 0;
        if (sizeof($dataImportProduct) == 0) {
            for ($i = 0; $i < sizeof($products); $i++) {
                $profitability = floatval(str_replace(',', '.', $products[$i]['profitability']));
                $commissionSale = floatval(str_replace(',', '.', $products[$i]['commissionSale']));

                if ($profitability > 100 || $commissionSale > 100 || is_nan($profitability) || is_nan($commissionSale)) {
                    $i = $i + 2;
                    $dataImportProduct = array('error' => true, 'message' => "La rentabilidad y comision debe ser menor al 100%, fila: $i");
                    break;
                }

                if ($id_user == '1') {
                    $findProduct = $generalProductsDao->findProductById($products[$i]['id']);
                } else {
                    $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (empty(trim($products[$i]['composite'])) || trim($products[$i]['composite']) == '') {
                            $i = $i + 2;
                            $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                            break;
                        }

                        if ($findProduct && strtoupper(trim($products[$i]['composite'] == 'NO'))) {
                            $product = $generalCompositeProductsDao->findCompositeProductByChild($findProduct['id_product']);

                            if (sizeof($product) > 0) {
                                $dataImportProduct = array('error' => true, 'message' => "No se puede desactivar el producto. Tiene datos relacionados a él, fila: $i");
                                break;
                            }
                        }
                    }
                }

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
})->add(new SessionMiddleware());

$app->post('/addProducts', function (Request $request, Response $response, $args) use (
    $productsDao,
    $generalProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $lastDataDao,
    $FilesDao,
    $productsCostDao,
    $productsQuantityDao,
    $expensesRecoverDao,
    $totalExpenseDao,
    $calcRecoveryExpenses
) {
    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];
    $coverage_usd = $_SESSION['coverage_usd'];

    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

    if ($product['quantity'] < $product['cant_products'] || $product['quantity'] == 0 && $product['cant_products'] == 0) {
        $dataProducts = sizeof($dataProduct);

        if ($dataProducts > 1) {
            $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

            if (!$product) {
                $dataProduct['active'] = 1;
                //INGRESA id_company, referencia, producto. BD
                $products = $productsDao->insertProductByCompany($dataProduct, $id_company);

                if ($products == null) {
                    //ULTIMO REGISTRO DE ID, EL MÁS ALTO
                    $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

                    if (sizeof($_FILES) > 0)
                        $FilesDao->imageProduct($lastProductId['id_product'], $id_company);

                    //AGREGA ULTIMO ID A DATA
                    $dataProduct['idProduct'] = $lastProductId['id_product'];
                    $dataProduct['newProduct'] = 1;
                    $products = $productsCostDao->insertProductsCostByCompany($dataProduct, $id_company);

                    // 
                    // $generalProductsDao->updateStatusNewProduct($lastProductId['id_product'], 1);
                }

                //Ingresar porcentaje de gasto
                $dataExpense['idProduct'] = $dataProduct['idProduct'];
                $dataExpense['percentage'] = 0;

                $expensesRecoverDao->insertRecoverExpenseByCompany($dataExpense, $id_company);

                //Calcular el porcentaje de recuperacion
                $flag = $_SESSION['flag_expense_distribution'];

                if ($flag === 1 && $id_company === 1) { // Distribucion por recuperacion
                    $products = [['id_product' => $dataProduct['idProduct'], 'created_at' => date('Y-m-d')]];

                    $sales = $totalExpenseDao->findTotalRevenuesByCompany($id_company);
                    $findExpense = $totalExpenseDao->findTotalExpenseByCompany($id_company);
                    $calcRecoveryExpenses->calculateAndStore($products, $sales['expenses_value'], $findExpense['total_expense'], $id_company);
                    $products = null;
                }

                if ($products == null)
                    $resp = array('success' => true, 'message' => 'Producto creado correctamente');
                else if (isset($products['info']))
                    $resp = array('info' => true, 'message' => $products['message']);
                else
                    $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
            } else
                $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
        } else {
            $products = $dataProduct['importProducts'];
            $resolution = null;
            $id_user = $_SESSION['idUser'];

            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($resolution['info'])) break;

                if ($id_user == '1')
                    $product = $generalProductsDao->findProductById($products[$i]['id']);
                else
                    $product = $generalProductsDao->findProduct($products[$i], $id_company);

                strtoupper(trim($products[$i]['active'])) == 'SI' ? $products[$i]['active'] = 1 : $products[$i]['active'] = 0;

                if (!$product) {
                    $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);

                    if (isset($resolution['info'])) break;

                    $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

                    $products[$i]['idProduct'] = $lastProductId['id_product'];
                    $products[$i]['newProduct'] = 1;

                    $resolution = $productsCostDao->insertProductsCostByCompany($products[$i], $id_company);
                } else {
                    $products[$i]['idProduct'] = $product['id_product'];
                    $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);

                    if (isset($resolution['info'])) break;

                    $resolution = $productsCostDao->updateProductsCostByCompany($products[$i]);

                    if (isset($resolution['info'])) break;

                    $product = $priceProductDao->calcPrice($products[$i]['idProduct']);
                    if (isset($product['totalPrice']))
                        $resolution = $generalProductsDao->updatePrice($products[$i]['idProduct'], $product['totalPrice']);

                    // Convertir a Dolares
                    if (isset($resolution['info'])) break;

                    if ($_SESSION['flag_currency_usd'] == '1') {
                        $arr = [];
                        $arr['price'] = $product['totalPrice'];
                        $arr['sale_price'] = $product['sale_price'];
                        $arr['id_product'] = $products[$i]['idProduct'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($arr, $coverage_usd);
                    }
                }

                if (isset($resolution['info'])) break;

                if ($_SESSION['flag_composite_product'] == '1') {
                    strtoupper(trim($products[$i]['composite'])) == 'SI' ? $op = 1 : $op = 0;

                    $resolution = $generalProductsDao->changeCompositeProduct($products[$i]['idProduct'], $op);
                }
            }
            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Productos importados correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
        }
    } else
        $resp = array('error' => true, 'message' => 'Llegaste al limite de tu plan. Comunicate con tu administrador y sube de categoria para obtener más espacio');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/updateProducts', function (Request $request, Response $response, $args) use (
    $productsDao,
    $FilesDao,
    $productsCostDao,
    $generalProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao
) {
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];

    $dataProduct = $request->getParsedBody();

    $data = [];

    $status = true;

    $products = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    foreach ($products as $arr) {
        if ($arr['id_product'] != $dataProduct['idProduct']) {
            $status = false;
            break;
        }
    }

    if ($status == true) {
        $product = $generalProductsDao->findProduct($dataProduct, $id_company);

        !is_array($product) ? $data['id_product'] = 0 : $data = $product;

        if ($data['id_product'] == $dataProduct['idProduct'] || $data['id_product'] == 0) {
            $dataProduct['active'] = 1;
            // Actualizar Datos, Imagen y Calcular Precio del producto
            $products = $productsDao->updateProductByCompany($dataProduct, $id_company);

            if (sizeof($_FILES) > 0)
                $FilesDao->imageProduct($dataProduct['idProduct'], $id_company);

            if ($products == null)
                $products = $productsCostDao->updateProductsCostByCompany($dataProduct);

            if ($products == null)
                $product = $priceProductDao->calcPrice($dataProduct['idProduct']);
            if (isset($product['totalPrice']))
                $products = $generalProductsDao->updatePrice($dataProduct['idProduct'], $product['totalPrice']);

            // Convertir a Dolares
            if ($products == null && $_SESSION['flag_currency_usd'] == '1') {
                $arr = [];
                $arr['price'] = $product['totalPrice'];
                $arr['sale_price'] = $product['sale_price'];
                $arr['id_product'] = $dataProduct['idProduct'];

                $products = $pricesUSDDao->calcPriceUSDandModify($arr, $coverage_usd);
            }

            if ($products == null)
                $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
            else if (isset($products['info']))
                $resp = array('info' => true, 'message' => $products['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        }
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

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
    $generalExServicesDao,
    $expensesDistributionDao,
    $familiesDao,
    $generalExpenseDistributionDao,
    $expensesRecoverDao,
    $generalExpenseRecoverDao,
    $costMaterialsDao,
    $costWorkforceDao,
    $indirectCostDao,
    $assignableExpenseDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao
) {
    $id_company = $_SESSION['id_company'];
    $id_plan = $_SESSION['plan'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */

    $product = $productsQuantityDao->totalProductsByCompany($id_company, $id_plan);

    if ($product['quantity'] < $product['cant_products']) {

        $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

        if (!$product) {
            $dataProduct['active'] = 1;
            //INGRESA id_company, referencia, producto. BD
            $resolution = $productsDao->insertProductByCompany($dataProduct, $id_company);

            if ($resolution == null)
                //ULTIMO REGISTRO DE ID, EL MÁS ALTO
                $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

            if (isset($lastProductId)) {
                //AGREGA ULTIMO ID A DATA
                $dataProduct['idProduct'] = $lastProductId['id_product'];
                $dataProduct['newProduct'] = 1;
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
                    $arr['efficiency'] = $arr['efficiency'];
                    $arr['autoMachine'] = $arr['auto_machine'];
                    $arr['employees'] = $arr['employee'];

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
                    // Guardar servicio en la tabla 'general_external_services'
                    $findExternalService = $generalExServicesDao->findExternalService($arr, $id_company);

                    if (!$findExternalService) {
                        $resolution = $generalExServicesDao->insertExternalServicesByCompany($arr, $id_company);

                        $lastData = $lastDataDao->findLastInsertedGeneralServices($id_company);
                        $arr['idGService'] = $lastData['id_general_service'];
                    } else
                        $arr['idGService'] = $findExternalService['id_general_service'];
                    // $arr['idGService'] = $arr['id_general_service'];
                    $arr['idProduct'] = $dataProduct['idProduct'];

                    $resolution = $externalServicesDao->insertExternalServicesByCompany($arr, $id_company);
                }
            }

            if ($resolution == null) {
                // Copiar data expenses_distribution
                $flag = $_SESSION['flag_expense_distribution'];
                $oldProduct = $generalExpenseDistributionDao->findExpenseDistributionByIdProduct($dataProduct['idOldProduct'], $id_company);
                $arr = array();

                // $generalProductsDao->updateStatusNewProduct($dataProduct['idProduct'], 1);

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
                    }
                    // else {
                    // $findExpenseDistribution = $expensesDistributionDao->findExpenseDistribution($arr, $id_company);

                    // if (!$findExpenseDistribution)
                    //     $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($arr, $id_company);
                    // else {
                    //     $dataExpensesDistribution['idExpensesDistribution'] = $findExpenseDistribution['id_expenses_distribution'];
                    //     $resolution = $expensesDistributionDao->updateExpensesDistribution($dataExpensesDistribution, $id_company);
                    // }
                    // }
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

                foreach ($productsMaterials as $arr) {
                    if ($resolution != null) break;
                    // Obtener materia prima
                    $material = $materialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                    // Convertir unidades
                    $quantities = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                    // Modificar costo
                    $materialsDao->updateCostProductMaterial($arr, $quantities);
                }
                $status = false;

                if ($_SESSION['flag_composite_product'] == '1') {
                    $composite = $generalCompositeProductsDao->findCompositeProductCost($dataProduct['idProduct']);

                    !$composite ? $status = false : $status = true;

                    if ($status == true)
                        $dataMaterial = $costMaterialsDao->calcCostMaterialByCompositeProduct($dataProduct, $id_company);
                }

                if ($_SESSION['flag_composite_product'] == '0' || $status == false)
                    $dataMaterial = $costMaterialsDao->calcCostMaterial($dataProduct, $id_company);

                $resolution = $costMaterialsDao->updateCostMaterials($dataMaterial, $id_company);
            }

            if ($resolution == null) {
                // Calcular costo nomina
                if ($_SESSION['inyection'] == 1)
                    $resolution = $costWorkforceDao->calcCostPayrollInyection($dataProduct['idProduct'], $id_company);
                else
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
                // Cambiar a 0
                $indirectCostDao->updateCostIndirectCostByProduct(0, $dataProduct['idProduct']);
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

                        // if (isset($resolution['info'])) break;
                        // $arr['year'] = date('Y');
                        // $arr['month'] = date('n');
                        // $arr['assignable_expense'] = $expense['assignableExpense'];

                        // // Guardar ED Historico (mes)
                        // $historical = $historicalEDDao->findHistorical($arr, $id_company);

                        // if (!$historical)
                        //     $resolution = $historicalEDDao->insertHistoricalExpense($arr, $id_company);
                        // else {
                        //     $arr['id_historical_distribution'] = $historical['id_historical_distribution'];

                        //     $resolution = $historicalEDDao->updateHistoricalExpense($arr);
                        // }
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
                    $data = [];
                    $data = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($resolution['info']))
                        break;
                    if (isset($data['totalPrice']))
                        $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                    if (isset($resolution['info']))
                        break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
                    if (isset($resolution['info']))
                        break;
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
})->add(new SessionMiddleware());

$app->post('/deleteProduct', function (Request $request, Response $response, $args) use (
    $generalPMaterialsDao,
    $generalPProcessDao,
    $generalServicesDao,
    $generalExpenseDistributionDao,
    $generalExpenseRecoverDao,
    $productsCostDao,
    $productsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao
) {
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataProduct = $request->getParsedBody();

    if ($_SESSION['flag_composite_product'] == '1')
        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($dataProduct['idProduct']);

    $resolution = $generalPMaterialsDao->deleteProductMaterialByProduct($dataProduct);
    if ($resolution == null)
        $resolution = $generalPProcessDao->deleteProductProcessByProduct($dataProduct);
    if ($resolution == null)
        $resolution = $generalServicesDao->deleteExternalServiceByProduct($dataProduct);
    if ($resolution == null)
        $resolution = $generalExpenseDistributionDao->deleteExpensesDistributionByProduct($dataProduct);
    if ($resolution == null)
        $resolution = $generalExpenseRecoverDao->deleteRecoverExpenseByProduct($dataProduct);
    if ($resolution == null)
        $resolution = $productsCostDao->deleteProductsCost($dataProduct);
    if ($resolution == null)
        $resolution = $generalCompositeProductsDao->deleteCompositeProductByProduct($dataProduct['idProduct']);
    if ($resolution == null)
        $resolution = $generalCompositeProductsDao->deleteChildProductByProduct($dataProduct['idProduct']);
    if ($resolution == null)
        $resolution = $productsDao->deleteProduct($dataProduct['idProduct']);

    if ($resolution == null && $_SESSION['flag_composite_product'] == '1') {
        // Calcular costo material porq
        foreach ($productsCompositer as $arr) {
            if (isset($resolution['info'])) break;

            $data = [];
            // $data['compositeProduct'] = $arr['id_child_product'];
            $data['idProduct'] = $arr['id_product'];
            // $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
            // $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

            if (isset($resolution['info'])) break;
            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

            if (isset($resolution['info'])) break;

            $data = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($data['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

            if (isset($resolution['info'])) break;

            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $arr['id_product'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }
            if (isset($resolution['info'])) break;

            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

            foreach ($productsCompositer2 as $k) {
                if (isset($resolution['info'])) break;

                $data = [];
                // $data['compositeProduct'] = $k['id_child_product'];
                $data['idProduct'] = $k['id_product'];

                // $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                // $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($resolution['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($resolution['info'])) break;

                $data = $priceProductDao->calcPrice($k['id_product']);

                if (isset($data['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);

                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $i = [];
                    $i['price'] = $data['totalPrice'];
                    $i['sale_price'] = $data['sale_price'];
                    $i['id_product'] = $k['id_product'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($i, $coverage_usd);
                }
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

/* Activar o Inactivar Producto */
$app->get('/changeActiveProduct/{id_product}/{op}', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $assignableExpenseDao,
    $familiesDao
) {
    $id_company = $_SESSION['id_company'];
    $flag = $_SESSION['flag_expense_distribution'];

    $product = $generalProductsDao->activeOrInactiveProducts($args['id_product'], $args['op']);

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

                // if (isset($resolution['info'])) break;
                // $arr['year'] = date('Y');
                // $arr['month'] = date('n');
                // $arr['assignable_expense'] = $expense['assignableExpense'];

                // // Guardar ED Historico (mes)
                // $historical = $historicalEDDao->findHistorical($arr, $id_company);

                // if (!$historical)
                //     $resolution = $historicalEDDao->insertHistoricalExpense($arr, $id_company);
                // else {
                //     $arr['id_historical_distribution'] = $historical['id_historical_distribution'];

                //     $resolution = $historicalEDDao->updateHistoricalExpense($arr);
                // }
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

    if ($product == null) {
        $args['op'] == '0' ? $msg = 'inactivado' : $msg = 'activado';

        $resp = array('success' => true, 'message' => "Producto $msg correctamente");
    } else if (isset($products['info']))
        $resp = array('info' => true, 'message' => $products['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

/* Activar Productos */
$app->post('/activeProducts', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $assignableExpenseDao,
    $familiesDao
) {
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

                // if (isset($resolution['info'])) break;
                // $arr['year'] = date('Y');
                // $arr['month'] = date('n');
                // $arr['assignable_expense'] = $expense['assignableExpense'];

                // // Guardar ED Historico (mes)
                // $historical = $historicalEDDao->findHistorical($arr, $id_company);

                // if (!$historical)
                //     $resolution = $historicalEDDao->insertHistoricalExpense($arr, $id_company);
                // else {
                //     $arr['id_historical_distribution'] = $historical['id_historical_distribution'];

                //     $resolution = $historicalEDDao->updateHistoricalExpense($arr);
                // }
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
})->add(new SessionMiddleware());

$app->get('/changeComposite/{id_product}/{op}', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    $status = true;

    if ($args['op'] == 0) {
        $product = $generalCompositeProductsDao->findCompositeProductByChild($args['id_product']);
        if (sizeof($product) > 0)
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
})->add(new SessionMiddleware());
