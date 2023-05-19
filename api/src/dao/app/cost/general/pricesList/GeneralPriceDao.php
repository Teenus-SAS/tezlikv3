<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPricesListDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPricesList($dataPrice, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM price_list WHERE price_name = :price_name AND id_company ");
        $stmt->execute(['price_name' => trim($dataPrice['namePrice'])]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $pricesList = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pricesList", array('pricesList' => $pricesList));
        return $pricesList;
    }
}
