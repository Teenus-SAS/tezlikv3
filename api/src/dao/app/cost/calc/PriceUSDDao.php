<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceUSDDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcAverageTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT (SUM(value_trm) / COUNT(id_trm)) AS average_trm FROM historical_trm");
            $stmt->execute();
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $price = $stmt->fetch($connection::FETCH_ASSOC);
            return $price;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function calcPriceUSDandModify($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            // Calculo
            $priceUsd = floatval($dataProduct['price']) / $dataProduct['average_trm'];

            // Actualizar
            $stmt = $connection->prepare("UPDATE products_cost SET price_usd = :price_usd WHERE id_product = :id_product AND id_company = :id_company");
            $stmt->execute([
                'price_usd' => $priceUsd,
                'id_product' => $dataProduct['idProduct'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $price = $stmt->fetch($connection::FETCH_ASSOC);
            return $price;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function calcStandardDeviation($data)
    {
        $count = sizeof($data);

        // Calcular promedio
        $sum = 0;

        foreach ($data as $key => $value) {
            $sum += floatval($value);
        }

        $average = $sum / $count;

        $sum = 0;

        // (Promedio - valor) elevado a la 2
        foreach ($data as $key => $value) {
            $sum += pow(($average - floatval($value)), 2);
        }

        $standardDeviation = pow(($sum / ($count - 1)), 0.5);

        return $standardDeviation;
    }

    public function calcDollarCoverage($actualUSD, $standardDeviation, $numDeviation)
    {
        $dollarCoverage = $actualUSD - ($standardDeviation * $numDeviation);

        return $dollarCoverage;
    }
}
