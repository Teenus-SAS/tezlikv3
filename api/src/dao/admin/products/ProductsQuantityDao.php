<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
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

        $quantityProducts = $stmt->fetch($connection::FETCH_ASSOC);


        return $quantityProducts;
    }


    //CANTIDAD TOTAL DE PRODUCTOS POR EMPRESA
    public function totalProductsByCompany($id_company, $id_plan)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_product) AS quantity, (SELECT cant_products FROM plans_access WHERE id_plan = :id_plan) AS cant_products
                                      FROM products 
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company,
            'id_plan' => $id_plan
        ]);



        $products = $stmt->fetch($connection::FETCH_ASSOC);

        return $products;
    }
}
