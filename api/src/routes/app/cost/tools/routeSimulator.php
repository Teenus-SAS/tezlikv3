<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostMinuteDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\DashboardProductsDao;
use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\FactoryLoadDao;
use tezlikv3\dao\FamiliesDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\GeneralExternalServicesDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralServicesDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\MachinesDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\dao\MinuteDepreciationDao;
use tezlikv3\dao\PayrollDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\dao\ProductsCostDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\SimulatorDao;
use tezlikv3\dao\WebTokenDao;

$dashboardProductsDao = new DashboardProductsDao();
$simulatorDao = new SimulatorDao();
$externalServicesDao = new ExternalServicesDao();
$generalServicesDao = new GeneralServicesDao();
$webTokenDao = new WebTokenDao();
$expensesDistributionDao = new ExpensesDistributionDao();
$generalExpenseRecoverDao = new GeneralExpenseRecoverDao();
$familiesDao = new FamiliesDao();
$productsCostDao = new ProductsCostDao();
$generalProductsDao = new GeneralProductsDao();
$machinesDao = new MachinesDao();
$minuteDepreciationDao = new MinuteDepreciationDao();
$materialsDao = new MaterialsDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$productsProcessDao = new ProductsProcessDao();
$factoryLoadDao = new FactoryLoadDao();
$costMinuteDao = new CostMinuteDao();
$payrollDao = new PayrollDao();
$expenseRecoverDao = new ExpenseRecoverDao();
$assignableExpenseDao = new AssignableExpenseDao();
$costMaterialsDao = new CostMaterialsDao();
$costWorkforceDao = new CostWorkforceDao();
$indirectCostDao = new IndirectCostDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$costCompositeProductsDao = new CostCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardPricesSimulator/{id_product}', function (Request $request, Response $response, $args) use (
    $dashboardProductsDao,
    $generalExpenseRecoverDao,
    $expensesDistributionDao,
    $familiesDao,
    $webTokenDao,
    $assignableExpenseDao,
    $generalServicesDao,
    $simulatorDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    // Consultar analisis de costos por producto
    $costAnalysisProducts = $dashboardProductsDao->findCostAnalysisByProduct($args['id_product'], $id_company);
    // Consultar Costo Materia prima por producto
    $costRawMaterials = $dashboardProductsDao->findCostRawMaterialsByProduct($args['id_product'], $id_company, 1);
    // Consultar Ficha tecnica Proceso del producto
    $totalTimeProcess = $dashboardProductsDao->findProductProcessByProduct($args['id_product'], $id_company, 1);
    // Carga fabril
    $factoryLoad = $simulatorDao->findAllFactoryLoadByProduct($args['id_product'], $id_company);
    // Servicios Externos
    $externalServices = $generalServicesDao->findAllExternalServicesByIdProduct($args['id_product'], $id_company);
    // Nomina
    $payroll = $simulatorDao->findAllPayrollByProduct($args['id_product'], $id_company);

    if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {
        $totalExpense = $assignableExpenseDao->findTotalExpense($id_company);

        $_SESSION['flag_expense_distribution'] == 1 || $_SESSION['flag_expense_distribution'] == 0 ?
            $expensesDistribution = $expensesDistributionDao->findAllExpensesDistributionByCompany($id_company) :
            $expensesDistribution = $familiesDao->findAllFamiliesByCompany($id_company);

        $data['expensesDistribution'] = $expensesDistribution;
        $data['totalExpense'] = $totalExpense['total_expense'];
    } else {
        $expenseRecover = $generalExpenseRecoverDao->findExpenseRecoverByIdProduct($args['id_product']);
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

$app->post('/addSimulator', function (Request $request, Response $response, $args) use (
    $productsCostDao,
    $webTokenDao,
    $materialsDao,
    $machinesDao,
    $minuteDepreciationDao,
    $productsMaterialsDao,
    $productsProcessDao,
    $factoryLoadDao,
    $costMinuteDao,
    $externalServicesDao,
    $payrollDao,
    $expensesDistributionDao,
    $familiesDao,
    $assignableExpenseDao,
    $expenseRecoverDao,
    $generalProductsDao,
    $costMaterialsDao,
    $costWorkforceDao,
    $indirectCostDao,
    $generalCompositeProductsDao,
    $priceProductDao,
    $pricesUSDDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['error' => $info['info']]));
        // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $data = $request->getParsedBody();

    $simulator = $data['simulator'];

    // Guardar data productos
    $products['idProduct'] = $simulator['products'][0]['id_product'];
    $products['profitability'] = $simulator['products'][0]['profitability'];
    $products['commissionSale'] = $simulator['products'][0]['commission_sale'];

    $resolution = $productsCostDao->updateProductsCostByCompany($products);
    // Modificar 'product_cost'
    if ($resolution == null) {
        $resolution = $generalProductsDao->updatePrice($products['idProduct'], $simulator['products'][0]['price']);

        $products['cost'] = $simulator['products'][0]['cost_materials'];
        $resolution = $costMaterialsDao->updateCostMaterials($products, $id_company);
        $resolution = $costWorkforceDao->updateTotalCostWorkforce($simulator['products'][0]['cost_workforce'], $products['idProduct'], $id_company);
        $resolution = $indirectCostDao->updateTotalCostIndirectCost($simulator['products'][0]['cost_indirect_cost'], $products['idProduct'], $id_company);

        if ($resolution == null && $_SESSION['flag_composite_product'] == '1') {
            // Calcular costo material porq
            $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($products['idProduct']);

            foreach ($productsCompositer as $arr) {
                if (isset($resolution['info'])) break;

                $data = [];
                $data['idProduct'] = $arr['id_product'];
                $data['compositeProduct'] = $arr['id_child_product'];

                /* Calcular costo indirecto */
                // Buscar la maquina asociada al producto
                // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                // // Calcular costo indirecto
                // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                // // Actualizar campo
                // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                // // Calcular costo nomina total
                // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                // if (isset($resolution['info'])) break;

                // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                // $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                // if (isset($resolution['info'])) break;

                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                if (isset($resolution['info'])) break;
                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                if (isset($resolution['info'])) break;

                $data = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($data['totalPrice']))
                    $resolution = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

                if (isset($resolution['info'])) break;
                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                    $k = [];
                    $k['price'] = $data['totalPrice'];
                    $k['sale_price'] = $data['sale_price'];
                    $k['id_product'] = $arr['id_product'];

                    $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                }

                if (isset($resolution['info'])) break;

                $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer2 as $j) {
                    if (isset($resolution['info'])) break;

                    $data = [];
                    $data['compositeProduct'] = $j['id_child_product'];
                    $data['idProduct'] = $j['id_product'];

                    /* Calcular costo indirecto */
                    // Buscar la maquina asociada al producto
                    // $dataProductMachine = $indirectCostDao->findMachineByProduct($data['idProduct'], $id_company);
                    // // Calcular costo indirecto
                    // $indirectCost = $indirectCostDao->calcIndirectCost($dataProductMachine);
                    // // Actualizar campo
                    // $resolution = $indirectCostDao->updateTotalCostIndirectCost($indirectCost, $data['idProduct'], $id_company);
                    // if (isset($resolution['info'])) break;

                    // // Calcular costo nomina total
                    // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                    // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                    // if (isset($resolution['info'])) break;

                    // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    // $resolution = $indirectCostDao->updateTotalCostIndirectCost($data['cost_indirect_cost'], $data['idProduct'], $id_company);
                    // if (isset($resolution['info'])) break;

                    // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                    // if (isset($resolution['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($resolution['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($resolution['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($resolution['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $resolution = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
                }
            }
        }
    }

    // Guardar data maquinas
    if ($resolution == null) {
        if (!isset($simulator['dataMachine']))
            $count = 0;
        else {
            $machines = $simulator['dataMachine'];
            $count = sizeof($machines);
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $machines[$i]['idMachine'] = $machines[$i]['id_machine'];
            $machines[$i]['costMachine'] = $machines[$i]['cost_machine'];
            $machines[$i]['depreciationYears'] = $machines[$i]['years_depreciation'];
            $machines[$i]['residualValue'] = $machines[$i]['residual_value'];
            $machines[$i]['hoursMachine'] = $machines[$i]['hours_machine'];
            $machines[$i]['daysMachine'] = $machines[$i]['days_machine'];
            $machines[$i]['minuteDepreciation'] = $machines[$i]['minute_depreciation'];

            $resolution = $machinesDao->updateMachine($machines[$i]);
            if (isset($resolution['info'])) break;

            $resolution = $minuteDepreciationDao->updateMinuteDepreciation($machines[$i], $id_company);
        }
    }
    // Guardar data materiales
    if ($resolution == null) {
        if (!isset($simulator['materials']))
            $count = 0;
        else {
            $materials = $simulator['materials'];
            $count = sizeof($materials);
        }


        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $materials[$i]['costRawMaterial'] = $materials[$i]['cost_material'];
            $materials[$i]['idMaterial'] = $materials[$i]['id_material'];
            $materials[$i]['refRawMaterial'] = $materials[$i]['reference'];
            $materials[$i]['nameRawMaterial'] = $materials[$i]['material'];
            $materials[$i]['unit'] = $materials[$i]['unit_material'];

            $resolution = $materialsDao->updateMaterialsByCompany($materials[$i], $id_company);
        }
    }
    // Guardar data ficha tecnica materiales
    if ($resolution == null) {
        if (!isset($simulator['materials']))
            $count = 0;
        else {
            $productsMaterials = $simulator['materials'];
            $count = sizeof($productsMaterials);
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $productsMaterials[$i]['idProductMaterial'] = $productsMaterials[$i]['id_product_material'];
            $productsMaterials[$i]['material'] = $productsMaterials[$i]['id_material'];
            $productsMaterials[$i]['unit'] = $productsMaterials[$i]['unit_product_material'];
            $productsMaterials[$i]['idProduct'] = $productsMaterials[$i]['id_product'];

            $resolution = $productsMaterialsDao->updateProductsMaterials($productsMaterials[$i]);
        }
    }
    // Guardar data ficha tecnica procesos
    if ($resolution == null) {
        if (!isset($simulator['productsProcess']))
            $count = 0;
        else {
            $productsProcess = $simulator['productsProcess'];
            $count = sizeof($productsProcess);
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $productsProcess[$i]['idProductProcess'] = $productsProcess[$i]['id_product_process'];
            $productsProcess[$i]['idProduct'] = $productsProcess[$i]['id_product'];
            $productsProcess[$i]['idProcess'] = $productsProcess[$i]['id_process'];
            $productsProcess[$i]['idMachine'] = $productsProcess[$i]['id_machine'];
            $productsProcess[$i]['enlistmentTime'] = $productsProcess[$i]['enlistment_time'];
            $productsProcess[$i]['operationTime'] = $productsProcess[$i]['operation_time'];

            $resolution = $productsProcessDao->updateProductsProcess($productsProcess[$i]);
        }
    }
    // Guardar data carga fabril
    if ($resolution == null) {
        if (!isset($simulator['factoryLoad']))
            $count = 0;
        else {
            $factoryLoad = $simulator['factoryLoad'];
            $count = sizeof($factoryLoad);
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $factoryLoad[$i]['idManufacturingLoad'] = $factoryLoad[$i]['id_manufacturing_load'];
            $factoryLoad[$i]['idMachine'] = $factoryLoad[$i]['id_machine'];
            $factoryLoad[$i]['descriptionFactoryLoad'] = $factoryLoad[$i]['input'];
            $factoryLoad[$i]['costFactory'] = $factoryLoad[$i]['cost'];
            $factoryLoad[$i]['costMinute'] = $factoryLoad[$i]['cost_minute'];


            $resolution = $factoryLoadDao->updateFactoryLoad($factoryLoad[$i]);
            if (isset($resolution['info'])) break;

            $resolution = $costMinuteDao->updateCostMinuteFactoryLoad($factoryLoad[$i], $id_company);
        }
    }
    // Guardar data servicios externos
    if ($resolution == null) {
        if (!isset($simulator['externalServices']))
            $count = 0;
        else {
            $services = $simulator['externalServices'];
            $count = sizeof($services);
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $services[$i]['idProduct'] = $services[$i]['id_product'];
            $services[$i]['service'] = $services[$i]['name_service'];
            $services[$i]['costService'] = $services[$i]['cost'];
            if (isset($services[$i]['id_service'])) {
                $services[$i]['idService'] = $services[$i]['id_service'];

                $resolution = $externalServicesDao->updateExternalServices($services[$i]);
            } else
                $resolution = $expenseRecoverDao->insertRecoverExpenseByCompany($services[$i], $id_company);
        }
    }
    // Guardar data nomina
    if ($resolution == null) {
        if (!isset($simulator['payroll']))
            $count = 0;
        else {
            $payroll = $simulator['payroll'];
            $count = sizeof($payroll);
        }

        for ($i = 0; $i < $count; $i++) {
            if (isset($resolution['info'])) break;
            $payroll[$i]['idPayroll'] = $payroll[$i]['id_payroll'];
            $payroll[$i]['idProcess'] = $payroll[$i]['id_process'];
            $payroll[$i]['basicSalary'] = $payroll[$i]['salary'];
            $payroll[$i]['extraTime'] = $payroll[$i]['extra_time'];
            $payroll[$i]['workingDaysMonth'] = $payroll[$i]['working_days_month'];
            $payroll[$i]['workingHoursDay'] = $payroll[$i]['hours_day'];
            $payroll[$i]['factor'] = $payroll[$i]['factor_benefit'];
            $payroll[$i]['typeFactor'] = $payroll[$i]['type_contract'];
            $payroll[$i]['risk'] = $payroll[$i]['id_risk'];
            $payroll[$i]['minuteValue'] = $payroll[$i]['minute_value'];
            $payroll[$i]['salaryNet'] = $payroll[$i]['salary_net'];

            $resolution = $payrollDao->updatePayroll($payroll[$i]);
        }
    }

    if ($resolution == null) {
        if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { // Guardar data distribucion de gastos
            if (!isset($simulator['expensesDistribution']))
                $count = 0;
            else {
                $expensesDistribution = $simulator['expensesDistribution'];
                $count = sizeof($expensesDistribution);
            }
            for ($i = 0; $i < $count; $i++) {
                if (isset($resolution['info'])) break;

                $expensesDistribution[$i]['unitsSold'] = $expensesDistribution[$i]['units_sold'];

                if ($_SESSION['flag_expense_distribution'] == 1 || $_SESSION['flag_expense_distribution'] == 0) {
                    $expensesDistribution[$i]['selectNameProduct'] = $expensesDistribution[$i]['id_product'];

                    if (isset($expensesDistribution[$i]['id_expenses_distribution'])) {
                        $expensesDistribution[$i]['idExpensesDistribution'] = $expensesDistribution[$i]['id_expenses_distribution'];

                        $resolution = $expensesDistributionDao->updateExpensesDistribution($expensesDistribution[$i]);
                    } else
                        $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($expensesDistribution[$i], $id_company);

                    $resolution = $assignableExpenseDao->updateAssignableExpense($expensesDistribution[$i]['id_product'], $expensesDistribution[$i]['assignable_expense']);
                } else {
                    $expensesDistribution[$i]['idFamily'] = $expensesDistribution[$i]['id_family'];

                    if ($simulator['products'][0]['id_family'] == $expensesDistribution[$i]['id_family']) {
                        $expensesDistribution[$i]['selectNameProduct'] = $simulator['products'][0]['id_product'];
                        $resolution = $familiesDao->updateFamilyProduct($expensesDistribution[$i]);
                    }

                    $resolution = $familiesDao->updateDistributionFamily($expensesDistribution[$i]);
                    $resolution = $assignableExpenseDao->updateAssignableExpenseByFamily($expensesDistribution[$i]['id_family'], $expensesDistribution[$i]['assignable_expense']);
                }
                if (isset($resolution['info'])) break;
            }
        } else { // Guardar data recuperacion de gastos
            if (!isset($simulator['expenseRecover']))
                $count = 0;
            else {
                $expenseRecovers = $simulator['expenseRecover'];
                $count = sizeof($expenseRecovers);
            }

            for ($i = 0; $i < $count; $i++) {
                if (isset($resolution['info'])) break;
                $expenseRecovers[$i]['idProduct'] = $expenseRecovers[$i]['id_product'];
                $expenseRecovers[$i]['percentage'] = $expenseRecovers[$i]['expense_recover'];

                if (isset($expenseRecovers[$i]['id_expense_recover'])) {
                    $expenseRecovers[$i]['idExpenseRecover'] = $expenseRecovers[$i]['id_expense_recover'];

                    $resolution = $expenseRecoverDao->updateRecoverExpense($expenseRecovers[$i]);
                } else
                    $resolution = $expenseRecoverDao->insertRecoverExpenseByCompany($expenseRecovers[$i], $id_company);
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Se guardo la simulacion en la base de datos correctamente');
    elseif (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
