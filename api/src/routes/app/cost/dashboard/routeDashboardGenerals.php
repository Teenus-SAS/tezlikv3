<?php

use tezlikv3\dao\AutenticationUserDao;
use tezlikv3\dao\DashboardGeneralDao;
use tezlikv3\dao\LicenseCompanyDao;
use tezlikv3\dao\MultiproductsDao;
use tezlikv3\dao\PricesDao;

$dashboardGeneralDao = new DashboardGeneralDao();
$autenticationDao = new AutenticationUserDao();
$pricesDao = new PricesDao();
$LicenseCompanyDao = new LicenseCompanyDao();
$multiproductsDao = new MultiproductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardExpensesGenerals', function (Request $request, Response $response, $args) use (
    $dashboardGeneralDao,
    $pricesDao,
    $autenticationDao,
    $LicenseCompanyDao,
    $multiproductsDao
) {
    $info = $autenticationDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    $validate = $autenticationDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

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

    $expenseRecoverValue = array();

    $company = $LicenseCompanyDao->findLicenseCompany($id_company);

    if ($company['flag_expense'] == 2) $expenseRecoverValue = $dashboardGeneralDao->findExpensesRecoverValueByCompany($id_company);

    // Consulta valor del gasto
    $expenseValue = $dashboardGeneralDao->findExpensesDistributionValueByCompany($id_company);

    $expenses = $dashboardGeneralDao->findAllExpensesByPuc($id_company);

    // Consulta cantidad materias primas
    $quantityMaterials = $dashboardGeneralDao->findRawMaterialsByCompany($id_company);

    $multiproducts = $dashboardGeneralDao->findTotalMultiproducts($id_company);

    if (!($multiproducts)) {
        $data['total_units'] = 0;
        $data['total_units_sold'] = 0;
        $multiproductsDao->updateTotalUnits($data, $id_company);
        $multiproducts = $dashboardGeneralDao->findTotalMultiproducts($id_company);
    }

    $generalExpenses['details_prices'] = $prices;
    $generalExpenses['time_process'] = $timeProcess;
    $generalExpenses['average_time_process'] = $averageTimeProcess;
    $generalExpenses['process_minute_value'] = $processMinuteValue;
    $generalExpenses['factory_load_minute_value'] = $factoryLoadMinuteValue;
    $generalExpenses['expense_value'] = $expenseValue;
    $generalExpenses['expenses'] = $expenses;
    $generalExpenses['expense_recover'] = $expenseRecoverValue;
    $generalExpenses['quantity_materials'] = $quantityMaterials;
    $generalExpenses['multiproducts'] = $multiproducts;

    $response->getBody()->write(json_encode($generalExpenses, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
