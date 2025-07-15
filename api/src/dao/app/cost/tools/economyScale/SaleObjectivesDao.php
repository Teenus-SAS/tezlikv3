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
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pc.sale_price, pc.profitability, pc.commission_sale, pc.price, p.img, IFNULL(ed.units_sold, 0) AS units_sold, IFNULL(ed.turnover / ed.units_sold, 0) AS real_price, IFNULL(IFNULL(eda.turnover, 0) / IFNULL(eda.units_sold, 0), 0) AS real_price_anual, 
                                             p.composite, IFNULL(so.unit_sold, 0) AS unit_sold, IFNULL(eda.units_sold, 0) AS units_sold_anual, IFNULL(so.profitability, 0) AS profitability, 'false' AS error
                                      FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                        LEFT JOIN expenses_distribution_anual eda ON eda.id_product = p.id_product
                                        LEFT JOIN sale_objectives so ON so.id_product = p.id_product 
                                      WHERE p.id_company = :id_company AND p.active = 1 AND (ed.units_sold != 0 OR er.expense_recover != 0)
                                      ORDER BY p.product, p.reference ASC");
        $stmt->execute(['id_company' => $id_company]);



        $products = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $products;
    }

    public function findSaleObjectiveByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM sale_objectives WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $saleObjective = $stmt->fetch($connection::FETCH_ASSOC);
        return $saleObjective;
    }

    public function insertSaleObjectiveByCompany($dataSale, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("INSERT INTO sale_objectives (id_company, id_product, unit_sold, profitability) 
                                          VALUES (:id_company, :id_product, :unit_sold, :profitability)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataSale['id_product'],
                'unit_sold' => $dataSale['unit_sold'],
                'profitability' => $dataSale['profitability']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateSaleObjective($dataSale)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE sale_objectives SET unit_sold = :unit_sold, profitability = :profitability 
                                          WHERE id_product = :id_product");
            $stmt->execute([
                'unit_sold' => $dataSale['unit_sold'],
                'profitability' => $dataSale['profitability'],
                'id_product' => $dataSale['id_product'],
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
