<?php

use tezlikv3\dao\DashboardGeneralDao;
use tezlikv3\dao\PricesDao;

$dashboardGeneralDao = new DashboardGeneralDao();
$pricesDao = new PricesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardExpensesGenerals', function (Request $request, Response $response, $args) use ($dashboardGeneralDao, $pricesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Consultar rentabilidad y comision de ventas
    $prices = $pricesDao->findAllPricesByCompany($id_company);

    // Consultar tiempos de proceso por producto
    $timeProcess = $dashboardGeneralDao->findTimeProcessForProductByCompany($id_company);

    // Consultar promedio de tiempos procesos
    $averageTimeProcess = $dashboardGeneralDao->findAverageTimeProcessByCompany($id_company);

    // Consultar valor por minuto del proceso (nomina)
    $processMinuteValue = $dashboardGeneralDao->findProcessMinuteValueByCompany($id_company);

    // Consulta valor por minuto de la maquina
    $factoryLoadMinuteValue = $dashboardGeneralDao->findFactoryLoadMinuteValueByCompany($id_company);

    // Consulta valor del gasto
    $expenseValue = $dashboardGeneralDao->findExpensesValueByCompany($id_company);

    // Consulta cantidad materias primas
    $quantityMaterials = $dashboardGeneralDao->findRawMaterialsByCompany($id_company);

    $generalExpenses['details_prices'] = $prices;
    $generalExpenses['time_process'] = $timeProcess;
    $generalExpenses['average_time_process'] = $averageTimeProcess;
    $generalExpenses['process_minute_value'] = $processMinuteValue;
    $generalExpenses['factory_load_minute_value'] = $factoryLoadMinuteValue;
    $generalExpenses['expense_value'] = $expenseValue;
    $generalExpenses['quantity_materials'] = $quantityMaterials;

    $response->getBody()->write(json_encode($generalExpenses, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
