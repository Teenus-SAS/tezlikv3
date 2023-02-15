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

    public function getActualTrm()
    {
        $date = date("Y-m-d");
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
}
