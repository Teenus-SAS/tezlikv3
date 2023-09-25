<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceCustomDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcPriceCustomByProduct($dataPrice, $id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($dataPrice['typePrice'] == 0)
                $stmt = $connection->prepare("SELECT (sale_price * (1 + (:percentage / 100))) AS custom_price 
                                          FROM products_costs 
                                          WHERE id_product = :id_product");
            else
                $stmt = $connection->prepare("SELECT (price * (1 + (:percentage / 100))) AS custom_price 
                                          FROM products_costs 
                                          WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $id_product,
                'percentage' => $dataPrice['percentage']
            ]);
            $priceList = $stmt->fetch($connection::FETCH_ASSOC);

            $price = str_replace('.', ',', $priceList['custom_price']);
            return $price;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
    public function calcPriceCustomByCustomPrice($id_custom_price)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT (IF(cp.flag_price = 0, pc.sale_price, pc.price) * (1 + (pl.percentage / 100))) AS custom_price 
                                          FROM custom_prices cp
                                          LEFT JOIN products_costs pc ON pc.id_product = cp.id_product
                                          LEFT JOIN price_list pl ON pl.id_price_list = cp.id_price_list
                                          WHERE cp.id_custom_price = :id_custom_price");
            $stmt->execute([
                'id_custom_price' => $id_custom_price,
            ]);
            $priceList = $stmt->fetch($connection::FETCH_ASSOC);
            return $priceList;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
