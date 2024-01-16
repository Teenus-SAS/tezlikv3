<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralQuotesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPaymentMethod($id_method)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM quotes WHERE id_payment_method = :id_payment_method");
        $stmt->execute(['id_payment_method' => $id_method]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quotes = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotes;
    }

    public function findMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM quotes_products WHERE id_material = :id_material");
        $stmt->execute([
            'id_material' => $id_material
        ]);
        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $materials));
        return $materials;
    }

    public function findAllQuotesProductsByIdProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM quotes_products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quotesProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotesProducts;
    }

    public function updateQuotesProducts($dataQuote, $id_quote_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE quotes_products SET id_material = :id_material, quantity_material = :quantity_material 
                                          WHERE id_quote_product = :id_quote_product");
            $stmt->execute([
                'id_quote_product' => $id_quote_product,
                'id_material' => $dataQuote['idMaterial'],
                'quantity_material' => $dataQuote['quantity']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateFlagQuote($dataQuote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE quotes SET flag_quote = :flag_quote WHERE id_quote = :id_quote");
            $stmt->execute([
                'id_quote' => $dataQuote['idQuote'],
                'flag_quote' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteQuotesProductsByProduct($dataQuote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM quotes_products WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataQuote['idProduct']]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM quotes_products WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $dataQuote['idProduct']]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
