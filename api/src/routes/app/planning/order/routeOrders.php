<?php

use tezlikv3\dao\OrdersDao;

$ordersDao = new OrdersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/orders', function (Request $request, Response $response, $args) use ($ordersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $orders = $ordersDao->findAllOrdersByCompany($id_company);
    $response->getBody()->write(json_encode($orders, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
