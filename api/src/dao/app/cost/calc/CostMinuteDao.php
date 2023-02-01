<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostMinuteDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcCostMinuteByFactoryLoad($dataFactoryLoad, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT (ml.cost / m.days_machine / m.hours_machine /60) AS costMinute FROM machines m 
                                      INNER JOIN manufacturing_load ml ON ml.id_machine = m.id_machine
                                      WHERE ml.id_manufacturing_load = :id_manufacturing_load AND m.id_company = :id_company");
            $stmt->execute([
                'id_manufacturing_load' => $dataFactoryLoad['idManufacturingLoad'],
                'id_company' => $id_company
            ]);
            $dataCostMinute = $stmt->fetch($connection::FETCH_ASSOC);

            if (!isset($dataCostMinute['costMinute']))
                return 1;
            else {
                // Actualizar cost_minute
                $stmt = $connection->prepare("UPDATE manufacturing_load SET cost_minute = :cost_minute 
                                            WHERE id_manufacturing_load = :id_manufacturing_load AND id_company = :id_company");
                $stmt->execute([
                    'cost_minute' => $dataCostMinute['costMinute'],
                    'id_manufacturing_load' => $dataFactoryLoad['idManufacturingLoad'],
                    'id_company' => $id_company
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
    }
}
