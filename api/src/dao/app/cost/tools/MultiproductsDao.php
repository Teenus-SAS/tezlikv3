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
            $stmt = $connection->prepare("SELECT p.id_product, p.product, pc.price, IFNULL((SELECT SUM(expense_value) FROM expenses WHERE id_company = p.id_company), 0) AS expense, 
                                                 ((SELECT IFNULL(SUM(salary_net), 0) FROM (SELECT py.salary_net FROM payroll py INNER JOIN products_process pp ON pp.id_process = py.id_process WHERE py.id_company = :id_company GROUP BY employee) AS payroll)) AS cost_fixed,
                                                 IFNULL(pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * pc.price) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product), 0) AS variable_cost
                                        FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        LEFT JOIN multiproducts mp ON mp.id_product = p.id_product
                                        WHERE p.id_company = :id_company AND p.active = 1 ORDER BY p.product ASC");
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

    public function updateTotalUnits($total_units, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM general_data WHERE id_company = :id_company");
            $stmt->execute(['id_company' => $id_company]);
            $row = $stmt->rowCount();

            if ($row > 0)
                $stmt = $connection->prepare("UPDATE general_data SET total_units = :total_units WHERE id_company = :id_company");
            else
                $stmt = $connection->prepare("INSERT INTO general_data (total_units, id_company) VALUES (:total_units, :id_company)");

            $stmt->execute([
                'total_units' => $total_units,
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
