<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralMachinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findDataBasicMachinesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT m.id_machine, m.machine, m.unity_time
                                      FROM machines m
                                      WHERE id_company = :id_company 
                                      ORDER BY machine ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("machines", array('machines' => $machines));
        return $machines;
    }

    /* Buscar si existe maquina en la BD */
    public function findMachine($dataMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_machine FROM machines
                                 WHERE machine = :machine AND id_company = :id_company");
        $stmt->execute([
            'machine' => strtoupper(trim($dataMachine['machine'])),
            'id_company' => $id_company
        ]);
        $findMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMachine;
    }
}
