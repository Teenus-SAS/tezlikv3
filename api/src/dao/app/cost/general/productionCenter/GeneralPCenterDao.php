<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPCenterDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPCenter($dataPcenter, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_production_center FROM productions_center
                                  WHERE production_center = :production_center AND id_company = :id_company");
        $stmt->execute([
            'production_center' => strtoupper(trim($dataPcenter['production'])),
            'id_company' => $id_company
        ]);
        $findProcess = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProcess;
    }
}
