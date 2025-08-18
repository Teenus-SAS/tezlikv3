<?php

use tezlikv3\dao\{
    AssignableExpenseDao,
    CostMaterialsDao,
    FamiliesDao,
    GeneralCompositeProductsDao,
    GeneralExpenseDistributionDao,
    GeneralProductsDao,
    PriceProductDao,
    PriceUSDDao,
    ProductsDao
};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Helpers\ResponseHelper;
use App\Middleware\SessionMiddleware;

$app->group('/distributionByFamilies', function (RouteCollectorProxy $group) {

    $group->get('/families', function (Request $request, Response $response, $args) {
        $familiesDao = new FamiliesDao();

        $id_company = $_SESSION['id_company'];

        $families = $familiesDao->findAllFamiliesByCompany($id_company);
        $response->getBody()->write(json_encode($families, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/expensesDistributionFamilies', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();

        $id_company = $_SESSION['id_company'];
        $expensesDistribution = $familiesDao->findAllExpensesDistributionByCompany($id_company);
        $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/expensesDistributionFamilies/{id_family}', function (Request $request, Response $response, $args) {

        $generalExpenseDistributionDao = new GeneralExpenseDistributionDao();

        $expensesDistribution = $generalExpenseDistributionDao->findAllExpensesDistributionByFamily($args['id_family']);
        $response->getBody()->write(json_encode($expensesDistribution, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/productsFamilies', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();

        $id_company = $_SESSION['id_company'];

        $products = $familiesDao->findAllProductsFamiliesByCompany($id_company);
        $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/expensesDistributionFamiliesProducts', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();

        $id_company = $_SESSION['id_company'];

        $products = $familiesDao->findAllProductsInFamily(0, $id_company);
        $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/changeTypeExpenseDistribution/{flag}', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $productsDao = new ProductsDao();
        $generalProductsDao = new GeneralProductsDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $flag = $args['flag'];
        $resolution = $familiesDao->updateFlagFamily($args['flag'], $id_company);

        $_SESSION['flag_expense_distribution'] = $args['flag'];

        /* Calcular gasto asignable */
        // Consulta unidades vendidades y volumenes de venta por producto
        $unitVol = $assignableExpenseDao->findAllExpensesDistribution($id_company);

        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVol($id_company);

        // Obtener el total de gastos
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpense($arr['id_product'], $expense['assignableExpense']);
        }

        /* x Familia */
        if ($resolution == null && $flag == 2) {
            // Consulta unidades vendidades y volumenes de venta por familia
            $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

            // Calcular el total de unidades vendidas y volumen de ventas
            $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

            foreach ($unitVol as $arr) {
                if (isset($resolution['info'])) break;
                // Calcular gasto asignable
                $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
                // Actualizar gasto asignable
                $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
            }
        }

        $products = $productsDao->findAllProductsByCompany($id_company);

        if ($flag == 2 && $resolution == null) {
            // Calcular Precio del producto
            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($resolution['info'])) break;

                $expensesDistribution = $priceProductDao->calcPrice($products[$i]['id_product']);

                if (isset($expensesDistribution['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($products[$i]['id_product'], $expensesDistribution['totalPrice']);

                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $expensesDistribution['totalPrice'];
                    $k['sale_price'] = $expensesDistribution['sale_price'];
                    $k['id_product'] = $products[$i]['id_product'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }
            }
        } else if ($flag == 1 && $resolution == null) {
            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($resolution['info'])) break;
                $expensesDistribution = $priceProductDao->calcPrice($products[$i]['id_product']);

                // Calcular Precio del producto
                if (isset($expensesDistribution['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($products[$i]['id_product'], $expensesDistribution['totalPrice']);

                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $expensesDistribution['totalPrice'];
                    $k['sale_price'] = $expensesDistribution['sale_price'];
                    $k['id_product'] = $products[$i]['id_product'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Se selecciono el tipo de distribucion correctamente', 'flag' => $flag);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');

        $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/addFamily', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();

        $id_company = $_SESSION['id_company'];

        $dataFamily = $request->getParsedBody();

        $findFamily = $familiesDao->findFamily($dataFamily, $id_company);

        if (!$findFamily) {
            $families = $familiesDao->insertFamilyByCompany($dataFamily, $id_company);

            if ($families == null)
                $resp = array('success' => true, 'message' => 'Familia agregada correctamente');
            else if (isset($families['info']))
                $resp = array('info' => true, 'message' => $families['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Familia ya existente. Ingrese un nueva familia');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/saveProductFamily', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $generalProductsDao = new GeneralProductsDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];
        $dataFamily = $request->getParsedBody();

        $resolution = $familiesDao->updateFamilyProduct($dataFamily);

        /* Calcular gasto asignable x Familia */
        // Consulta unidades vendidades y volumenes de venta por familia
        $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

        // Obtener el total de gastos
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
        }

        $products = $familiesDao->findAllProductsInFamily($dataFamily['idFamily'], $id_company);

        // Calcular Precio del producto
        for ($i = 0; $i < sizeof($products); $i++) {
            if (isset($resolution['info'])) break;
            $products[$i]['selectNameProduct'] = $products[$i]['id_product'];

            $expensesDistribution = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

            if (isset($expensesDistribution['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $expensesDistribution['totalPrice']);

            if (isset($resolution['info'])) break;
            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $expensesDistribution['totalPrice'];
                $k['sale_price'] = $expensesDistribution['sale_price'];
                $k['id_product'] = $products[$i]['selectNameProduct'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }
        }

        if ($resolution == null && $dataFamily['idFamily'] != 0)
            $resp = array('success' => true, 'message' => 'Producto asignado a la familia correctamente');
        else if ($resolution == null && $dataFamily['idFamily'] == 0)
            $resp = array('success' => true, 'message' => 'Producto eliminado de la familia correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->post('/updateFamily', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();

        $dataFamily = $request->getParsedBody();

        $resolution = $familiesDao->updateFamily($dataFamily);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Familia modificada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras guardaba la información. Intente nuevamente');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/deleteFamily/{id_family}', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();
        $generalExpenseDistributionDao = new GeneralExpenseDistributionDao();

        $expensesDistribution = $generalExpenseDistributionDao->findAllExpensesDistributionByFamily($args['id_family']);

        if (!$expensesDistribution) {
            $resolution = $familiesDao->deleteFamily($args['id_family']);

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Familia eliminada correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('info' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Familia asociada a productos no se puede eliminar');

        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    $group->get('/deleteExpensesDistributionFamily/{id_family}', function (Request $request, Response $response, $args) {

        $familiesDao = new FamiliesDao();
        $priceProductDao = new PriceProductDao();
        $pricesUSDDao = new PriceUSDDao();
        $assignableExpenseDao = new AssignableExpenseDao();
        $generalProductsDao = new GeneralProductsDao();

        $id_company = $_SESSION['id_company'];
        $coverage_usd = $_SESSION['coverage_usd'];

        $data['idFamily'] = $args['id_family'];
        $data['selectNameProduct'] = 0;
        $data['unitsSold'] = 0;
        $data['turnover'] = 0;

        $resolution = $familiesDao->updateDistributionFamily($data);
        $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($args['id_family'], 0);
        $resolution = $familiesDao->updateFamilyProduct($data);

        /* Calcular gasto asignable x Familia */
        // Consulta unidades vendidades y volumenes de venta por familia
        $unitVol = $familiesDao->findAllExpensesDistributionByCompany($id_company);

        // Calcular el total de unidades vendidas y volumen de ventas
        $totalUnitVol = $assignableExpenseDao->findTotalUnitsVolByFamily($id_company);

        // Obtener el total de gastos
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        foreach ($unitVol as $arr) {
            if (isset($resolution['info'])) break;
            // Calcular gasto asignable
            $expense = $assignableExpenseDao->calcAssignableExpense($arr, $totalUnitVol, $totalExpense);
            // Actualizar gasto asignable
            $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($arr['id_family'], $expense['assignableExpense']);
        }

        $products = $familiesDao->findAllProductsInFamily($args['id_family'], $id_company);

        // Calcular Precio del producto
        for ($i = 0; $i < sizeof($products); $i++) {
            if (isset($resolution['info'])) break;
            $products[$i]['selectNameProduct'] = $products[$i]['id_product'];

            $expensesDistribution = $priceProductDao->calcPrice($products[$i]['selectNameProduct']);

            if (isset($expensesDistribution['totalPrice']))
                $resolution = $generalProductsDao->updatePrice($products[$i]['selectNameProduct'], $expensesDistribution['totalPrice']);

            if (isset($resolution['info'])) break;
            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $expensesDistribution['totalPrice'];
                $k['sale_price'] = $expensesDistribution['sale_price'];
                $k['id_product'] = $products[$i]['id_product'];

                $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Distribucion de gasto x familia eliminado correctamente');
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar el gasto, existe información asociada a él');

        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new SessionMiddleware());
