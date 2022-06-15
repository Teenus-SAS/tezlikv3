<?php

use tezlikv2\dao\PayrollDao;
use tezlikv2\dao\ProcessDao;
use tezlikv2\dao\CostWorkforceDao;
use tezlikv2\dao\PriceProductDao;

$payrollDao = new PayrollDao();
$processDao = new ProcessDao();
$costWorkforceDao = new CostWorkforceDao();
$priceProductDao = new PriceProductDao();

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

$app->post('/payrollDataValidation', function (Request $request, Response $response, $args) use ($payrollDao, $processDao) {
    $dataPayroll = $request->getParsedBody();

    if (isset($dataPayroll)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $payroll = $dataPayroll['importPayroll'];

        for ($i = 0; $i < sizeof($payroll); $i++) {
            // Obtener id proceso
            $findProcess = $processDao->findProcess($payroll[$i], $id_company);
            
            if (!$findProcess) {
                $i = $i + 1;
                $dataImportPayroll = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila {$i}");
                break;
            } else
                $payroll[$i]['idProcess'] = $findProcess['id_process'];

            if (isset($payroll[$i]['employee']))
                $employee = $payroll[$i]['employee'];
            if (isset($payroll[$i]['basicSalary']))
                $basicSalary = $payroll[$i]['basicSalary'];
            if (isset($payroll[$i]['workingDaysMonth']))
                $workingDaysMonth = $payroll[$i]['workingDaysMonth'];
            if (isset($payroll[$i]['workingHoursDay']))
                $workingHoursDay = $payroll[$i]['workingHoursDay'];
            if (isset($payroll[$i]['typeFactor']))
                $typeFactor = $payroll[$i]['typeFactor'];

            if (empty($employee) || empty($basicSalary) || empty($workingDaysMonth) || empty($workingHoursDay) || empty($typeFactor)) {
                $i = $i + 1;
                $dataImportPayroll = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                if ($workingDaysMonth > 31 || $workingHoursDay > 24) {
                    $i = $i + 1;
                    $dataImportPayroll = array('error' => true, 'message' => "El campo dias trabajo x mes debe ser menor a 31 <br>y horas trabajo x dia menor a 24, fila: {$i}");
                    break;
                } else {
                    $findPayroll = $payrollDao->findPayroll($payroll[$i], $id_company);

                    !$findPayroll ? $insert = $insert + 1 : $update = $update + 1;
                    $dataImportPayroll['insert'] = $insert;
                    $dataImportPayroll['update'] = $update;
                }
            }
        }
    } else
        $dataImportPayroll = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportPayroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPayroll', function (Request $request, Response $response) use ($payrollDao, $processDao, $costWorkforceDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    $dataPayrolls = sizeof($dataPayroll);

    if ($dataPayrolls > 1) {

        if ($dataPayroll['workingDaysMonth'] > 31 || $dataPayroll['workingHoursDay'] > 24) {
            $resp = array('error' => true, 'message' => "El campo dias trabajo x mes debe ser menor a 31 <br>y horas trabajo x dia menor a 24");
        } else {
            $payroll = $payrollDao->insertPayrollByCompany($dataPayroll, $id_company);

            if ($payroll == null)
                $resp = array('success' => true, 'message' => 'Nomina creada correctamente');
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        }
    } else {
        $payroll = $dataPayroll['importPayroll'];

        for ($i = 0; $i < sizeof($payroll); $i++) {
            // Obtener idProceso
            $findProcess = $processDao->findProcess($payroll[$i], $id_company);
            $payroll[$i]['idProcess'] = $findProcess['id_process'];

            $findPayroll = $payrollDao->findPayroll($payroll[$i], $id_company);
            if (!$findPayroll)
                $resolution = $payrollDao->insertPayrollByCompany($payroll[$i], $id_company);
            else {
                $payroll[$i]['idPayroll'] = $findPayroll['id_payroll'];
                $resolution = $payrollDao->updatePayroll($payroll[$i]);
                // Calcular costo nomina
                //$costWorkforce = $costWorkforceDao->calcCostPayrollByPayroll($payroll[$i], $id_company);
                // Calcular precio products_costs
                //$priceProduct = $priceProductDao->calcPriceByPayroll($payroll[$i]['idProcess'], $id_company);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Nomina importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePayroll', function (Request $request, Response $response, $args) use ($payrollDao, $costWorkforceDao, $priceProductDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    if (
        empty($dataPayroll['employee']) || empty($dataPayroll['basicSalary']) ||
        empty($dataPayroll['workingDaysMonth']) || empty($dataPayroll['workingHoursDay'])
        || empty($dataPayroll['typeFactor'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        if ($dataPayroll['workingDaysMonth'] > 31 || $dataPayroll['workingHoursDay'] > 24) {
            $resp = array('error' => true, 'message' => "El campo dias trabajo x mes debe ser menor a 31 <br>y horas trabajo x dia menor a 24");
        } else {
            $payroll = $payrollDao->updatePayroll($dataPayroll);

            // Calcular costo nomina
            $costWorkforce = $costWorkforceDao->calcCostPayrollByPayroll($dataPayroll, $id_company);

            // Calcular precio products_costs
            $priceProduct = $priceProductDao->calcPriceByPayroll($dataPayroll['idProcess'], $id_company);

            if ($payroll == null && $costWorkforce == null && $priceProduct == null)
                $resp = array('success' => true, 'message' => 'Nomina actualizada correctamente');
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePayroll/{id_payroll}', function (Request $request, Response $response, $args) use ($payrollDao) {
    $payroll = $payrollDao->deletePayroll($args['id_payroll']);

    if ($payroll == null)
        $resp = array('success' => true, 'message' => 'Nomina eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la nomina, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
