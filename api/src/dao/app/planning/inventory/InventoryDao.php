<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class InventoryDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllInventoryMaterialsAndSupplies($id_company, $category)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM materials WHERE id_company = :id_company AND category = :category");
        $stmt->execute([
            'id_company' => $id_company,
            'category' => $category
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("materials", array('materials' => $materials));
        return $materials;
    }

    public function findProduct($dataInventory, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // Cantidad de productos en productos materias
        $stmt = $connection->prepare("SELECT COUNT(*) AS cant_products_materials 
                                      FROM products p 
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                      WHERE p.reference = :reference AND p.id_company = :id_company");
        $stmt->execute([
            'reference' => $dataInventory['refProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products_materials = $stmt->fetch($connection::FETCH_ASSOC);

        // Cantidad de productos en productos procesos
        $stmt = $connection->prepare("SELECT COUNT(*) AS cant_products_process 
                                      FROM products p 
                                        INNER JOIN products_process pp ON pp.id_product = p.id_product 
                                      WHERE p.reference = :reference AND p.id_company = :id_company");
        $stmt->execute([
            'reference' => $dataInventory['refProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products_process = $stmt->fetch($connection::FETCH_ASSOC);

        // Cantidad de productos en productos en plan ciclos maquina
        $stmt = $connection->prepare("SELECT (COUNT(*) + :products_materials + :products_process) AS products 
                                      FROM products p 
                                        INNER JOIN plan_cicles_machine pcm ON pcm.id_product = p.id_product 
                                      WHERE p.reference = :reference AND p.id_company = :id_company");
        $stmt->execute([
            'products_materials' => $products_materials['cant_products_materials'],
            'products_process' => $products_process['cant_products_process'],
            'reference' => $dataInventory['refProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetch($connection::FETCH_ASSOC);

        return $products;
    }
}
