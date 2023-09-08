<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AMProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsRawMaterials($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, CONCAT(FORMAT(pm.quantity, 2, 'de_DE'), ' ', u.abbreviation) AS quantity, u.abbreviation AS abbreviation_material, (SELECT cu.abbreviation FROM materials cm INNER JOIN convert_units cu ON cu.id_unit = cm.unit WHERE cm.id_material = m.id_material) AS abbreviation_product_material, pm.cost AS cost_product_material,
                                                    m.cost, pm.cost AS unityCost, pm.cost AS totalCostMaterial,((pm.cost)/ (SELECT SUM(pm.cost) FROM products_materials pm INNER JOIN materials m ON m.id_material = pm.id_material WHERE pm.id_product = p.id_product AND pm.id_company = p.id_company))*100 AS participation 
                                            FROM products p 
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN materials m ON m.id_material = pm.id_material
                                            INNER JOIN convert_units u ON u.id_unit = pm.id_unit
                                          WHERE pm.id_product = :id_product AND pm.id_company = :id_company 
                                          ORDER BY `participation` ASC");
            $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
            $productsRawmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        } catch (\Exception $e) {
            if ($e->getCode() == 42000)
                $error = array('info' => true, 'message' => 'No hay ninguna relacion con el producto');
            else {
                $message = $e->getMessage();
                $error = array('info' => true, 'message' => $message);
            }
            return $error;
        }
        $this->logger->notice("products", array('products' => $productsRawmaterials));
        return $productsRawmaterials;
    }

    public function orderDataMaterial($dataMaterial)
    {
        foreach ($dataMaterial as $key => $row) {
            $participation[$key]  = $row['participation'];
        }

        array_multisort($participation, SORT_DESC, $dataMaterial);

        return $dataMaterial;
    }
}
