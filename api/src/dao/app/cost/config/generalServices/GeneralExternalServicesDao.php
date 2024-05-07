<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralExternalServicesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllExternalServices($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM general_external_services WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $externalservices = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $externalservices));
        return $externalservices;
    }

    // Consultar si existe el servicio en BD
    public function findExternalService($dataExternalService, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM general_external_services
                                      WHERE name_service = :name_service AND id_company = :id_company");
        $stmt->execute([
            'name_service' => strtoupper(trim($dataExternalService['service'])),
            'id_company' => $id_company
        ]);
        $findExternalService = $stmt->fetch($connection::FETCH_ASSOC);

        return $findExternalService;
    }

    public function insertExternalServicesByCompany($dataExternalService, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO general_external_services (name_service, cost, id_company)
                                          VALUES(:name_service, :cost, :id_company)");
            $stmt->execute([
                'name_service' => strtoupper(trim($dataExternalService['service'])),
                'cost' => $dataExternalService['costService'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Servicio duplicado. Ingrese una nuevo servicio';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateExternalServices($dataExternalService)
    {
        $connection = Connection::getInstance()->getConnection();
        // $costService = str_replace('.', '', $dataExternalService['costService']);

        try {
            $stmt = $connection->prepare("UPDATE general_external_services SET name_service = :name_service, cost = :cost
                                          WHERE id_general_service = :id_general_service");
            $stmt->execute([
                'name_service' => strtoupper(trim($dataExternalService['service'])),
                'cost' => $dataExternalService['costService'],
                'id_general_service' => $dataExternalService['idService']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteExternalService($idService)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM general_external_services WHERE id_general_service = :id_general_service");
        $stmt->execute(['id_general_service' => $idService]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM general_external_services WHERE id_general_service = :id_general_service");
            $stmt->execute(['id_general_service' => $idService]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
