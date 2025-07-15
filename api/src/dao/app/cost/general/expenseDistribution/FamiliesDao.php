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



        $families = $stmt->fetchAll($connection::FETCH_ASSOC);

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
        $stmt = $connection->prepare("SELECT * FROM families
                                      WHERE id_company = :id_company AND (assignable_expense > 0 OR units_sold > 0 OR turnover > 0)");
        $stmt->execute(['id_company' => $id_company]);



        $expenses = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $expenses;
    }

    public function findAllProductsInFamily($id_family, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products 
                                      WHERE active = 1 AND id_family = :id_family AND id_company = :id_company");
        $stmt->execute([
            'id_family' => $id_family,
            'id_company' => $id_company
        ]);
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
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateDistributionFamily($dataFamily)
    {
        $connection = Connection::getInstance()->getConnection();

        // $unitsSold = str_replace('.', '', $dataFamily['unitsSold']);
        // $turnover = str_replace('.', '', $dataFamily['turnover']);

        try {
            $stmt = $connection->prepare("UPDATE families SET units_sold = :units_sold, turnover = :turnover WHERE id_family = :id_family");
            $stmt->execute([
                'units_sold' => $dataFamily['unitsSold'],
                'turnover' => $dataFamily['turnover'],
                'id_family' => $dataFamily['idFamily']
            ]);
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
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
