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
            $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, CONCAT(FORMAT(pm.quantity, 2, 'de_DE'), ' ', u.abbreviation) AS quantity, 
                                                 m.cost, (pm.quantity*m.cost) AS unityCost,((pm.quantity*m.cost)/ (SELECT SUM(pm.quantity*m.cost) FROM products_materials pm INNER JOIN materials m ON m.id_material = pm.id_material WHERE pm.id_product = p.id_product AND pm.id_company = p.id_company))*100 AS participation 
                                          FROM products p 
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN materials m ON m.id_material = pm.id_material
                                            INNER JOIN convert_units u ON u.id_unit = pm.id_unit
                                          WHERE pm.id_product = :id_product AND pm.id_company = :id_company 
                                          ORDER BY `participation` DESC");
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

    public function findConsolidatedRawMaterialsByProduct($products, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if (sizeof($products) > 0)
                $products = implode(',', $products);
            else
                $products = $products[0];

            $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, 
                                                 CONCAT(FORMAT((SELECT SUM(quantity) FROM products_materials WHERE id_product IN($products) AND id_material = pm.id_material), 2, 'de_DE'), ' ', u.abbreviation) AS quantity, 
                                                 m.cost, ((SELECT SUM(quantity) FROM products_materials WHERE id_product IN($products) AND id_material = pm.id_material)*m.cost) AS unityCost,
                                                 (((SELECT SUM(quantity) FROM products_materials WHERE id_product IN($products) AND id_material = pm.id_material)*m.cost)/ 
                                                 (SELECT DISTINCT SUM((SELECT SUM(quantity) FROM products_materials WHERE id_product IN($products) AND id_material = cpm.id_material)*cm.cost) 
                                                 FROM products_materials cpm INNER JOIN materials cm ON cm.id_material = cpm.id_material WHERE cpm.id_product = p.id_product AND cpm.id_company = p.id_company))*100 AS participation 
                                          FROM products p 
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN materials m ON m.id_material = pm.id_material
                                            INNER JOIN convert_units u ON u.id_unit = pm.id_unit
                                          WHERE pm.id_product IN ($products) AND pm.id_company = :id_company GROUP BY PM.id_material ORDER BY `m`.`id_material` ASC");
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
}
