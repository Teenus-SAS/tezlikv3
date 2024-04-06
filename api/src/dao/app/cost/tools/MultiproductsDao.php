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

        try {
            // $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, IF(cl.flag_type_price = 0, pc.sale_price, pc.price) AS price, IFNULL((SELECT SUM(expense_value) FROM expenses WHERE id_company = p.id_company), 0) AS expense, (SELECT IFNULL(SUM(salary_net), 0) FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll) AS sum_payroll,
            // -- (IFNULL((SELECT SUM(expense_value) FROM expenses WHERE id_company = p.id_company), 0)) , ((IFNULL((SELECT SUM(expense_value) FROM expenses WHERE id_company = p.id_company), 0)) * (mp.participation / 100))), 0) AS expense,
            //                                  IFNULL(IF(IFNULL(mp.units_sold, 0) = 0, ((SELECT IFNULL(SUM(salary_net), 0) FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll)), (((SELECT IFNULL(SUM(salary_net), 0) FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll)) * (mp.participation /100))), 0) AS cost_fixed,
            //                                  IFNULL(pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * IF(cl.flag_type_price = 0, pc.sale_price, pc.price)) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product), 0) AS variable_cost
            //                             FROM products p
            //                             INNER JOIN products_costs pc ON p.id_product = pc.id_product
            //                             LEFT JOIN multiproducts mp ON mp.id_product = p.id_product
            //                             LEFT JOIN companies_licenses cl ON cl.id_company = p.id_company
            //                             WHERE p.id_company = :id_company AND p.active = 1 
            //                             AND IF(cl.flag_type_price = 0, pc.sale_price, pc.price) != 0
            //                             ORDER BY p.product ASC;");
            // IF(cl.flag_type_price = 0, pc.sale_price, pc.price) AS price, 
            $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, IFNULL(IFNULL(ed.turnover , 0) / IFNULL(ed.units_sold, 0), 0) AS price, IFNULL((SELECT SUM(expense_value) FROM expenses WHERE id_company = p.id_company), 0) AS expense, 
                                                 (SELECT IFNULL(SUM(salary_net), 0) FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll) AS sum_payroll, IFNULL(IF(IFNULL(mp.units_sold, 0) = 0, ((SELECT IFNULL(SUM(salary_net), 0) 
                                                 FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll)), (((SELECT IFNULL(SUM(salary_net), 0) FROM (SELECT salary_net FROM payroll WHERE id_company = :id_company GROUP BY employee) AS payroll)) * (mp.participation /100))), 0) AS cost_fixed,
                                                 IFNULL(pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * IFNULL(IFNULL(ed.turnover , 0) / IFNULL(ed.units_sold, 0), 0)) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product), 0) AS variable_cost
                                        FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        LEFT JOIN multiproducts mp ON mp.id_product = p.id_product
                                        LEFT JOIN companies_licenses cl ON cl.id_company = p.id_company
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        WHERE p.id_company = :id_company AND p.active = 1 
                                        AND IFNULL(IFNULL(ed.turnover , 0) / IFNULL(ed.units_sold, 0), 0) != 0
                                        ORDER BY p.product ASC");
            $stmt->execute([
                'id_company' => $id_company
            ]);

            $multiproducts = $stmt->fetchAll($connection::FETCH_ASSOC);

            return $multiproducts;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function findAllExistingMultiproducts($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM multiproducts WHERE id_company = :id_company");

        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $multiproducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $multiproducts;
    }

    public function findMultiproduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM multiproducts WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $multiproduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $multiproduct;
    }

    public function insertMultiproductByCompany($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO multiproducts(id_company, id_product, units_sold, participation, expense) 
                                          VALUES (:id_company, :id_product, :units_sold, :participation, :expense)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['id_product'],
                'units_sold' => $dataProduct['soldUnit'],
                'participation' => $dataProduct['participation'],
                'expense' => $dataProduct['expense']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateMultiProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE multiproducts SET units_sold = :units_sold, participation = :participation, expense = :expense 
                                          WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $dataProduct['id_product'],
                'units_sold' => $dataProduct['soldUnit'],
                'participation' => $dataProduct['participation'],
                'expense' => $dataProduct['expense']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateTotalUnits($data, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM general_data WHERE id_company = :id_company");
            $stmt->execute(['id_company' => $id_company]);
            $row = $stmt->rowCount();

            if ($row > 0)
                $stmt = $connection->prepare("UPDATE general_data SET total_units_sold = :total_units_sold, total_units = :total_units WHERE id_company = :id_company");
            else
                $stmt = $connection->prepare("INSERT INTO general_data (total_units_sold, total_units, id_company) VALUES (:total_units_sold, :total_units, :id_company)");

            $stmt->execute([
                'total_units_sold' => $data['total_units_sold'],
                'total_units' => $data['total_units'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
