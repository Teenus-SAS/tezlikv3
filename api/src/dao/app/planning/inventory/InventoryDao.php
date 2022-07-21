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

    // Contar cantidad productos
    public function countProduct($dataInventory, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT COUNT(*) AS cant_products_materials 
                                      FROM products p 
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                      WHERE p.reference = :reference AND p.id_company = :id_company");
        $stmt->execute([
            'reference' => $dataInventory['refProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetch($connection::FETCH_ASSOC);

        return $products;
    }

    // Contar cantidad Materiales o Insumos
    public function countMaterialsAndSupplies($dataInventory, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT COUNT(*)
                                      FROM materials m
                                        INNER JOIN products_materials pm ON pm.id_material = m.id_material
                                      WHERE m.reference = :reference AND m.id_company = :id_company AND m.category = :category");
        $stmt->execute([
            'reference' => $dataInventory['refRawMaterial'],
            'category' => $dataInventory['category'],
            'id_company' => $id_company,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $inventory = $stmt->fetch($connection::FETCH_ASSOC);
        return $inventory;
    }

    // Validar materiales de producto
    public function findMaterialsByProduct($dataInventory, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // Obtener referencias materiales 
        foreach ($dataInventory as $value) {
            if ($dataInventory['idMaterial'])
                $idMaterials = $value;
        }

        $stmt = $connection->prepare("SELECT pm.id_material, m.reference, m.material, m.category, m.quantity
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE p.reference = :reference AND p.id_company = :id_company AND m.reference NOT IN (:id_material)");
        $stmt->execute([
            'reference' => $dataInventory[0]['refProduct'],
            'id_company' => $id_company,
            'id_material' => $idMaterials
        ]);

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $materials;
    }
}
