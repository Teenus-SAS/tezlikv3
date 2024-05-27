<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralServicesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllExternalServicesByIdProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT sx.id_service, gs.id_general_service, p.id_product, p.reference, p.product, gs.name_service, sx.cost, sx.id_product 
                                        FROM services sx 
                                        INNER JOIN products p ON sx.id_product = p.id_product
                                        LEFT JOIN general_external_services gs ON gs.id_general_service = sx.name_service
                                        WHERE sx.id_company = :id_company AND sx.id_product = :id_product 
                                        ORDER BY sx.name_service ASC;");
        $stmt->execute(['id_product' => trim($id_product), 'id_company' => $id_company]);
        $externalservices = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $externalservices));
        return $externalservices;
    }

    // Consultar si existe el servicio en BD
    public function findExternalService($dataExternalService, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_service FROM services
                                      WHERE id_product = :id_product AND name_service = :name_service AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataExternalService['idProduct'],
            'name_service' => strtoupper(trim($dataExternalService['service'])),
            'id_company' => $id_company
        ]);
        $findExternalService = $stmt->fetch($connection::FETCH_ASSOC);

        return $findExternalService;
    }

    // Consultar si existe el servicio en BD en servicio general
    // public function findExternalService($dataExternalService, $id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     $stmt = $connection->prepare("SELECT id_service FROM services
    //                                   WHERE id_product = :id_product AND name_service = :name_service AND id_company = :id_company");
    //     $stmt->execute([
    //         'id_product' => $dataExternalService['idProduct'],
    //         'name_service' => strtoupper(trim($dataExternalService['service'])),
    //         'id_company' => $id_company
    //     ]);
    //     $findExternalService = $stmt->fetch($connection::FETCH_ASSOC);

    //     return $findExternalService;
    // }

    // Consultar el servicio en BD
    public function findExternalServiceByIdProduct($dataExternalService)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM services WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $dataExternalService['idOldProduct']
        ]);
        $findExternalService = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $findExternalService;
    }

    public function deleteExternalServiceByProduct($dataExternalService)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM services WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $dataExternalService['idProduct']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM services WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataExternalService['idProduct']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
