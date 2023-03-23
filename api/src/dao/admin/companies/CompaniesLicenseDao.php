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
                                        cl.quantity_user, cl.license_status, CASE WHEN cl.license_end > CURRENT_DATE
                                        THEN TIMESTAMPDIFF(DAY, CURRENT_DATE, license_end) ELSE 0 END license_days, cl.plan, cl.cost_price_usd
                                      FROM companies cp 
                                       INNER JOIN companies_licenses cl ON cp.id_company = cl.id_company");
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
            if (empty($dataLicense['license_start'])) {
                $licenseStart = date('Y-m-d');
                $licenseEnd = date("Y-m-d", strtotime($licenseStart . "+ 30 day"));

                $stmt = $connection->prepare("INSERT INTO companies_licenses (id_company, license_start, license_end, quantity_user, license_status, plan, cost, planning)
                                              VALUES (:id_company, :license_start, :license_end, :quantity_user, :license_status, :plan, :cost, :planning)");
                $stmt->execute([
                    'id_company' => $id_company,
                    'license_start' => $licenseStart,
                    'license_end' => $licenseEnd,
                    'quantity_user' => 1,
                    'license_status' => 1,
                    'plan' => 4,
                    'cost' => 1,
                    'planning' => 1
                ]);
            } else {
                $stmt = $connection->prepare("INSERT INTO companies_licenses (id_company, license_start, license_end, quantity_user, license_status, plan, cost_price_usd)
                                          VALUES (:id_company, :license_start, :license_end, :quantity_user, :license_status, :plan, :cost_price_usd)");
                $stmt->execute([
                    'id_company' => $id_company,
                    'license_start' => $dataLicense['license_start'],
                    'license_end' => $dataLicense['license_end'],
                    'quantity_user' => $dataLicense['quantityUsers'],
                    'license_status' => 1,
                    'plan' => $dataLicense['plan'],
                    'cost_price_usd' => $dataLicense['pricesUSD']
                ]);
            }

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Compañia duplicada, ingrese otra compañia';

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
                                                 quantity_user = :quantity_user, plan = :plan, cost_price_usd = :cost_price_usd
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'license_start' => $dataLicense['license_start'],
                'license_end' => $dataLicense['license_end'],
                'quantity_user' => $dataLicense['quantityUsers'],
                'plan' => $dataLicense['plan'],
                'id_company' => $dataLicense['company'],
                'cost_price_usd' => $dataLicense['pricesUSD']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
