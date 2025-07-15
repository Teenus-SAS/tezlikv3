<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class QCompaniesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCompanies($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM quote_companies WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);


        $companies = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $companies;
    }

    public function insertCompany($dataCompany, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO quote_companies (id_company, nit, company_name, address, phone, city) 
                                          VALUES (:id_company, :nit, :company_name, :addr, :phone, :city)");
            $stmt->execute([
                'id_company' => $id_company,
                'nit' => $dataCompany['nit'],
                'company_name' => $dataCompany['companyName'],
                'addr' => $dataCompany['address'],
                'phone' => $dataCompany['phone'],
                'city' => $dataCompany['city'],
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateCompany($dataCompany)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE quote_companies SET nit = :nit, company_name = :company_name, address = :addr, phone = :phone, city = :city
                                          WHERE id_quote_company = :id_quote_company");
            $stmt->execute([
                'id_quote_company' => $dataCompany['idCompany'],
                'nit' => $dataCompany['nit'],
                'company_name' => $dataCompany['companyName'],
                'addr' => $dataCompany['address'],
                'phone' => $dataCompany['phone'],
                'city' => $dataCompany['city']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM quote_companies WHERE id_quote_company = :id_quote_company");
            $stmt->execute(['id_quote_company' => $id_company]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM quote_companies WHERE id_quote_company = :id_quote_company");
                $stmt->execute(['id_quote_company' => $id_company]);
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 23000)
                $error = array('info' => true, 'message' => 'No se puede eliminar la compañia, existe información asociada a ella');
            else {
                $message = $e->getMessage();
                $error = array('info' => true, 'message' => $message);
            }
            return $error;
        }
    }
}
