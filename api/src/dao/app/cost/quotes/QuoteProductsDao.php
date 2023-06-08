<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class QuoteProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllQuotesProductsByIdQuote($id_quote)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT qp.id_product AS idProduct, p.reference AS ref, p.product AS nameProduct, qp.id_price_list AS idPriceList, qp.quantity, CONCAT('$ ', FORMAT(qp.price, 0, 'de_DE')) AS price, 
                                             qp.discount, CONCAT('$ ', FORMAT((qp.quantity * qp.price * (1- qp.discount / 100)),0,'de_DE')) AS totalPrice
                                      FROM quotes_products qp
                                        INNER JOIN products p ON p.id_product = qp.id_product
                                      WHERE qp.id_quote = :id_quote");
        $stmt->execute(['id_quote' => $id_quote]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quotesProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotesProducts;
    }

    public function insertQuotesProducts($dataQuote, $id_quote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO quotes_products (id_quote, id_product, id_price_list, quantity, price, discount) 
                                          VALUES (:id_quote, :id_product, :id_price_list, :quantity, :price, :discount)");
            $stmt->execute([
                'id_quote' => $id_quote,
                'id_product' => $dataQuote['idProduct'],
                'id_price_list' => $dataQuote['idPriceList'],
                'quantity' => $dataQuote['quantity'],
                'price' => $dataQuote['price'],
                'discount' => $dataQuote['discount']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteQuotesProducts($id_quote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("DELETE FROM quotes_products WHERE id_quote = :id_quote");
            $stmt->execute(['id_quote' => $id_quote]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
