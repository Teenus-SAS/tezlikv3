<?php

use tezlikv3\Dao\BenefitsDao;
use tezlikv3\dao\ConvertDataDao;
use tezlikv3\dao\CostCompositeProductsDao;
use tezlikv3\dao\CostMaterialsDao;
use tezlikv3\dao\PayrollDao;
use tezlikv3\dao\CostWorkforceDao;
use tezlikv3\dao\FactorBenefitDao;
use tezlikv3\dao\GeneralCompositeProductsDao;
use tezlikv3\dao\GeneralProductsDao;
use tezlikv3\dao\GeneralProcessDao;
use tezlikv3\dao\PriceProductDao;
use tezlikv3\dao\GeneralPayrollDao;
use tezlikv3\Dao\RisksDao;
use tezlikv3\dao\ValueMinuteDao;

$payrollDao = new PayrollDao();
$generalPayrollDao = new GeneralPayrollDao();
$valueMinuteDao = new ValueMinuteDao();
$convertDataDao = new ConvertDataDao();
$processDao = new GeneralProcessDao();
$costWorkforceDao = new CostWorkforceDao();
$priceProductDao = new PriceProductDao();
$generalProductsDao = new GeneralProductsDao();
$benefitsDao = new BenefitsDao();
$risksDao = new RisksDao();
$factorBenefitDao = new FactorBenefitDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/payroll', function (Request $request, Response $response, $args) use ($payrollDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $payroll = $payrollDao->findAllPayrollByCompany($id_company);
    $response->getBody()->write(json_encode($payroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/salarynet', function (Request $request, Response $response, $args) use ($generalPayrollDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $payroll = $generalPayrollDao->findSalarynetByPayroll($id_company);
    $response->getBody()->write(json_encode($payroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/process/{employee}', function (Request $request, Response $response, $args) use ($generalPayrollDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $process = $generalPayrollDao->findAllProcessByEmployeeNotIn($args['employee'], $id_company);
    $response->getBody()->write(json_encode($process, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/payrollDataValidation', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $processDao
) {
    $dataPayroll = $request->getParsedBody();

    if (isset($dataPayroll)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $payroll = $dataPayroll['importPayroll'];

        for ($i = 0; $i < sizeof($payroll); $i++) {
            if (
                empty(trim($payroll[$i]['process'])) || empty(trim($payroll[$i]['employee'])) || empty(trim($payroll[$i]['basicSalary'])) || empty(trim($payroll[$i]['workingDaysMonth'])) ||
                empty(trim($payroll[$i]['workingHoursDay'])) || empty(trim($payroll[$i]['typeFactor'])) || empty(trim($payroll[$i]['benefit'])) || empty(trim($payroll[$i]['riskLevel']))
            ) {
                $i = $i + 2;
                $dataImportPayroll = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }
            if ($payroll[$i]['workingDaysMonth'] > 31 || $payroll[$i]['workingHoursDay'] > 24) {
                $i = $i + 2;
                $dataImportPayroll = array('error' => true, 'message' => "El campo dias trabajo x mes debe ser menor a 31 <br>y horas trabajo x dia menor a 24, fila: {$i}");
                break;
            }

            // Obtener id proceso
            $findProcess = $processDao->findProcess($payroll[$i], $id_company);

            if (!$findProcess) {
                $i = $i + 2;
                $dataImportPayroll = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila {$i}");
                break;
            } else
                $payroll[$i]['idProcess'] = $findProcess['id_process'];

            $findPayroll = $generalPayrollDao->findPayroll($payroll[$i], $id_company);

            !$findPayroll ? $insert = $insert + 1 : $update = $update + 1;
            $dataImportPayroll['insert'] = $insert;
            $dataImportPayroll['update'] = $update;
        }
    } else
        $dataImportPayroll = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportPayroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPayroll', function (Request $request, Response $response) use (
    $payrollDao,
    $generalPayrollDao,
    $convertDataDao,
    $valueMinuteDao,
    $processDao,
    $costWorkforceDao,
    $priceProductDao,
    $generalProductsDao,
    $benefitsDao,
    $risksDao,
    $factorBenefitDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    $dataPayrolls = sizeof($dataPayroll);

    if ($dataPayrolls > 1) {

        $payroll = $generalPayrollDao->findPayroll($dataPayroll, $id_company);

        if (!$payroll) {
            $dataPayroll = $convertDataDao->strReplacePayroll($dataPayroll);

            // Calcular factor benefico
            $dataBenefits = $benefitsDao->findAllBenefits();

            $dataPayroll = $factorBenefitDao->calcFactorBenefit($dataBenefits, $dataPayroll);

            // Calcular Valor x minuto
            $dataPayroll = $valueMinuteDao->calculateValueMinute($dataPayroll);

            $payroll = $payrollDao->insertPayrollByCompany($dataPayroll, $id_company);

            if ($payroll == null)
                $resp = array('success' => true, 'message' => 'Nomina creada correctamente');
            else if (isset($payroll['info']))
                $resp = array('info' => true, 'message' => $payroll['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Empleado con Proceso ya existente. Ingrese nuevo proceso');
    } else {
        $payroll = $dataPayroll['importPayroll'];

        for ($i = 0; $i < sizeof($payroll); $i++) {
            empty($payroll[$i]['endowment']) ? $payroll[$i]['endowment'] = 0 : $payroll[$i]['endowment'];
            empty($payroll[$i]['extraTime']) ? $payroll[$i]['extraTime'] = 0 : $payroll[$i]['extraTime'];
            empty($payroll[$i]['bonification']) ? $payroll[$i]['bonification'] = 0 : $payroll[$i]['bonification'];
            empty($payroll[$i]['factor']) ? $payroll[$i]['factor'] = 0 : $payroll[$i]['factor'];

            $payroll[$i] = $convertDataDao->strReplacePayroll($payroll[$i]);

            // Obtener Data Prestaciones
            $dataBenefits = $benefitsDao->findAllBenefits();

            // Obtener data segun el nivel de riesgo
            $dataRisk = $risksDao->findRiskByName($payroll[$i]);
            $payroll[$i]['valueRisk'] = $dataRisk['percentage'];
            $payroll[$i]['risk'] = $dataRisk['id_risk'];

            // Verificar salario
            $payroll[$i]['benefit'] == 'SI' ? $payroll[$i]['salary'] = $payroll[$i]['basicSalary'] + $payroll[$i]['bonification'] :
                $payroll[$i]['salary'] = $payroll[$i]['basicSalary'];

            // Calcular Factor Prestacional
            $payroll[$i] = $factorBenefitDao->calcFactorBenefit($dataBenefits, $payroll[$i]);

            $payroll[$i] = $valueMinuteDao->calculateValueMinute($payroll[$i]);

            // Obtener idProceso
            $findProcess = $processDao->findProcess($payroll[$i], $id_company);
            $payroll[$i]['idProcess'] = $findProcess['id_process'];

            $findPayroll = $generalPayrollDao->findPayroll($payroll[$i], $id_company);

            if (!$findPayroll)
                $resolution = $payrollDao->insertPayrollByCompany($payroll[$i], $id_company);
            else {
                $payroll[$i]['idPayroll'] = $findPayroll['id_payroll'];
                $resolution = $payrollDao->updatePayroll($payroll[$i]);

                if ($resolution != null) break;
                $dataProducts = $costWorkforceDao->findProductByProcess($payroll[$i]['idProcess'], $id_company);

                foreach ($dataProducts as $arr) {
                    // Calcular costo nomina
                    $resolution = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
                    if (isset($resolution['info'])) break;

                    // Calcular costo nomina total  
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

                    $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);

                    if (isset($resolution['info'])) break;

                    // Calcular precio products_costs
                    $resolution = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($resolution['info'])) break;

                    $resolution = $generalProductsDao->updatePrice($arr['id_product'], $resolution['totalPrice']);

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($resolution['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer as $j) {
                            if (isset($resolution['info'])) break;

                            $data = [];
                            $data['idProduct'] = $j['id_product'];
                            $data['compositeProduct'] = $j['id_child_product'];

                            $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                            $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                            if (isset($resolution['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($resolution['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($resolution['info'])) break;

                            $data = $priceProductDao->calcPrice($j['id_product']);
                            $resolution = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                            if (isset($resolution['info'])) break;

                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($resolution['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                                $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                                if (isset($resolution['info'])) break;

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($resolution['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($resolution['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);
                                $resolution = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                            }
                        }
                    }
                }
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Nomina importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePayroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $generalPayrollDao,
    $convertDataDao,
    $valueMinuteDao,
    $costWorkforceDao,
    $priceProductDao,
    $generalProductsDao,
    $benefitsDao,
    $factorBenefitDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $type_payroll = $_SESSION['type_payroll'];
    $dataPayroll = $request->getParsedBody();

    $payroll = $generalPayrollDao->findPayroll($dataPayroll, $id_company);

    !is_array($payroll) ? $data['id_payroll'] = 0 : $data = $payroll;

    if ($data['id_payroll'] == $dataPayroll['idPayroll'] || $data['id_payroll'] == 0) {
        if ($type_payroll == '1') {
            $dataPayroll = $convertDataDao->strReplacePayroll($dataPayroll);

            // Calcular factor benefico
            $dataBenefits = $benefitsDao->findAllBenefits();

            $dataPayroll = $factorBenefitDao->calcFactorBenefit($dataBenefits, $dataPayroll);

            // Calcular Valor x Minuto
            $dataPayroll = $valueMinuteDao->calculateValueMinute($dataPayroll);

            $payroll = $payrollDao->updatePayroll($dataPayroll);

            if ($payroll == null) {

                $dataProducts = $costWorkforceDao->findProductByProcess($dataPayroll['idProcess'], $id_company);

                foreach ($dataProducts as $arr) {
                    if (isset($payroll['info'])) break;

                    // Calcular costo nomina
                    $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
                    if (isset($payroll['info'])) break;

                    // Calcular costo nomina total 
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

                    $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);

                    if (isset($payroll['info'])) break;

                    // Calcular precio products_costs
                    $payroll = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($payroll['info'])) break;

                    $payroll = $generalProductsDao->updatePrice($arr['id_product'], $payroll['totalPrice']);

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($payroll['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer as $j) {
                            if (isset($payroll['info'])) break;

                            $data = [];
                            $data['idProduct'] = $j['id_product'];
                            $data['compositeProduct'] = $j['id_child_product'];

                            $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                            $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                            if (isset($payroll['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($payroll['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($payroll['info'])) break;

                            $data = $priceProductDao->calcPrice($j['id_product']);
                            $payroll = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                            if (isset($payroll['info'])) break;

                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($payroll['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                                $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                                if (isset($payroll['info'])) break;

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($payroll['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($payroll['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);
                                $payroll = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                            }
                        }
                    }
                }
            }
        } else {
            $payroll = $generalPayrollDao->updatePayroll($dataPayroll);
        }

        if ($payroll == null)
            $resp = array('success' => true, 'message' => 'Nomina actualizada correctamente');
        else if (isset($payroll['info']))
            $resp = array('info' => true, 'message' => $payroll['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Empleado con Proceso ya existente. Ingrese nuevo proceso');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/copyPayroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $costWorkforceDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    $payroll = $payrollDao->insertPayrollByCompany($dataPayroll, $id_company);

    if ($payroll == null) {

        $dataProducts = $costWorkforceDao->findProductByProcess($dataPayroll['idProcess'], $id_company);

        foreach ($dataProducts as $arr) {
            if (isset($payroll['info'])) break;
            // Calcular costo nomina
            $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
            if (isset($payroll['info'])) break;

            // Calcular costo nomina total
            $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

            $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);

            if (isset($payroll['info'])) break;

            // Calcular precio products_costs
            $payroll = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($payroll['info'])) break;

            $payroll = $generalProductsDao->updatePrice($arr['id_product'], $payroll['totalPrice']);

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($payroll['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer as $j) {
                    if (isset($payroll['info'])) break;

                    $data = [];
                    $data['idProduct'] = $j['id_product'];
                    $data['compositeProduct'] = $j['id_child_product'];

                    $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                    if (isset($payroll['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($payroll['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($payroll['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);
                    $payroll = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($payroll['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $k) {
                        if (isset($payroll['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $k['id_child_product'];
                        $data['idProduct'] = $k['id_product'];

                        $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                        $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                        if (isset($payroll['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($payroll['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($payroll['info'])) break;

                        $data = $priceProductDao->calcPrice($k['id_product']);
                        $payroll = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                    }
                }
            }
        }
    }

    if ($payroll == null)
        $resp = array('success' => true, 'message' => 'Nomina copiada correctamente');
    else if (isset($payroll['info']))
        $resp = array('info' => true, 'message' => $payroll['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});



$app->post('/deletePayroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $generalPayrollDao,
    $costWorkforceDao,
    $priceProductDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    $payrolls = $generalPayrollDao->findAllPayrollByEmployee($dataPayroll['employee'], $id_company);

    if (sizeof($payrolls) > 1) {
        $payroll = $payrollDao->deletePayroll($dataPayroll['idPayroll']);

        if ($payroll == null) {

            $dataProducts = $costWorkforceDao->findProductByProcess($dataPayroll['idProcess'], $id_company);

            foreach ($dataProducts as $arr) {
                // Calcular costo nomina
                $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);

                if (isset($payroll['info'])) break;
                // Calcular costo nomina total
                $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

                $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);

                if (isset($payroll['info'])) break;

                // Calcular precio products_costs
                $payroll = $priceProductDao->calcPrice($arr['id_product']);

                if (isset($payroll['info'])) break;

                $payroll = $generalProductsDao->updatePrice($arr['id_product'], $payroll['totalPrice']);

                if ($_SESSION['flag_composite_product'] == '1') {
                    if (isset($payroll['info'])) break;
                    // Calcular costo material porq
                    $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                    foreach ($productsCompositer as $j) {
                        if (isset($payroll['info'])) break;

                        $data = [];
                        $data['idProduct'] = $j['id_product'];
                        $data['compositeProduct'] = $j['id_child_product'];

                        $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                        $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                        if (isset($payroll['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($payroll['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($payroll['info'])) break;

                        $data = $priceProductDao->calcPrice($j['id_product']);
                        $payroll = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                        if (isset($payroll['info'])) break;

                        $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                        foreach ($productsCompositer2 as $k) {
                            if (isset($payroll['info'])) break;

                            $data = [];
                            $data['compositeProduct'] = $k['id_child_product'];
                            $data['idProduct'] = $k['id_product'];

                            $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                            $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                            if (isset($payroll['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($payroll['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($payroll['info'])) break;

                            $data = $priceProductDao->calcPrice($k['id_product']);
                            $payroll = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        }
                    }
                }
            }
        }
        if ($payroll == null)
            $resp = array('success' => true, 'message' => 'Nomina eliminada correctamente');
        else if (isset($payroll['info']))
            $resp = array('info' => true, 'message' => $payroll['message']);
        else
            $resp = array('error' => true, 'message' => 'No es posible eliminar la nomina, existe información asociada a ella');
    } else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la nomina, ultimo empleado de nomina');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
