<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Consultar si existe producto en BD por compañia */
    public function findProduct($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products
                                  WHERE reference = :reference
                                  AND product = :product 
                                  AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => strtoupper(trim($dataProduct['product'])),
            'id_company' => $id_company
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    public function deleteProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM products WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM products WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $id_product]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'No es posible eliminar, el producto esta asociado a cotización';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
