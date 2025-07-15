<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralFactoryLoadDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findFactoryLoad($dataFactoryLoad)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM manufacturing_load  
                                      WHERE id_machine = :id_machine AND input = :input");
        $stmt->execute([
            'id_machine' => $dataFactoryLoad['idMachine'],
            'input' => strtoupper(trim($dataFactoryLoad['descriptionFactoryLoad']))
        ]);

        $factoryload = $stmt->fetch($connection::FETCH_ASSOC);

        return $factoryload;
    }
}
