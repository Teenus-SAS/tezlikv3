<?php

use tezlikv3\dao\ProductsInProcessDao;
use tezlikv3\dao\PlanProductsDao;

$productsInProcessDao = new ProductsInProcessDao();
$productsDao = new PlanProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Todos los productos asociados a la tabla `plan_products_in_process` */

$app->get('/productsInProcessByCompany/{id_product}', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productsInProcess = $productsInProcessDao->findAllProductsInProcessByCompany($args['id_product'], $id_company);

    $response->getBody()->write(json_encode($productsInProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Todos los productos en proceso */
$app->get('/productsInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    $productsInProcess = $productsInProcessDao->findAllProductsInProcess();
    $response->getBody()->write(json_encode($productsInProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productsInProcessDataValidation', function (Request $request, Response $response, $args) use ($productsInProcessDao, $productsDao) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productsInProcess = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($productsInProcess); $i++) {
            // Obtener Id Producto Final
            $findProduct = $productsDao->findProduct($productsInProcess[$i], $id_company);

            if (!$findProduct) {
                $i = $i + 1;
                $dataImportProductsInProcess = array('error' => true, 'message' => "Producto final no existe en la Base de datos. Fila: {$i}");
                break;
            }

            /* Obtener Id Producto en proceso
            $productsInProcess[$i][''] = $productsInProcess[$i][''];
            $productsInProcess[$i][''] = $productsInProcess[$i][''];
            $findProduct = $productsDao->findProduct($productsInProcess[$i], $id_company);

            if (!$findProduct) {
                $i = $i + 1;
                $dataImportProductsInProcess = array('error' => true, 'message' => "Producto en proceso no existe en la Base de datos. Fila: {$i}");
                break;
            } */

            // Saber si existe con categoria en proceso
            $findProductInProcess = $productsDao->findProductByCategoryInProcess($productsInProcess[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportProductsInProcess = array('error' => true, 'message' => "Producto no esta en la categoria en proceso. Fila: {$i}");
                break;
            }

            $productsInProcess[$i]['idProduct'] = $findProduct['id_product'];

            $findProductInProcess = $productsInProcessDao->findProductInProcess($productsInProcess[$i], $id_company);
            if ($findProductInProcess) {
                $i = $i + 1;
                $dataImportProductsInProcess = array('error' => true, 'message' => "Producto ya asignado en proceso. Fila: {$i}");
                break;
            } else
                $insert = $insert + 1;
            $dataImportProductsInProcess['insert'] = $insert;
            $dataImportProductsInProcess['update'] = $update;
        }
    } else
        $dataImportProductsInProcess = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProductsInProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao, $productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    $dataProducts = sizeof($dataProduct);

    if ($dataProducts > 1) {
        $productsInProcess = $productsInProcessDao->insertProductInProcessByCompany($dataProduct, $id_company);

        if ($productsInProcess == null)
            $resp = array('success' => true, 'message' => 'Producto en proceso guardado correctamente');
        else if (isset($productsInProcess['info']))
            $resp = array('info' => true, 'message' => $productsInProcess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    } else {
        $productsInProcess = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($productsInProcess); $i++) {

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productsInProcess[$i], $id_company);
            $productsInProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Insertar producto en proceso
            $resolution = $productsInProcessDao->insertProductInProcessByCompany($productsInProcess[$i], $id_company);

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Productos en proceso importados correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->post('/updateProductInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
//     $dataProduct = $request->getParsedBody();

//     if (empty($dataProduct['idProduct']))
//         $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
//     else {
//         $productsInProcess = $productsInProcessDao->updateProductInProcess($dataProduct);

//         if ($productsInProcess == null)
//             $resp = array('success' => true, 'message' => 'Producto en proceso actualizado correctamente');
//         else if (isset($productsInProcess['info']))
//             $resp = array('info' => true, 'message' => $productsInProcess['message']);
//         else
//             $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
//     }
//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

$app->get('/deleteProductInProcess/{id_product_category}', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    $productsInProcess = $productsInProcessDao->deleteProductInProcess($args['id_product_category']);

    if ($productsInProcess == null)
        $resp = array('success' => true, 'message' => 'Producto en proceso eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto en proceso, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
