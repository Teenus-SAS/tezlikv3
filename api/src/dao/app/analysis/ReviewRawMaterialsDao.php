<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ReviewRawMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function productsRawMaterials($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT SUM(pm.quantity*m.cost) AS totalCost
                                      FROM products_materials pm  
                                      INNER JOIN materials m ON m.id_material = pm.id_material 
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $dataTotalCost = $stmt->fetch($connection::FETCH_ASSOC);

        $totalCost = $dataTotalCost['totalCost'];
        $stmt = $connection->prepare("SELECT 
                                        pm.id_product_material, m.id_material, m.reference, m.material, pm.quantity, m.cost, 
                                        (pm.quantity*m.cost) AS unityCost,((pm.quantity*m.cost)/{$totalCost})*100 AS participation 
                                      FROM products p
                                      INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                      INNER JOIN materials m ON m.id_material = pm.id_material 
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company ORDER BY `participation` DESC");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $productsRawmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("products", array('products' => $productsRawmaterials));
        return $productsRawmaterials;
    }
}
