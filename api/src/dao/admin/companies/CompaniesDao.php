<?php

namespace tezlikv2\dao;

use DateTime;
use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompaniesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //Obtener todas las empresas activas / inactivas
    public function findAllCompanies($stat)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT cp.id_company, cp.company, cp.state, cp.city, cp.country, cp.address,
                                      cp.telephone, cp.nit, cp.logo, cp.created_at, cp.creador FROM companies cp 
                                      INNER JOIN companies_licenses cl ON cp.id_company = cl.id_company 
                                      WHERE cl.status = :stat");
        $stmt->execute(['stat' => $stat]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companyData = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("AllCompanies", array('AllCompanies' => $companyData));

        return $companyData;
    }

    //Agregar Empresa
    public function addCompany($dataCompany)
    {
        $connection = Connection::getInstance()->getConnection();
        try {

            $stmt = $connection->prepare("INSERT INTO companies (company, state, city, country, address, telephone, nit, logo, created_at, creador)
                                          VALUES (:company, :state, :city, :country, :address, :telephone, :nit, :logo, :created_at, :creador)");
            $stmt->execute([
                'company' => $dataCompany['company'],              'state' => $dataCompany['companyState'],
                'city' => $dataCompany['companyCity'],             'country' => $dataCompany['companyCountry'],
                'address' => $dataCompany['companyAddress'],       'telephone' => $dataCompany['companyTel'],
                'nit' => $dataCompany['companyNIT'],               'logo' => $dataCompany['companyLogo'],
                'created_at' => $dataCompany['companyCreated_at'], 'creador' => $dataCompany['companyCreator']
            ]);
            $stmt = $connection->prepare("SELECT MAX(id_company) AS idCompany FROM companies");
            $stmt->execute();
            $lastId = $stmt->fetch($connection::FETCH_ASSOC);

            return $lastId;
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    //Actualizar Empresa
    public function updateCompany($dataCompany)
    {
        $connection = Connection::getInstance()->getConnection();
        try {

            $stmt = $connection->prepare("UPDATE companies SET company = :company, state = :state, city = :city,
                                          country = :country, address = :address, telephone = :telephone, nit = :nit,
                                          logo = :logo, created_at = :created_at, creador = :creador
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'company' => $dataCompany['company'],              'state' => $dataCompany['companyState'],
                'city' => $dataCompany['companyCity'],             'country' => $dataCompany['companyCountry'],
                'address' => $dataCompany['companyAddress'],       'telephone' => $dataCompany['companyTel'],
                'nit' => $dataCompany['companyNIT'],               'logo' => $dataCompany['companyLogo'],
                'created_at' => $dataCompany['companyCreated_at'], 'creador' => $dataCompany['companyCreator'],
                'id_company' => $dataCompany['id_company'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
