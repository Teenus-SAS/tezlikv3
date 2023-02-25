<?php

use tezlikv3\dao\MultiproductsDao;

$multiProductsDao = new MultiproductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/multiproducts', function (Request $request, Response $response, $args) use ($multiProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $multiproducts = $multiProductsDao->findAllMultiproducts($id_company);

    $response->getBody()->write(json_encode($multiproducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
