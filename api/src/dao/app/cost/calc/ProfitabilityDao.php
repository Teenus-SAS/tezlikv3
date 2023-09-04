<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProfitabilityDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcProfitability($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            if ($_SESSION['flag_expense'] == 1 || $_SESSION['flag_expense'] == 0) {

                $stmt = $connection->prepare("SELECT (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = pc.id_product) + IFNULL(ed.assignable_expense, 0)) AS cost_total,
                                                     ((pc.sale_price) - (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = pc.id_product) + IFNULL(ed.assignable_expense, 0))) AS profitability_value,
                                                     IFNULL(((((pc.sale_price) - (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = pc.id_product) + IFNULL(ed.assignable_expense, 0))) / pc.sale_price) * 100), 0) AS profitability                   
                                              FROM  products_costs pc
                                                LEFT JOIN expenses_distribution ed ON ed.id_product = pc.id_product
                                              WHERE pc.id_product = :id_product");
            } else {
                $stmt = $connection->prepare("SELECT (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = pc.id_product) - (1 - IFNULL(er.expense_recover, 0) / 100)) AS cost_total,
                                                     ((pc.sale_price) - (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = pc.id_product) - (1 - IFNULL(er.expense_recover, 0) / 100))) AS profitability_value,
                                                     IFNULL(((((pc.sale_price) - (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = pc.id_product) - (1 - IFNULL(er.expense_recover, 0) / 100))) / pc.sale_price) * 100), 0) AS profitability                   
                                              FROM  products_costs pc
                                                LEFT JOIN expenses_recover er ON er.id_product = pc.id_product
                                              WHERE pc.id_product = :id_product");
            }
            $stmt->execute(['id_product' => $id_product]);

            $product = $stmt->fetch($connection::FETCH_ASSOC);
            return $product;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
