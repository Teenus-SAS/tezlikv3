<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class TpInyectionDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Buscar si existe maquina en la BD */
    public function calcUnityTime($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (cicles_machine / cavities) AS unityTime
                                      FROM machines
                                      WHERE id_machine = :id_machine");
        $stmt->execute(['id_machine' => $id_machine]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function updateInyection($dataMachine)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE machines SET cicles_machine = :cicles_machine, cavities = :cavities WHERE id_machine = :id_machine");
            $stmt->execute([
                'cicles_machine' => $dataMachine['ciclesMachine'],
                'cavities' => $dataMachine['cavities'],
                'id_machine' => $dataMachine['idMachine']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateUnityTime($id_machine, $machine)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE machines SET unity_time = :unity_time WHERE id_machine = :id_machine");
            $stmt->execute([
                'unity_time' => $machine['unityTime'],
                'id_machine' => $id_machine
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
