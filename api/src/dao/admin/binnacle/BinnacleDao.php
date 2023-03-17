<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class BinnacleDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllBinnacle()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM binnacle");
        $stmt->execute();

        $binnacle = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $binnacle;
    }
}
