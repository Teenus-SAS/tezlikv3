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

    public function findProductsByMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.product, o.id_order, o.num_order
                                      FROM products p 
                                      INNER JOIN plan_cicles_machine pcm ON pcm.id_product = p.id_product
                                      INNER JOIN orders o ON o.id_product = p.id_product 
                                      WHERE pcm.id_machine = :id_machine");
        $stmt->execute([
            'id_machine' => $id_machine,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }
}
