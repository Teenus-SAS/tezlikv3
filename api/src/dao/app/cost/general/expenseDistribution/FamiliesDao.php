<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FamiliesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllFamiliesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM families WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $families = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("families", array('families' => $families));
        return $families;
    }

    // Consultar si existe distribucion de gasto en BD
    public function findFamily($dataFamily, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_family FROM families WHERE family = :family AND id_company = :id_company");
        $stmt->execute([
            'family' => strtoupper(trim($dataFamily['family'])),
            'id_company' => $id_company
        ]);
        $findFamily = $stmt->fetch($connection::FETCH_ASSOC);
        return $findFamily;
    }

    public function findAllProductsFamiliesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, f.id_family, f.family
                                      FROM products p
                                        INNER JOIN families f ON f.id_family = p.id_family
                                      WHERE p.id_company = :id_company  
                                      ORDER BY `f`.`family` ASC");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllExpensesDistributionByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT f.id_family, f.family, SUM(me.units_sold) AS units_sold, SUM(me.turnover) AS turnover, f.assignable_expense
                                      FROM expenses_distribution me
                                        INNER JOIN products p ON p.id_product = me.id_product
                                        INNER JOIN families f ON f.id_family = p.id_family
                                      WHERE me.id_company = :id_company AND p.active = 1 
                                      GROUP BY p.id_family");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("expenses", array('expenses' => $expenses));
        return $expenses;
    }

    public function findAllProductsNotInEDistribution($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products p WHERE p.id_company = :id_company AND p.active = 1 AND 
                                      (p.id_product NOT IN (SELECT id_product FROM expenses_distribution WHERE id_product = p.id_product) OR p.id_family = 0)");
        $stmt->execute(['id_company' => $id_company]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllProductsInFamily($id_family)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_product
                                      FROM products 
                                      WHERE active = 1 AND id_family = :id_family");
        $stmt->execute(['id_family' => $id_family]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function insertFamilyByCompany($dataFamily, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO families (family, id_company) VALUES (:family, :id_company)");
            $stmt->execute([

                'family' => strtoupper(trim($dataFamily['family'])),
                'id_company' => $id_company,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Familia duplicada. Ingrese una nueva familia';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateFamily($dataFamily)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE families SET family = :family WHERE id_family = :id_family");
            $stmt->execute([
                'id_family' => $dataFamily['idFamily'],
                'family' => strtoupper(trim($dataFamily['family']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateFlagFamily($flag_family, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET flag_family = :flag_family WHERE id_company = :id_company");
            $stmt->execute([
                'flag_family' => $flag_family,
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateFamilyProduct($dataFamily)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products SET id_family = :id_family WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $dataFamily['selectNameProduct'],
                'id_family' => $dataFamily['idFamily']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteFamily($id_family)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM families WHERE id_family = :id_family");
            $stmt->execute(['id_family' => $id_family]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM families WHERE id_family = :id_family");
                $stmt->execute(['id_family' => $id_family]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
