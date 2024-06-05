<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsmaterialsByIdProduct($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.reference AS reference_material, m.material, IFNULL(mg.magnitude, '') AS magnitude, IFNULL(u.unit, '') AS unit, IFNULL(u.abbreviation, '') AS abbreviation, pm.quantity, m.cost, m.cost_total, m.cost AS cost_material, 
                                             pm.cost AS cost_product_material, pm.cost_usd AS cost_product_material_usd, pm.id_product_material, m.id_material, IFNULL(mg.id_magnitude, 0) AS id_magnitude, pm.id_unit, 'MATERIAL' AS type, pm.waste, ((pm.cost / pc.cost_materials)* 100) AS participation
                                      FROM products p
                                      	INNER JOIN products_costs pc ON pc.id_product = p.id_product
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN companies_licenses cl ON cl.id_company = p.id_company
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        LEFT JOIN convert_units u ON u.id_unit = pm.id_unit
                                        LEFT JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                        LEFT JOIN composite_products cp ON cp.id_product = p.id_product
                                      WHERE pm.id_company = :id_company AND pm.id_product = :id_product");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    // Consultar si existe el product_material en la BD
    public function findProductMaterial($dataProductMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_product_material FROM products_materials
                                      WHERE id_product = :id_product AND id_material = :id_material AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataProductMaterial['idProduct'],
            'id_material' => $dataProductMaterial['material'],
            'id_company' => $id_company
        ]);
        $findProductMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProductMaterial;
    }

    public function findProductMaterialAndUnits($id_product, $id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_material, m.reference, m.material, mg.id_magnitude, 
                                                mg.magnitude, u.id_unit, u.unit, u.abbreviation, pm.quantity, m.cost 
                                      FROM products_materials pm
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN convert_units u ON u.id_unit = pm.id_unit
                                        INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE pm.id_product = :id_product AND pm.id_material = :id_material");
        $stmt->execute([
            'id_product' => $id_product,
            'id_material' => $id_material
        ]);
        $findProductMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProductMaterial;
    }

    // Insertar productos materia prima
    public function insertProductsMaterialsByCompany($dataProductMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO products_materials (id_material, id_unit, id_company, id_product, quantity, waste)
                                          VALUES (:id_material, :id_unit, :id_company, :id_product, :quantity, :waste)");
            $stmt->execute([
                'id_material' => $dataProductMaterial['material'],
                'id_unit' => $dataProductMaterial['unit'],
                'id_company' => $id_company,
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),
                'waste' => trim($dataProductMaterial['waste']),
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Actualizar productos materia prima general
    public function updateProductsMaterials($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_materials SET id_material = :id_material, id_unit = :id_unit,
                                                 id_product = :id_product, quantity = :quantity, waste = :waste
                                          WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $dataProductMaterial['idProductMaterial'],
                'id_material' => $dataProductMaterial['material'],
                'id_unit' => $dataProductMaterial['unit'],
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),
                'waste' => trim($dataProductMaterial['waste']),
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Borrar productos materia prima general
    public function deleteProductMaterial($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials WHERE id_product_material = :id_product_material");
        $stmt->execute(['id_product_material' => $dataProductMaterial['idProductMaterial']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_materials WHERE id_product_material = :id_product_material");
            $stmt->execute(['id_product_material' => $dataProductMaterial['idProductMaterial']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
