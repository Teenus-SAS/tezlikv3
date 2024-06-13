<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExternalServicesDao
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
        $stmt = $connection->prepare("SELECT sx.id_service, sx.id_general_service, p.id_product, p.reference, p.product, IFNULL(gs.name_service, sx.name_service) AS name_service, sx.cost, sx.id_product 
                                        FROM services sx 
                                        INNER JOIN products p ON sx.id_product = p.id_product
                                        LEFT JOIN general_external_services gs ON CAST(gs.id_general_service AS CHAR) = sx.name_service
                                        WHERE sx.id_company = :id_company AND p.active = 1
                                        ORDER BY sx.name_service ASC");
        $stmt->execute(['id_company' => $id_company]);
        $externalservices = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $externalservices));
        return $externalservices;
    }

    public function insertExternalServicesByCompany($dataExternalService, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        // $costService = str_replace('.', '', $dataExternalService['costService']);

        try {
            $stmt = $connection->prepare("INSERT INTO services (id_product, id_company, name_service, cost)
                                          VALUES (:id_product, :id_company, :name_service, :cost)");
            $stmt->execute([
                'id_product' => $dataExternalService['idProduct'],
                // 'id_general_service' => $dataExternalService['idGService'],
                'name_service' => strtoupper(trim($dataExternalService['idGService'])),
                'cost' => $dataExternalService['costService'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // if ($e->getCode() == 23000)
            //     $message = 'Servicio duplicado. Ingrese una nuevo servicio';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateExternalServices($dataExternalService)
    {
        $connection = Connection::getInstance()->getConnection();
        // $costService = str_replace('.', '', $dataExternalService['costService']);

        try {
            $stmt = $connection->prepare("UPDATE services SET id_product = :id_product, name_service = :name_service, cost = :cost 
                                          WHERE id_service = :id_service");
            $stmt->execute([
                'id_product' => $dataExternalService['idProduct'],
                // 'id_general_service' => $dataExternalService['idGService'],
                'name_service' => strtoupper(trim($dataExternalService['idGService'])),
                'cost' => $dataExternalService['costService'],
                'id_service' => $dataExternalService['idService']
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

        $stmt = $connection->prepare("SELECT * FROM services WHERE id_service = :id_service");
        $stmt->execute(['id_service' => $idService]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM services WHERE id_service = :id_service");
            $stmt->execute(['id_service' => $idService]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
