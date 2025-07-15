<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPricesListDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findPricesList($dataPrice, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM price_list WHERE price_name = :price_name AND id_company = :id_company");
        $stmt->execute([
            'price_name' => strtoupper(trim($dataPrice['priceName'])),
            'id_company' => $id_company
        ]);



        $pricesList = $stmt->fetch($connection::FETCH_ASSOC);

        return $pricesList;
    }

    public function updatePercentage($dataPrice)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE price_list SET percentage = :percentage WHERE id_price_list = :id_price_list");
            $stmt->execute([
                'id_price_list' => $dataPrice['idPriceList'],
                'percentage' => $dataPrice['percentage']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
