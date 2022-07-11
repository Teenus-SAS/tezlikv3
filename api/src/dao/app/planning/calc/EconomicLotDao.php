<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class EconomicLotDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcEconomicLot($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT ((:assembly_production - (:assembly_time / 60)) * cicles_hour) AS economic_lot
                                      FROM plan_cicles_machine 
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'assembly_production' => $dataProduct['assemblyProduction'],
            'assembly_time' => $dataProduct['assemblyTime'],
            'id_product' => $dataProduct['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $economicLot = $stmt->fetch($connection::FETCH_ASSOC);
        return $economicLot;
    }
}
