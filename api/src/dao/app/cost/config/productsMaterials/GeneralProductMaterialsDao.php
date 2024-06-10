<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsmaterials($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.reference AS reference_material, m.material, IFNULL(mg.magnitude, '') AS magnitude, IFNULL(u.unit, '') AS unit, IFNULL(u.abbreviation, '') AS abbreviation, pm.quantity, m.cost AS cost_material, 
                                             pm.cost AS cost_product_material, pm.id_product_material, m.id_material, IFNULL(mg.id_magnitude, 0) AS id_magnitude, pm.id_unit, 'Material' AS type, pm.waste, ((pm.cost / pc.cost_materials)* 100) AS participation
                                      FROM products p
                                      	INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        LEFT JOIN convert_units u ON u.id_unit = pm.id_unit
                                        LEFT JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                        LEFT JOIN composite_products cp ON cp.id_product = p.id_product
                                      WHERE pm.id_company = :id_company
                                        -- AND pm.id_material IN (SELECT id_material FROM materials INNER JOIN convert_units ON convert_units.id_unit = materials.unit WHERE id_material = pm.id_material)
                                        AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    public function findDataBasicProductsMaterials($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.reference AS reference_product, p.product, m.id_material
                                      FROM products p 
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product 
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE pm.id_company = :id_company AND p.active = 1");
        $stmt->execute(['id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    // Consultar datos product_material
    public function findProductMaterialByIdProduct($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials WHERE id_product = :id_product");
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

    public function updateUnitProductMaterial($dataProductMaterial)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_materials SET id_unit = :id_unit WHERE id_product_material = id_product_material");
            $stmt->execute([
                'id_unit' => $dataProductMaterial['id_unit'],
                'id_product_material' => $dataProductMaterial['id_product_material']
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }
}
