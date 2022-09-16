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

    /* Todos los productos asociados a la tabla `plan_products_categories` */
    public function findAllProductsInProcessByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pp.id_product_category, p.id_product, p.reference, p.product
                                      FROM products p
                                      INNER JOIN plan_products_categories pp ON p.id_product = pp.id_product
                                      WHERE p.id_company = :id_company;");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productsInProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsInProcess;
    }

    /* Todos los productos en proceso */
    public function findAllProductsInProcess()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product
                                      FROM products p
                                      INNER JOIN plan_categories c ON c.id_category = p.category
                                      WHERE c.category LIKE '%en proceso'");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productsInProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsInProcess;
    }

    public function findProductInProcess($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_products_categories 
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataProduct['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productInProcess = $stmt->fetch($connection::FETCH_ASSOC);
        return $productInProcess;
    }

    public function insertProductInProcessByCompany($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO plan_products_categories (id_product, id_company)
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

    // public function updateProductInProcess($dataProduct)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     try {
    //         $stmt = $connection->prepare("UPDATE plan_products_categories SET id_product = :id_product
    //                                       WHERE id_product_category = :id_product_category");
    //         $stmt->execute([
    //             'id_product' => $dataProduct['idProduct'],
    //             'id_product_category' => $dataProduct['idProductInProcess']
    //         ]);
    //         $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();
    //         $error = array('info' => true, 'message' => $message);
    //         return $error;
    //     }
    // }

    public function deleteProductInProcess($id_product_category)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_products_categories WHERE id_product_category = :id_product_category");
        $stmt->execute(['id_product_category' => $id_product_category]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM plan_products_categories WHERE id_product_category = :id_product_category");
            $stmt->execute(['id_product_category' => $id_product_category]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
