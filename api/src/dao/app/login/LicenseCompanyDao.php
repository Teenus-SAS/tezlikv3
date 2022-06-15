<?php

namespace tezlikv2\dao;

use DateTime;
use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LicenseCompanyDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findLicense($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cl.license_end 
                                  FROM companies_licenses cl WHERE cl.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $licenseData = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("licenses get", array('licenses' => $licenseData));

        $today = date('Y-m-d');
        $licenseDay = $licenseData[0]['license_end'];
        $today < $licenseDay ? $license = 1 : $license = 0;
        return $license;
    }

    public function insertLicenseCompanyByCompany($dataLicenseCompany, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {

            if (empty($dataLicenseCompany['idRegisterUser'])) {

                $licenseStart = date('Y-m-d');
                $licenseEnd = date("Y-m-d", strtotime($licenseStart . "+ 30 day"));

                $stmt = $connection->prepare("INSERT INTO companies_licenses (id_company, license_start, quantity_user, status)
                                          VALUES (:id_company, :license_start, :quantity_user, :status)");
                $stmt->execute([
                    'id_company' => $id_company,
                    'license_start' => $licenseStart,
                    'license_end' => $licenseEnd,
                    'quantity_user' => 1,
                    'status' => 1
                ]);
            } else {
                $stmt = $connection->prepare("INSERT INTO companies_licenses (id_company, license_start, quantity_user, status)
                                          VALUES (:id_company, :license_start, :quantity_user, :status)");
                $stmt->execute([
                    'id_company' => $id_company,
                    'license_start' => $dataLicenseCompany['licenseStart'],
                    'license_end' => $dataLicenseCompany['licenseEnd'],
                    'quantity_user' => $dataLicenseCompany['quantityUser'],
                    'status' => 1
                ]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateLicenseCompany($dataLicenseCompany)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET license_start = :license_start, license_end = :license_end, quantity_user = :quantity_user, status = :status
                                          WHERE id_company_license = :id_company_license");
            $stmt->execute([
                'id_company_license' => $dataLicenseCompany['idCompanyLicense'],
                'license_start' => $dataLicenseCompany['licenseStart'],
                'license_end' => $dataLicenseCompany['licenseEnd'],
                'quantity_user' => $dataLicenseCompany['quantityUser'],
                'status' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteLicenseCompany($id_company_license)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM companies_licenses WHERE id_company_license = :id_company_license");
        $stmt->execute(['id_company_license' => $id_company_license]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM companies_licenses WHERE id_company_license = :id_company_license");
            $stmt->execute(['id_company_license' => $id_company_license]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
