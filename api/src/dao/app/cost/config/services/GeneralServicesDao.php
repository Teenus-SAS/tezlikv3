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
