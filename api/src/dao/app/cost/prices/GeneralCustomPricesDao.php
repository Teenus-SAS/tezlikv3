<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCustomPricesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findCustomPrice($dataPrice, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM custom_prices 
                                      WHERE id_product = :id_product AND id_price_list = :id_price_list AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataPrice['idProduct'],
            'id_price_list' => $dataPrice['idPriceList'],
            'id_company' => $id_company
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $pricesList = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pricesList", array('pricesList' => $pricesList));
        return $pricesList;
    }

    public function deleteCustomPriceByPriceList($id_price_list)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM custom_prices WHERE id_price_list = :id_price_list");
            $stmt->execute(['id_price_list' => $id_price_list]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM custom_prices WHERE id_price_list = :id_price_list");
                $stmt->execute(['id_price_list' => $id_price_list]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePrice($id_custom_price, $price)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE custom_prices SET price = :price WHERE id_custom_price = :id_custom_price");
            $stmt->execute([
                'id_custom_price' => $id_custom_price,
                'price' => $price
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeflagPrice($dataPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE custom_prices SET flag_price = :flag_price");
            $stmt->execute([
                'flag_price' => $dataPrice['typePrice']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
