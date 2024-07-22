<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCompositeProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCompositeProductsByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        // $stmt = $connection->prepare("SELECT cp.id_composite_product, 0 AS id_product_material, cp.id_child_product, cp.id_product, p.reference, p.reference AS reference_material, p.product AS material, mg.id_magnitude, mg.magnitude, 
        //                                      u.id_unit, u.unit, u.abbreviation, cp.quantity, TRUNCATE(cp.cost, 2) AS cost_product_material, pc.cost_materials, pc.price, pc.sale_price, 'Producto' AS type, 0 AS waste, (cp.cost / total_material_cost.total_cost) * 100 AS participation
        //                               FROM products p 
        //                                 INNER JOIN composite_products cp ON cp.id_child_product = p.id_product
        //                                 INNER JOIN products_costs pc ON pc.id_product = cp.id_child_product
        //                                 INNER JOIN convert_units u ON u.id_unit = cp.id_unit
        //                                 INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
        //                                 INNER JOIN (
        //                                     SELECT cpm.id_product, (SUM(cpm.cost) + IFNULL(SUM(ccp.cost), 0)) AS total_cost FROM products_materials cpm 
        //                                     LEFT JOIN composite_products ccp ON ccp.id_product = cpm.id_product GROUP BY cpm.id_product
        //                                     ) AS total_material_cost ON p.id_product = total_material_cost.id_product
        //                               WHERE cp.id_company = :id_company AND p.active = 1 AND (SELECT active FROM products WHERE id_product = cp.id_product) = 1");
        $stmt = $connection->prepare("SELECT cp.id_composite_product, 0 AS id_product_material, cp.id_child_product, cp.id_product, p.reference, p.reference AS reference_material, p.product AS material, mg.id_magnitude, mg.magnitude, 
                                             u.id_unit, u.unit, u.abbreviation, cp.quantity, TRUNCATE(cp.cost, 2) AS cost_product_material, TRUNCATE(cp.cost_usd, 2) AS cost_product_material_usd, pc.cost_materials, pc.price, pc.sale_price, 'Producto' AS type, cp.waste, ((cp.cost / pc.cost_materials) * 100) AS participation
                                      FROM products p 
                                        INNER JOIN composite_products cp ON cp.id_child_product = p.id_product
                                        INNER JOIN products_costs pc ON pc.id_product = cp.id_product
                                        INNER JOIN convert_units u ON u.id_unit = cp.id_unit
                                        INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE cp.id_company = :id_company AND p.active = 1 AND (SELECT active FROM products WHERE id_product = cp.id_product) = 1");
        $stmt->execute(['id_company' => $id_company]);
        $compositeProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProducts));
        return $compositeProducts;
    }

    public function findCompositeProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product AND id_child_product = :id_child_product");
        $stmt->execute([
            'id_product' => $dataProduct['idProduct'],
            'id_child_product' => $dataProduct['compositeProduct']
        ]);
        $compositeProduct = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCompositeProductCost($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $compositeProduct = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCompositeProductByChild($id_child_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT cp.id_product, cp.id_child_product 
                                      FROM composite_products cp
                                      INNER JOIN products p ON p.id_product = cp.id_child_product
                                      INNER JOIN products_costs pc ON pc.id_product = cp.id_child_product
                                      WHERE cp.id_child_product = :id_child_product AND p.active = 1 
                                      GROUP BY cp.id_product");
        $stmt->execute([
            'id_child_product' => $id_child_product
        ]);
        $compositeProduct = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCostMaterialByCompositeProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT IFNULL(((cp.quantity * (1 + cp.waste / 100)) * (IFNULL(pc.cost_materials, 0) + IFNULL(pc.cost_workforce, 0) + IFNULL(pc.cost_indirect_cost, 0) + IFNULL((SELECT SUM(cost) FROM services WHERE id_product = cp.id_child_product), 0))), 0) AS cost
                                          FROM composite_products cp
                                            LEFT JOIN products_costs pc ON cp.id_child_product = pc.id_product
                                          WHERE cp.id_product = :id_product AND cp.id_child_product = :id_child_product");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
                'id_child_product' => $dataProduct['compositeProduct']
            ]);
            $costMaterialsProduct = $stmt->fetch($connection::FETCH_ASSOC);

            $dataProduct['cost'] = $costMaterialsProduct['cost'];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dataProduct = array('info' => true, 'message' => $message);
        }

        return $dataProduct;
    }

    public function updateCostCompositeProduct($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            if ($_SESSION['flag_currency_usd'] == 1) {
                $cost_usd = 0;

                if ($_SESSION['coverage_usd'] > 0)
                    $cost_usd = floatval($dataProduct['cost']) / floatval($_SESSION['coverage_usd']);

                $stmt = $connection->prepare("UPDATE composite_products SET cost = :cost, cost_usd = :cost_usd
                                              WHERE id_product = :id_product AND id_child_product = :id_child_product");
                $stmt->execute([
                    'cost' => $dataProduct['cost'],
                    'cost_usd' => $cost_usd,
                    'id_product' => $dataProduct['idProduct'],
                    'id_child_product' => $dataProduct['compositeProduct']
                ]);
            } else {
                $stmt = $connection->prepare("UPDATE composite_products SET cost = :cost WHERE id_product = :id_product AND id_child_product = :id_child_product");
                $stmt->execute([
                    'cost' => $dataProduct['cost'],
                    'id_product' => $dataProduct['idProduct'],
                    'id_child_product' => $dataProduct['compositeProduct']
                ]);
            }
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function deleteCompositeProductByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM composite_products WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }

    public function deleteChildProductByProduct($id_child_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_child_product = :id_child_product");
        $stmt->execute(['id_child_product' => $id_child_product]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM composite_products WHERE id_child_product = :id_child_product");
            $stmt->execute(['id_child_product' => $id_child_product]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
