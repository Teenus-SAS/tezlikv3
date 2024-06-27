<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceObjectivesDao
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
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.sale_price, pc.profitability, pc.commission_sale, pc.price, p.img, IFNULL(ed.turnover / ed.units_sold, 0) AS real_price, 
                                             p.composite, IFNULL(po.unit_1, 0) AS unit_1, IFNULL(po.unit_2, 0) AS unit_2, IFNULL(po.unit_3, 0) AS unit_3, IFNULL(po.profitability, 0) AS profitability_po,
                                             IFNULL(po.price_1, 0) AS price_1, IFNULL(po.price_2, 0) AS price_2, IFNULL(po.price_3, 0) AS price_3
                                      FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        INNER JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        LEFT JOIN expenses_distribution_anual eda ON eda.id_product = p.id_product
                                        LEFT JOIN price_objectives po ON po.id_product = p.id_product 
                                      WHERE p.id_company = :id_company AND p.active = 1 AND ed.units_sold != 0
                                      ORDER BY p.product, p.reference ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findPriceObjectiveByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM price_objectives WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $priceObjective = $stmt->fetch($connection::FETCH_ASSOC);
        return $priceObjective;
    }

    public function insertPriceObjectiveByCompany($dataPrice, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("INSERT INTO price_objectives (id_company, id_product, profitability, unit_1, unit_2, unit_3, price_1, price_2, price_3) 
                                          VALUES (:id_company, :id_product, :profitability, :unit_1, :unit_2, :unit_3, :price_1, :price_2, :price_3)");
            $stmt->execute([
                'id_company' => $id_company,
                'profitability' => $dataPrice['profitability'],
                'unit_1' => $dataPrice['unit_1'],
                'unit_2' => $dataPrice['unit_2'],
                'unit_3' => $dataPrice['unit_3'],
                'price_1' => $dataPrice['price_1'],
                'price_2' => $dataPrice['price_2'],
                'price_3' => $dataPrice['price_3'],
                'id_product' => $dataPrice['id_product'],
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updatePriceObjective($dataPrice)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE price_objectives SET profitability = :profitability, unit_1 = :unit_1, unit_2 = :unit_2, unit_3 = :unit_3, price_1 = :price_1, price_2 = :price_2, price_3 = :price_3
                                          WHERE id_product = :id_product");
            $stmt->execute([
                'profitability' => $dataPrice['profitability'],
                'unit_1' => $dataPrice['unit_1'],
                'unit_2' => $dataPrice['unit_2'],
                'unit_3' => $dataPrice['unit_3'],
                'price_1' => $dataPrice['price_1'],
                'price_2' => $dataPrice['price_2'],
                'price_3' => $dataPrice['price_3'],
                'id_product' => $dataPrice['id_product'],
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
