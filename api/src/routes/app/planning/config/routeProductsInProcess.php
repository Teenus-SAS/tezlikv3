<?php

use tezlikv3\dao\ProductsInProcessDao;

$productsInProcessDao = new ProductsInProcessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Todos los productos asociados a la tabla `plan_products_in_process` */

$app->get('/productsInProcessByCompany', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productsInProcess = $productsInProcessDao->findAllProductsInProcessByCompany($id_company);

    $response->getBody()->write(json_encode($productsInProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Todos los productos en proceso */
$app->get('/productsInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    $productsInProcess = $productsInProcessDao->findAllProductsInProcess();
    $response->getBody()->write(json_encode($productsInProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    if (empty($dataProduct['idProduct']))
        $resp = array('error' => true, 'message' => 'Seleccione un producto');
    else {
        $productsInProcess = $productsInProcessDao->insertProductInProcessByCompany($dataProduct, $id_company);

        if ($productsInProcess == null)
            $resp = array('success' => true, 'message' => 'Producto en proceso guardado correctamente');
        else if (isset($productsInProcess['info']))
            $resp = array('info' => true, 'message' => $productsInProcess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    $dataProduct = $request->getParsedBody();

    if (empty($dataProduct['idProduct']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $productsInProcess = $productsInProcessDao->updateProductInProcess($dataProduct);

        if ($productsInProcess == null)
            $resp = array('success' => true, 'message' => 'Producto en proceso actualizado correctamente');
        else if (isset($productsInProcess['info']))
            $resp = array('info' => true, 'message' => $productsInProcess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteProductInProcess/{id_product_in_process}', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    $productsInProcess = $productsInProcessDao->deleteProductInProcess($args['id_product_in_process']);

    if ($productsInProcess == null)
        $resp = array('success' => true, 'message' => 'Producto en proceso eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto en proceso, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
