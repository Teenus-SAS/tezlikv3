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
}
