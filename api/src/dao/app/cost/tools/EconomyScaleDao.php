<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class EconomyScaleDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findFixedCostByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pc.cost_workforce + IFNULL((IF(mp.units_sold IS NULL,(SELECT SUM(e.expense_value) FROM expenses e INNER JOIN puc pu ON e.id_puc = pu.id_puc WHERE e.id_company = p.id_company 
                                             AND (pu.number_count LIKE '51%' OR pu.number_count LIKE '52%' OR pu.number_count LIKE '53%')) , (SELECT SUM(e.expense_value) FROM expenses e INNER JOIN puc pu ON e.id_puc = pu.id_puc 
                                             WHERE e.id_company = p.id_company AND (pu.number_count LIKE '51%' OR pu.number_count LIKE '52%' OR pu.number_count LIKE '53%')) * (mp.participation / 100))), 0) AS costFixed
                                      FROM products p
                                        LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN multiproducts mp ON mp.id_product = p.id_product
                                      WHERE p.id_product = :id_product AND p.id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $fixedCosts = $stmt->fetch($connection::FETCH_ASSOC);
        return $fixedCosts;
    }

    public function findVariableCostByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT ((pc.commission_sale / 100) * pc.price) AS commission, pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * pc.price) + 
                                             (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product) AS variableCost
                                      FROM products p
                                        LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                      WHERE p.id_product = :id_product AND p.id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $variablesCosts = $stmt->fetch($connection::FETCH_ASSOC);
        return $variablesCosts;
    }
}
