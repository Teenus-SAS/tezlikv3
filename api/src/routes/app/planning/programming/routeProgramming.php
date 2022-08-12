<?php

use tezlikv3\dao\ProgrammingDao;
use tezlikv3\dao\DatesMachinesDao;
use tezlikv3\dao\FinalDateDao;
use tezlikv3\dao\EconomicLotDao;
use tezlikv3\dao\OrdersDao;
// use tezlikv3\dao\PlanCiclesMachineDao;
// use tezlikv3\dao\PlanMachinesDao;
// use tezlikv3\dao\PlanProductsDao;

$programmingDao = new ProgrammingDao();
$datesMachinesDao = new DatesMachinesDao();
$finalDateDao = new FinalDateDao();
$economicLotDao = new EconomicLotDao();
$ordersDao = new OrdersDao();
// $planCiclesMachineDao = new PlanCiclesMachineDao();
// $machinesDao = new PlanMachinesDao();
// $productsDao = new PlanProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsByMachine/{id_machine}', function (Request $request, Response $response, $args) use ($programmingDao) {
    $products = $programmingDao->findProductsByMachine($args['id_machine']);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Consultar fecha inicio maquina
$app->post('/dateMachine', function (Request $request, Response $response, $args) use ($datesMachinesDao, $programmingDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProgramming = $request->getParsedBody();

    // Validar que exista producto y maquina en la BD
    $resolution = $programmingDao->findAExistingProductAndMachine($dataProgramming, $id_company);

    if (!$resolution) {
        $resp = array('error' => true, 'message' => 'El producto y la maquina no tiene relación o no estan agregadas a la base de datos');
    } else {
        $datesMachines = $datesMachinesDao->findDatesMachine($dataProgramming, $id_company);
        if (!$datesMachines)
            $resp = array('nonExisting' => true);
        else
            $resp = array('existing' => true);
    }

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Obtener información
$app->post('/getProgrammingInfo', function (Request $request, Response $response, $args) use ($finalDateDao, $economicLotDao, $datesMachinesDao, $ordersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProgramming = $request->getParsedBody();

    if (isset($dataProgramming['startDate'])) {
        // Insertar fechas maquina
        $datesMachinesDao->insertDatesMachine($dataProgramming, $id_company);

        // Calcular fecha final
        $finalDate = $finalDateDao->calcFinalDate($dataProgramming, $id_company);
        $dataProgramming['finalDate'] = $finalDate['final_date'];

        // Actualizar fecha final
        $finalDateDao->updateFinalDate($dataProgramming, $id_company);
    }

    // Calcular Lote economico
    $economicLot = $economicLotDao->calcEconomicLot($dataProgramming, $id_company);

    // Obtener fechas maquina
    $datesMachines = $datesMachinesDao->findDatesMachine($dataProgramming, $id_company);

    // Obtener información producto, pedido y cliente
    $orders = $ordersDao->findOrdersByCompany($dataProgramming, $id_company);

    $data['economicLot'] = $economicLot['economic_lot'];
    $data['datesMachines'] = $datesMachines;
    $data['order'] = $orders;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
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
