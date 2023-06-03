<?php

use tezlikv3\dao\AssignableExpenseDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\CostMinuteDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\DashboardProductsDao;
use tezlikv3\dao\ExpenseRecoverDao;
use tezlikv3\dao\ExpensesDistributionDao;
use tezlikv3\dao\ExternalServicesDao;
use tezlikv3\dao\FactoryLoadDao;
use tezlikv3\dao\GeneralExpenseRecoverDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\IndirectCostDao;
use tezlikv3\dao\MachinesDao;
use tezlikv3\dao\MaterialsDao;
use tezlikv3\dao\MinuteDepreciationDao;
use tezlikv3\dao\PayrollDao;
use tezlikv3\dao\ProductsCostDao;
use tezlikv3\dao\ProductsMaterialsDao;
use tezlikv3\dao\ProductsProcessDao;
use tezlikv3\dao\SimulatorDao;

$dashboardProductsDao = new DashboardProductsDao();
$simulatorDao = new SimulatorDao();
$externalServicesDao = new ExternalServicesDao();
$expensesDistributionDao = new ExpensesDistributionDao();
$generalExpenseRecoverDao = new GeneralExpenseRecoverDao();
$assignableExpenseDao = new AssignableExpenseDao();
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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/dashboardPricesSimulator/{id_product}', function (Request $request, Response $response, $args) use (
    $dashboardProductsDao,
    $generalExpenseRecoverDao,
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
    $assignableExpenseDao,
    $expenseRecoverDao,
    $generalProductsDao,
    $costMaterialsDao,
    $costWorkforceDao,
    $indirectCostDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
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
        $resolution = $costWorkforceDao->updateCostWorkforce($simulator['products'][0]['cost_workforce'], $products['idProduct'], $id_company);
        $resolution = $indirectCostDao->updateCostIndirectCost($simulator['products'][0]['cost_indirect_cost'], $products['idProduct'], $id_company);
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


            $resolution = $factoryLoadDao->updateFactoryLoad($factoryLoad);
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
            if (isset($services[$i]['idService'])) {
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
    if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) { // Guardar data distribucion de gastos
        if ($resolution == null) {
            if (!isset($simulator['expensesDistribution']))
                $count = 0;
            else {
                $expensesDistribution = $simulator['expensesDistribution'];
                $count = sizeof($expensesDistribution);
            }

            for ($i = 0; $i < $count; $i++) {
                if (isset($resolution['info'])) break;
                $expensesDistribution[$i]['selectNameProduct'] = $expensesDistribution[$i]['id_product'];
                $expensesDistribution[$i]['unitsSold'] = $expensesDistribution[$i]['units_sold'];
                if (isset($expensesDistribution[$i]['idExpensesDistribution'])) {
                    $expensesDistribution[$i]['idExpensesDistribution'] = $expensesDistribution[$i]['id_expenses_distribution'];

                    $resolution = $expensesDistributionDao->updateExpensesDistribution($expensesDistribution[$i]);
                } else
                    $resolution = $expensesDistributionDao->insertExpensesDistributionByCompany($expensesDistribution[$i], $id_company);

                if (isset($resolution['info'])) break;

                $resolution = $assignableExpenseDao->updateAssignableExpense($expensesDistribution[$i]['id_product'], $expensesDistribution[$i]['assignable_expense']);
            }
        }
    } else { // Guardar data recuperacion de gastos
        if ($resolution == null) {
            if (!isset($simulator['expenseRecover']))
                $count = 0;
            else {
                $expenseRecovers = $simulator['expenseRecover'];
                $count = sizeof($expenseRecovers);
            }

            for ($i = 0; $i < $count; $i++) {
                if (isset($resolution['info'])) break;
                $expenseRecovers[$i]['idProduct'] = $expenseRecovers[$i]['id_product'];

                if (isset($expenseRecovers[$i]['idExpenseRecover'])) {
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
