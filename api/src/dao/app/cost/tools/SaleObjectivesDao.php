<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SaleObjectivesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.sale_price, pc.profitability, pc.commission_sale, pc.price, p.img, IFNULL(ed.turnover / ed.units_sold, 0) AS real_price, p.composite
                                      FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        INNER JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1 AND ed.units_sold != 0
                                      ORDER BY p.product, p.reference ASC;");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }
}
