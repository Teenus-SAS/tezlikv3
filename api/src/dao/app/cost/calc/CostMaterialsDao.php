<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Buscar producto por el idMaterial
    public function findProductByMaterial($idMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_product 
                                      FROM products p
                                      INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        WHERE pm.id_material =:id_material AND p.id_company = :id_company");
        $stmt->execute(['id_material' => $idMaterial, 'id_company' => $id_company]);
        $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $dataProduct;
    }

    public function calcCostMaterial($dataMaterials, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT SUM(pm.cost) as cost 
                                        FROM materials m 
                                        INNER JOIN products_materials pm ON pm.id_material = m.id_material 
                                        WHERE m.id_company = :id_company AND pm.id_product = :id_product");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataMaterials['idProduct']
            ]);
            $costMaterialsProduct = $stmt->fetch($connection::FETCH_ASSOC);

            $dataMaterials['cost'] = $costMaterialsProduct['cost'];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dataMaterials = array('info' => true, 'message' => $message);
        }

        return $dataMaterials;
    }

    public function calcCostMaterialByCompositeProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT IFNULL((SUM(cp.cost) + (SELECT SUM(cost) FROM products_materials WHERE id_product = cp.id_product)), 0) AS cost
                                          FROM composite_products cp 
                                          WHERE cp.id_product = :id_product -- AND cp.id_child_product = :id_child_product");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
                // 'id_child_product' => $dataProduct['compositeProduct']
            ]);
            $costMaterialsProduct = $stmt->fetch($connection::FETCH_ASSOC);

            $dataProduct['cost'] = $costMaterialsProduct['cost'];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dataProduct = array('info' => true, 'message' => $message);
        }

        return $dataProduct;
    }

    public function updateCostMaterials($dataMaterials, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_costs SET cost_materials = :materials
                                         WHERE id_product = :id_product AND id_company = :id_company");
            $stmt->execute([
                'materials' => $dataMaterials['cost'],
                'id_product' => $dataMaterials['idProduct'],
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
