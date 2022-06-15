<?php

namespace tezlikv2\dao;

use DateTime;
use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsQuantityDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //CANTIDAD DE PRODUCTOS GENERAL
    public function totalProducts()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT COUNT(id_product) AS quantity FROM products");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $quantityProducts = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("AllProducts", array('AllProducts' => $quantityProducts));

        return $quantityProducts;
    }


    //CANTIDAD TOTAL DE PRODUCTOS POR EMPRESA
    public function totalProductsByCompany()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.profitability, pc.commission_sale, pc.price, p.img 
                                      FROM products p 
                                      INNER JOIN products_costs pc ON p.id_product = pc.id_product");
        $stmt->execute();

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }
}
