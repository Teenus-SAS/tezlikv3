<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostProductMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Consultar datos product_material
    public function findProductMaterialByIdProduct($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_material, FORMAT(quantity, 2, 'de_DE') AS quantity
                                      FROM products_materials WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $dataProductMaterial['idOldProduct']
        ]);
        $findProductMaterial = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $findProductMaterial;
    }

    public function deleteProductMaterialByProduct($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $dataProductMaterial['idProduct']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_materials WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $dataProductMaterial['idProduct']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
