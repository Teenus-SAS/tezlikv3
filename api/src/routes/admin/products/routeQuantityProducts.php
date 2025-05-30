<?php

use tezlikv3\dao\ProductsQuantityDao;

$productsQuantityDao = new ProductsQuantityDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

//Cantidad productos general
$app->get('/quantityProductsGeneral', function (Request $request, Response $response, $args) use ($productsQuantityDao) {
    //NÚMERO DE PRODUCTOS GENERALES
    $resp = $productsQuantityDao->totalProducts();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
