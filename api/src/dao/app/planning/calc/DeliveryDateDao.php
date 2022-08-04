<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DeliveryDateDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcDeliveryDate($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("");
        $stmt->execute([
            'id_order' => $dataOrder['idOrder'],
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $deliveryDate = $stmt->fecth($connection::FETCH_ASSOC);
        return $deliveryDate;
    }
}
