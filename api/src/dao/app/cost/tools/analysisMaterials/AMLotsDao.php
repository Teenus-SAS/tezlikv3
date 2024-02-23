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
            // if (sizeof($products) > 0){}
            //     $products = implode(',', $products);
            // else
            //     $products = $products[0];
            // $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, m.cost, SUM(pm.cost) AS cost_product_material,
            //                                      CONCAT(FORMAT(SUM(ROUND(pm.quantity, 2)), 2, 'de_DE'), ' ', u.abbreviation) AS quantity, u.abbreviation AS abbreviation_material, (SELECT cu.abbreviation FROM materials cm INNER JOIN convert_units cu ON cu.id_unit = cm.unit WHERE cm.id_material = m.id_material) AS abbreviation_product_material -- , (SUM(ROUND(pm.quantity, 2))*pm.cost) AS unityCost
            //                               FROM products p
            //                                 INNER JOIN products_materials pm ON pm.id_product = p.id_product
            //                                 INNER JOIN materials m ON m.id_material = pm.id_material
            //                                 INNER JOIN convert_units u ON u.id_unit = pm.id_unit
            //                               WHERE p.id_product IN ($products) AND p.id_company = :id_company GROUP BY pm.id_material");
            // $stmt->execute(['id_company' => $id_company]); CONCAT(FORMAT(ROUND(pm.quantity, 2), 2, 'de_DE'), ' ', u.abbreviation) AS quantity,
            $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, m.cost, pm.cost AS cost_product_material,
                                                 pm.quantity AS quantity1, u.abbreviation AS abbreviation_material, (SELECT cu.abbreviation FROM materials cm INNER JOIN convert_units cu ON cu.id_unit = cm.unit WHERE cm.id_material = m.id_material) AS abbreviation_product_material -- , (SUM(ROUND(pm.quantity, 2))*pm.cost) AS unityCost
                                          FROM products p
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN materials m ON m.id_material = pm.id_material
                                            INNER JOIN convert_units u ON u.id_unit = pm.id_unit
                                          WHERE p.id_product IN ($products) AND p.id_company = :id_company");
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

    public function groupDataLots($data, $products)
    {
        try {
            for ($i = 0; $i < sizeof($products); $i++) {
                for ($j = 0; $j < sizeof($data); $j++) {
                    if ($products[$i]['id_product'] == $data[$j]['id_product']) {
                        // $data[$j]['total_quantity'] = $data[$j]['quantity1'] * $products[$i]['unit'] . " " . $data[$j]['abbreviation_material'];
                        $data[$j]['total_quantity'] = $data[$j]['quantity1'] * $products[$i]['unit'];
                    }
                }
            }

            // Consolidar referencias
            $lots = array();

            foreach ($data as $t) {
                $repeat = false;
                for ($i = 0; $i < count($lots); $i++) {
                    if ($lots[$i]['id_material'] == $t['id_material']) {
                        $lots[$i]['quantity1'] += $t['quantity1'];
                        $lots[$i]['quantity'] = $lots[$i]['quantity1'] . " " . $t['abbreviation_material'];
                        $lots[$i]['cost_product_material'] += $t['cost_product_material'];
                        $repeat = true;
                        break;
                    }
                }
                if ($repeat == false) {
                    $lots[] = $t;
                    $lots[count($lots) - 1]['quantity'] = $data[$i]['quantity1'] . " " . $data[$i]['abbreviation_material'];
                }
            }

            return $lots;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }

    public function calcTotalUnityCost($dataMaterial)
    {
        $totalUnits = 0;

        foreach ($dataMaterial as $arr) {
            $totalUnits += $arr['cost_product_material'];
        }

        return $totalUnits;
    }

    public function calcAndSetParticipation($dataMaterial, $totalUnits)
    {
        for ($i = 0; $i < sizeof($dataMaterial); $i++) {
            /* Calculo participacion */
            $dataMaterial[$i]['participation'] = ($dataMaterial[$i]['cost_product_material'] / $totalUnits) * 100;
        }

        return $dataMaterial;
    }
}
