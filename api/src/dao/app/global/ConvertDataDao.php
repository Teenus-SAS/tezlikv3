<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConvertDataDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Maquinas */
    public function strReplaceMachines($dataMachine)
    {
        $dataMachine['costMachine'] = str_replace('.', '', $dataMachine['cost']);
        $dataMachine['costMachine'] = str_replace(',', '.', $dataMachine['costMachine']);
        $dataMachine['residualValue'] = str_replace('.', '', $dataMachine['residualValue']);
        $dataMachine['residualValue'] = str_replace(',', '.', $dataMachine['residualValue']);
        $dataMachine['depreciationYears'] = str_replace(',', '.', $dataMachine['depreciationYears']);
        $dataMachine['hoursMachine'] = str_replace(',', '.', $dataMachine['hoursMachine']);
        $dataMachine['daysMachine'] = str_replace(',', '.', $dataMachine['daysMachine']);

        return $dataMachine;
    }

    /* Productos Materias */
    public function strReplaceProductsMaterials($dataProductMaterial)
    {
        $dataProductMaterial['quantity'] = str_replace('.', '', $dataProductMaterial['quantity']);
        $dataProductMaterial['quantity'] = str_replace(',', '.', $dataProductMaterial['quantity']);

        return $dataProductMaterial;
    }

    /* Productos Procesos */
    public function strReplaceProductsProcess($dataProductProcess)
    {
        $dataProductProcess['enlistmentTime'] = str_replace('.', '', $dataProductProcess['enlistmentTime']);
        $dataProductProcess['enlistmentTime'] = str_replace(',', '.', $dataProductProcess['enlistmentTime']);
        $dataProductProcess['operationTime'] = str_replace('.', '', $dataProductProcess['operationTime']);
        $dataProductProcess['operationTime'] = str_replace(',', '.', $dataProductProcess['operationTime']);

        return $dataProductProcess;
    }

    /* Nomina */
    public function strReplacePayroll($dataPayroll)
    {
        $salaryBasic = str_replace('.', '', $dataPayroll['basicSalary']);
        $transport = str_replace('.', '', $dataPayroll['transport']);
        $bonification = str_replace('.', '', $dataPayroll['bonification']);
        $extraTime = str_replace('.', '', $dataPayroll['extraTime']);
        $endowment = str_replace('.', '', $dataPayroll['endowment']);
        $factor = str_replace('.', '', $dataPayroll['factor']);
        $factor = str_replace(',', '.', $dataPayroll['factor']);

        $transport == '' ? $transport = 0 : $transport;
        $bonification == '' ? $bonification = 0 : $bonification;
        $extraTime == '' ? $extraTime = 0 : $extraTime;
        $endowment == '' ? $endowment = 0 : $endowment;
        $factor == '' ? $factor = 0 : $factor;

        $dataPayroll['basicSalary']  = $salaryBasic;
        $dataPayroll['transport'] = $transport;
        $dataPayroll['bonification'] = $bonification;
        $dataPayroll['extraTime'] = $extraTime;
        $dataPayroll['endowment'] = $endowment;
        $dataPayroll['factor'] = $factor;

        return $dataPayroll;
    }

    /* Cotizacion */
    public function strReplaceQuotes($dataQuote)
    {
        $dataQuote['quantity'] = str_replace('.', '', $dataQuote['quantity']);

        $price = str_replace('$ ', '', $dataQuote['price']);
        $price = str_replace('.', '', $price);
        $dataQuote['price'] = str_replace(',', '.', $price);

        return $dataQuote;
    }

    /* Moldes 
    public function strReplaceMold($dataMold)
    {
        $dataMold['assemblyTime'] = str_replace('.', '', $dataMold['assemblyTime']);
        $dataMold['assemblyTime'] = str_replace(',', '.', $dataMold['assemblyTime']);
        $dataMold['assemblyProduction'] = str_replace('.', '', $dataMold['assemblyProduction']);
        $dataMold['assemblyProduction'] = str_replace(',', '.', $dataMold['assemblyProduction']);
        $dataMold['cavity'] = str_replace('.', '', $dataMold['cavity']);
        $dataMold['cavity'] = str_replace(',', '.', $dataMold['cavity']);
        $dataMold['cavityAvailable'] = str_replace('.', '', $dataMold['cavityAvailable']);
        $dataMold['cavityAvailable'] = str_replace(',', '.', $dataMold['cavityAvailable']);

        return $dataMold;
    }

    // Pedidos 
    public function changeDateOrder($dataOrder)
    {
        $date = str_replace('/', '-', $dataOrder['dateOrder']);
        $minDate = str_replace('/', '-', $dataOrder['minDate']);
        $maxDate = str_replace('/', '-', $dataOrder['maxDate']);
        $dataOrder['dateOrder'] = date('Y-m-d', strtotime($date));
        $dataOrder['minDate'] = date('Y-m-d', strtotime($minDate));
        $dataOrder['maxDate'] = date('Y-m-d', strtotime($maxDate));

        return $dataOrder;
    } */
}
