<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCompanyLicenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function changeDateContract($id_company, $date)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE companies_licenses SET date_contract = :date_contract WHERE id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company,
                'date_contract' => $date
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateFlagExpense($flag_expense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("UPDATE companies_licenses SET flag_expense = :flag_expense WHERE id_company = :id_company");
        $stmt->execute([
            'flag_expense' => $flag_expense,
            'id_company' => $id_company
        ]);
    }

    public function updateFlagPrice($flag_price, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE companies_licenses SET flag_type_price = :flag_type_price WHERE id_company = :id_company");
            $stmt->execute([
                'flag_type_price' => $flag_price,
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
