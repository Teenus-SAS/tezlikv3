<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceCustomDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcPriceCustom($id_custom_price)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (cp.price * (1 - (pl.percentage / 100))) AS price 
                                      FROM custom_prices cp
                                        INNER JOIN price_list pl ON pl.id_price_list = cp.id_price_list
                                      WHERE cp.id_custom_price = :id_custom_price");
        $stmt->execute(['id_custom_price' => $id_custom_price]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $customPrice = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("customPrice", array('customPrice' => $customPrice));
        return $customPrice;
    }
}
