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

        !is_array($findMachine) ? $findMachine['id_machine'] = 0 : $findMachine;

        return $findMachine;
    }
}
