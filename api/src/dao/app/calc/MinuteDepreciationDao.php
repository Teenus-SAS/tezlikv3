<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MinuteDepreciationDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcMinuteDepreciationByMachine($nameMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT ((cost - residual_value) / (years_depreciation * 12)) / hours_machine / days_machine / 60 AS minute_depreciation 
                                      FROM `machines` 
                                      WHERE machine = :machine");
        $stmt->execute(['machine' => $nameMachine]);
        $dataMachine = $stmt->fetch($connection::FETCH_ASSOC);

        // Modificar depreciacion por minuto
        $stmt = $connection->prepare("UPDATE machines SET minute_depreciation = :minute_depreciation 
                                      WHERE machine = :machine AND id_company = :id_company");
        $stmt->execute([
            'minute_depreciation' => $dataMachine['minute_depreciation'],
            'machine' => $nameMachine,
            'id_company' => $id_company
        ]);
    }

    public function calcMinuteDepreciationImportedByMachine($machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $minute_depreciation = ($machine['cost'] - $machine['residualValue']) / ($machine['depreciationYears'] * 12) / $machine['daysMachine'] / $machine['hoursMachine'] / 60;

        // Modificar depreciacion por minuto
        $stmt = $connection->prepare("UPDATE machines SET minute_depreciation = :minute_depreciation 
                                      WHERE machine = :machine AND id_company = :id_company");
        $stmt->execute([
            'minute_depreciation' => $minute_depreciation,
            'machine' =>  ucfirst(strtolower($machine['machine'])),
            'id_company' => $id_company
        ]);
    }
}
