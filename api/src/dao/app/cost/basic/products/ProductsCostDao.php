<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsCostDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Falta la funcion de consultar */
    public function findAllProductsCost($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_costs WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productsCosts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsCosts;
    }

    /* Insertar products_costs */
    public function insertProductsCostByCompany($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            // $salePrice = str_replace('.', '', $dataProduct['salePrice']);
            // $salePrice = str_replace(',', '.', $salePrice);

            $stmt = $connection->prepare("INSERT INTO products_costs(id_product, id_company, sale_price, profitability, commission_sale, new_product) 
                                        VALUES (:id_product, :id_company, :sale_price, :profitability, :commission_sale, :new_product)");
            $stmt->execute([
                'id_product' => trim($dataProduct['idProduct']),
                'id_company' => $id_company,
                'sale_price' => $dataProduct['salePrice'],
                'profitability' => trim($dataProduct['profitability']),
                'commission_sale' => trim($dataProduct['commissionSale']),
                'new_product' => 1
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }
    /* Actualizar products_costs */
    public function updateProductsCostByCompany($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            // $salePrice = str_replace('.', '', $dataProduct['salePrice']);
            // $salePrice = str_replace(',', '.', $salePrice);

            $stmt = $connection->prepare("UPDATE products_costs SET sale_price = :sale_price, profitability = :profitability, commission_sale = :commission_sale
                                      WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => trim($dataProduct['idProduct']),
                'sale_price' => $dataProduct['salePrice'],
                'profitability' => trim($dataProduct['profitability']),
                'commission_sale' => trim($dataProduct['commissionSale'])
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function deleteProductsCost($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_costs WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $dataProduct['idProduct']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_costs WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataProduct['idProduct']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
