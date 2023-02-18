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

    public function getActualTrm($actualDate)
    {
        try {
            $soap = new \SoapClient("https://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService?WSDL", array(
                'soap_version'   => SOAP_1_1,
                'trace' => 1,
                "location" => "http://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService",
            ));
            $response = $soap->queryTCRM(array('tcrmQueryAssociatedDate' => $actualDate));
            $response = $response->return;
            if ($response->success) {
                return $response->value;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
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
