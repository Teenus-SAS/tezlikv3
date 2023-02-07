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
        return $findMachine;
    }

    public function deleteMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM machines WHERE id_machine = :id_machine");
            $stmt->execute(['id_machine' => $id_machine]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM machines WHERE id_machine = :id_machine");
                $stmt->execute(['id_machine' => $id_machine]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Maquina asociada a un proceso. No es posible eliminar';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
