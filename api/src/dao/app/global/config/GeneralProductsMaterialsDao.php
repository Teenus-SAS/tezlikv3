<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsmaterials($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_material, m.reference, m.material, mg.id_magnitude, 
                                             mg.magnitude, u.id_unit, u.unit, u.abbreviation, pm.quantity, m.cost 
                                      FROM products_materials pm
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN units u ON u.id_unit = pm.id_unit
                                        INNER JOIN magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company AND pm.id_material IN (SELECT id_material FROM materials INNER JOIN units ON units.id_unit = materials.unit WHERE id_material = pm.id_material)
                                      ORDER BY `m`.`material` ASC");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    // Consultar si existe el product_material en la BD
    public function findProductMaterial($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_product_material FROM products_materials
                                      WHERE id_product = :id_product AND id_material = :id_material");
        $stmt->execute([
            'id_product' => $dataProductMaterial['idProduct'],
            'id_material' => $dataProductMaterial['material']
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
                                        INNER JOIN units u ON u.id_unit = pm.id_unit
                                        INNER JOIN magnitudes mg ON mg.id_magnitude = u.id_magnitude
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
            $stmt = $connection->prepare("INSERT INTO products_materials (id_material, id_unit, id_company, id_product, quantity)
                                          VALUES (:id_material, :id_unit, :id_company, :id_product, :quantity)");
            $stmt->execute([
                'id_material' => $dataProductMaterial['material'],
                'id_unit' => $dataProductMaterial['unit'],
                'id_company' => $id_company,
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),
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
                                                 id_product = :id_product, quantity = :quantity
                                          WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $dataProductMaterial['idProductMaterial'],
                'id_material' => $dataProductMaterial['material'],
                'id_unit' => $dataProductMaterial['unit'],
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),
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
