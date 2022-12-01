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

    public function findAllCompanies()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM quote_companies");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

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
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
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
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function findLastInsertedQCompany()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_quote_company) AS id_quote_company FROM quote_companies");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $company = $stmt->fetch($connection::FETCH_ASSOC);
        return $company;
    }

    public function imageQCompany($id_q_company, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(dirname(__DIR__)))) . '/assets/images/Qcompanies/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = $_FILES['img']['name'];
        $tmp_name   = $_FILES['img']['tmp_name'];
        $size       = $_FILES['img']['size'];
        $type       = $_FILES['img']['type'];
        $error      = $_FILES['img']['error'];


        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/api/src/assets/images/Qcompanies/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE quote_companies SET img = :img WHERE id_quote_company = :id_quote_company AND id_company = :id_company";
            $query = $connection->prepare($sql);
            $query->execute([
                'img' => $targetFilePath,
                'id_quote_company' => $id_q_company,
                'id_company' => $id_company
            ]);

            $targetDir = dirname(dirname(dirname(dirname(__DIR__)))) . '/assets/images/Qcompanies/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
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
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
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
