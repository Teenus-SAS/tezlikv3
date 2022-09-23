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

        $costFactory = str_replace('.', '', $dataFactoryLoad['costFactory']);

        $stmt = $connection->prepare("SELECT (ml.cost / m.days_machine / m.hours_machine /60) AS costMinute FROM machines m 
                                      INNER JOIN manufacturing_load ml ON ml.id_machine = m.id_machine
                                      WHERE m.id_machine = :id_machine AND ml.input = :input AND 
                                      ml.cost = :cost AND ml.id_company = :id_company");
        $stmt->execute([
            'id_machine' => $dataFactoryLoad['idMachine'],
            'input' => $dataFactoryLoad['descriptionFactoryLoad'],
            'cost' => $costFactory,
            'id_company' => $id_company
        ]);
        $dataCostMinute = $stmt->fetch($connection::FETCH_ASSOC);

        if ($dataCostMinute['costMinute'] == null)
            return 1;
        else {
            // Actualizar cost_minute
            $stmt = $connection->prepare("UPDATE manufacturing_load SET cost_minute = :cost_minute 
                                      WHERE id_machine = :id_machine AND input = :input AND 
                                      cost = :cost AND id_company = :id_company");
            $stmt->execute([
                'cost_minute' => $dataCostMinute['costMinute'],
                'id_machine' => $dataFactoryLoad['idMachine'],
                'input' => $dataFactoryLoad['descriptionFactoryLoad'],
                'cost' => $costFactory,
                'id_company' => $id_company
            ]);
        }
    }
}
