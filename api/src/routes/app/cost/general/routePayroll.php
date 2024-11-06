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
use tezlikv3\dao\GeneralProductsProcessDao;
use tezlikv3\dao\LastDataDao;
use tezlikv3\Dao\PriceUSDDao;
use tezlikv3\Dao\RisksDao;
use tezlikv3\dao\ValueMinuteDao;
use tezlikv3\dao\WebTokenDao;

$payrollDao = new PayrollDao();
$generalPayrollDao = new GeneralPayrollDao();
$generalProductsProcessDao = new GeneralProductsProcessDao();
$webTokenDao = new WebTokenDao();
$valueMinuteDao = new ValueMinuteDao();
$convertDataDao = new ConvertDataDao();
$processDao = new GeneralProcessDao();
$costWorkforceDao = new CostWorkforceDao();
$priceProductDao = new PriceProductDao();
$pricesUSDDao = new PriceUSDDao();
$generalProductsDao = new GeneralProductsDao();
$benefitsDao = new BenefitsDao();
$risksDao = new RisksDao();
$factorBenefitDao = new FactorBenefitDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$costMaterialsDao = new CostMaterialsDao();
$costCompositeProductsDao = new CostCompositeProductsDao();
$generalProductProcessDao = new GeneralProductsProcessDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/payroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $payroll = $payrollDao->findAllPayrollByCompany($id_company);
    $response->getBody()->write(json_encode($payroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/basicPayroll', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $payroll = $generalPayrollDao->findDataBasicPayrollByCompany($id_company);
    $response->getBody()->write(json_encode($payroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/salarynet', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $payroll = $generalPayrollDao->findSalarynetByPayroll($id_company);
    $response->getBody()->write(json_encode($payroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/process/{employee}', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $process = $generalPayrollDao->findAllProcessByEmployeeNotIn($args['employee'], $id_company);
    $response->getBody()->write(json_encode($process, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->post('/payrollDataValidation', function (Request $request, Response $response, $args) use (
//     $webTokenDao,
//     $generalPayrollDao,
//     $processDao
// ) {
//     $info = $webTokenDao->getToken();

//     if (!is_object($info) && ($info == 1)) {
//         $response->getBody()->write(json_encode(['error' => 'Unauthenticated request']));
//         return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
//     }

//     if (is_array($info)) {
//         $response->getBody()->write(json_encode(['error' => $info['info']]));
//         // return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
//         return $response->withHeader('Location', '/')->withStatus(302);
//     }

//     $validate = $webTokenDao->validationToken($info);

//     if (!$validate) {
//         $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
//         return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
//     }

//     $dataPayroll = $request->getParsedBody();

//     if (isset($dataPayroll)) {
//         // session_start();
//         $id_company = $_SESSION['id_company'];

//         $insert = 0;
//         $update = 0;

//         $payroll = $dataPayroll['importPayroll'];

//         for ($i = 0; $i < sizeof($payroll); $i++) {
//             if (
//                 empty($payroll[$i]['process']) || empty($payroll[$i]['employee']) || empty($payroll[$i]['basicSalary']) || empty($payroll[$i]['workingDaysMonth']) ||
//                 empty($payroll[$i]['workingHoursDay']) || empty($payroll[$i]['typeFactor']) || empty($payroll[$i]['benefit']) || empty($payroll[$i]['riskLevel'])
//             ) {
//                 $i = $i + 2;
//                 $dataImportPayroll = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
//                 break;
//             }

//             if (
//                 empty(trim($payroll[$i]['process'])) || empty(trim($payroll[$i]['employee'])) || empty(trim($payroll[$i]['basicSalary'])) || empty(trim($payroll[$i]['workingDaysMonth'])) ||
//                 empty(trim($payroll[$i]['workingHoursDay'])) || empty(trim($payroll[$i]['typeFactor'])) || empty(trim($payroll[$i]['benefit'])) || empty(trim($payroll[$i]['riskLevel']))
//             ) {
//                 $i = $i + 2;
//                 $dataImportPayroll = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
//                 break;
//             }

//             if ($payroll[$i]['workingDaysMonth'] > 31 || $payroll[$i]['workingHoursDay'] > 24) {
//                 $i = $i + 2;
//                 $dataImportPayroll = array('error' => true, 'message' => "El campo dias trabajo x mes debe ser menor a 31 <br>y horas trabajo x dia menor a 24, fila: {$i}");
//                 break;
//             }

//             // Obtener id proceso
//             $findProcess = $processDao->findProcess($payroll[$i], $id_company);

//             if (!$findProcess) {
//                 $i = $i + 2;
//                 $dataImportPayroll = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila {$i}");
//                 break;
//             } else
//                 $payroll[$i]['idProcess'] = $findProcess['id_process'];

//             $findPayroll = $generalPayrollDao->findPayroll($payroll[$i], $id_company);

//             !$findPayroll ? $insert = $insert + 1 : $update = $update + 1;
//             $dataImportPayroll['insert'] = $insert;
//             $dataImportPayroll['update'] = $update;
//         }
//     } else
//         $dataImportPayroll = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

//     $response->getBody()->write(json_encode($dataImportPayroll, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->post('/addPayroll', function (Request $request, Response $response) use (
    $payrollDao,
    $webTokenDao,
    $generalPayrollDao,
    $lastDataDao,
    $convertDataDao,
    $valueMinuteDao,
    $processDao,
    $costWorkforceDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $benefitsDao,
    $risksDao,
    $factorBenefitDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataPayroll = $request->getParsedBody();

    $dataPayrolls = sizeof($dataPayroll);

    if ($dataPayrolls > 1) {

        $payroll = $generalPayrollDao->findPayroll($dataPayroll, $id_company);

        if (!$payroll) {
            // $dataPayroll = $convertDataDao->strReplacePayroll($dataPayroll);

            // Calcular factor benefico
            $dataBenefits = $benefitsDao->findAllBenefits();

            $dataPayroll = $factorBenefitDao->calcFactorBenefit($dataBenefits, $dataPayroll);

            // Calcular Valor x minuto
            $dataPayroll = $valueMinuteDao->calculateValueMinute($dataPayroll);

            $payroll = $payrollDao->insertPayrollByCompany($dataPayroll, $id_company);

            if ($payroll == null) {
                $lastInserted = $lastDataDao->findLastInsertedPayroll($id_company);

                $lastRoute = $generalPayrollDao->findNextRoute($id_company);

                $payroll = $generalPayrollDao->changeRouteById($lastInserted['id_payroll'], $lastRoute['route']);
            }

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

            // // Obtener data segun el nivel de riesgo
            // $dataRisk = $risksDao->findRiskByName($payroll[$i]);
            // $payroll[$i]['valueRisk'] = $dataRisk['percentage'];
            // $payroll[$i]['risk'] = $dataRisk['id_risk'];

            // Verificar salario
            $payroll[$i]['benefit'] == 'SI' ? $payroll[$i]['salary'] = $payroll[$i]['basicSalary'] + $payroll[$i]['bonification'] :
                $payroll[$i]['salary'] = $payroll[$i]['basicSalary'];

            // Calcular Factor Prestacional
            $payroll[$i] = $factorBenefitDao->calcFactorBenefit($dataBenefits, $payroll[$i]);

            $payroll[$i] = $valueMinuteDao->calculateValueMinute($payroll[$i]);

            // Obtener idProceso
            // $findProcess = $processDao->findProcess($payroll[$i], $id_company);
            // $payroll[$i]['idProcess'] = $findProcess['id_process'];

            $findPayroll = $generalPayrollDao->findPayroll($payroll[$i], $id_company);

            if (!$findPayroll) {
                $resolution = $payrollDao->insertPayrollByCompany($payroll[$i], $id_company);

                if (isset($resolution['info'])) break;

                $lastInserted = $lastDataDao->findLastInsertedPayroll($id_company);

                $lastRoute = $generalPayrollDao->findNextRoute($id_company);

                $resolution = $generalPayrollDao->changeRouteById($lastInserted['id_payroll'], $lastRoute['route']);
            } else {
                $payroll[$i]['idPayroll'] = $findPayroll['id_payroll'];
                $resolution = $payrollDao->updatePayroll($payroll[$i]);

                if ($resolution != null) break;
                $dataProducts = $costWorkforceDao->findProductByProcess($payroll[$i]['idProcess'], $id_company);

                foreach ($dataProducts as $arr) {
                    /*
                        // Calcular costo nomina
                        if ($_SESSION['inyection'])
                            $resolution = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                        else
                            $resolution = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);

                        if (isset($resolution['info'])) break;

                        // Calcular costo nomina total  
                        $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

                        $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
                    */
                    if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0) {
                        if ($_SESSION['inyection'] == 1)
                            $request = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                        else
                            $request = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
                    } else {
                        if ($_SESSION['inyection'] == 1)
                            $resolution = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($arr['id_product'], $arr['employee']);
                        else {
                            $resolution = $costWorkforceDao->calcCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                        }
                    }
                    // Calcular costo nomina total
                    if ($resolution == null) {
                        // if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0)
                        $dataPayroll = $costWorkforceDao->sumTotalCostPayroll($arr['id_product'], $id_company);
                        // else {
                        //     // $employees = implode(',', $dataProductProcess['employees']);
                        //     $dataPayroll = $costWorkforceDao->calcTotalCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                        // }

                        $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
                    }

                    if (isset($resolution['info'])) break;

                    // Calcular precio products_costs
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

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($resolution['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer as $j) {
                            if (isset($resolution['info'])) break;

                            $data = [];
                            $data['idProduct'] = $j['id_product'];
                            $data['compositeProduct'] = $j['id_child_product'];

                            // Calcular costo nomina total
                            // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                            // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                            // if (isset($resolution['info'])) break;

                            // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
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
                            if (isset($resolution['info'])) break;

                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($resolution['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                // Calcular costo nomina total
                                // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                                // if (isset($resolution['info'])) break;

                                // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                                // $resolution = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                                // if (isset($resolution['info'])) break;

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $resolution = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($resolution['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $resolution = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($resolution['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);

                                if (isset($data['totalPrice']))
                                    $resolution = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                                if (isset($resolution['info'])) break;
                                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                                    $l = [];
                                    $l['price'] = $data['totalPrice'];
                                    $l['sale_price'] = $data['sale_price'];
                                    $l['id_product'] = $k['id_product'];

                                    $resolution = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                                }
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
    $webTokenDao,
    $generalPayrollDao,
    $convertDataDao,
    $valueMinuteDao,
    $costWorkforceDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $benefitsDao,
    $factorBenefitDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $type_payroll = $_SESSION['type_payroll'];
    $dataPayroll = $request->getParsedBody();

    $payroll = $generalPayrollDao->findPayroll($dataPayroll, $id_company);

    !is_array($payroll) ? $data['id_payroll'] = 0 : $data = $payroll;

    if ($data['id_payroll'] == $dataPayroll['idPayroll'] || $data['id_payroll'] == 0) {
        if ($type_payroll == 1) {
            // $dataPayroll = $convertDataDao->strReplacePayroll($dataPayroll);

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
                    /*
                        // Calcular costo nomina
                        if ($_SESSION['inyection'])
                            $payroll = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                        else
                            $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
                        if (isset($payroll['info'])) break;

                        // Calcular costo nomina total 
                        $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

                        $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
                    */
                    if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0) {
                        if ($_SESSION['inyection'] == 1)
                            $payroll = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                        else
                            $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
                    } else {
                        if ($_SESSION['inyection'] == 1)
                            $payroll = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($arr['id_product'], $arr['employee']);
                        else {
                            $payroll = $costWorkforceDao->calcCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                        }
                    }
                    // Calcular costo nomina total
                    if ($payroll == null) {
                        // if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0)
                        $dataPayroll = $costWorkforceDao->sumTotalCostPayroll($arr['id_product'], $id_company);
                        // else {
                        //     // $employees = implode(',', $dataProductProcess['employees']);
                        //     $dataPayroll = $costWorkforceDao->calcTotalCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                        // }

                        $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
                    }
                    if (isset($payroll['info'])) break;

                    $data = [];
                    // Calcular precio products_costs
                    $data = $priceProductDao->calcPrice($arr['id_product']);

                    if (isset($data['totalPrice']))
                        $payroll = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);
                    if (isset($payroll['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $arr['id_product'];

                        $payroll = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }

                    if ($_SESSION['flag_composite_product'] == '1') {
                        if (isset($payroll['info'])) break;
                        // Calcular costo material porq
                        $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                        foreach ($productsCompositer as $j) {
                            if (isset($payroll['info'])) break;

                            $data = [];
                            $data['idProduct'] = $j['id_product'];
                            $data['compositeProduct'] = $j['id_child_product'];

                            // Calcular costo nomina total
                            // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                            // $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                            // if (isset($payroll['info'])) break;

                            // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                            // $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                            // if (isset($payroll['info'])) break;

                            $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                            $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                            if (isset($payroll['info'])) break;
                            $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                            $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                            if (isset($payroll['info'])) break;

                            $data = $priceProductDao->calcPrice($j['id_product']);

                            if (isset($data['totalPrice']))
                                $payroll = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                            if (isset($payroll['info'])) break;

                            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                                $k = [];
                                $k['price'] = $data['totalPrice'];
                                $k['sale_price'] = $data['sale_price'];
                                $k['id_product'] = $j['id_product'];

                                $payroll = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                            }

                            if (isset($payroll['info'])) break;
                            $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                            foreach ($productsCompositer2 as $k) {
                                if (isset($payroll['info'])) break;

                                $data = [];
                                $data['compositeProduct'] = $k['id_child_product'];
                                $data['idProduct'] = $k['id_product'];

                                // Calcular costo nomina total
                                // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                                // $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                                // if (isset($payroll['info'])) break;

                                // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                                // $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                                // if (isset($payroll['info'])) break;

                                $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                                $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                                if (isset($payroll['info'])) break;
                                $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                                $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                                if (isset($payroll['info'])) break;

                                $data = $priceProductDao->calcPrice($k['id_product']);

                                if (isset($data['totalPrice']))
                                    $payroll = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                                if (isset($payroll['info'])) break;
                                if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                                    $l = [];
                                    $l['price'] = $data['totalPrice'];
                                    $l['sale_price'] = $data['sale_price'];
                                    $l['id_product'] = $k['id_product'];

                                    $payroll = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // $dataPayroll = $convertDataDao->strReplacePayroll($dataPayroll);
            $payroll = $generalPayrollDao->updatePayroll($dataPayroll);
        }

        if ($payroll == null)
            $resp = array(
                'success' => true,
                'message' => 'Nomina actualizada correctamente'
                // 'basicSalary' => $dataPayroll2['basicSalary'],
                // 'salary' => $dataPayroll2['salary'],
                // 'salaryNet' => $dataPayroll2['salaryNet']
            );
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
    $webTokenDao,
    $lastDataDao,
    $generalPayrollDao,
    $costWorkforceDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataPayroll = $request->getParsedBody();

    $payroll = $payrollDao->insertPayrollByCompany($dataPayroll, $id_company);

    // Ingresar orden de insersion de acuerdo a la ultima ruta insertada
    if ($payroll == null) {
        $lastInserted = $lastDataDao->findLastInsertedPayroll($id_company);

        $lastRoute = $generalPayrollDao->findNextRoute($id_company);

        $payroll = $generalPayrollDao->changeRouteById($lastInserted['id_payroll'], $lastRoute['route']);
    }

    if ($payroll == null) {
        $dataProducts = $costWorkforceDao->findProductByProcess($dataPayroll['idProcess'], $id_company);

        foreach ($dataProducts as $arr) {
            if (isset($payroll['info'])) break;
            /*
                // Calcular costo nomina
                if ($_SESSION['inyection'] == 1)
                    $payroll = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                else
                    $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);

                if (isset($payroll['info'])) break;

                // Calcular costo nomina total
                $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($arr['id_product'], $id_company);

                $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
            */
            if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0) {
                if ($_SESSION['inyection'] == 1)
                    $payroll = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                else
                    $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
            } else {
                if ($_SESSION['inyection'] == 1)
                    $payroll = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($arr['id_product'], $arr['employee']);
                else {
                    $payroll = $costWorkforceDao->calcCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                }
            }
            // Calcular costo nomina total
            if ($payroll == null) {
                // if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0)
                $dataPayroll = $costWorkforceDao->sumTotalCostPayroll($arr['id_product'], $id_company);
                // else {
                //     // $employees = implode(',', $dataProductProcess['employees']);
                //     $dataPayroll = $costWorkforceDao->calcTotalCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                // }

                $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
            }
            if (isset($payroll['info'])) break;
            $data = [];
            // Calcular precio products_costs
            $data = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($data['totalPrice']))
                $payroll = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

            if (isset($payroll['info'])) break;

            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $arr['id_product'];

                $payroll = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if (isset($payroll['info'])) break;

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($payroll['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer as $j) {
                    if (isset($payroll['info'])) break;

                    $data = [];
                    $data['idProduct'] = $j['id_product'];
                    $data['compositeProduct'] = $j['id_child_product'];

                    // Calcular costo nomina total
                    $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                    $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                    if (isset($payroll['info'])) break;

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

                    if (isset($data['totalPrice']))
                        $payroll = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($payroll['info'])) break;

                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $payroll = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }

                    if (isset($payroll['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $k) {
                        if (isset($payroll['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $k['id_child_product'];
                        $data['idProduct'] = $k['id_product'];

                        // Calcular costo nomina total
                        $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                        $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                        if (isset($payroll['info'])) break;

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

                        if (isset($data['totalPrice']))
                            $payroll = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        if (isset($payroll['info'])) break;
                        if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                            $l = [];
                            $l['price'] = $data['totalPrice'];
                            $l['sale_price'] = $data['sale_price'];
                            $l['id_product'] = $k['id_product'];

                            $payroll = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                        }
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

$app->post('/saveRoutePayroll', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $dataPayroll = $request->getParsedBody();

    $payroll = $dataPayroll['data'];

    $resolution = null;

    for ($i = 0; $i < sizeof($payroll); $i++) {
        $resolution = $generalPayrollDao->changeRouteById($payroll[$i]['id_payroll'], $payroll[$i]['route']);

        if (isset($resolution['info'])) break;
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Filas modificadas correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deletePayroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $webTokenDao,
    $generalPayrollDao,
    $generalProductsProcessDao,
    $costWorkforceDao,
    $priceProductDao,
    $pricesUSDDao,
    $generalProductsDao,
    $generalCompositeProductsDao,
    $costMaterialsDao,
    $costCompositeProductsDao,
    $generalProductProcessDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];
    $coverage_usd = $_SESSION['coverage_usd'];
    $dataPayroll = $request->getParsedBody();
    $payroll = null;

    $productProcess = explode(',', $dataPayroll['id_product_process']);

    if ($productProcess[0] != 0) {
        for ($i = 0; $i < sizeof($productProcess); $i++) {
            if (isset($payroll['info'])) break;

            $arr = $generalProductProcessDao->findProductProcessById($productProcess[$i]);

            $employees = explode(',', $arr['employee']);

            // Eliminar el valor de $id del array $arr
            $employees = array_diff($employees, [$dataPayroll['idPayroll']]);

            // Reindexar el array para evitar índices desordenados
            $employees = array_values($employees);

            // Convertir a string
            $employees = implode(',', $employees);

            // Guardar nuevo cambio
            $payroll = $generalProductsProcessDao->updateEmployees($arr['id_product_process'], $employees);
        }
    }

    if ($payroll == null) {
        $payroll = $payrollDao->deletePayroll($dataPayroll['idPayroll']);
    }

    if ($payroll == null) {

        $dataProducts = $costWorkforceDao->findProductByProcess($dataPayroll['idProcess'], $id_company);

        foreach ($dataProducts as $arr) {
            if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0) {
                if ($_SESSION['inyection'] == 1)
                    $payroll = $costWorkforceDao->calcCostPayrollInyection($arr['id_product'], $id_company);
                else
                    $payroll = $costWorkforceDao->calcCostPayroll($arr['id_product'], $id_company);
            } else {
                if ($_SESSION['inyection'] == 1)
                    $payroll = $costWorkforceDao->calcCostPayrollInyectionGroupEmployee($arr['id_product'], $arr['employee']);
                else {
                    $payroll = $costWorkforceDao->calcCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                }
            }
            // Calcular costo nomina total
            if ($payroll == null) {
                // if ($arr['employee'] == '' || $_SESSION['flag_employee'] == 0)
                $dataPayroll = $costWorkforceDao->sumTotalCostPayroll($arr['id_product'], $id_company);
                // else {
                //     // $employees = implode(',', $dataProductProcess['employees']);
                //     $dataPayroll = $costWorkforceDao->calcTotalCostPayrollGroupByEmployee($arr['id_product'], $id_company, $arr['employee']);
                // }

                $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $arr['id_product'], $id_company);
            }

            // if ($arr['employee'] != '') {
            //     $payroll = $generalProductProcessDao->updateEmployees($arr['id_product_process'], '');
            // }

            if (isset($payroll['info'])) break;
            $data = [];
            // Calcular precio products_costs
            $data = $priceProductDao->calcPrice($arr['id_product']);

            if (isset($data['totalPrice']))
                $payroll = $generalProductsDao->updatePrice($arr['id_product'], $data['totalPrice']);

            if (isset($payroll['info'])) break;
            if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                $k = [];
                $k['price'] = $data['totalPrice'];
                $k['sale_price'] = $data['sale_price'];
                $k['id_product'] = $arr['id_product'];

                $payroll = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
            }

            if (isset($payroll['info'])) break;

            if ($_SESSION['flag_composite_product'] == '1') {
                if (isset($payroll['info'])) break;
                // Calcular costo material porq
                $productsCompositer = $generalCompositeProductsDao->findCompositeProductByChild($arr['id_product']);

                foreach ($productsCompositer as $j) {
                    if (isset($payroll['info'])) break;

                    $data = [];
                    $data['idProduct'] = $j['id_product'];
                    $data['compositeProduct'] = $j['id_child_product'];

                    // Calcular costo nomina total
                    // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                    // $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                    // if (isset($payroll['info'])) break;

                    // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);
                    // $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                    // if (isset($payroll['info'])) break;

                    $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                    $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                    if (isset($payroll['info'])) break;
                    $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                    $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                    if (isset($payroll['info'])) break;

                    $data = $priceProductDao->calcPrice($j['id_product']);

                    if (isset($data['totalPrice']))
                        $payroll = $generalProductsDao->updatePrice($j['id_product'], $data['totalPrice']);

                    if (isset($payroll['info'])) break;
                    if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                        $k = [];
                        $k['price'] = $data['totalPrice'];
                        $k['sale_price'] = $data['sale_price'];
                        $k['id_product'] = $j['id_product'];

                        $payroll = $pricesUSDDao->calcPriceUSDandModify($k, $coverage_usd);
                    }
                    if (isset($payroll['info'])) break;

                    $productsCompositer2 = $generalCompositeProductsDao->findCompositeProductByChild($j['id_product']);

                    foreach ($productsCompositer2 as $k) {
                        if (isset($payroll['info'])) break;

                        $data = [];
                        $data['compositeProduct'] = $k['id_child_product'];
                        $data['idProduct'] = $k['id_product'];

                        // Calcular costo nomina total
                        // $dataPayroll = $costWorkforceDao->calcTotalCostPayroll($data['idProduct'], $id_company);

                        // $payroll = $costWorkforceDao->updateTotalCostWorkforce($dataPayroll['cost'], $data['idProduct'], $id_company);

                        // if (isset($payroll['info'])) break;

                        // $data = $costCompositeProductsDao->calcCostCompositeProduct($data);

                        // $payroll = $costWorkforceDao->updateTotalCostWorkforce($data['workforce_cost'], $data['idProduct'], $id_company);
                        // if (isset($payroll['info'])) break;

                        $data = $generalCompositeProductsDao->findCostMaterialByCompositeProduct($data);
                        $payroll = $generalCompositeProductsDao->updateCostCompositeProduct($data);

                        if (isset($payroll['info'])) break;
                        $data = $costMaterialsDao->calcCostMaterialByCompositeProduct($data);
                        $payroll = $costMaterialsDao->updateCostMaterials($data, $id_company);

                        if (isset($payroll['info'])) break;

                        $data = $priceProductDao->calcPrice($k['id_product']);

                        if (isset($data['totalPrice']))
                            $payroll = $generalProductsDao->updatePrice($k['id_product'], $data['totalPrice']);
                        if (isset($payroll['info'])) break;
                        if ($_SESSION['flag_currency_usd'] == '1') { // Convertir a Dolares 
                            $l = [];
                            $l['price'] = $data['totalPrice'];
                            $l['sale_price'] = $data['sale_price'];
                            $l['id_product'] = $k['id_product'];

                            $payroll = $pricesUSDDao->calcPriceUSDandModify($l, $coverage_usd);
                        }
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
    // } else
    //     $resp = array('error' => true, 'message' => 'No es posible eliminar la nomina, ultimo empleado de nomina');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/checkEmployee/{employee}', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $webTokenDao
) {
    $info = $webTokenDao->getToken();

    if (!is_object($info) && ($info == 1)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthenticated request']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    if (is_array($info)) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => $info['info']]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $validate = $webTokenDao->validationToken($info);

    if (!$validate) {
        $response->getBody()->write(json_encode(['reload' => true, 'error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // session_start();
    $id_company = $_SESSION['id_company'];

    $payroll = $generalPayrollDao->findAllPayrollByEmployee($args['employee'], $id_company);
    $response->getBody()->write(json_encode($payroll));
    return $response->withHeader('Content-Type', 'application/json');
});
