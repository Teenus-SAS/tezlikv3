<?php

use tezlikv3\dao\invCategoriesDao;
use tezlikv3\dao\InvMoldsDao;
use tezlikv3\dao\PlanProductsDao;

$productsDao = new PlanProductsDao();
$invMoldsDao = new InvMoldsDao();
$invCategoriesDao = new invCategoriesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/planProducts', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/planProductsDataValidation', function (Request $request, Response $response, $args) use ($productsDao, $invMoldsDao, $invCategoriesDao) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            // Obtener id Molde
            $findMold = $invMoldsDao->findInvMold($products[$i], $id_company);
            if (!$findMold) {
                $i = $i + 1;
                $dataImportProduct = array('error' => true, 'message' => "Molde no existe en la base de datos<br>Fila: {$i}");
                break;
            }

            // Obtener id Categoria
            $findCategory = $invCategoriesDao->findCategory($products[$i]);
            if (!$findCategory) {
                $i = $i + 1;
                $dataImportProduct = array('error' => true, 'message' => "Categoria no existe en la base de datos<br>Fila: {$i}");
                break;
            }

            if (empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) || empty($products[$i]['quantity'])) {
                $i = $i + 1;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            } else {
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

$app->post('/addPlanProduct', function (Request $request, Response $response, $args) use ($productsDao, $invMoldsDao, $invCategoriesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $dataProducts = sizeof($dataProduct);

    if ($dataProducts > 1) {
        //INGRESA id_company, referencia, producto. BD
        $products = $productsDao->insertProductByCompany($dataProduct, $id_company);

        //ULTIMO REGISTRO DE ID, EL MÁS ALTO
        $lastProductId = $productsDao->lastInsertedProductId($id_company);

        if (sizeof($_FILES) > 0) $productsDao->imageProduct($lastProductId['id_product'], $id_company);

        if ($products == null)
            $resp = array('success' => true, 'message' => 'Producto creado correctamente');
        else if (isset($products['info']))
            $resp = array('info' => true, 'message' => $products['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {

            // Obtener id Molde
            $findMold = $invMoldsDao->findInvMold($products[$i], $id_company);
            $products[$i]['idMold'] = $findMold['id_mold'];

            // Obtener id Categoria
            $findCategory = $invCategoriesDao->findCategory($products[$i]);
            $products[$i]['category'] = $findCategory['id_category'];

            $product = $productsDao->findProduct($products[$i], $id_company);

            if (!$product) {
                $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);
                // $lastProductId = $productsDao->lastInsertedProductId($id_company);

                // $products[$i]['idProduct'] = $lastProductId['id_product'];
            } else {
                $products[$i]['idProduct'] = $product['id_product'];
                $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Productos importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanProduct', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();
    //$imgProduct = $request->getUploadedFiles();

    if (empty($dataProduct['referenceProduct']) || empty($dataProduct['product']) || empty($dataProduct['idMold']) || empty($dataProduct['quantity']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        // Actualizar Datos, Imagen y Calcular Precio del producto
        $products = $productsDao->updateProductByCompany($dataProduct, $id_company);

        if (sizeof($_FILES) > 0)
            $products = $productsDao->imageProduct($dataProduct['idProduct'], $id_company);

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

$app->get('/deletePlanProduct/{id_product}', function (Request $request, Response $response, $args) use ($productsDao) {
    $product = $productsDao->deleteProduct($args['id_product']);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
