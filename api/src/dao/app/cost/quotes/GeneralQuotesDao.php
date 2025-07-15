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

        return $materials;
    }

    public function findAllQuotesProductsByIdProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM quotes_products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);


        $quotesProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quotesProducts;
    }

    public function findAllQuotesProductsAndMaterialsByIdQuote($id_quote)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT qp.id_quote, 0 AS idMaterial, qp.id_product AS idProduct, p.reference AS ref, p.product AS nameProduct, qp.id_price_list AS idPriceList, qp.quantity, 0 AS quantityMaterial,
                                             CONCAT('$ ', FORMAT(qp.price, 0, 'de_DE')) AS price, qp.discount, qp.profitability, CONCAT('$ ', FORMAT(((qp.quantity * qp.price * (1- qp.discount / 100) / (1 - (qp.profitability / 100)))),0,'de_DE')) AS totalPrice, 0 AS indirect
                                      FROM quotes_products qp
                                      INNER JOIN products p ON qp.id_product = p.id_product
                                      INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                      WHERE qp.id_quote = :id_quote
                                      GROUP BY qp.id_product
                                      UNION
                                      SELECT qp.id_quote, qp.id_material AS idMaterial, qp.id_product AS idProduct, m.reference AS ref, m.material AS nameProduct, qp.id_price_list AS idPriceList, qp.quantity_material AS quantity, qp.quantity_material AS quantityMaterial, 
                                             CONCAT('$ ', FORMAT(m.cost, 0, 'de_DE')) AS price, qp.discount, qp.profitability, CONCAT('$ ', FORMAT(((qp.quantity_material * m.cost * (1- qp.discount / 100)) / (1 - (qp.profitability / 100))),0,'de_DE')) AS totalPrice, 1 AS indirect
                                      FROM quotes_products qp
                                      INNER JOIN materials m ON qp.id_material = m.id_material
                                      WHERE qp.id_quote = :id_quote");
        $stmt->execute(['id_quote' => $id_quote]);


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
                'quantity_material' => $dataQuote['quantityMaterial']
            ]);
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
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
