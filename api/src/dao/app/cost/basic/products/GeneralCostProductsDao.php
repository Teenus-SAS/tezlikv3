<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCostProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProductCost($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.img, IFNULL(pc.price, 0) AS price
                                  FROM products p
                                  LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                  WHERE p.id_product = :id_product AND p.id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $product = $stmt->fetch($connection::FETCH_ASSOC);
        return $product;
    }

    public function findAllProductsByCRM($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, IFNULL(pc.price, 0) AS price, p.img 
                                  FROM products p
                                    LEFT JOIN products_costs pc ON p.id_product = pc.id_product
                                  WHERE p.id_company = :id_company AND p.active = 1 ORDER BY `p`.`product`, `p`.`reference` ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findAllInactivesProducts($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, IFNULL(pc.profitability, 0) AS profitability, IFNULL(pc.commission_sale, 0) AS commission_sale, pc.price, p.img 
                                  FROM products p
                                    LEFT JOIN products_costs pc ON p.id_product = pc.id_product
                                  WHERE p.id_company = :id_company AND p.active = 0");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }


    public function activeOrInactiveProducts($id_product, $active)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products SET active = :active WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $id_product,
                'active' => $active
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
