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

    /* Cotizacion */
    public function convertDataPayroll($dataPayroll)
    {
        $salaryBasic = str_replace('.', '', $dataPayroll['basicSalary']);
        $transport = str_replace('.', '', $dataPayroll['transport']);
        $bonification = str_replace('.', '', $dataPayroll['bonification']);
        $extraTime = str_replace('.', '', $dataPayroll['extraTime']);
        $endowment = str_replace('.', '', $dataPayroll['endowment']);

        $transport == '' ? $transport = 0 : $transport;
        $bonification == '' ? $bonification = 0 : $bonification;
        $extraTime == '' ? $extraTime = 0 : $extraTime;
        $endowment == '' ? $endowment = 0 : $endowment;

        $dataReplace['basicSalary']  = $salaryBasic;
        $dataReplace['transport'] = $transport;
        $dataReplace['bonification'] = $bonification;
        $dataReplace['extraTime'] = $extraTime;
        $dataReplace['endowment'] = $endowment;

        return $dataReplace;
    }


    /* Cotizacion */
    public function convertDataQuotes($dataQuote)
    {
        $dataQuote['quantity'] = str_replace('.', '', $dataQuote['quantity']);

        $price = str_replace('$ ', '', $dataQuote['price']);
        $price = str_replace('.', '', $price);
        $dataQuote['price'] = str_replace(',', '.', $price);

        return $dataQuote;
    }
}
