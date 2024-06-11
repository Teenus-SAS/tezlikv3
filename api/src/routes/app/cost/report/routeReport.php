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

$app->get('/processCostReport', function (Request $request, Response $response, $args) use (
    $webTokenDao,
    $reportCostDao,
    $processDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $id_company = $_SESSION['id_company'];

    $costWorkforce = $reportCostDao->findAllCostWorkforceByCompany($id_company);

    $process = $processDao->findAllProcessByCompany($id_company);

    $data = [];
    $data['costWorkforce'] = $costWorkforce;
    $data['process'] = $process;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
