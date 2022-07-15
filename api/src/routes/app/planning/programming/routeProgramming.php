<?php

use tezlikv3\dao\OrdersDao;
use tezlikv3\dao\PlanCiclesMachineDao;
use tezlikv3\dao\PlanMachinesDao;
use tezlikv3\dao\PlanProductsDao;
use tezlikv3\dao\ProgrammingDao;

$ordersDao = new OrdersDao();
$planCiclesMachineDao = new PlanCiclesMachineDao();
$machinesDao = new PlanMachinesDao();
$productsDao = new PlanProductsDao();
$programmingDao = new ProgrammingDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/programming', function (Request $request, Response $response, $args) use ($programmingDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $programmingDao->findAllProgramming($id_company);
    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/programmingDataValidation', function (Request $request, Response $response, $args) use ($ordersDao, $planCiclesMachineDao, $machinesDao, $productsDao) {
    $dataProgramming = $request->getParsedBody();

    if (isset($dataProgramming['importProgramming'])) {
    } else
        $dataImportProgramming = array('error' => true, 'message' => 'El archivo se encuentra vacio');

    $response->getBody()->write(json_encode($dataImportProgramming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
