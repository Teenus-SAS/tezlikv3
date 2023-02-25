<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MultiproductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllMultiproducts($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.product, pc.price, (pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost) AS cost, '0,00 %' AS participation, 
                                             (price - (pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost)) AS margin_contribution, '0,00' AS average, 0 AS unitsToSold
                                      FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $multiproducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $multiproducts;
    }
}
