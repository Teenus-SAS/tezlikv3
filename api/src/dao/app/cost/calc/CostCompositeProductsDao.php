<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CostCompositeProductsDao
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

    public function calcCostCompositeProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT IFNULL(pc.cost_indirect_cost + SUM(pp.indirect_cost), 0) AS cost_indirect_cost, IFNULL(pc.cost_workforce + SUM(pp.workforce_cost), 0) AS workforce_cost
                                          FROM composite_products cp 
                                          INNER JOIN products_process pp ON pp.id_product = cp.id_child_product
                                          INNER JOIN products_costs pc ON pc.id_product = cp.id_product
                                          WHERE cp.id_product = :id_product;");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
            ]);
            $costMaterialsProduct = $stmt->fetch($connection::FETCH_ASSOC);

            $dataProduct['cost_indirect_cost'] = $costMaterialsProduct['cost_indirect_cost'];
            $dataProduct['workforce_cost'] = $costMaterialsProduct['workforce_cost'];
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
