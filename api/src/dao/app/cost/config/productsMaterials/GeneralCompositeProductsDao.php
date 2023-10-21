<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCompositeProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findCompositeProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product AND id_child_product = :id_child_product");
        $stmt->execute([
            'id_product' => $dataProduct['idProduct'],
            'id_child_product' => $dataProduct['compositeProduct']
        ]);
        $compositeProduct = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCompositeProductCost($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pc.cost_materials
                                      FROM composite_products cp
                                        INNER JOIN products_costs pc ON pc.id_product = cp.id_product
                                      WHERE cp.id_product = :id_product LIMIT 1");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $compositeProduct = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCompositeProductByChild($id_child_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_child_product = :id_child_product GROUP BY id_product");
        $stmt->execute([
            'id_child_product' => $id_child_product
        ]);
        $compositeProduct = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }
}
