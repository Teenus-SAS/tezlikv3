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

        $stmt = $connection->prepare("SELECT p.id_product, p.product, pc.price, IFNULL(pc.cost_workforce + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost + (SELECT IFNULL(SUM(cost), 0) 
                                                FROM services WHERE id_product = p.id_product)) / (1 - er.expense_recover / 100)) * (er.expense_recover / 100), ed.assignable_expense), 0) AS cost_fixed, IFNULL(pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * pc.price) + 
                                                (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product), 0) AS variable_cost
                                      FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                      WHERE p.id_company = :id_company AND p.active = 1 ORDER BY p.product ASC LIMIT 100");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $multiproducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $multiproducts;
    }
}
