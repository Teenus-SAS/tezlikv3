<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProgrammingDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProgramming($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pcm.id_machine, o.id_order, o.num_order, p.reference, p.product, o.original_quantity, o.quantity, o.accumulated_quantity, c.client, pcm.cicles_hour
                                      FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_cicles_machine pcm ON pcm.id_product = o.id_product
                                      WHERE o.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }
}
