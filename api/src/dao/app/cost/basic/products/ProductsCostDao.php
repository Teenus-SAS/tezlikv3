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


        $productsCosts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsCosts;
    }

    /* Insertar products_costs */
    public function insertProductsCostByCompany($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO products_costs(id_product, id_company, sale_price, profitability, commission_sale, new_product) 
                                        VALUES (:id_product, :id_company, :sale_price, :profitability, :commission_sale, :new_product)");
            $stmt->execute([
                'id_product' => trim($dataProduct['idProduct']),
                'id_company' => $id_company,
                'sale_price' => $dataProduct['salePrice'],
                'profitability' => trim($dataProduct['profitability']),
                'commission_sale' => trim($dataProduct['commissionSale']),
                'new_product' => $dataProduct['newProduct']
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
            $sql = "UPDATE products_costs 
                    SET sale_price = :sale_price, profitability = :profitability, commission_sale = :commission_sale
                    WHERE id_product = :id_product";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_product' => trim($dataProduct['idProduct']),
                'sale_price' => $dataProduct['salePrice'],
                'profitability' => trim($dataProduct['profitability']),
                'commission_sale' => trim($dataProduct['commissionSale'])
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function updateCostByCompany($dataProduct, $id_company, $connection = null)
    {
        $useExternalConnection = $connection !== null;
        $stmt = null;

        if (!$useExternalConnection)
            $connection = Connection::getInstance()->getConnection();

        try {
            // Construcción dinámica de la consulta SQL
            $updates = [];
            $params = [
                'id_product' => trim($dataProduct['idProduct']),
                'id_company' => $id_company
            ];

            // Verificar y agregar cada campo si está presente
            if (isset($dataProduct['salesPrice'])) {
                $updates[] = 'price = :salesPrice';
                $params['salesPrice'] = trim($dataProduct['salesPrice']);
            }

            if (isset($dataProduct['profit'])) {
                $updates[] = 'profitability = :profitability';
                $params['profitability'] = trim($dataProduct['profit']);
            }

            if (isset($dataProduct['commission'])) {
                $updates[] = 'commission_sale = :commission';
                $params['commission'] = trim($dataProduct['commission']);
            }

            // Si no hay campos para actualizar, retornar
            if (empty($updates)) {
                return true;
            }

            // Construir la consulta SQL final
            $sql = "UPDATE products_costs SET " . implode(', ', $updates) .
                " WHERE id_product = :id_product AND id_company = :id_company";

            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
        } catch (\Exception $e) {
            $this->logger->error(__FUNCTION__, ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($stmt)
                $stmt->closeCursor();

            if (!$useExternalConnection && $connection)
                $connection = null;
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
        }
    }
}
