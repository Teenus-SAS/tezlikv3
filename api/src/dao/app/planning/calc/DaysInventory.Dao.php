<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DaysInventoryDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcDaysInventory($dataInventory)
    {
        $connection = Connection::getInstance()->getconnection();

        $stmt = $connection->prepare("SELECT (((:cantProducts/(january + february + march + april + june + july + august + september + november + december)/12)/4)/7) AS daysInventory 
                                      FROM products_price_history
                                      WHERE id_product = :id_product");
        $stmt->execute([
            'cantProducts' => $dataInventory['cantProducts'],
            'id_product' => $dataInventory['idProduct']
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $daysInventory = $stmt->fetch($connection::FETCH__ASSOC);
        return $daysInventory;
    }
}
