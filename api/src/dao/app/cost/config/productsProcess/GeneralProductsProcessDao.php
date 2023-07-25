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

    public function findAllProductsprocess($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.reference, p.product, pp.enlistment_time, pp.operation_time, IFNULL(mc.machine, 'PROCESO MANUAL') AS machine, pc.process
                                  FROM products p 
                                  INNER JOIN products_process pp ON pp.id_product = p.id_product
                                  LEFT JOIN machines mc ON mc.id_machine = pp.id_machine 
                                  INNER JOIN process pc ON pc.id_process = pp.id_process
                                  WHERE p.id_company = :id_company ORDER BY pp.id_machine ASC");
        $stmt->execute(['id_company' => $id_company]);
        $productsprocess = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsprocess));
        return $productsprocess;
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

    public function findProductProcessByIdMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_machine = :id_machine");
        $stmt->execute([
            'id_machine' => $id_machine
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
