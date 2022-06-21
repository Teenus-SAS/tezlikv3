<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompaniesLicenseDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //Obtener datos de licencia de empresas activas
    public function findCompanyLicenseActive()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cp.id_company, cp.nit, cp.company, cl.license_start, cl.license_end,
                                      cl.quantity_user, cl.status, CASE WHEN cl.license_end > CURRENT_DATE
                                      THEN TIMESTAMPDIFF(DAY, CURRENT_DATE, license_end) ELSE 0 END license_days
                                      FROM companies cp INNER JOIN companies_licenses cl ON cp.id_company = cl.id_company");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $licenses = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("licenses", array('licenses' => $licenses));

        return $licenses;
    }

    //Agregar Licencia
    public function addLicense($dataLicense, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {

            $stmt = $connection->prepare("INSERT INTO companies_licenses (id_company, license_start, license_end, quantity_user, status)
                                          VALUES (:id_company, :license_start, :license_end, :quantity_user, :status)");
            $stmt->execute([
                'id_company' => $id_company,
                'license_start' => $dataLicense['companyLic_start'],
                'license_end' => $dataLicense['companyLic_end'],
                'quantity_user' => $dataLicense['companyUsers'],
                'status' => $dataLicense['companyStatus'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    //Actualizar Licencia
    public function updateLicense($dataLicense)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET license_start = :license_start, license_end = :license_end,
                                          quantity_user = :quantity_user WHERE id_company = :id_company");
            $stmt->execute([
                'license_start' => $dataLicense['license_start'],
                'license_end' => $dataLicense['license_end'],
                'quantity_user' => $dataLicense['quantityUsers'],
                'id_company' => $dataLicense['id_company'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    //Modificar costos o planeación
    public function addCostOrPlanning($dataLicenseCompany, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET cost = :cost, planning = :planning
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company,
                'cost' => $dataLicenseCompany['cost'],
                'planning' => $dataLicenseCompany['planning']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
