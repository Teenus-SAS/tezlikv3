<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CustomPricesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCustomPricesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cp.id_custom_price, cp.id_product, p.reference, p.product, IF(cp.flag_price = 0, pc.sale_price, pc.price) AS price_cost, cp.id_price_list, pl.price_name, cp.price AS price_custom, cp.flag_price, pl.percentage
                                             -- , (((pc.price) - (pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = cp.id_product))) / (pc.price) * 100) AS profitability_price,
                                             -- (((cp.price) - (pc.cost_workforce + pc.cost_materials + pc.cost_indirect_cost + (SELECT IFNULL(SUM(cost), 0) FROM services WHERE id_product = cp.id_product))) / (cp.price)* 100) AS profitability_custom
                                      FROM custom_prices cp
                                        INNER JOIN products p ON p.id_product = cp.id_product
                                        INNER JOIN products_costs pc ON pc.id_product = cp.id_product
                                        INNER JOIN price_list pl ON pl.id_price_list = cp.id_price_list
                                      WHERE cp.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $pricesList = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pricesList", array('pricesList' => $pricesList));
        return $pricesList;
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

    public function insertCustomPricesByCompany($dataPrice, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $price = str_replace('.', '', $dataPrice['customPricesValue']);
        $price = str_replace(',', '.', $price);

        try {
            $stmt = $connection->prepare("INSERT INTO custom_prices (id_company, id_product, id_price_list, price, flag_price) 
                                          VALUES (:id_company, :id_product, :id_price_list, :price, :flag_price)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataPrice['idProduct'],
                'id_price_list' => $dataPrice['idPriceList'],
                'price' => $price,
                'flag_price' => $dataPrice['typePrice']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateCustomPrice($dataPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        $price = str_replace('.', '', $dataPrice['customPricesValue']);
        $price = str_replace(',', '.', $price);

        try {
            $stmt = $connection->prepare("UPDATE custom_prices SET id_product = :id_product, id_price_list = :id_price_list, price = :price 
                                          WHERE id_custom_price = :id_custom_price");
            $stmt->execute([
                'id_custom_price' => $dataPrice['idCustomPrice'],
                'id_product' => $dataPrice['idProduct'],
                'id_price_list' => $dataPrice['idPriceList'],
                'price' => $price,
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
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
            $stmt = $connection->prepare("UPDATE custom_prices SET flag_price = :flag_price WHERE id_custom_price = :id_custom_price");
            $stmt->execute([
                'id_custom_price' => $dataPrice['idCustomPrice'],
                'flag_price' => $dataPrice['typePrice']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteCustomPrice($id_custom_price)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM custom_prices WHERE id_custom_price IN ($id_custom_price)");
            $stmt->execute();
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM custom_prices WHERE id_custom_price IN ($id_custom_price)");
                $stmt->execute();
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
