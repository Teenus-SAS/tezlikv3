<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class QuotesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllQuotes()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT q.id_quote, q.id_product, p.product, q.quantity, q.discount, q.offer_validity, q.warranty, q.id_payment_method, pm.method
                                      FROM quotes q
                                      INNER JOIN products p ON p.id_product = q.id_product
                                      INNER JOIN quote_payment_methods pm ON pm.id_method = q.id_payment_method");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quotes = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotes;
    }

    public function updateQuote($dataQuote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE quotes SET id_product = :id_product, quantity = :quantity, discount = :discount, offer_validity = :offer_validity, warranty = :warranty, id_payment_method = :id_payment_method  
                                          WHERE id_quote = :id_quote");
            $stmt->execute([
                'id_quote' => $dataQuote['idQuote'],
                'id_product' => $dataQuote['idProduct'],
                'quantity' => $dataQuote['quantity'],
                'discount' => $dataQuote['discount'],
                'offer_validity' => $dataQuote['offerValidity'],
                'warranty' => $dataQuote['warranty'],
                'id_payment_method' => $dataQuote['idPaymentMethod']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteQuote($id_quote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM quotes WHERE id_quote = :id_quote");
            $stmt->execute(['id_quote' => $id_quote]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM quotes WHERE id_quote = :id_quote");
                $stmt->execute(['id_quote' => $id_quote]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
