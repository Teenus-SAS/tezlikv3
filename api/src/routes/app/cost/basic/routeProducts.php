<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\ProductsDao;
use tezlikv3\dao\ProductsCostDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\ProductsQuantityDao;

$productsDao = new ProductsDao();
$productsCostDao = new ProductsCostDao();
$priceProductDao = new PriceProductDao();
$productsQuantityDao = new ProductsQuantityDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$productsProcessDao = new ProductsProcessDao();
$externalServicesDao = new ExternalServicesDao();
$expensesDistributionDao = new ExpensesDistributionDao();
$costMaterialsDao = new CostMaterialsDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productCost/{id_product}', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findProductCost($args['id_product'], $id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/inactivesProducts', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllInactivesProducts($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/productsDataValidation', function (Request $request, Response $response, $args) use ($productsDao) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {

            if (isset($products[$i]['referenceProduct']))
                $reference = $products[$i]['referenceProduct'];

            if (isset($products[$i]['product']))
                $product = $products[$i]['product'];

            if (isset($products[$i]['profitability']))
                $profitability = $products[$i]['profitability'];

            if (isset($products[$i]['commissionSale']))
                $commisionSale = $products[$i]['commissionSale'];

            if (empty($reference) || empty($product) || empty($profitability) || empty($commisionSale))
                $dataImportProduct = array('error' => true, 'message' => 'Ingrese todos los datos');
            else {
                $findProduct = $productsDao->findProduct($products[$i], $id_company);
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

$app->post('/addProducts', function (Request $request, Response $response, $args) use ($productsDao, $productsCostDao, $productsQuantityDao) {
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

            //ULTIMO REGISTRO DE ID, EL MÁS ALTO
            $lastProductId = $productsDao->lastInsertedProductId($id_company);

            if (sizeof($_FILES) > 0)
                $productsDao->imageProduct($lastProductId['id_product'], $id_company);


            //AGREGA ULTIMO ID A DATA
            $dataProduct['idProduct'] = $lastProductId['id_product'];
            $productsCost = $productsCostDao->insertProductsCostByCompany($dataProduct, $id_company);

            if ($products == null &&  $productsCost == null)
                $resp = array('success' => true, 'message' => 'Producto creado correctamente');
            else if (isset($products['info']))
                $resp = array('info' => true, 'message' => $products['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $products = $dataProduct['importProducts'];

            for ($i = 0; $i < sizeof($products); $i++) {

                $product = $productsDao->findProduct($products[$i], $id_company);

                if (!$product) {
                    $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);
                    $lastProductId = $productsDao->lastInsertedProductId($id_company);

                    $products[$i]['idProduct'] = $lastProductId['id_product'];

                    $resolution = $productsCostDao->insertProductsCostByCompany($products[$i], $id_company);
                } else {
                    $products[$i]['idProduct'] = $product['id_product'];
                    $resolution = $productsDao->updateProductByCompany($products[$i]);
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
    $productsCostDao,
    $productsQuantityDao,
    $productsMaterialsDao,
    $productsProcessDao,
    $externalServicesDao,
    $expensesDistributionDao,
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
            $lastProductId = $productsDao->lastInsertedProductId($id_company);

        if (isset($lastProductId)) {
            //AGREGA ULTIMO ID A DATA
            $dataProduct['idProduct'] = $lastProductId['id_product'];
            $resolution = $productsCostDao->insertProductsCostByCompany($dataProduct, $id_company);
        }

        if ($resolution == null) {
            // Copiar data products_materials
            $oldProduct = $productsMaterialsDao->findProductMaterialByIdProduct($dataProduct);

            foreach ($oldProduct as $arr) {
                $arr['idProduct'] = $dataProduct['idProduct'];
                $arr['material'] = $arr['id_material'];
                $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($arr, $id_company);
            }

            if ($resolution == null) {
                // Copiar data products_process
                $oldProduct = $productsProcessDao->findProductProcessByIdProduct($dataProduct);

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
                $oldProduct = $externalServicesDao->findExternalServiceByIdProduct($dataProduct);

                foreach ($oldProduct as $arr) {
                    $arr['costService'] = $arr['cost'];
                    $arr['service'] = $arr['name_service'];
                    $arr['idProduct'] = $dataProduct['idProduct'];

                    $resolution = $externalServicesDao->insertExternalServicesByCompany($arr, $id_company);
                }
            }

            if ($resolution == null) {
                // Copiar data expenses_distribution
                $oldProduct = $expensesDistributionDao->findExpenseDistributionByIdProduct($dataProduct, $id_company);
                if ($oldProduct != false) {
                    $arr['selectNameProduct'] = $dataProduct['idProduct'];
                    $arr['unitsSold'] = $oldProduct['units_sold'];
                    $arr['turnover'] = $oldProduct['turnover'];
                    $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($arr, $id_company);
                }
            }


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

$app->post('/updateProducts', function (Request $request, Response $response, $args) use ($productsDao, $productsCostDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();
    //$imgProduct = $request->getUploadedFiles();

    if (
        empty($dataProduct['referenceProduct']) || empty($dataProduct['product']) ||
        empty($dataProduct['profitability']) || empty($dataProduct['commissionSale'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        // Actualizar Datos, Imagen y Calcular Precio del producto
        $products = $productsDao->updateProductByCompany($dataProduct);

        if (sizeof($_FILES) > 0)
            $products = $productsDao->imageProduct($dataProduct['idProduct'], $id_company);

        $products = $productsCostDao->updateProductsCostByCompany($dataProduct);
        $products = $priceProductDao->calcPrice($dataProduct['idProduct']);

        if ($products == null)
            $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
        else if (isset($products['info']))
            $resp = array('info' => true, 'message' => $products['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProduct', function (Request $request, Response $response, $args) use ($productsDao, $productsCostDao) {
    $dataProduct = $request->getParsedBody();

    $productsCost = $productsCostDao->deleteProductsCost($dataProduct);
    $product = $productsDao->deleteProduct($dataProduct);

    if ($product == null && $productsCost == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Inactivar Producto */
$app->get('/inactiveProducts/{id_product}', function (Request $request, Response $response, $args) use ($productsDao) {
    $product = $productsDao->activeOrInactiveProducts($args['id_product'], 0);

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
$app->post('/activeProducts', function (Request $request, Response $response, $args) use ($productsDao) {
    $dataProducts = $request->getParsedBody();

    $products = $dataProducts['data'];

    for ($i = 0; $i < sizeof($products); $i++) {
        $resolution = $productsDao->activeOrInactiveProducts($products[$i]['idProduct'], 1);
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
