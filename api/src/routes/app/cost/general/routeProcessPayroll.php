<?php

use tezlikv3\dao\GeneralPayrollDao;

$generalPayrollDao = new GeneralPayrollDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/processPayroll', function (Request $request, Response $response, $args) use ($generalPayrollDao) {
    $id_company = $_SESSION['id_company'];
    $processPayroll = $generalPayrollDao->findAllProcessByPayroll($id_company);
    $response->getBody()->write(json_encode($processPayroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
