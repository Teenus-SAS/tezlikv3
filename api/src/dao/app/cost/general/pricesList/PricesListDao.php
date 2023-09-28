<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PricesListDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPricesListByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pl.id_price_list, pl.price_name, pl.percentage, IFNULL((SELECT IF(flag_price = 0, 'PRECIO ACTUAL', 'PRECIO SUGERIDO') FROM custom_prices WHERE id_price_list = pl.id_price_list ORDER BY price DESC LIMIT 1), '') AS type_price 
                                      FROM price_list pl
                                      WHERE pl.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $pricesList = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pricesList", array('pricesList' => $pricesList));
        return $pricesList;
    }

    public function findAllPricesListByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pl.id_price_list, pl.price_name, cp.price
                                      FROM price_list pl
                                        LEFT JOIN custom_prices cp ON cp.id_price_list = pl.id_price_list
                                        INNER JOIN products p ON p.id_product = cp.id_product
                                      WHERE p.id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $pricesList = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pricesList", array('pricesList' => $pricesList));
        return $pricesList;
    }

    public function insertPricesListByCompany($dataPrice, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO price_list (id_company, price_name) VALUES (:id_company, :price_name)");
            $stmt->execute([
                'id_company' => $id_company,
                'price_name' => strtoupper(trim($dataPrice['priceName']))
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePriceList($dataPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE price_list SET price_name = :price_name WHERE id_price_list = :id_price_list");
            $stmt->execute([
                'id_price_list' => $dataPrice['idPriceList'],
                'price_name' => strtoupper(trim($dataPrice['priceName']))
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deletePriceList($id_price_list)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM price_list WHERE id_price_list = :id_price_list");
            $stmt->execute(['id_price_list' => $id_price_list]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM price_list WHERE id_price_list = :id_price_list");
                $stmt->execute(['id_price_list' => $id_price_list]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
