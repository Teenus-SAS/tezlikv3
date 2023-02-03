<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ValueMinuteDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calculateValueMinute($dataPayroll, $dataReplace)
    {
        /* Calcular salario neto */
        $salaryNet = ((intval($dataReplace['basicSalary']) + $dataReplace['transport']) * (1 + floatval($dataPayroll['factor']) / 100)) + $dataReplace['bonification'] + $dataReplace['endowment'];

        /* Total horas */
        $totalHoursMonth = floatval($dataPayroll['workingDaysMonth']) * floatval($dataPayroll['workingHoursDay']);
        $hourCost = $salaryNet / $totalHoursMonth;

        /* Calcular valor minuto salario */
        $minuteValue =  $hourCost / 60;

        /* retorna los valores calculados */
        $payrollCalculate = array('salaryNet' => $salaryNet, 'minuteValue' => $minuteValue);
        return $payrollCalculate;
    }
}
