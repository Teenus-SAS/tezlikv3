<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\DashboardProductsDao;
use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\SimulatorDao;

$dashboardProductsDao = new DashboardProductsDao();
$simulatorDao = new SimulatorDao();
$externalServicesDao = new ExternalServicesDao();
$expensesDistributionDao = new ExpensesDistributionDao();
$expenseRecoverDao = new GeneralExpenseRecoverDao();
$assignableExpenseDao = new AssignableExpenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardPricesSimulator/{id_product}', function (Request $request, Response $response, $args) use (
    $dashboardProductsDao,
    $expenseRecoverDao,
    $expensesDistributionDao,
    $assignableExpenseDao,
    $externalServicesDao,
    $simulatorDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Consultar analisis de costos por producto
    $costAnalysisProducts = $dashboardProductsDao->findCostAnalysisByProduct($args['id_product'], $id_company);
    // Consultar Costo Materia prima por producto
    $costRawMaterials = $dashboardProductsDao->findCostRawMaterialsByProduct($args['id_product'], $id_company);
    // Consultar Ficha tecnica Proceso del producto
    $totalTimeProcess = $dashboardProductsDao->findProductProcessByProduct($args['id_product'], $id_company);
    // Carga fabril
    $factoryLoad = $simulatorDao->findAllFactoryLoadByProduct($args['id_product'], $id_company);
    // Servicios Externos
    $externalServices = $externalServicesDao->findAllExternalServicesByIdProduct($args['id_product'], $id_company);
    // Nomina
    $payroll = $simulatorDao->findAllPayrollByProduct($args['id_product'], $id_company);

    if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);
        $expensesDistribution = $expensesDistributionDao->findAllExpensesDistributionByCompany($id_company);
        $data['expensesDistribution'] = $expensesDistribution;
        $data['totalExpense'] = $totalExpense['total_expense'];
    } else {
        $expenseRecover = $expenseRecoverDao->findExpenseRecoverByIdProduct($args['id_product']);
        $data['expenseRecover'] = $expenseRecover;
    }

    $data['products'] = $costAnalysisProducts;
    $data['materials'] = $costRawMaterials;
    $data['productsProcess'] = $totalTimeProcess;
    $data['factoryLoad'] = $factoryLoad;
    $data['externalServices'] = $externalServices;
    $data['payroll'] = $payroll;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
