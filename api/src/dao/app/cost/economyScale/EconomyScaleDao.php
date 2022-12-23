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
        $stmt = $connection->prepare("SELECT 
                                            CAST((SELECT SUM(py.minute_value * (pp.enlistment_time + pp.operation_time)) FROM products_process pp INNER JOIN payroll py ON py.id_process = pp.id_process WHERE pp.id_product = p.id_product AND pp.id_company = p.id_company) + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost) * er.expense_recover)/100, ed.assignable_expense) AS UNSIGNED) AS fixedCost100, 
                                            CAST((SELECT SUM(py.minute_value * (pp.enlistment_time + pp.operation_time)) FROM products_process pp INNER JOIN payroll py ON py.id_process = pp.id_process WHERE pp.id_product = p.id_product AND pp.id_company = p.id_company) + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost) * er.expense_recover)/100, ed.assignable_expense) AS UNSIGNED) AS fixedCost150, 
                                            CAST((SELECT SUM(py.minute_value * (pp.enlistment_time + pp.operation_time)) FROM products_process pp INNER JOIN payroll py ON py.id_process = pp.id_process WHERE pp.id_product = p.id_product AND pp.id_company = p.id_company) + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost) * er.expense_recover)/100, ed.assignable_expense) AS UNSIGNED) AS fixedCost200, 
                                            CAST((((SELECT SUM(py.minute_value * (pp.enlistment_time + pp.operation_time)) FROM products_process pp INNER JOIN payroll py ON py.id_process = pp.id_process WHERE pp.id_product = p.id_product AND pp.id_company = p.id_company) + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost) * er.expense_recover)/100, ed.assignable_expense)))*2 AS UNSIGNED) AS fixedCost300, 
                                            CAST((((SELECT SUM(py.minute_value * (pp.enlistment_time + pp.operation_time)) FROM products_process pp INNER JOIN payroll py ON py.id_process = pp.id_process WHERE pp.id_product = p.id_product AND pp.id_company = p.id_company) + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost) * er.expense_recover)/100, ed.assignable_expense)))*2 AS UNSIGNED) AS fixedCost500 
                                      FROM products p
                                        LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        LEFT JOIN expenses_recover er ON er.id_product = p.id_product
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
        $stmt = $connection->prepare("SELECT CAST((pc.cost_materials + SUM(pp.enlistment_time + pp.operation_time) + 
                                                 SUM(m.minute_depreciation) + SUM(ml.cost_minute) / (pc.commission_sale-pc.profitability)/100) AS UNSIGNED) AS variableCost
                                      FROM products p
                                        LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN products_materials pm ON pm.id_product = p.id_product
                                        LEFT JOIN products_process pp ON pp.id_product = p.id_product
                                        LEFT JOIN manufacturing_load ml ON ml.id_machine = pp.id_machine	 
                                        LEFT JOIN machines m ON m.id_machine = pp.id_machine
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
