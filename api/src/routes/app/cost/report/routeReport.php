<?php

use tezlikv3\dao\ProcessDao;
use tezlikv3\dao\ReportCostDao;

$reportCostDao = new ReportCostDao();
$processDao = new ProcessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/generalCostReport', function (Request $request, Response $response, $args) use ($reportCostDao, $processDao) {
    $id_company = $_SESSION['id_company'];

    //$costWorkforce = $reportCostDao->findAllCostWorkforceByCompany($id_company);
    $data = $reportCostDao->findAllCostByCompany($id_company);

    //$process = $processDao->findAllProcessByCompany($id_company);

    //$data = [];
    //$data['costWorkforce'] = $costWorkforce;
    //$data['process'] = $process;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->get('/processCostReport', function (Request $request, Response $response, $args) use ($reportCostDao, $processDao) {

    $id_company = $_SESSION['id_company'];

    //$costWorkforce = $reportCostDao->findAllCostWorkforceByCompany($id_company);
    $data = $reportCostDao->findAllCostWorkforceByCompany($id_company);

    //$process = $processDao->findAllProcessByCompany($id_company);

    //$data = [];
    //$data['costWorkforce'] = $costWorkforce;
    //$data['process'] = $process;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
