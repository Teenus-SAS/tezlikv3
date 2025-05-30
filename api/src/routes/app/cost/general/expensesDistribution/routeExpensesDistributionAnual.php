<?php

// use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\TotalExpenseDao;
use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\ExpensesDistributionAnualDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralExpenseDistributionAnualDao;
use tezlikv3\dao\GeneralPCenterDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\MultiproductsDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductsDao;

// $expensesDistributionDao = new ExpensesDistributionDao();
$expensesDistributionAnualDao = new ExpensesDistributionAnualDao();
$generalExpenseDistributionAnualDao = new GeneralExpenseDistributionAnualDao();

$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$totalExpenseDao = new TotalExpenseDao();
$assignableExpenseDao = new AssignableExpenseDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalProductsDao = new GeneralProductsDao();
$familiesDao = new FamiliesDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$multiproductsDao = new MultiproductsDao();
$generalPCenterDao = new GeneralPCenterDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

/* Consulta todos */

$app->get('/expensesDistributionAnual', function (Request $request, Response $response, $args) use ($expensesDistributionAnualDao) {
    // session_start();
    $id_company = $_SESSION['id_company'];
    $expensesDistribution = $expensesDistributionAnualDao->findAllExpensesDistributionAnualByCompany($id_company);
    $response->getBody()->write(json_encode($expensesDistribution));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->get('/expensesDistributionAnualProducts', function (Request $request, Response $response, $args) use ($generalExpenseDistributionAnualDao) {
    // session_start();
    $id_company = $_SESSION['id_company'];

    $products = $generalExpenseDistributionAnualDao->findAllProductsNotInEDistribution($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/expenseDistributionAnualDataValidation', function (Request $request, Response $response, $args) use (
    $expensesDistributionAnualDao,
    $generalPCenterDao,
    $totalExpenseDao,
    $generalProductsDao
) {

    $dataExpensesDistribution = $request->getParsedBody();

    if (isset($dataExpensesDistribution)) {
        // session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $expensesDistribution = $dataExpensesDistribution['importExpense'];

        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
            if (
                empty($expensesDistribution[$i]['referenceProduct']) || empty($expensesDistribution[$i]['product']) ||
                $expensesDistribution[$i]['unitsSold'] == '' || $expensesDistribution[$i]['turnover'] == ''
            ) {
                $i = $i + 2;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            if (
                empty(trim($expensesDistribution[$i]['referenceProduct'])) || empty(trim($expensesDistribution[$i]['product'])) ||
                trim($expensesDistribution[$i]['unitsSold']) == '' || trim($expensesDistribution[$i]['turnover']) == ''
            ) {
                $i = $i + 2;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            $expenseTotal = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);

            if (empty($expenseTotal) || !$expenseTotal) {
                $dataImportExpenseDistribution = array('error' => true, 'message' => 'Asigne un gasto primero antes de distribuir');
                break;
            }

            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($expensesDistribution[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportExpenseDistribution = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $expensesDistribution[$i]['selectNameProduct'] = $findProduct['id_product'];

            $findExpenseDistribution = $expensesDistributionAnualDao->findExpenseDistributionAnual($expensesDistribution[$i], $id_company);
            if (!$findExpenseDistribution) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportExpenseDistribution['insert'] = $insert;
            $dataImportExpenseDistribution['update'] = $update;
        }
    } else
        $dataImportExpenseDistribution = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportExpenseDistribution, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/addExpensesDistributionAnual', function (Request $request, Response $response, $args) use (
    $expensesDistributionAnualDao,
    $familiesDao,
    $productsDao,
    $generalProductsDao,
    $assignableExpenseDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $multiproductsDao,
    $generalPCenterDao,
    $totalExpenseDao
) {
    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $flag = $_SESSION['flag_expense_distribution'];
    $products = false;
    $dataExpensesDistribution = $request->getParsedBody();

    $dataExpensesDistributions = sizeof($dataExpensesDistribution);
    $resolution = null;

    if ($dataExpensesDistributions > 1) {

        $findExpenseDistribution = $expensesDistributionAnualDao->findExpenseDistributionAnual($dataExpensesDistribution, $id_company);

        if (!$findExpenseDistribution)
            $resolution = $expensesDistributionAnualDao->insertExpensesDistributionAnualByCompany($dataExpensesDistribution, $id_company);
        else {
            $dataExpensesDistribution['idExpensesDistribution'] = $findExpenseDistribution['id_expense_distribution_anual'];
            $resolution = $expensesDistributionAnualDao->updateExpensesDistributionAnual($dataExpensesDistribution, $id_company);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto asignado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
    } else {
        $expensesDistribution = $dataExpensesDistribution['importExpense'];

        $arrProducts = [];

        for ($i = 0; $i < sizeof($expensesDistribution); $i++) {
            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($expensesDistribution[$i], $id_company);
            $expensesDistribution[$i]['selectNameProduct'] = $findProduct['id_product'];

            $findExpenseDistribution = $expensesDistributionAnualDao->findExpenseDistributionAnual($expensesDistribution[$i], $id_company);

            if (!$findExpenseDistribution)
                $resolution = $expensesDistributionAnualDao->insertExpensesDistributionAnualByCompany($expensesDistribution[$i], $id_company);
            else {
                $expensesDistribution[$i]['idExpensesDistribution'] = $findExpenseDistribution['id_expense_distribution_anual'];
                $resolution = $expensesDistributionAnualDao->updateExpensesDistributionAnual($expensesDistribution[$i]);
            }
            if ($resolution != null) break;

            // Activar Productos
            $resolution = $generalProductsDao->activeOrInactiveProducts($expensesDistribution[$i]['selectNameProduct'], 1);

            $arrProducts[$i] = $expensesDistribution[$i]['selectNameProduct'];
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Distribución de gasto importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    if ($resolution == null) {
        // Consulta unidades vendidades y volumenes de venta por producto
        $unitVol = $assignableExpenseDao->findAllExpensesDistributionAnual($id_company);
        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolAnual($id_company);
        // Obtener el total de gastos
        $totalExpense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);
        $totalExpense['total_expense'] = $totalExpense['expenses_value'];

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
        }
    }

    if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else if ($resolution != null)
        $resp = array('error' => true, 'message' => 'Ocurrio un error guardaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/updateExpensesDistributionAnual', function (Request $request, Response $response, $args) use (
    $expensesDistributionAnualDao,
    $totalExpenseDao,
    $familiesDao,
    $assignableExpenseDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $multiproductsDao
) {
    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $flag = $_SESSION['flag_expense_distribution'];
    $products = false;
    $dataExpensesDistribution = $request->getParsedBody();
    $findExpenseDistribution = $expensesDistributionAnualDao->findExpenseDistributionAnual($dataExpensesDistribution, $id_company);

    $dataExpensesDistribution['idExpensesDistribution'] = $findExpenseDistribution['id_expense_distribution_anual'];
    $expensesDistribution = $expensesDistributionAnualDao->updateExpensesDistributionAnual($dataExpensesDistribution, $id_company);

    /* Calcular gasto asignable */
    if ($expensesDistribution == null) {
        // Consulta unidades vendidades y volumenes de venta por producto
        $unitVol = $assignableExpenseDao->findAllExpensesDistributionAnual($id_company);
        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolAnual($id_company);
        // Obtener el total de gastos
        $totalExpense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);
        $totalExpense['total_expense'] = $totalExpense['expenses_value'];

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
        }
    }

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribución de gasto asignado correctamente');
    else if (isset($expensesDistribution['info']))
        $resp = array('info' => true, 'message' => $expensesDistribution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());

$app->post('/deleteExpensesDistributionAnual', function (Request $request, Response $response, $args) use (
    $expensesDistributionAnualDao,
    $assignableExpenseDao,
    $totalExpenseDao,
    $priceProductDao,
    $pricesUSDDao,
    $productsDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $multiproductsDao,
    $productionCenterDao
) {
    // session_start();
    $id_company = $_SESSION['id_company'];
    // $coverage_usd = $_SESSION['coverage_usd'];
    $dataExpensesDistribution = $request->getParsedBody();

    $expensesDistribution = $expensesDistributionAnualDao->deleteExpensesDistributionAnual($dataExpensesDistribution);

    // Calcular gasto asignable
    if ($expensesDistribution == null) {
        // Consulta unidades vendidades y volumenes de venta por producto
        $unitVol = $assignableExpenseDao->findAllExpensesDistributionAnual($id_company);

        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolAnual($id_company);

        // Obtener el total de gastos 
        $totalExpense = $totalExpenseDao->calcTotalExpenseAnualByCompany($id_company);
        $totalExpense['total_expense'] = $totalExpense['expenses_value'];

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseAnual($arr['id_product'], $expense['assignableExpense']);
        }
    }

    if ($expensesDistribution == null)
        $resp = array('success' => true, 'message' => 'Distribucion de gasto eliminado correctamente');
    else if (isset($expensesDistribution['info']))
        $resp = array('info' => true, 'message' => $expensesDistribution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new SessionMiddleware());
