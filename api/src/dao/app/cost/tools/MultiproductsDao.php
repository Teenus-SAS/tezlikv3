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

        $stmt = $connection->prepare("SELECT p.id_product, p.product, pc.price, IFNULL((SELECT SUM(expense_value) FROM expenses WHERE id_company = p.id_company), 0) AS expense, 
                                            (pc.cost_workforce + IF(IFNULL(ed.assignable_expense, 0) = 0, ((pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost + (SELECT IFNULL(SUM(cost), 0) 
                                            FROM services WHERE id_product = p.id_product)) / (1 - er.expense_recover / 100)) * (er.expense_recover / 100), ed.assignable_expense)) AS cost_fixed, 
                                            IFNULL(pc.cost_materials + pc.cost_indirect_cost + ((pc.commission_sale / 100) * pc.price) + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = p.id_product), 0) AS variable_cost
                                        FROM products p
                                        INNER JOIN products_costs pc ON p.id_product = pc.id_product
                                        LEFT JOIN expenses_distribution ed ON ed.id_product = p.id_product
                                        LEFT JOIN expenses_recover er ON er.id_product = p.id_product
                                        WHERE p.id_company = :id_company AND p.active = 1 ORDER BY p.product ASC");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $multiproducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $multiproducts;
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
}
