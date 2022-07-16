<?php

use tezlikv3\dao\DatesMachinesDao;
use tezlikv3\dao\OrdersDao;
use tezlikv3\dao\PlanCiclesMachineDao;
use tezlikv3\dao\PlanMachinesDao;
use tezlikv3\dao\PlanProductsDao;

$datesMachinesDao = new DatesMachinesDao();
$ordersDao = new OrdersDao();
$planCiclesMachineDao = new PlanCiclesMachineDao();
$machinesDao = new PlanMachinesDao();
$productsDao = new PlanProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/findDateMachine', function (Request $request, Response $response, $args) use ($datesMachinesDao, $ordersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProgramming = $request->getParsedBody();

    $datesMachines = $datesMachinesDao->findDatesMachine($dataProgramming, $id_company);

    // Obtener información producto, pedido y cliente
    $orders = $ordersDao->findOrdersByCompany($dataProgramming, $id_company);

    if (!$datesMachines)
        $resp = array('error' => true, 'order' => $orders);
    else
        $resp = array('success' => true, 'order' => $orders, 'datesMachines' => $datesMachines);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/programmingDataValidation', function (Request $request, Response $response, $args) use ($ordersDao, $planCiclesMachineDao, $machinesDao, $productsDao) {
    $dataProgramming = $request->getParsedBody();

    if (isset($dataProgramming['importProgramming'])) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $programming = $dataProgramming['importProgramming'];
    } else
        $dataImportProgramming = array('error' => true, 'message' => 'El archivo se encuentra vacio');

    $response->getBody()->write(json_encode($dataImportProgramming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
