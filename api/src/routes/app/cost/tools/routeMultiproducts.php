<?php

use tezlikv3\dao\MultiproductsDao;

$multiproductsDao = new MultiproductsDao();

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

$app->post('/addMultiproduct', function (Request $request, Response $response, $args) use ($multiproductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $product = $dataProduct['data'];

    for ($i = 0; $i < sizeof($product); $i++) {
        $multiproducts = $multiproductsDao->findMultiproduct($product[$i]['id_product']);

        if (!$multiproducts)
            $resolution = $multiproductsDao->insertMultiproductByCompany($product[$i], $id_company);
        else
            $resolution = $multiproductsDao->updateMultiProduct($product[$i]);
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
