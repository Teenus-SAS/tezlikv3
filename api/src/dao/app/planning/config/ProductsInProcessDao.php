<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsInProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsInProcessByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pp.id_product_in_process, p.id_product, p.reference, p.product
                                      FROM products p
                                      INNER JOIN plan_products_in_process pp ON p.id_product = pp.id_product
                                      WHERE p.id_company = :id_company;");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productsInProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsInProcess;
    }

    public function findAllProductsInProcess()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product
                                      FROM products p
                                      INNER JOIN plan_categories c ON c.id_category = p.category
                                      WHERE c.category = 'En proceso';");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productsInProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsInProcess;
    }

    public function insertProductInProcessByCompany($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO plan_products_in_process (id_product, id_company)
                                          VALUES (:id_product, :id_company)");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProductInProcess($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_products_in_process SET id_product = :id_product
                                          WHERE id_product_in_process = :id_product_in_process");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
                'id_product_in_process' => $dataProduct['idProductInProcess']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProductInProcess($id_product_in_process)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_products_in_process WHERE id_product_in_process = :id_product_in_process");
        $stmt->execute(['id_product_in_process' => $id_product_in_process]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM plan_products_in_process WHERE id_product_in_process = :id_product_in_process");
            $stmt->execute(['id_product_in_process' => $id_product_in_process]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
