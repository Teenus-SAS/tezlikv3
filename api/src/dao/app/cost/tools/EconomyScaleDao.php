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

    public function findAllFixedCostByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT p.id_product, IFNULL((IF(IFNULL(mp.units_sold , 0) = 0, (SELECT IFNULL(SUM(salary_net), 0) AS salary_net FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll), ((SELECT IFNULL(SUM(salary_net), 0) AS salary_net FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll) * (mp.participation / 100)))) , 0) + IFNULL((IF(IFNULL(mp.units_sold, 0) = 0,(SELECT SUM(e.expense_value) FROM expenses e INNER JOIN puc pu ON e.id_puc = pu.id_puc WHERE e.id_company = p.id_company),
                                             (SELECT SUM(e.expense_value) FROM expenses e INNER JOIN puc pu ON e.id_puc = pu.id_puc WHERE e.id_company = p.id_company) * (mp.participation / 100))), 0) AS costFixed
                                        FROM products p
                                        INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN multiproducts mp ON mp.id_product = p.id_product
                                      WHERE p.active = 1 AND p.id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $fixedCosts = $stmt->fetchAll($connection::FETCH_ASSOC);
            return $fixedCosts;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function findAllVariableCostByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT p.id_product, ((pc.commission_sale / 100) * IF(cl.flag_type_price = 0, pc.sale_price, pc.price)) AS commission, pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * pc.price) + 
                                             (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product) AS variableCost
                                      FROM products p
                                        INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN companies_licenses cl ON cl.id_company = p.id_company
                                      WHERE p.active = 1 AND p.id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $variablesCosts = $stmt->fetchAll($connection::FETCH_ASSOC);
            return $variablesCosts;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function findFixedCostByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT IFNULL((IF(IFNULL(mp.units_sold , 0) = 0, (SELECT IFNULL(SUM(salary_net), 0) AS salary_net FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll), ((SELECT IFNULL(SUM(salary_net), 0) AS salary_net FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll) * (mp.participation / 100)))) , 0) + IFNULL((IF(IFNULL(mp.units_sold, 0) = 0,(SELECT SUM(e.expense_value) FROM expenses e INNER JOIN puc pu ON e.id_puc = pu.id_puc WHERE e.id_company = p.id_company),
                                             (SELECT SUM(e.expense_value) FROM expenses e INNER JOIN puc pu ON e.id_puc = pu.id_puc WHERE e.id_company = p.id_company) * (mp.participation / 100))), 0) AS costFixed
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
        $stmt = $connection->prepare("SELECT ((pc.commission_sale / 100) * IF(cl.flag_type_price = 0, pc.sale_price, pc.price)) AS commission, pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * pc.price) + 
                                             (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product) AS variableCost
                                      FROM products p
                                        LEFT JOIN products_costs pc ON pc.id_product = p.id_product
                                        LEFT JOIN companies_licenses cl ON cl.id_company = p.id_company
                                      WHERE p.id_product = :id_product AND p.id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $variablesCosts = $stmt->fetch($connection::FETCH_ASSOC);
        return $variablesCosts;
    }

    public function combinedData($array1, $array2, $key)
    {
        try {
            $result = array();

            // Crear un índice para $array1 basado en la clave
            $index = array();
            foreach ($array1 as $item) {
                $index[$item[$key]] = $item;
            }

            // Iterar sobre $array2 y combinar solo si la clave existe en $array1
            foreach ($array2 as $item) {
                if (isset($index[$item[$key]])) {
                    $result[$item[$key]] = array_merge($index[$item[$key]], $item);
                }
            }

            return array_values($result);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
