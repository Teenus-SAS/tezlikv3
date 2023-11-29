<?php

use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\MultiproductsDao;

$multiproductsDao = new MultiproductsDao();
$productsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/multiproducts', function (Request $request, Response $response, $args) use ($multiproductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $multiproducts = $multiproductsDao->findAllMultiproducts($id_company);

    $existingMultiproducts = $multiproductsDao->findAllExistingMultiproducts($id_company);

    $data['multiproducts'] = $multiproducts;
    $data['existingMultiproducts'] = $existingMultiproducts;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/multiproductsDataValidation', function (Request $request, Response $response, $args) use (
    $productsDao,
    $multiproductsDao
) {
    $dataMultiproducts = $request->getParsedBody();

    if (isset($dataMultiproducts)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $multiproducts = $dataMultiproducts['importMultiproducts'];

        $existingMultiproducts = $multiproductsDao->findAllExistingMultiproducts($id_company);

        if (sizeof($existingMultiproducts) > 0)
            $multiproducts[0]['expense'] = $existingMultiproducts[0]['expense'];

        else
            $multiproducts[0]['expense'] = 0;


        $status = true;
        for ($i = 0; $i < sizeof($multiproducts); $i++) {
            if (
                empty($multiproducts[$i]['referenceProduct']) || empty($multiproducts[$i]['product']) ||
                $multiproducts[$i]['soldUnit'] == ''
            ) {
                $status = false;
                $i = $i + 2;
                $dataImportMultiproducts = array('error' => true, 'message' => "Campos vacios. Fila: $i");
                break;
            }

            // Obtener id producto
            $product = $productsDao->findProduct($multiproducts[$i], $id_company);

            if (!$product) {
                $status = false;
                $i = $i + 2;
                $dataImportMultiproducts = array('error' => true, 'message' => "Producto no existe en la base de datos. Fila $i");
                break;
            }
        }
        if ($status == true)
            $dataImportMultiproducts = $multiproducts;
    } else
        $dataImportMultiproducts = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportMultiproducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});



$app->post('/addMultiproduct', function (Request $request, Response $response, $args) use (
    $multiproductsDao,
    $productsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct['data'])) {
        $product = $dataProduct['data'];

        // Modificar total unidades
        $resolution = $multiproductsDao->updateTotalUnits($product[sizeof($product) - 1], $id_company);

        if ($resolution == null) {
            for ($i = 0; $i < sizeof($product) - 1; $i++) {
                $multiproducts = $multiproductsDao->findMultiproduct($product[$i]['id_product']);

                if (!$multiproducts)
                    $resolution = $multiproductsDao->insertMultiproductByCompany($product[$i], $id_company);
                else
                    $resolution = $multiproductsDao->updateMultiProduct($product[$i]);
            }
        }
    } else {
        $multiproducts = $dataProduct['importMultiproducts'];

        for ($i = 0; $i < sizeof($multiproducts); $i++) {
            // Obtener id producto
            $product = $productsDao->findProduct($multiproducts[$i], $id_company);
            $multiproducts[$i]['id_product'] = $product['id_product'];
            $multiproducts[$i]['expense'] = $multiproducts[0]['expense'];

            $product = $multiproductsDao->findMultiproduct($multiproducts[$i]['id_product']);

            if (!$product)
                $resolution = $multiproductsDao->insertMultiproductByCompany($multiproducts[$i], $id_company);
            else
                $resolution = $multiproductsDao->updateMultiProduct($multiproducts[$i]);
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Producto guardado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
