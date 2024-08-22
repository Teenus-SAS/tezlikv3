<?php

namespace tezlikv3\Dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class TrmDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllHistoricalTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM historical_trm");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $historicalTrm = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $historicalTrm;
    }

    public function findLastInsertedTrm($date)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM historical_trm WHERE date_trm = :date_trm");
            $stmt->execute(['date_trm' => $date]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $historicalTrm = $stmt->fetch($connection::FETCH_ASSOC);
            return $historicalTrm;
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function getLastTrm()
    {
        try {
            // return 1;
            $url = 'https://www.datos.gov.co/resource/32sa-8pi3.json?$limit=1&$order=vigenciahasta%20DESC';

            $json = file_get_contents($url);

            // Verificar si hubo un error al obtener el contenido
            if ($json === false || !$json) {
                return 1;
            }

            $historicalTrm = json_decode($json, true);

            // Verificar si hubo un error al decodificar el JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 1;
            }

            return $historicalTrm;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllHistoricalTrm()
    {
        try {
            $url = 'https://www.datos.gov.co/resource/32sa-8pi3.json?$limit=480&$order=vigenciahasta%20DESC';

            $json = file_get_contents($url);

            // Verificar si hubo un error al obtener el contenido
            if ($json === false || !$json) {
                return 1;
            }

            $historicalTrm = json_decode($json, true);

            // Verificar si hubo un error al decodificar el JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 1;
            }

            return $historicalTrm;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function insertTrm($date, $price)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO historical_trm (date_trm, value_trm) VALUES (:date_trm, :value_trm)");
            $stmt->execute([
                'date_trm' => $date,
                'value_trm' => $price
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("DELETE FROM historical_trm WHERE date_trm < DATE_ADD(CURRENT_DATE, INTERVAL -2 YEAR)");
            $stmt->execute();
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }

    public function deleteAllHistoricalTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("DELETE FROM historical_trm");
            $stmt->execute();
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }
}
