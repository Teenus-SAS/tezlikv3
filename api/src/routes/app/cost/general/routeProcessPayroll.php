<?php

use tezlikv3\dao\GeneralPayrollDao;
use tezlikv3\dao\WebTokenDao;

$generalPayrollDao = new GeneralPayrollDao();
$webTokenDao = new WebTokenDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/processPayroll', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $processPayroll = $generalPayrollDao->findAllProcessByPayroll($id_company);
    $response->getBody()->write(json_encode($processPayroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
