<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Consultar datos del prodcuto en la BD
    public function findProductProcessByIdProduct($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $dataProductProcess['idOldProduct']
        ]);
        $findProductProcess = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $findProductProcess;
    }

    public function deleteProductProcessByProduct($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $dataProductProcess['idProduct']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_process WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataProductProcess['idProduct']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
