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

        $stmt = $connection->prepare("SELECT q.id_quote, q.id_contact, q.offer_validity, q.warranty, q.id_payment_method, q.id_company, CONCAT(c.firstname, ' ' , c.lastname) AS contact, 
                                             cp.company_name, SUM((qp.quantity * qp.price) * (1 - (qp.discount/100))) AS price, q.delivery_date, q.observation, pm.method
                                        FROM quotes q 
                                        INNER JOIN quote_customers c ON c.id_contact = q.id_contact 
                                        INNER JOIN quote_companies cp ON cp.id_quote_company  = c.id_company  
                                        INNER JOIN quotes_products qp ON qp.id_quote = q.id_quote
                                        INNER JOIN quote_payment_methods pm ON pm.id_method = q.id_payment_method
                                        GROUP BY qp.id_quote");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quotes = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotes;
    }

    public function findQuote($id_quote)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT q.id_quote, CONCAT(c.firstname, ' ' , c.lastname) AS contact, c.phone AS contact_phone, c.email, cp.img, 
                                             cp.company_name, cp.address, cp.phone, cp.city, q.delivery_date, pm.method, q.observation
                                      FROM quotes q
                                        INNER JOIN quote_customers c ON c.id_contact = q.id_contact 
                                        INNER JOIN quote_companies cp ON cp.id_quote_company  = c.id_company
                                        INNER JOIN quote_payment_methods pm ON pm.id_method = q.id_payment_method
                                      WHERE q.id_quote = :id_quote");
        $stmt->execute(['id_quote' => $id_quote]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quote = $stmt->fetch($connection::FETCH_ASSOC);
        return $quote;
    }

    public function findAllQuotesProductsByIdQuote($id_quote)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT qp.id_product AS idProduct, p.reference AS ref, p.product AS nameProduct, qp.quantity, qp.price, qp.discount, (qp.quantity * qp.price * (1- qp.discount / 100)) AS totalPrice
                                      FROM quotes_products qp
                                        INNER JOIN products p ON p.id_product = qp.id_product
                                      WHERE qp.id_quote = :id_quote");
        $stmt->execute(['id_quote' => $id_quote]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quotesProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotesProducts;
    }

    public function findLastQuote()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_quote) AS id_quote FROM quotes");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $quote = $stmt->fetch($connection::FETCH_ASSOC);
        return $quote;
    }

    public function insertQuote($dataQuote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO quotes (id_company, id_contact, offer_validity, warranty, id_payment_method, delivery_date, observation) 
                                          VALUES (:id_company, :id_contact, :offer_validity, :warranty, :id_payment_method, :delivery_date, :observation)");
            $stmt->execute([
                'id_company' => $dataQuote['company'],
                'id_contact' => $dataQuote['contact'],
                'offer_validity' => $dataQuote['offerValidity'],
                'warranty' => $dataQuote['warranty'],
                'id_payment_method' => $dataQuote['idPaymentMethod'],
                'delivery_date' => $dataQuote['deliveryDate'],
                'observation' => $dataQuote['observation']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function insertQuotesProducts($dataQuote, $id_quote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            foreach ($dataQuote['products'] as $array) {

                $array = $this->convertDataQuotes($array);

                $stmt = $connection->prepare("INSERT INTO quotes_products (id_quote, id_product, quantity, price, discount) 
                                          VALUES (:id_quote, :id_product, :quantity, :price, :discount)");
                $stmt->execute([
                    'id_quote' => $id_quote,
                    'id_product' => $array['idProduct'],
                    'quantity' => $array['quantity'],
                    'price' => $array['price'],
                    'discount' => $array['discount']
                ]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
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

    public function updateQuote($dataQuote)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE quotes SET id_company = :id_company, id_contact = :id_contact, offer_validity = :offer_validity, warranty = :warranty, 
                                                            id_payment_method = :id_payment_method, delivery_date = :delivery_date, observation = :observation
                                          WHERE id_quote= :id_quote");
            $stmt->execute([
                'id_quote' => $dataQuote['idQuote'],
                'id_company' => $dataQuote['company'],
                'id_contact' => $dataQuote['contact'],
                'offer_validity' => $dataQuote['offerValidity'],
                'warranty' => $dataQuote['warranty'],
                'id_payment_method' => $dataQuote['idPaymentMethod'],
                'delivery_date' => $dataQuote['deliveryDate'],
                'observation' => $dataQuote['observation']
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

    public function convertDataQuotes($dataQuote)
    {
        $dataQuote['quantity'] = str_replace('.', '', $dataQuote['quantity']);

        $price = str_replace('.', '', $dataQuote['price']);
        $dataQuote['price'] = str_replace(',', '.', $price);

        return $dataQuote;
    }
}
