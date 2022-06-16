<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Planning_machinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPlanMachines($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_program_machines, m.machine, pm.number_workers, pm.hours_day, pm.hour_start, pm.hour_end, pm.year 
                                      FROM plan_program_machines pm
                                        INNER JOIN machines m ON m.id_machine = pm.id_machine
                                      WHERE pm.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $planningMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planningMachines;
    }
}
