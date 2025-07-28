<?php

namespace tezlikv3\Dao;

use tezlikv3\dao\{
    DashboardGeneralDao,
    ExpenseRecoverDao,
    LicenseCompanyDao,
    MultiproductsDao,
    PricesDao,
    ProductsCostDao
};

$dashboardGeneralDao = new DashboardGeneralDao();
$pricesDao = new PricesDao();
$LicenseCompanyDao = new LicenseCompanyDao();
$multiproductsDao = new MultiproductsDao();
$productCostDao = new ProductsCostDao();
$expenseRecoverDao = new ExpenseRecoverDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/dashboardExpensesGenerals', function (Request $request, Response $response, $args) use (
    $dashboardGeneralDao,
    $pricesDao,
    $LicenseCompanyDao,
    $multiproductsDao
) {
    // session_start();
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
})->add(new SessionMiddleware());

$app->post('/updateCosts', function (Request $request, Response $response, $args) use (
    $productCostDao,
    $expenseRecoverDao
) {
    // session_start();
    $id_company = $_SESSION['id_company'];
    $data = $request->getParsedBody();


    try {
        // Iniciar una transacción
        $connection = Connection::getInstance()->getConnection();
        $connection->beginTransaction();

        if (isset($data['commission']) or isset($data['profit']))
            $productCostDao->updateCostByCompany($data, $id_company, $connection);
        else {
            $expense = $expenseRecoverDao->findExpenseRecover($data, $id_company);
            $data['idExpenseRecover'] = $expense['id_expense_recover'];
            $expenseRecoverDao->updateRecoverExpense($data);
        }

        // Confirmar la transacción
        $connection->commit();

        $resp = ['success' => true, 'message' => 'Actualizacion exitosa'];
        return ResponseHelper::withJson($response, $resp, 200);
    } catch (\Exception $e) {
        if ($connection && $connection->inTransaction()) {
            $connection->rollBack();
        }

        error_log("Error en addAreas: " . $e->getMessage()); // Log interno
        $resp = ['error' => true, 'message' => 'Internal server error'];
        return ResponseHelper::withJson($response, $resp, 500);
    }
})->add(new SessionMiddleware());
