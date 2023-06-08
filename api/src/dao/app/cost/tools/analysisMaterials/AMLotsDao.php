<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AMLotsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findConsolidatedRawMaterialsByProduct($products, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if (sizeof($products) > 0)
                $products = implode(',', $products);
            else
                $products = $products[0];
            $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, m.cost, pm.cost AS cost_product_material,
                                                 CONCAT(FORMAT(SUM(ROUND(pm.quantity, 2)), 2, 'de_DE'), ' ', u.abbreviation) AS quantity, u.abbreviation AS abbreviation_material, (SELECT cu.abbreviation FROM materials cm INNER JOIN convert_units cu ON cu.id_unit = cm.unit WHERE cm.id_material = m.id_material) AS abbreviation_product_material, (SUM(ROUND(pm.quantity, 2))*pm.cost) AS unityCost
                                          FROM products p
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN materials m ON m.id_material = pm.id_material
                                            INNER JOIN convert_units u ON u.id_unit = pm.id_unit
                                          WHERE p.id_product IN ($products) AND p.id_company = :id_company GROUP BY pm.id_material");
            $stmt->execute(['id_company' => $id_company]);
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

    public function calcTotalUnityCost($dataMaterial)
    {
        $totalUnits = 0;

        foreach ($dataMaterial as $arr) {
            $totalUnits += $arr['unityCost'];
        }

        return $totalUnits;
    }

    public function calcAndSetParticipation($dataMaterial, $totalUnits)
    {
        for ($i = 0; $i < sizeof($dataMaterial); $i++) {
            /* Calculo participacion */
            $dataMaterial[$i]['participation'] = ($dataMaterial[$i]['unityCost'] / $totalUnits) * 100;
        }

        return $dataMaterial;
    }
}
