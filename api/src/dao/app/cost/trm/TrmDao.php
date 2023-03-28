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

    public function findLastInsertedTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(date_trm) AS date_trm FROM historical_trm");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $historicalTrm = $stmt->fetch($connection::FETCH_ASSOC);
        return $historicalTrm;
    }

    public function getTrm($date)
    {
        try {
            $soap = new \SoapClient("https://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService?WSDL", array(
                'soap_version'   => SOAP_1_1,
                'trace' => 1,
                "location" => "http://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService",
            ));
            $response = $soap->queryTCRM(array('tcrmQueryAssociatedDate' => $date));
            $response = $response->return;
            if ($response->success) {
                return $response->value;
            }
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

    public function deleteFirstTrm()
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("DELETE FROM historical_trm ORDER BY id_trm ASC LIMIT 1");
            $stmt->execute();
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }
}
