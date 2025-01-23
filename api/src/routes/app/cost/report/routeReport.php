<?php

use tezlikv3\dao\ProcessDao;
use tezlikv3\dao\ReportCostDao;
use tezlikv3\dao\WebTokenDao;

$webTokenDao = new WebTokenDao();
$reportCostDao = new ReportCostDao();
$processDao = new ProcessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/generalCostReport', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $reportCostDao,
    $processDao
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

    $id_company = $_SESSION['id_company'];

    //$costWorkforce = $reportCostDao->findAllCostWorkforceByCompany($id_company);
    $data = $reportCostDao->findAllCostByCompany($id_company);

    //$process = $processDao->findAllProcessByCompany($id_company);

    //$data = [];
    //$data['costWorkforce'] = $costWorkforce;
    //$data['process'] = $process;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/processCostReport', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $reportCostDao,
    $processDao
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

    $id_company = $_SESSION['id_company'];

    //$costWorkforce = $reportCostDao->findAllCostWorkforceByCompany($id_company);
    $data = $reportCostDao->findAllCostWorkforceByCompany($id_company);

    //$process = $processDao->findAllProcessByCompany($id_company);

    //$data = [];
    //$data['costWorkforce'] = $costWorkforce;
    //$data['process'] = $process;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
