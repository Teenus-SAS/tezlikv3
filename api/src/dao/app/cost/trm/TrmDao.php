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

    public function getTrm($method, $params)
    {
        $wsdl = 'https://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService?WSDL';
        $options = array(
            'uri' => 'http://schemas.xmlsoap.org/soap/envelope/',
            'actor' => 'http://action.trm.services.generic.action.superfinanciera.nexura.sc.com.co/',
            'style' => SOAP_RPC,
            'use' => SOAP_ENCODED,
            'soap_version' => SOAP_1_1, //version 1 obligatorio
            'cache_wsdl' => WSDL_CACHE_NONE,
            'connection_timeout' => 5,
            'trace' => true,
            'encoding' => 'UTF-8',
            'exceptions' => false,
            'location' => 'https://www.superfinanciera.gov.co/SuperfinancieraWebServiceTRM/TCRMServicesWebService/TCRMServicesWebService', //endpoint
            'typemap' =>
            [ //namespace
                "type_ns"  => "http://action.trm.services.generic.action.superfinanciera.nexura.sc.com.co/",
                "type_name" => "WebServiceTRMReference.TCRMServicesInterface",
                "to_xml"  => "some_funktion_name"
            ],
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ])
            //'proxy_port'     => 8080,
            //'local_cert' => '/www/cer_superfinanciera.cer'
        );
        $nroError = '';
        $msjError = '';
        $result = '';
        try {
            $soap = new \SoapClient($wsdl, $options);
            $data = $soap->$method($params);
            return $data->return;
        } catch (\SoapFault $e) {
            return array('info' => true, 'message' => (string)$e->faultcode . (string)$e->getMessage());
        }
    }

    public function getAllHistoricalTrm()
    {
        try {
            $url = 'https://www.datos.gov.co/resource/32sa-8pi3.json?$limit=480&$order=vigenciahasta%20DESC';

            $json = file_get_contents($url);
            $historicalTrm = json_decode($json);

            return $historicalTrm;
        } catch (\Exception $e) {
            return array('info' => true);
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
