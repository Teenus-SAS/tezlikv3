<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PriceEURDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /*
    public function calcAverageTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT (SUM(value_trm) / COUNT(id_trm)) AS average_trm FROM historical_trm");
            $stmt->execute();


            $price = $stmt->fetch($connection::FETCH_ASSOC);
            return $price;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    } 

    public function calcStandardDeviation($dataHistorical)
    {
        $count = sizeof($dataHistorical);

        // Calcular promedio
        $sum = 0;

        foreach ($dataHistorical as $arr) {
            $sum += floatval($arr['value_trm']);
        }

        $average = $sum / $count;

        $sum = 0;

        // (Promedio - valor) elevado a la 2
        foreach ($dataHistorical as $arr) {
            $sum += pow(($average - floatval($arr['value_trm'])), 2);
        }

        $standardDeviation = pow(($sum / ($count - 1)), 0.5);

        return $standardDeviation;
    }

    public function calcDollarCoverage($averageTrm, $standardDeviation, $numDeviation)
    {
        $dollarCoverage = $averageTrm - ($standardDeviation * $numDeviation);

        return $dollarCoverage;
    } */

    public function calcPriceUSDandModify($dataProduct, $coverage)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            // Calculo
            $salePriceEUR = floatval($dataProduct['sale_price']) / $coverage;
            $priceEUR = floatval($dataProduct['price']) / $coverage;

            // Actualizar
            $stmt = $connection->prepare("UPDATE products_costs SET price_eur = :price_eur, sale_price_eur = :sale_price_eur 
                                          WHERE id_product = :id_product");
            $stmt->execute([
                'price_eur' => $priceEUR,
                'sale_price_eur' => $salePriceEUR,
                'id_product' => $dataProduct['id_product'],
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateLastEuroCoverage($euroCoverage, $numDeviation, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET coverage_eur = :coverage_eur, deviation = :deviation 
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company,
                'deviation' => $numDeviation,
                'coverage_eur' => $euroCoverage
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
