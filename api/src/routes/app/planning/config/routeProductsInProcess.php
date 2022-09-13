<?php

use tezlikv3\dao\ProductsInProcessDao;

$productsInProcessDao = new ProductsInProcessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsInProcess', function (Request $request, Response $response, $args) use ($productsInProcessDao) {
    $productsInProcess = $productsInProcessDao->findAllProductsInProcess();

    $response->getBody()->write(json_encode($productsInProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
