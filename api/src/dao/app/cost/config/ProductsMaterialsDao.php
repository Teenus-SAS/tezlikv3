<?php

namespace tezlikv2\dao;

use tezlikv2\Constants\Constants;
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

    public function productsmaterials($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.id_product_material, m.id_material, m.reference, m.material, m.unit, pm.quantity, m.cost 
                                      FROM  products_materials pm
                                      INNER JOIN materials m ON m.id_material = pm.id_material 
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company");
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

    // Insertar productos materia prima
    public function insertProductsMaterialsByCompany($dataProductMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        // $quantity = $this->decimalsQuantity($dataProductMaterial);

        try {
            $stmt = $connection->prepare("INSERT INTO products_materials (id_material, id_company, id_product, quantity)
                                          VALUES (:id_material, :id_company, :id_product, :quantity)");
            $stmt->execute([
                'id_material' => $dataProductMaterial['material'],
                'id_company' => $id_company,
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),

                // 'id_material' => $dataProductMaterial['material'],
                // 'id_company' => $id_company,
                // 'id_product' => $dataProductMaterial['idProduct'],
                // 'quantity' => $dataProductMaterial['quantity']
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
        //$quantity = $this->decimalsQuantity($dataProductMaterial);

        try {
            $stmt = $connection->prepare("UPDATE products_materials SET id_material = :id_material, id_product = :id_product, quantity = :quantity
                                    WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $dataProductMaterial['idProductMaterial'],
                'id_material' => $dataProductMaterial['material'],
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),

                // 'id_product_material' => $dataProductMaterial['idProductMaterial'],
                // 'id_material' => $dataProductMaterial['material'],
                // 'id_product' => $dataProductMaterial['idProduct'],
                // 'quantity' => $dataProductMaterial['quantity']
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

    /* public function decimalsQuantity($dataProductMaterial)
    {
        if (ctype_digit($dataProductMaterial['quantity'])) $quantity = str_replace('.', '', $dataProductMaterial['quantity']);
        else $quantity = $dataProductMaterial['quantity'];

        return $quantity;
    }*/
}
