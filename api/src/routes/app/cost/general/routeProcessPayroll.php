<?php

use tezlikv3\dao\ProcessPayrollDao;

$processPayrollDao = new ProcessPayrollDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/processPayroll', function (Request $request, Response $response, $args) use ($processPayrollDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $processPayroll = $processPayrollDao->findAllProcessByPayroll($id_company);
    $response->getBody()->write(json_encode($processPayroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
