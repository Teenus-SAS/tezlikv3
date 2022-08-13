<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConsolidatedDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findConsolidated($id_company)
    {
        $connection = Connection::getInstance()->getconnection();

        $stmt = $connection->prepare("SELECT o.num_order, p.reference, o.accumulated_quantity
                                            (pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december) AS inventory_day
                                      FROM orders o
                                      INNER JOIN products p ON p.id_product = o.id_product
                                      INNER JOIN products_price_history pph ON pph.id_product = o.id_product
                                      WHERE o.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $consolidated = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $consolidated;
    }
}
