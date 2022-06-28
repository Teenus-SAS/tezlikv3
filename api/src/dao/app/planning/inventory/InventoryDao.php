<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class InventoryDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllInventory($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("");
        $stmt->execute(['id_company' => $id_company]);
        $inventory = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $inventory;
    }
}
