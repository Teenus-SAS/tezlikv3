<?php

namespace tezlikv3\Dao;

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\DataCostDao;
use tezlikv3\dao\ExpensesDao;
use tezlikv3\dao\HistoricalExpenseDistributionDao;
use tezlikv3\dao\HistoricalExpensesDao;
use tezlikv3\dao\HistoricalProductsDao;
use tezlikv3\dao\PricesDao;

$historicalProductsDao = new HistoricalProductsDao();
$historicalExpensesDao = new HistoricalExpensesDao();
$expensesDao = new ExpensesDao();
$historicalEDDao = new HistoricalExpenseDistributionDao();
$assignableExpenseDao = new AssignableExpenseDao();
$pricesDao = new PricesDao();
$dataCostDao = new DataCostDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->get('/historicalResume', function (Request $request, Response $response, $args) use ($historicalProductsDao) {
    $id_company = $_SESSION['id_company'];
    $data = $historicalProductsDao->findResumeHistorical($id_company);
    return ResponseHelper::withJson($response, $data, 200);
})->add(new SessionMiddleware());

$app->get('/historicalProducts/{period}', function (Request $request, Response $response, $args) use ($historicalProductsDao) {
    // Obtener datos históricos
    try {
        $id_company = $_SESSION['id_company'];
        $period = $args['period'];

        // Separar por el guion
        list($year, $period) = explode('-', $period);

        $data = $historicalProductsDao->findAllHistoricalByCompany($id_company, $year, $period);


        // 3. Retornar respuesta exitosa
        return ResponseHelper::withJson($response, $data);
    } catch (\Exception $e) {
        // 4. Manejo de errores
        error_log('Historical data error' . $e->getMessage() . "\n" . $e->getTraceAsString());

        return ResponseHelper::withJson($response, [
            'error' => 'Error al obtener datos históricos',
            'details' => $e->getMessage()
        ], 500);
    }
})->add(new SessionMiddleware());

$app->get('/historical/{id_historic}', function (Request $request, Response $response, $args) use ($historicalProductsDao) {
    $data = $historicalProductsDao->findHistorical($args['id_historic']);
    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->get('/lastHistorical', function (Request $request, Response $response, $args) use ($historicalProductsDao) {
    $id_company = $_SESSION['id_company'];

    $data = $historicalProductsDao->findLastHistorical($id_company);

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/saveHistorical', function (Request $request, Response $response, $args) use (
    $pricesDao,
    $dataCostDao,
    $historicalProductsDao,
    $historicalExpensesDao,
    $historicalEDDao,
    $assignableExpenseDao,
    $expensesDao
) {
    //Obtener data
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];
    $flag_expense = $_SESSION['flag_expense'];

    $dataHistorical = $request->getParsedBody();
    $year = $dataHistorical['year'];
    $month = $dataHistorical['month'];

    // Iniciar transacción
    $connection = Connection::getInstance()->getConnection();
    $connection->beginTransaction();

    try {
        //Borrado suave historicos
        $historicalProductsDao->deleteSoftPeriodHistorical($id_company, $id_user, $dataHistorical, $connection);
        $historicalExpensesDao->deleteSoftHistoricalExpense($id_company, $id_user, $dataHistorical, $connection);
        $historicalEDDao->deleteSoftHistoricalExpenseDistribution($id_company, $id_user, $dataHistorical, $connection);

        //Buscar productos
        $products = $pricesDao->findAllPricesByCompany($id_company);

        // Productos
        foreach ($products as $product) {
            if (isset($resolution['info'])) break;

            $data = [];
            $data['idProduct'] = $product['id_product'];
            $data['price'] = $product['price'];
            $data['salePrice'] = $product['sale_price'];
            $data['profitability'] = $product['profitability'];
            $data['commisionSale'] = $product['commission_sale'];
            $data['costMaterials'] = $product['cost_materials'];
            $data['costWorkforce'] = $product['cost_workforce'];
            $data['costIndirect'] = $product['cost_indirect_cost'];
            $data['externalServices'] = $product['services'];
            $data['unitsSold'] = $product['units_sold'];
            $data['turnover'] = $product['turnover'];
            $data['assignableExpense'] = $product['assignable_expense'];
            $data['expenseRecover'] = $product['expense_recover'];
            $data['month'] = $month;
            $data['year'] = $year;

            $k = $dataCostDao->calcMinProfitability($data, $flag_expense);
            $data['minProfitability'] = $k;

            $historicalProductsDao->insertHistoricalByCompany($data, $id_company, $connection);
        }

        // Gastos
        $expenses = $expensesDao->findAllExpensesByCompany($id_company);
        foreach ($expenses as $expense) {
            $expense['year'] = $year;
            $expense['month'] = $month;
            $resolution = $historicalExpensesDao->insertHistoricalExpense($expense, $id_company, $connection);
        }


        // Distribucion
        $expenses = $assignableExpenseDao->findAllExpensesDistribution($id_company);
        foreach ($expenses as $expense) {
            $expense['year'] = $year;
            $expense['month'] = $month;
            $expense['assignable_expense'] = $expense['assignable_expense'];
            $historicalEDDao->insertHistoricalExpense($expense, $id_company, $connection);
        }

        // Confirmar transacción
        $connection->commit();

        return ResponseHelper::withJson($response, ['success' => true, 'message' => 'Historico guardado correctamente']);
    } catch (\Exception $e) {
        // Revertir transacción en caso de error
        $connection->rollBack();

        return ResponseHelper::withJson($response, [
            'error' => true,
            'message' => 'Error al procesar datos históricos: ' . $e->getMessage()
        ], 500);
    }
})->add(new SessionMiddleware());

$app->get('/historical_config', function (Request $request, Response $response, $args) {
    $historical_config = $_SESSION['historical_config'];

    $response->getBody()->write(json_encode($historical_config, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/deleteHistorical', function (Request $request, Response $response, $args) use (
    $historicalProductsDao,
    $historicalExpensesDao,
    $historicalEDDao
) {
    //Obtener data
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];
    $dataHistorical = json_decode($request->getBody()->getContents(), true);

    // Iniciar transacción
    $connection = Connection::getInstance()->getConnection();
    $connection->beginTransaction();

    try {
        //eliminar suave data
        $historicalProductsDao->deleteSoftPeriodHistorical($id_company, $id_user, $dataHistorical, $connection);
        $historicalExpensesDao->deleteSoftHistoricalExpense($id_company, $id_user, $dataHistorical, $connection);
        $historicalEDDao->deleteSoftHistoricalExpenseDistribution($id_company, $id_user, $dataHistorical, $connection);

        // Confirmar transacción
        $connection->commit();

        return ResponseHelper::withJson($response, ['success' => true, 'message' => 'Historico eliminado correctamente']);
    } catch (\Exception $e) {
        // Revertir transacción en caso de error
        $connection->rollBack();

        return ResponseHelper::withJson($response, [
            'error' => true,
            'message' => 'Error al procesar datos históricos: ' . $e->getMessage()
        ], 500);
    }
})->add(new SessionMiddleware());
