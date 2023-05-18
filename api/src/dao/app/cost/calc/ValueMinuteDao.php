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

    public function calcFactorBenefit($dataPayroll)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function calculateValueMinuteByTypePayroll($dataPayroll)
    {
        /* Calcular salario neto */
        $salaryNet = (intval($dataPayroll['basicSalary']) + $dataPayroll['transport']) + floatval($dataPayroll['factor']) + $dataPayroll['bonification'] + $dataPayroll['endowment'];

        /* Total horas */
        $totalHoursMonth = floatval($dataPayroll['workingDaysMonth']) * floatval($dataPayroll['workingHoursDay']);
        $hourCost = $salaryNet / $totalHoursMonth;

        /* Calcular valor minuto salario */
        $minuteValue =  $hourCost / 60;

        /* retorna los valores calculados */
        $dataPayroll['salaryNet'] = $salaryNet;
        $dataPayroll['minuteValue'] = $minuteValue;
        return $dataPayroll;
    }

    public function calculateValueMinute($dataPayroll)
    {
        /* Calcular salario neto */
        $salaryNet = ((intval($dataPayroll['basicSalary']) + $dataPayroll['transport']) * (1 + floatval($dataPayroll['factor']) / 100)) + $dataPayroll['bonification'] + $dataPayroll['endowment'];

        /* Total horas */
        $totalHoursMonth = floatval($dataPayroll['workingDaysMonth']) * floatval($dataPayroll['workingHoursDay']);
        $hourCost = $salaryNet / $totalHoursMonth;

        /* Calcular valor minuto salario */
        $minuteValue =  $hourCost / 60;

        /* retorna los valores calculados */
        $dataPayroll['salaryNet'] = $salaryNet;
        $dataPayroll['minuteValue'] = $minuteValue;
        return $dataPayroll;
    }
}
